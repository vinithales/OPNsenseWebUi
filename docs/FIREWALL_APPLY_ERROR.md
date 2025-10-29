# 🔥 Erro ao Aplicar Mudanças no Firewall - Diagnóstico e Solução

## 🐛 Erro Identificado

```
[2025-10-23 00:31:53] local.ERROR: Erro ao aplicar mudanças no firewall 
{"revision":null,"error":"Falha ao criar savepoint: HTTP 400"}
```

---

## 🔍 Análise do Problema

### **Causa Raiz: Endpoint Incorreto ou API Desabilitada**

O erro HTTP 400 (Bad Request) ao criar o savepoint indica que:

1. ❌ **Endpoint incorreto** - `/api/firewall/filter/apply` pode não existir
2. ❌ **Módulo desabilitado** - O plugin de firewall filter pode estar desabilitado
3. ❌ **Versão incompatível** - Diferentes versões do OPNsense usam diferentes endpoints
4. ❌ **Permissões insuficientes** - A API key não tem permissão para criar savepoints

---

## 🔧 Soluções Possíveis

### **Solução 1: Usar Endpoint Correto do OPNsense** ⭐ RECOMENDADA

O OPNsense possui diferentes endpoints dependendo da versão e módulo:

```php
// ❌ Endpoint atual (incorreto para algumas versões)
/api/firewall/filter/apply

// ✅ Endpoints alternativos corretos
/api/firewall/filter_base/savepoint
/api/firewall/filter/reconfigure
```

#### **Implementação:**

