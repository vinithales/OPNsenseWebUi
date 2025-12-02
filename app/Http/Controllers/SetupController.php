<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SetupController extends Controller
{
    public function index()
    {
        // Verifica se já foi configurado
        $firstRun = env('APP_FIRST_RUN', 'true');
        if ($firstRun === 'false' || $firstRun === false) {
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
            // 1) Testa as credenciais do OPNsense antes de persistir
            $test = $this->testOpnsenseCredentials(
                $request->opnsense_url,
                $request->opnsense_api_key,
                $request->opnsense_api_secret
            );
            if ($test !== true) {
                return redirect()->back()
                    ->with('error', $test)
                    ->with('setup_active_tab', 'opnsense')
                    ->withInput();
            }

            // Ajusta config runtime da porta do banco (sem depender de novo bootstrap)
            if ($request->db_port) {
                config(['database.connections.mysql.port' => $request->db_port]);
            }

            // Executa migrations antes de alterar estado de primeira execução
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                Log::warning('Migrations já executadas ou erro ao executar: ' . $e->getMessage());
            }

            // Criação do usuário admin em transação
            DB::beginTransaction();
            try {
                // Evita duplicidade se rodar novamente por algum motivo
                $existing = User::where('email', $request->admin_email)->first();
                if (!$existing) {
                    User::create([
                        'name' => $request->admin_name,
                        'email' => $request->admin_email,
                        'password' => Hash::make($request->admin_password),
                        'user_type' => User::TYPE_ADMIN,
                        'status' => User::STATUS_ATIVO,
                    ]);
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Falha ao criar usuário admin no setup: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Erro ao criar usuário administrador: ' . $e->getMessage())
                    ->withInput();
            }

            // Atualiza arquivo .env somente após sucesso das operações críticas
            $this->updateEnvFile([
                'APP_NAME' => $request->app_name ?? 'OPNsense Web UI',
                'APP_URL' => $request->app_url ?? config('app.url'),
                'DB_PORT' => $request->db_port ?? env('DB_PORT', '3307'),
                'OPNSENSE_API_BASE_URL' => $request->opnsense_url,
                'OPNSENSE_API_KEY' => $request->opnsense_api_key,
                'OPNSENSE_API_SECRET' => $request->opnsense_api_secret,
                'APP_FIRST_RUN' => 'false',
            ]);

            // Ajusta runtime para este request (artisan serve reutiliza processo)
            putenv('APP_FIRST_RUN=false');
            $_ENV['APP_FIRST_RUN'] = 'false';
            $_SERVER['APP_FIRST_RUN'] = 'false';

            // Limpa caches para refletir novo estado
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

    /**
     * Valida as credenciais informadas do OPNsense realizando uma chamada simples autenticada.
     * Retorna true em caso de sucesso, ou string com mensagem de erro em caso de falha.
     */
    private function testOpnsenseCredentials(string $baseUrl, string $apiKey, string $apiSecret)
    {
        try {
            // Normaliza base URL
            $baseUrl = rtrim($baseUrl, '/');

            $client = new Client([
                'base_uri' => $baseUrl,
                'auth' => [$apiKey, $apiSecret],
                'verify' => false,
                'headers' => [ 'Accept' => 'application/json' ],
                'http_errors' => false,
                'timeout' => 10,
                'connect_timeout' => 5,
            ]);

            // Endpoint simples e seguro para testar auth: listar usuários
            $resp = $client->post('/api/auth/user/search', [ 'json' => [] ]);
            $code = $resp->getStatusCode();
            $body = (string) $resp->getBody();

            if ($code !== 200) {
                if (in_array($code, [401, 403], true)) {
                    return 'Credenciais inválidas do OPNsense (API Key/Secret). Verifique se as chaves estão corretas e ativas (System → Access → Users → API Keys).';
                }
                if ($code === 404) {
                    return 'URL do OPNsense incorreta. Confirme o endereço (http/https) e se a API está acessível.';
                }
                if ($code >= 500) {
                    return 'Erro no OPNsense (HTTP 5xx). Tente novamente mais tarde ou verifique o appliance.';
                }
                return 'Falha ao conectar ao OPNsense (HTTP ' . $code . '). Verifique a URL e as credenciais.';
            }

            // Opcional: verificar estrutura
            $data = json_decode($body, true);
            if (!is_array($data)) {
                return 'Resposta inesperada da API do OPNsense. Verifique a URL e tente novamente.';
            }

            return true;
        } catch (RequestException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'timed out')) {
                return 'Tempo de conexão esgotado. Verifique a rede/URL do OPNsense.';
            }
            return 'Não foi possível conectar ao OPNsense. Verifique a rede e a URL informada.';
        } catch (\Throwable $e) {
            return 'Erro inesperado ao validar as credenciais do OPNsense.';
        }
    }

    /**
     * Marca o sistema como primeira execução novamente
     * para permitir novo cadastro do administrador e credenciais do OPNsense.
     */
    public function resetSystem(Request $request)
    {
        try {
            // Remove todos os usuários e dados relacionados
            try {
                DB::beginTransaction();
                // Desabilita FKs para truncates seguros (MySQL)
                try { DB::statement('SET FOREIGN_KEY_CHECKS=0'); } catch (\Throwable $e) {}
                try { DB::table('password_reset_tokens')->truncate(); } catch (\Throwable $e) { Log::warning('Falha ao truncar password_reset_tokens: ' . $e->getMessage()); }
                try { DB::table('personal_access_tokens')->truncate(); } catch (\Throwable $e) { Log::warning('Falha ao truncar personal_access_tokens: ' . $e->getMessage()); }
                try { DB::table('users')->truncate(); } catch (\Throwable $e) { Log::warning('Falha ao truncar users: ' . $e->getMessage()); }
                try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $e) {}
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Erro ao remover usuários durante reset: ' . $e->getMessage());
            }

            // Atualiza .env para primeira execução
            $this->updateEnvFile([
                'APP_FIRST_RUN' => 'true',
            ]);

            // Ajusta runtime para refletir imediatamente
            putenv('APP_FIRST_RUN=true');
            $_ENV['APP_FIRST_RUN'] = 'true';
            $_SERVER['APP_FIRST_RUN'] = 'true';

            return redirect()->route('setup.index')
                ->with('info', 'Sistema redefinido. Todos os usuários foram removidos. Conclua a configuração inicial novamente.');
        } catch (\Throwable $e) {
            Log::error('Falha ao redefinir sistema: ' . $e->getMessage());
            return back()->with('error', 'Falha ao redefinir sistema. Tente novamente.');
        }
    }
}
