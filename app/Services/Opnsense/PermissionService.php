<?php

namespace App\Services\Opnsense;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Services\Opnsense\GroupService;

class PermissionService extends BaseService
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        parent::__construct();
        $this->groupService = $groupService;
    }

    /**
     * Busca privilégios disponíveis da API do OPNsense
     */
    public function fetchPrivileges()
    {
        try {
            $response = $this->client->post('/api/auth/priv/search', [
                'json' => [],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    Log::debug('Effective request URL previlegios: ' . $stats->getEffectiveUri());
                }
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
           // Log::debug('Response Body: ' . $body);

            if ($statusCode !== 200) {
                throw new \Exception("Failed to fetch privileges: HTTP $statusCode");
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            if (!isset($data['rows']) || !is_array($data['rows'])) {
                throw new \Exception('Invalid response structure: missing or invalid "rows" key');
            }

            return [
                'privileges' => $data['rows'],
                'total' => $data['total'] ?? count($data['rows']),
                'current_page' => $data['current'] ?? 1
            ];
        } catch (\Throwable $e) {
            Log::error('Error fetching privileges from OPNsense: ' . $e->getMessage());
            throw $e;
        }
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
