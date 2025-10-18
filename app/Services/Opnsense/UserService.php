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
            $data = json_decode($body, true);


            if (!is_array($data) || !isset($data['user'])) {
                return null;
            }

            $user = $data['user'];

            if (!isset($user['uid']) || !isset($user['name'])) {
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
            //Log::debug('Response Body: ' . $body);

            $data = json_decode($body, true);

            if (isset($data['result']) && $data['result'] === 'saved') {
                return true;
            }

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

    public function updateUser(string $userId, array $userData)
    {
        try {
            Log::info('Tentando atualizar usuário:', [
                 'user' => $userData
            ]);

            $response = $this->client->post("/api/auth/user/set/{$userId}", [
                'json' => [
                    'user' => $userData
                ]
            ]);

            $body = $response->getBody()->getContents();
            Log::debug('Body aqui:' .$body);
            $data = json_decode($body, true);

            Log::info('Resposta da API:', $data);

            if (isset($data['result']) && $data['result'] === 'saved') {
                return true;
            }

            $errorMessage = isset($data['validations'])
                ? json_encode($data['validations'])
                : ($data['message'] ?? $data['result'] ?? 'Unknown error');

            Log::error('Erro ao atualizar user: ' . $errorMessage);
            throw new \Exception('Failed to update user: ' . $errorMessage);
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

    /**
     * Extrai informações do campo comment
     * Formato esperado: "RA: 123456 | Tipo: aluno | ..."
     *
     * @param string $comment
     * @return array ['ra' => '123456', 'user_type' => 'aluno']
     */
    public function parseComment($comment)
    {
        $data = [
            'ra' => null,
            'user_type' => null,
        ];

        if (empty($comment)) {
            return $data;
        }

        // Extrai RA
        if (preg_match('/RA:\s*([^\|]+)/', $comment, $matches)) {
            $data['ra'] = trim($matches[1]);
        }

        // Extrai Tipo
        if (preg_match('/Tipo:\s*([^\|]+)/', $comment, $matches)) {
            $data['user_type'] = trim($matches[1]);
        }

        return $data;
    }

    /**
     * Enriquece a lista de usuários com dados do comment
     *
     * @param array $users
     * @return array
     */
    public function enrichUsersWithMetadata(array $users)
    {
        foreach ($users as &$user) {
            $metadata = $this->parseComment($user['descr'] ?? $user['comment'] ?? '');
            $user['ra'] = $metadata['ra'];
            $user['user_type'] = $metadata['user_type'];
        }

        return $users;
    }
}
