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
            //Log::debug('Response Body: ' . $body);
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
            $response = $this->client->post('/api/auth/group/add', [
                'json' => $groupData,
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL: ' . $stats->getEffectiveUri());
                }
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            $data = json_decode($body, true);

            if ($statusCode !== 200) {
                throw new \Exception("HTTP $statusCode - " . ($data['message'] ?? 'Unknown error'));
            }

            if (isset($data['result']) && $data['result'] === 'saved') {
                Log::info('Group created successfully');
                return true;
            }

            Log::error('Group creation failed with response:', $data);
            throw new \Exception('Failed to create group: ' . json_encode($data));
        } catch (\Throwable $e) {
            Log::error('Error creating group in OPNsense', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    public function updateGroup(string $groupId, array $groupData)
    {
        try {
            $response = $this->client->post("/api/auth/group/set/{$groupId}", [
                'json' => $groupData
            ]);

            $body = $response->getBody()->getContents();

            $data = json_decode($body, true);

            if (isset($data['result']) && $data['result'] === 'saved') {
                Log::info("Grupo {$groupId} atualizado com sucesso");
                return true;
            }

            $errorMessage = isset($data['validations'])
                ? json_encode($data['validations'])
                : ($data['result'] ?? 'Unknown error');

            Log::error('Erro ao atualizar grupo: ' . $errorMessage);
            throw new \Exception('Failed to update group: ' . $errorMessage);
        } catch (\Throwable $e) {
            Log::error('Error updating group in OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteGroup($groupId)
    {
        try {
            $response = $this->client->post("/api/auth/group/del/{$groupId}", [
                'json' => [],
            ]);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($data['result'] === 'deleted') {
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
            $response = $this->client->post("/api/auth/group/get/{$groupId}", [
                'json' => [],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (!is_array($data) || !isset($data['group'])) {
                Log::warning("Resposta inesperada ao buscar grupo {$groupId}: " . $body);
                return null;
            }

            $group = $data['group'];

            if (!isset($group['gid']) || !isset($group['name'])) {
                Log::info("Grupo {$groupId} não encontrado ou dados incompletos.");
                return null;
            }

            return $group;
        } catch (\Exception $e) {
            Log::error("Error fetching grouo {$groupId}: " . $e->getMessage());
            throw $e;
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

    /**
     * Busca grupo com variações de nome (aluno, alunos, Aluno, Alunos, etc)
     *
     * @param string $baseGroupName Nome base (ex: 'aluno' ou 'professor')
     * @return array|null
     */
    public function findGroupWithVariations(string $baseGroupName): ?array
    {
        try {
            $response = $this->client->post('/api/auth/group/search', [
                'json' => [],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (!isset($data['rows'])) {
                return null;
            }

            // Normaliza o nome base para comparação
            $normalizedBase = strtolower(trim($baseGroupName));

            // Variações possíveis
            $variations = [
                $normalizedBase,                    // aluno
                $normalizedBase . 's',              // alunos
                ucfirst($normalizedBase),           // Aluno
                ucfirst($normalizedBase) . 's',     // Alunos
                strtoupper($normalizedBase),        // ALUNO
                strtoupper($normalizedBase) . 'S',  // ALUNOS
            ];

            foreach ($data['rows'] as $group) {
                if (!isset($group['name'])) {
                    continue;
                }

                $groupName = trim($group['name']);

                // Compara com todas as variações
                foreach ($variations as $variation) {
                    if (strcasecmp($groupName, $variation) === 0) {
                        Log::info("Grupo encontrado com variação", [
                            'buscado' => $baseGroupName,
                            'encontrado' => $groupName,
                            'gid' => $group['gid'] ?? $group['uuid'] ?? null
                        ]);
                        return $group;
                    }
                }
            }

            Log::warning("Nenhum grupo encontrado para: {$baseGroupName}", [
                'variacoes_buscadas' => $variations
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar grupo com variações: ' . $e->getMessage());
            return null;
        }
    }
}

