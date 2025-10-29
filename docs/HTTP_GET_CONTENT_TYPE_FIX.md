# 🔧 Correção: Erro 400 "Invalid JSON syntax" em requisições GET

## 🐛 Problema Identificado

### **Erro:**
```json
{
  "status": 400,
  "body": "{\"status\":400,\"message\":\"Invalid JSON syntax\"}"
}
```

### **Causa Raiz:**
O `BaseService` estava configurando `Content-Type: application/json` **globalmente** para todas as requisições, incluindo **GET**.

Requisições **GET não devem ter Content-Type**, pois:
- ❌ GET não envia body
- ❌ APIs REST rejeitam GET com Content-Type application/json
- ❌ OPNsense retorna erro 400 "Invalid JSON syntax"

---

## ✅ Solução Implementada

### **1. BaseService.php (Mantido)**
```php
// Headers globais permanecem (usados em POST/PUT/DELETE)
$this->client = new Client([
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json', // OK para POST/PUT/DELETE
    ]
]);
```

### **2. Métodos GET - FirewallService.php (Corrigidos)**

#### **Antes (❌ Incorreto):**
```php
public function getRules(): array
{
    $response = $this->client->get('/api/firewall/filter/get');
    // Herda Content-Type: application/json do BaseService
}
```

#### **Depois (✅ Correto):**
```php
public function getRules(): array
{
    // Sobrescreve headers, removendo Content-Type
    $response = $this->client->get('/api/firewall/filter/get', [
        'headers' => [
            'Accept' => 'application/json'
        ]
    ]);
}
```

---

## 📋 Métodos Corrigidos

### **FirewallService.php**

| Método | Endpoint | Status |
|--------|----------|--------|
| `getRules()` | GET `/api/firewall/filter/get` | ✅ Corrigido |
| `getRule($uuid)` | GET `/api/firewall/filter/getRule/{uuid}` | ✅ Corrigido |
| `getAlias($uuid)` | GET `/api/firewall/alias/getItem/{uuid}` | ✅ Corrigido |

### **Outros Services**
- ✅ `UserService.php` - Usa apenas POST
- ✅ `GroupService.php` - Usa apenas POST
- ✅ `PermissionService.php` - Usa apenas POST

---

## 🧪 Como Testar

### **Opção 1: Via Laravel**
```bash
php artisan tinker
```
```php
$service = app(\App\Services\Opnsense\FirewallService::class);
$rules = $service->getRules(); // Deve funcionar sem erro 400
dd($rules);
```

### **Opção 2: Via Script PowerShell**
```powershell
.\test_firewall_get_endpoint.ps1
```

### **Opção 3: Via Interface Web**
1. Acesse a página de Firewall
2. As regras devem carregar sem erro
3. Verifique o console do navegador (F12)

---

## 📊 Comparação de Headers

### **Requisição POST (Correto):**
```http
POST /api/firewall/filter/addRule HTTP/1.1
Accept: application/json
Content-Type: application/json
Authorization: Basic ...

{"rule":{"action":"pass",...}}
```

### **Requisição GET (Incorreto - ANTES):**
```http
GET /api/firewall/filter/get HTTP/1.1
Accept: application/json
Content-Type: application/json  ❌ CAUSA O ERRO
Authorization: Basic ...
```

### **Requisição GET (Correto - DEPOIS):**
```http
GET /api/firewall/filter/get HTTP/1.1
Accept: application/json
Authorization: Basic ...
```

---

## 🎯 Regra Geral

| Método HTTP | Content-Type | Body | Exemplo |
|-------------|--------------|------|---------|
| **GET** | ❌ NÃO | ❌ NÃO | Buscar dados |
| **POST** | ✅ SIM | ✅ SIM | Criar recurso |
| **PUT** | ✅ SIM | ✅ SIM | Atualizar completo |
| **PATCH** | ✅ SIM | ✅ SIM | Atualizar parcial |
| **DELETE** | ⚠️  Opcional | ⚠️  Opcional | Deletar recurso |

---

## 🔍 Debugging

### **Verificar Headers Enviados:**
```php
// Em FirewallService.php
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

$history = [];
$stack = HandlerStack::create();
$stack->push(Middleware::history($history));

$client = new Client(['handler' => $stack]);

// Após fazer requisição:
foreach ($history as $transaction) {
    Log::info('Request Headers:', $transaction['request']->getHeaders());
}
```

### **Logs do Laravel:**
```bash
tail -f storage/logs/laravel.log | grep "Buscando regras"
```

### **Teste Manual (curl):**
```bash
# Correto - Sem Content-Type
curl -k -u "KEY:SECRET" \
  -H "Accept: application/json" \
  https://192.168.56.102/api/firewall/filter/get

# Incorreto - Com Content-Type (retorna erro 400)
curl -k -u "KEY:SECRET" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  https://192.168.56.102/api/firewall/filter/get
```

---

## 💡 Lições Aprendidas

1. **Não configure Content-Type globalmente** se você usa GET
2. **GET nunca deve ter Content-Type application/json**
3. **Guzzle permite sobrescrever headers** por requisição
4. **OPNsense é rigoroso** com headers HTTP
5. **Sempre teste com curl** antes de implementar

---

## ✅ Status Final

- ✅ Erro 400 "Invalid JSON syntax" **CORRIGIDO**
- ✅ Todos os métodos GET **atualizados**
- ✅ Script de teste **atualizado**
- ✅ Documentação **criada**

**Problema resolvido!** 🎉
