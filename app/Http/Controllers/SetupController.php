<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    public function index()
    {
        // Verifica se já foi configurado
        if (env('APP_FIRST_RUN', 'true') === 'false') {
            return redirect()->route('login')->with('info', 'Sistema já configurado.');
        }

        return view('setup.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Dados do usuário admin
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',

            // Configurações do OPNsense
            'opnsense_url' => 'required|url',
            'opnsense_api_key' => 'required|string',
            'opnsense_api_secret' => 'required|string',

            // Configurações avançadas (opcionais)
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'db_port' => 'nullable|integer|min:1|max:65535',
        ], [
            'admin_name.required' => 'O nome do administrador é obrigatório',
            'admin_email.required' => 'O email do administrador é obrigatório',
            'admin_email.email' => 'Digite um email válido',
            'admin_password.required' => 'A senha é obrigatória',
            'admin_password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'admin_password.confirmed' => 'As senhas não conferem',
            'opnsense_url.required' => 'A URL do OPNsense é obrigatória',
            'opnsense_url.url' => 'Digite uma URL válida',
            'opnsense_api_key.required' => 'A chave API é obrigatória',
            'opnsense_api_secret.required' => 'O secret API é obrigatório',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Atualiza arquivo .env
            $this->updateEnvFile([
                'APP_NAME' => $request->app_name ?? 'OPNsense Web UI',
                'APP_URL' => $request->app_url ?? config('app.url'),
                'DB_PORT' => $request->db_port ?? env('DB_PORT', '3307'),
                'OPNSENSE_API_BASE_URL' => $request->opnsense_url,
                'OPNSENSE_API_KEY' => $request->opnsense_api_key,
                'OPNSENSE_API_SECRET' => $request->opnsense_api_secret,
                'APP_FIRST_RUN' => 'false',
            ]);

            // Executa migrations
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                Log::warning('Migrations já executadas ou erro ao executar: ' . $e->getMessage());
            }

            // Cria usuário administrador no banco local
            DB::table('users')->insert([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Limpa cache de configuração
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()->route('login')
                ->with('success', 'Sistema configurado com sucesso! Faça login para continuar.');

        } catch (\Exception $e) {
            Log::error('Erro ao configurar sistema: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao configurar sistema: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Atualiza valores no arquivo .env
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

        $formatValue = function ($value, $key) {
            $value = (string) $value;
            // Do not quote canonical booleans and integers
            $lower = strtolower($value);
            $isBoolean = in_array($lower, ['true', 'false'], true);
            $isInteger = preg_match('/^-?\d+$/', $value) === 1;
            if ($isBoolean || $isInteger) {
                return $value;
            }
            $needsQuotes = preg_match('/\s|#|=|^$/', $value) === 1;
            $escaped = str_replace('"', '\\"', str_replace("\r", '', str_replace("\n", '', $value)));
            return $needsQuotes ? '"' . $escaped . '"' : $escaped;
        };

        foreach ($data as $key => $value) {
            $formatted = $formatValue($value, $key);
            if (preg_match("/^{$key}=.*$/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$formatted}", $envContent);
            } else {
                $envContent .= (str_ends_with($envContent, "\n") ? '' : "\n") . "{$key}={$formatted}\n";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
