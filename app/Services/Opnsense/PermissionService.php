<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Services\Opnsense\GroupService;

class PermissionService extends BaseService
{
    protected $groupService;

    public function __construct($client, GroupService $groupService)
    {
        parent::__construct($client);
        $this->groupService = $groupService;
    }
    /**
     * Lista local de privilégios como fallback
     */
    public function getAvailablePrivileges()
    {
        return [
            'page-all' => 'Acesso completo',
            'page-system' => 'Sistema',
            'page-system-advanced' => 'Sistema: Configurações avançadas',
            'page-system-usermanagement' => 'Sistema: Gerenciamento de usuários',
            'page-diagnostics' => 'Diagnósticos',
            'page-services' => 'Serviços',
            'page-vpn' => 'VPN',
            'page-firewall' => 'Firewall',
            'page-captiveportal' => 'Portal Cativo',
            'gui-zenarmor-dashboard' => 'Zenarmor: Dashboard/Reports',
            'user-shell-access' => 'Acesso ao shell',
            'page-intrusion-detection' => 'Detecção de intrusão'
        ];
    }

    /**
     * Busca privilégios disponíveis da API do OPNsense
     */
    public function fetchPrivileges()
    {
        return Cache::remember('opnsense_privileges', 3600, function () {
            try {
                $endpoints = [
                    '/api/auth/user/get',
                    '/api/auth/group/get',
                    '/api/auth/user/getOption/privileges'
                ];

                foreach ($endpoints as $endpoint) {
                    try {
                        $response = $this->client->get($endpoint);

                        if (isset($response['options']['privileges'])) {
                            return $response['options']['privileges'];
                        }

                        if (isset($response['privileges'])) {
                            return $response['privileges'];
                        }

                        if (isset($response['options']) && is_array($response['options'])) {
                            return $response['options'];
                        }
                    } catch (\Exception $e) {
                        Log::debug("Endpoint {$endpoint} falhou: " . $e->getMessage());
                        continue;
                    }
                }

                throw new \Exception("Nenhum endpoint retornou dados de privilégios válidos");
            } catch (\Exception $e) {
                Log::warning("Falha ao buscar privilégios da API: " . $e->getMessage());
                return $this->getAvailablePrivileges();
            }
        });
    }

    /**
     * Atribui privilégios a um grupo
     */
    public function assignPrivilegesToGroup($groupId, array $privileges)
    {
        try {
            $groupData = $this->groupService->getGroup($groupId);
            if (!$groupData) {
                throw new \Exception("Grupo não encontrado: {$groupId}");
            }

            // Prepara os dados para envio
            $data = [
                'group' => [
                    'name' => $groupData['name'] ?? '',
                    'description' => $groupData['description'] ?? '',
                    'priv' => implode(',', $privileges),
                    'member' => $groupData['member'] ?? '0',
                ]
            ];

            // Envia a requisição
            $response = $this->client->post("/api/auth/group/set/{$groupId}", $data);

            // Verifica a resposta
            if (isset($response['result']) && $response['result'] === 'saved') {
                // Recarrega os serviços para aplicar as mudanças
                $this->client->post('/api/core/service/reload');
                return true;
            }

            // Tratamento de erro detalhado
            $errorMsg = $response['validation_errors'] ??
                       $response['validation'] ??
                       $response['result'] ??
                       'Erro desconhecido';

            throw new \Exception('Falha ao atribuir permissões: ' . json_encode($errorMsg));

        } catch (\Exception $e) {
            Log::error('Erro ao atribuir permissões no OPNsense: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtém os privilégios de um grupo específico
     */
    public function getGroupPrivileges($groupId)
    {
        try {
            $groupData = $this->groupService->getGroup($groupId);
            if ($groupData && isset($groupData['priv'])) {
                return array_filter(explode(',', $groupData['priv']));
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar permissões do grupo: ' . $e->getMessage());
            return [];
        }
    }

    // Métodos de grupo foram movidos para GroupService

    /**
     * Limpa o cache de privilégios
     */
    public function clearPrivilegesCache()
    {
        Cache::forget('opnsense_privileges');
    }
}
