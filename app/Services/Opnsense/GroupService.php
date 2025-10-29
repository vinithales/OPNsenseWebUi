<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;


class GroupService extends BaseService
{
    protected $userService;

    public function __construct()
    {
        parent::__construct();
        // Injeta UserService para contar membros
        $this->userService = app(UserService::class);
    }

    public function getGroups($withMembersCount = true)
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
            Log::debug('GroupService Response Body: ' . $body);

            if ($statusCode !== 200) {
                throw new \Exception("Failed to fetch groups: HTTP $statusCode");
            }

            $data = json_decode($body, true);
            if (!isset($data['rows'])) {
                throw new \Exception('Invalid response structure');
            }

            $groups = $data['rows'];

            // Log para debug da estrutura dos grupos
            Log::info('Estrutura dos grupos retornados:', [
                'total' => count($groups),
                'first_group' => $groups[0] ?? null,
                'all_groups_keys' => array_keys($groups[0] ?? [])
            ]);

            // Adiciona contagem de membros se solicitado
            if ($withMembersCount) {
                $groups = $this->addMembersCount($groups);
            }

            return $groups;
        } catch (\Exception $e) {
            Log::error('Error fetching groups from OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Adiciona a contagem de membros para cada grupo
     */
    protected function addMembersCount(array $groups)
    {
        try {
            // Busca todos os usuários uma única vez
            $allUsers = $this->userService->getUsers();

            // Para cada grupo, conta quantos usuários pertencem a ele
            foreach ($groups as &$group) {
                $count = 0;
                $groupName = $group['name'] ?? '';
                $groupGid = $group['gid'] ?? '';
                $groupUuid = $group['uuid'] ?? '';

                foreach ($allUsers as $user) {
                    if (isset($user['group_memberships']) && !empty($user['group_memberships'])) {
                        $memberships = is_string($user['group_memberships'])
                            ? array_map('trim', explode(',', $user['group_memberships']))
                            : $user['group_memberships'];

                        // Verifica se o usuário pertence ao grupo (por UUID, GID ou nome)
                        foreach ($memberships as $membership) {
                            if ($membership === $groupUuid ||
                                $membership === $groupGid ||
                                strcasecmp($membership, $groupName) === 0) {
                                $count++;
                                break; // Usuário já contado, próximo usuário
                            }
                        }
                    }
                }

                $group['members_count'] = $count;
            }

            return $groups;
        } catch (\Exception $e) {
            Log::warning('Erro ao contar membros dos grupos: ' . $e->getMessage());
            // Retorna os grupos sem a contagem em caso de erro
            foreach ($groups as &$group) {
                $group['members_count'] = 0;
            }
            return $groups;
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