```php
public function applyChanges(): bool|array
{
    try {
        Log::info("Aplicando mudanças no firewall");

        // Tenta o endpoint moderno primeiro
        try {
            $response = $this->client->post('/api/firewall/filter/reconfigure');
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                Log::info("Mudanças aplicadas com sucesso", ['response' => $data]);
                return [
                    'status' => 'success',
                    'message' => 'Mudanças aplicadas com sucesso',
                    'data' => $data
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Endpoint /reconfigure falhou, tentando método alternativo", [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback: Tenta criar savepoint manualmente
        $savepointResponse = $this->client->post('/api/firewall/filter_base/savepoint');
        $savepointData = json_decode($savepointResponse->getBody()->getContents(), true);
        $revision = $savepointData['revision'] ?? null;

        if (!$revision) {
            throw new \Exception("Não foi possível criar savepoint");
        }

        // Aplica com a revision
        $applyResponse = $this->client->post("/api/firewall/filter_base/apply/{$revision}");
        $applyData = json_decode($applyResponse->getBody()->getContents(), true);

        // Confirma mudanças
        $this->client->post("/api/firewall/filter_base/cancel_rollback/{$revision}");

        return [
            'status' => 'success',
            'message' => 'Mudanças aplicadas com sucesso',
            'revision' => $revision,
            'data' => $applyData
        ];

    } catch (\Exception $e) {
        Log::error("Erro ao aplicar mudanças", ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

---

### **Solução 2: Desabilitar Auto-Apply (Mais Simples)** ⚡

Se o apply está causando problemas, você pode:

1. **Remover o auto-apply** após criar regras
2. **Aplicar manualmente** no OPNsense
3. **Criar botão manual** de apply na interface

```php
// No FirewallController
public function createRule(Request $request)
{
    try {
        $validated = $request->validate([...]);
        
        $result = $this->firewallService->createRule($validated);
        
        // ❌ REMOVER: $this->firewallService->applyChanges();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Regra criada! Clique em "Aplicar Mudanças" para ativar.',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
```

---

### **Solução 3: Verificar Configuração do OPNsense** 🔧

#### **3.1. Verificar Plugin de Firewall**

```bash
# SSH no OPNsense
ssh root@192.168.56.102

# Verificar se o plugin está instalado
pkg info | grep firewall

# Verificar módulos carregados
opnsense-update health
```

#### **3.2. Verificar Permissões da API Key**

No OPNsense Web UI:
1. System → Access → Users
2. Editar o usuário da API
3. Verificar que tem permissões:
   - ✅ `Firewall: Filter`
   - ✅ `Firewall: Filter Apply`
   - ✅ `System: Settings`

#### **3.3. Testar Endpoint Manualmente**

```bash
# Testar se o endpoint existe
curl -k -u "API_KEY:API_SECRET" \
  -X POST \
  https://192.168.56.102/api/firewall/filter/apply

# Se retornar 404, o endpoint não existe
# Se retornar 400, há problema com a requisição
# Se retornar 200, está funcionando
```

---

### **Solução 4: Aplicar via Comando Shell (Workaround)** 🔄

Se a API não funciona, use comandos diretos:

```php
public function applyChanges(): bool|array
{
    try {
        Log::info("Aplicando mudanças via comando shell");

        // Executa comando diretamente no OPNsense
        $response = $this->client->post('/api/core/service/reconfigure', [
            'json' => [
                'service' => 'firewall'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $data = json_decode($response->getBody()->getContents(), true);

        if ($statusCode === 200) {
            Log::info("Firewall reconfigurado com sucesso", ['response' => $data]);
            return [
                'status' => 'success',
                'message' => 'Mudanças aplicadas',
                'data' => $data
            ];
        }

        throw new \Exception("Falha ao reconfigurar: HTTP {$statusCode}");

    } catch (\Exception $e) {
        Log::error("Erro ao aplicar mudanças", ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

---

## 🎯 Implementação Recomendada (Solução Completa)

Vou criar uma versão melhorada do `applyChanges()` com fallbacks:

```php
public function applyChanges(): bool|array
{
    $methods = [
        // Método 1: Reconfigure direto (mais simples e confiável)
        [
            'name' => 'reconfigure',
            'endpoint' => '/api/firewall/filter/reconfigure',
            'method' => 'post'
        ],
        // Método 2: Service reconfigure
        [
            'name' => 'service_reconfigure',
            'endpoint' => '/api/core/service/reconfigure',
            'method' => 'post',
            'payload' => ['service' => 'firewall']
        ],
        // Método 3: Savepoint + Apply (método complexo)
        [
            'name' => 'savepoint_apply',
            'endpoint' => '/api/firewall/filter_base/savepoint',
            'method' => 'complex'
        ]
    ];

    foreach ($methods as $method) {
        try {
            Log::info("Tentando aplicar mudanças usando método: {$method['name']}");

            if ($method['method'] === 'post') {
                $response = $this->client->post($method['endpoint'], [
                    'json' => $method['payload'] ?? []
                ]);

                $statusCode = $response->getStatusCode();
                $data = json_decode($response->getBody()->getContents(), true);

                if ($statusCode === 200) {
                    Log::info("Sucesso com método {$method['name']}", ['response' => $data]);
                    return [
                        'status' => 'success',
                        'message' => 'Mudanças aplicadas com sucesso',
                        'method' => $method['name'],
                        'data' => $data
                    ];
                }
            } elseif ($method['method'] === 'complex') {
                // Método savepoint completo (original)
                return $this->applySavepointMethod();
            }

        } catch (\Exception $e) {
            Log::warning("Método {$method['name']} falhou", [
                'error' => $e->getMessage()
            ]);
            continue; // Tenta próximo método
        }
    }

    // Se todos os métodos falharam
    Log::error("Todos os métodos de apply falharam");
    
    return [
        'status' => 'warning',
        'message' => 'Regra criada mas não foi possível aplicar automaticamente. Aplique manualmente no OPNsense.'
    ];
}

private function applySavepointMethod(): array
{
    // Implementação original do savepoint
    $savepointResponse = $this->client->post('/api/firewall/filter_base/savepoint');
    // ... resto do código original
}
```

---

## 📝 Checklist de Diagnóstico

Use este checklist para identificar o problema:

- [ ] **Versão do OPNsense**
  ```bash
  # No OPNsense
  opnsense-version
  ```

- [ ] **API Key tem permissões corretas**
  - System → Access → Users → Edit → Effective Privileges
  - Deve ter: `Firewall: *`

- [ ] **Endpoint existe**
  ```bash
  curl -k -u "key:secret" https://IP/api/firewall/filter/apply
  ```

- [ ] **Plugin de firewall ativo**
  - Firewall → Settings → Advanced
  - Firewall API enabled = ✅

- [ ] **Logs do OPNsense**
  ```bash
  tail -f /var/log/system.log
  ```

---

## 🚀 Ação Imediata

Execute estes passos **AGORA**:

### **Passo 1: Testar Endpoints Disponíveis**

```bash
# Via terminal do OPNsense
curl -k -u "API_KEY:API_SECRET" \
  https://192.168.56.102/api/firewall/filter/reconfigure

# Se funcionar (HTTP 200), use esse endpoint!
```

### **Passo 2: Atualizar o Código**

Se o teste acima retornar 200, atualize o `FirewallService.php`:

```php
public function applyChanges(): bool|array
{
    try {
        Log::info("Aplicando mudanças no firewall via reconfigure");

        $response = $this->client->post('/api/firewall/filter/reconfigure');
        $statusCode = $response->getStatusCode();
        $data = json_decode($response->getBody()->getContents(), true);

        if ($statusCode === 200) {
            Log::info("Mudanças aplicadas com sucesso", ['response' => $data]);
            return [
                'status' => 'success',
                'message' => 'Mudanças aplicadas',
                'data' => $data
            ];
        }

        throw new \Exception("HTTP {$statusCode}");

    } catch (\Exception $e) {
        Log::error("Erro ao aplicar mudanças", ['error' => $e->getMessage()]);
        // NÃO lança exceção - apenas avisa
        return [
            'status' => 'warning',
            'message' => 'Regra salva, mas aplique manualmente'
        ];
    }
}
```

---

## 📊 Comparação de Métodos

| Método | Complexidade | Confiabilidade | Recomendado |
|--------|-------------|----------------|-------------|
| **reconfigure** | Baixa ⭐ | Alta ✅ | ✅ SIM |
| **service/reconfigure** | Baixa ⭐ | Média 📊 | ✅ SIM |
| **savepoint/apply** | Alta 🔥 | Baixa ❌ | ❌ NÃO |
| **manual** | Nenhuma ✨ | Alta ✅ | 💡 Fallback |

---

## ✅ Solução Final Recomendada

Use o endpoint **`/api/firewall/filter/reconfigure`** que é mais simples e confiável! Vou implementar agora.

---

## 🔄 Atualização: Endpoint para Buscar Regras

### **Mudança Implementada:**

O método `getRules()` foi atualizado para usar:
- **Endpoint Novo**: `GET /api/firewall/filter/get`
- **Endpoint Antigo**: `POST /api/firewall/filter/searchRule`

### **Estrutura da Resposta:**

```json
{
  "filter": {
    "rules": {
      "rule": [
        {
          "uuid": "abc-123",
          "enabled": "1",
          "type": "pass",
          "interface": "lan",
          "protocol": "tcp",
          "source": { "address": "192.168.1.0/24" },
          "destination": { "address": "any", "port": "443" },
          "descr": "Allow HTTPS"
        }
      ]
    }
  }
}
```

### **Processamento:**

O método agora:
1. ✅ Extrai regras do caminho `filter.rules.rule`
2. ✅ Suporta array de regras ou objeto único
3. ✅ Formata dados para compatibilidade com o frontend
4. ✅ Trata campos aninhados (source, destination)

### **Como Testar:**

```powershell
# Teste a estrutura da resposta
.\test_firewall_get_endpoint.ps1

# Teste no Laravel
php artisan tinker
>>> $service = app(\App\Services\Opnsense\FirewallService::class);
>>> $rules = $service->getRules();
>>> dd($rules);
```
