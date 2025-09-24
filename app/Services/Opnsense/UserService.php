<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    public function getUser($userId)
    {
        try {
            $response = $this->client->post("/api/auth/user/get/{$userId}", [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $body = $response->getBody()->getContents();
            Log::debug("Retonro de busca especifica:" .$body);
            $data = json_decode($body, true);


            if (!is_array($data) || !isset($data['user'])) {
                Log::warning("Resposta inesperada ao buscar usuário {$userId}: " . $body);
                return null;
            }

            $user = $data['user'];

            if (!isset($user['uid']) || !isset($user['name'])) {
                Log::info("Usuário {$userId} não encontrado ou dados incompletos.");
                return null;
            }

            return $user;
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

            $response = $this->client->post('/api/auth/user/set/{$userId}', [
                'json' => $userData,
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            Log::debug('RETORNO DA ATT:'. $response);

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

    public function findUserByName($name)
    {
        try {
            $response = $this->client->post('/api/auth/user/search', [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);


            $body = (string) $response->getBody();
            Log::debug('Response Body: ' . $body);
            $data = json_decode($body, true);


            if (isset($data['rows']) && is_array($data['rows'])) {
                foreach ($data['rows'] as $user) {
                    if (isset($user['name']) && $user['name'] === $name) {
                        return $user;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error searching user by name in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }
}
