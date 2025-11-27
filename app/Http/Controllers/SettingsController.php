<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => env('APP_NAME'),
            'app_url' => env('APP_URL'),
            'db_port' => env('DB_PORT'),
            'opnsense_url' => env('OPNSENSE_API_BASE_URL'),
            'opnsense_api_key' => env('OPNSENSE_API_KEY'),
            'opnsense_api_secret' => env('OPNSENSE_API_SECRET'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opnsense_url' => 'required|url',
            'opnsense_api_key' => 'required|string',
            'opnsense_api_secret' => 'required|string',
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'db_port' => 'nullable|integer|min:1|max:65535',
        ], [
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
                'APP_NAME' => $request->app_name ?? env('APP_NAME'),
                'APP_URL' => $request->app_url ?? env('APP_URL'),
                'DB_PORT' => $request->db_port ?? env('DB_PORT'),
                'OPNSENSE_API_BASE_URL' => $request->opnsense_url,
                'OPNSENSE_API_KEY' => $request->opnsense_api_key,
                'OPNSENSE_API_SECRET' => $request->opnsense_api_secret,
            ]);

            // Limpa cache de configuração
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()->route('settings.index')
                ->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage())
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
