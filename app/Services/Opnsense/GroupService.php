<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;


class GroupService extends BaseService
{
    public function getGroups()
    {
        try {
            $response = $this->client->post('/api/auth/group/search', [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            Log::debug('Response Body: ' . $body);
            if ($statusCode !== 200) {
                throw new \Exception("Failed to fetch groups: HTTP $statusCode");
            }

            $data = json_decode($body, true);
            if (!isset($data['rows'])) {
                throw new \Exception('Invalid response structure');
            }

            return $data['rows'];
        } catch (\Exception $e) {
            Log::error('Error fetching groups from OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createGroup(array $groupData)
    {
        try {
            $response = $this->client->post('auth/group/add', [
                'json' => $groupData
            ]);

            if ($response['result'] === 'saved') {
                return true;
            }
            throw new \Exception('Failed to create group: ' . ($response['validation'] ?? $response['result']));
        } catch (\Throwable $e) {
            Log::error('Error creating group in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateGroup($groupId, array $groupData)
    {
        try {
            $response = $this->client->post("auth/group/set/{$groupId}", [
                'json' => $groupData
            ]);
            if ($response['result'] === 'saved') {
                return true;
            }
            throw new \Exception('Failed to update group: ' . ($response['validation'] ?? $response['result']));
        } catch (\Throwable $e) {
            Log::error('Error updating group in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteGroup($groupId)
    {
        try {
            $response = $this->client->post("auth/group/del/{$groupId}");

            if ($response['result'] === 'deleted') {
                return true;
            }
            throw new \Exception('Failed to delete group: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Throwable $e) {
            Log::error('Error deleting group in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca dados de um grupo específico
     */
    public function getGroup($groupId)
    {
        try {
            // Tenta buscar o grupo diretamente pela API
            $response = $this->client->get("/api/auth/group/get/{$groupId}");
            if (isset($response['group'])) {
                return $response['group'];
            }
            // Fallback: busca todos os grupos e filtra
            $allGroups = $this->getGroups();
            foreach ($allGroups as $group) {
                if (isset($group['uuid']) && $group['uuid'] === $groupId) {
                    return $group;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar grupo {$groupId}: " . $e->getMessage());
            return null;
        }
    }


    public function findGroupByName($groupName)
    {
        try {
            $response = $this->client->post('/api/auth/group/search', [
                'json' => [],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['rows'])) {
                foreach ($data['rows'] as $group) {
                    if (isset($group['name']) && $group['name'] === $groupName) {
                        return $group;
                    }
                    if (is_numeric($groupName) && isset($group['gid']) && $group['gid'] == $groupName) {
                        return $group;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error finding group by name: ' . $e->getMessage());
            return null;
        }
    }
}
