<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    public function getUser($userId)
    {
        try {
            $response = $this->client->get("auth/user/get/{$userId}");
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

            $body = $response->getBody();
            Log::debug('Response Body: ' . $body); // Debugging line to inspect the raw response body
            $data = json_decode($body, true);
            print_r($data); // Debugging line to inspect the response structure

            if ($data['status'] === 'success') {
                return $data['rows'];
            }

            throw new \Exception('Failed to fetch users from OPNsense: ' . ($data['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Error fetching users from OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }



    public function createUser(array $userData)
    {
        try {
            $response = $this->client->post('auth/user/add', [
                'json' => $userData
            ]);

            if ($response['result'] === 'saved') {
                return true;
            }
            throw new \Exception('Failed to create user: ' . ($response['validation'] ?? $response['result']));
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
            $response = $this->client->post('auth/user/del/{$userId}');

            if ($response['result'] === 'deleted') {
                return true;
            }

            throw new \Exception('Failed to delete user: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Throwable $e) {
            Log::error('Error deleting user in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }
}
