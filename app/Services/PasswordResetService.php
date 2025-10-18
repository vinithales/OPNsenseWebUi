<?php

namespace App\Services;

use App\Services\Opnsense\UserService as OPNsenseUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    protected $opnsenseUserService;

    public function __construct(OPNsenseUserService $opnsenseUserService)
    {
        $this->opnsenseUserService = $opnsenseUserService;
    }

    /**
     * Cria um token de reset de senha
     *
     * @param string $email E-mail ou username do OPNsense
     * @return string Token gerado
     */
    public function createResetToken(string $identifier): string
    {
        // Busca usuário no OPNsense por nome ou email
        $user = $this->findUserByIdentifier($identifier);

        if (!$user) {
            throw new \Exception('Usuário não encontrado no sistema.');
        }

        // Gera token único
        $token = Str::random(64);

        // Armazena token no banco (temporário)
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user['email']],
            [
                'email' => $user['email'],
                'username' => $user['name'],
                'uuid' => $user['uuid'] ?? $user['uid'],
                'token' => hash('sha256', $token),
                'created_at' => Carbon::now(),
            ]
        );

        Log::info('Token de reset criado', [
            'email' => $user['email'],
            'username' => $user['name']
        ]);

        return $token;
    }

    /**
     * Valida token de reset
     *
     * @param string $token
     * @return array|null Dados do reset se válido
     */
    public function validateToken(string $token): ?array
    {
        $hashedToken = hash('sha256', $token);

        $reset = DB::table('password_resets')
            ->where('token', $hashedToken)
            ->first();

        if (!$reset) {
            return null;
        }

        // Verifica se token expirou (1 hora)
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->addHour()->isPast()) {
            DB::table('password_resets')->where('token', $hashedToken)->delete();
            return null;
        }

        return (array) $reset;
    }

    /**
     * Reseta senha no OPNsense
     *
     * @param string $token
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $resetData = $this->validateToken($token);

        if (!$resetData) {
            throw new \Exception('Token inválido ou expirado.');
        }

        try {
            // Busca dados completos do usuário no OPNsense
            $user = $this->opnsenseUserService->getUser($resetData['uuid']);

            if (!$user) {
                throw new \Exception('Usuário não encontrado no OPNsense.');
            }

            // Atualiza apenas a senha no OPNsense
            $userData = [
                'password' => $newPassword,
                'scrambled_password' => '0', // Indica que senha não está criptografada ainda
            ];

            $updated = $this->opnsenseUserService->updateUser($resetData['uuid'], $userData);

            if ($updated) {
                // Remove token usado
                DB::table('password_resets')
                    ->where('token', hash('sha256', $token))
                    ->delete();

                Log::info('Senha resetada com sucesso no OPNsense', [
                    'username' => $resetData['username'],
                    'email' => $resetData['email']
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Erro ao resetar senha no OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca usuário no OPNsense por e-mail ou username
     *
     * @param string $identifier
     * @return array|null
     */
    protected function findUserByIdentifier(string $identifier): ?array
    {
        try {
            // Busca por nome (username/RA)
            $user = $this->opnsenseUserService->findUserByName($identifier);
            if ($user) {
                return $user;
            }

            // Busca por e-mail na lista de usuários
            $users = $this->opnsenseUserService->getUsers();
            foreach ($users as $user) {
                if (isset($user['email']) && strtolower($user['email']) === strtolower($identifier)) {
                    return $user;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Limpa tokens expirados (executar periodicamente)
     */
    public function cleanExpiredTokens(): int
    {
        $expiredTime = Carbon::now()->subHour();

        $deleted = DB::table('password_resets')
            ->where('created_at', '<', $expiredTime)
            ->delete();

        Log::info("Tokens expirados removidos: {$deleted}");

        return $deleted;
    }

    /**
     * Gera nova senha aleatória e atualiza no OPNsense
     * Útil para reset administrativo
     *
     * @param string $userUuid
     * @return string Nova senha gerada
     */
    public function generateAndSetNewPassword(string $userUuid): string
    {
        $newPassword = $this->generateSecurePassword();

        $userData = [
            'password' => $newPassword,
            'scrambled_password' => '0',
        ];

        $updated = $this->opnsenseUserService->updateUser($userUuid, $userData);

        if (!$updated) {
            throw new \Exception('Falha ao atualizar senha no OPNsense.');
        }

        Log::info('Nova senha gerada para usuário', ['uuid' => $userUuid]);

        return $newPassword;
    }

    /**
     * Gera senha segura
     */
    protected function generateSecurePassword(int $length = 10): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%&*';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $allCharacters = $uppercase . $lowercase . $numbers . $special;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        return str_shuffle($password);
    }
}
