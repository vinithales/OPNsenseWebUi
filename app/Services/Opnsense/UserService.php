<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    public function getUser($userId)
    {
        try {
            $response = $this->client->get("/api/auth/user/get/{$userId}");
            if (isset($response['user'])) {
                return $response['user'];
            }

            // Fallback: search in all users
            $allUsers = $this->getUsers();
            foreach ($allUsers as $user) {
                if (isset($user['uuid']) && $user['uuid'] === $userId) {
                    return $user;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching user {$userId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUsers()
    {
        try {
            $response = $this->client->post('/api/auth/user/search', [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $statusCode = $response->getStatusCode(); // pega o código HTTP
            $body = (string) $response->getBody();
            Log::debug('Response Body: ' . $body);

            if ($statusCode !== 200) {
                throw new \Exception("Failed to fetch users: HTTP $statusCode");
            }

            $data = json_decode($body, true);
            if (!isset($data['rows'])) {
                throw new \Exception('Invalid response structure');
            }

            return $data['rows'];
        } catch (\Exception $e) {
            Log::error('Error fetching users from OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }



    public function createUser(array $userData)
    {
        try {
            // Log da requisição enviada
            Log::debug('Request Payload: ' . json_encode($userData));

            $response = $this->client->post('/api/auth/user/add', [
                'json' => $userData, // Remove json_encode() aqui - Guzzle já faz isso
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $body = $response->getBody()->getContents();
            Log::debug('Response Body: ' . $body);

            $data = json_decode($body, true);

            if (isset($data['result']) && $data['result'] === 'saved') {
                return true;
            }

            // Captura detalhes de validação se disponíveis
            $errorMessage = 'Failed to create user: ';
            if (isset($data['validations'])) {
                $errorMessage .= json_encode($data['validations']);
            } elseif (isset($data['result'])) {
                $errorMessage .= $data['result'];
            } else {
                $errorMessage .= 'Unknown error';
            }

            throw new \Exception($errorMessage);
        } catch (\Throwable $e) {
            Log::error('Error creating user in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUser($userId, array $userData)
    {
        try {

            $response = $this->client->post('auth/user/set/{$userId}', [
                'json' => $userData
            ]);

            if ($response['result'] === 'saved') {
                return true;
            }
            throw new \Exception('Failed to update user: ' . ($response['validation'] ?? $response['result']));
        } catch (\Throwable $e) {
            Log::error('Error updating user in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser($userId)
    {
        try {

            $response = $this->client->post("/api/auth/user/del/{$userId}", [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $body = (string) $response->getBody();
            Log::debug('Response Body: ' . $body);
            $data = json_decode($body, true);

           if (isset($data['result']) && $data['result'] === 'deleted') {
            return true;
        }

            throw new \Exception('Failed to delete user: ' . ($data['result'] ?? 'Unknown error'));
        } catch (\Throwable $e) {
            Log::error('Error deleting user in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }
}
