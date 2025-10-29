<?php

namespace App\Services\Opnsense;

use Illuminate\Support\Facades\Log;

/**
 * Serviço para gerenciar aliases no OPNsense
 */
class AliasService extends BaseService
{
    /**
     * Adiciona um IP a um alias
     *
     * @param string $aliasName Nome do alias
     * @param string $ipAddress Endereço IP a ser adicionado
     * @return bool|array
     */
    public function addIpToAlias(string $aliasName, string $ipAddress): bool|array
    {
        try {
            Log::info("Adicionando IP ao alias", [
                'alias' => $aliasName,
                'ip' => $ipAddress
            ]);

            $response = $this->client->post('/api/firewall/alias_util/add', [
                'json' => [
                    'alias' => $aliasName,
                    'address' => $ipAddress
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("IP adicionado com sucesso ao alias", ['response' => $data]);
                return $data;
            }

            Log::error("Falha ao adicionar IP ao alias", [
                'status' => $statusCode,
                'body' => $body
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar IP ao alias: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove um IP de um alias
     *
     * @param string $aliasName Nome do alias
     * @param string $ipAddress Endereço IP a ser removido
     * @return bool|array
     */
    public function removeIpFromAlias(string $aliasName, string $ipAddress): bool|array
    {
        try {
            Log::info("Removendo IP do alias", [
                'alias' => $aliasName,
                'ip' => $ipAddress
            ]);

            $response = $this->client->post('/api/firewall/alias_util/delete', [
                'json' => [
                    'alias' => $aliasName,
                    'address' => $ipAddress
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("IP removido com sucesso do alias", ['response' => $data]);
                return $data;
            }

            Log::error("Falha ao remover IP do alias", [
                'status' => $statusCode,
                'body' => $body
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao remover IP do alias: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lista todos os aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        try {
            Log::info("Listando todos os aliases");

            $response = $this->client->get('/api/firewall/alias/search_item');
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Aliases listados com sucesso", ['count' => count($data['rows'] ?? [])]);
                return $data;
            }

            Log::error("Falha ao listar aliases", [
                'status' => $statusCode,
                'body' => $body
            ]);

            return ['rows' => []];
        } catch (\Exception $e) {
            Log::error("Erro ao listar aliases: " . $e->getMessage());
            return ['rows' => []];
        }
    }

    /**
     * Obtém configuração de aliases
     * Endpoint: /api/firewall/alias/get
     */
    public function getAliasesConfig(): array
    {
        try {
            Log::info("Obtendo configuração de aliases");

            $response = $this->client->get('/api/firewall/alias/get', [
                'query' => [
                    'current' => 1,
                    'rowCount' => -1,
                    'sort' => 'name',
                    'searchPhrase' => ''
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Configuração de aliases obtida com sucesso");
                return $data;
            }

            Log::error("Falha ao obter configuração de aliases", [
                'status' => $statusCode,
                'body' => $body
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao obter configuração de aliases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém um alias específico pelo UUID
     */
    public function getAlias(string $uuid): ?array
    {
        try {
            Log::info("Obtendo alias específico", ['uuid' => $uuid]);

            $response = $this->client->get("/api/firewall/alias/get_item/{$uuid}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Alias obtido com sucesso", ['uuid' => $uuid]);
                return $data;
            }

            Log::warning("Alias não encontrado", [
                'uuid' => $uuid,
                'status' => $statusCode,
                'body' => $body
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao obter alias: " . $e->getMessage(), ['uuid' => $uuid]);
            return null;
        }
    }

    /**
     * Cria um novo alias
     */
    public function createAlias(array $aliasData): bool|array
    {
        try {
            Log::info("Criando novo alias", ['data' => $aliasData]);

            $response = $this->client->post('/api/firewall/alias/add_item', [
                'json' => [
                    'alias' => $aliasData
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Alias criado com sucesso", ['response' => $data]);
                return $data;
            }

            Log::error("Falha ao criar alias", [
                'status' => $statusCode,
                'body' => $body,
                'data' => $aliasData
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao criar alias: " . $e->getMessage(), ['data' => $aliasData]);
            throw $e;
        }
    }

    /**
     * Atualiza um alias existente
     */
    public function updateAlias(string $uuid, array $aliasData): bool|array
    {
        try {
            Log::info("Atualizando alias", ['uuid' => $uuid, 'data' => $aliasData]);

            $response = $this->client->post("/api/firewall/alias/set_item/{$uuid}", [
                'json' => [
                    'alias' => $aliasData
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Alias atualizado com sucesso", ['uuid' => $uuid, 'response' => $data]);
                return $data;
            }

            Log::error("Falha ao atualizar alias", [
                'uuid' => $uuid,
                'status' => $statusCode,
                'body' => $body,
                'data' => $aliasData
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar alias: " . $e->getMessage(), ['uuid' => $uuid, 'data' => $aliasData]);
            throw $e;
        }
    }

    /**
     * Deleta um alias
     */
    public function deleteAlias(string $uuid): bool
    {
        try {
            Log::info("Deletando alias", ['uuid' => $uuid]);

            $response = $this->client->post("/api/firewall/alias/del_item/{$uuid}");
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Alias deletado com sucesso", ['uuid' => $uuid, 'response' => $data]);
                return true;
            }

            Log::error("Falha ao deletar alias", [
                'uuid' => $uuid,
                'status' => $statusCode,
                'body' => $body
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao deletar alias: " . $e->getMessage(), ['uuid' => $uuid]);
            throw $e;
        }
    }

    /**
     * Aplica as mudanças dos aliases
     */
    public function applyAliases(): bool|array
    {
        try {
            Log::info("Aplicando mudanças dos aliases");

            $response = $this->client->post('/api/firewall/alias/reconfigure');
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $data = json_decode($body, true);
                Log::info("Mudanças dos aliases aplicadas com sucesso", ['response' => $data]);
                return $data;
            }

            Log::error("Falha ao aplicar mudanças dos aliases", [
                'status' => $statusCode,
                'body' => $body
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao aplicar mudanças dos aliases: " . $e->getMessage());
            throw $e;
        }
    }
}
