# FirewallService - Documentação

## Arquitetura

O `FirewallService` segue o padrão estabelecido no projeto, estendendo a classe `BaseService` que gerencia a conexão com o OPNsense via Guzzle HTTP Client.

```
BaseService (Conexão OPNsense)
    ↓
FirewallService (Regras e Aliases)
```

## Configuração

As variáveis já estão configuradas no projeto. Verifique o arquivo `.env`:

```env
OPNSENSE_API_BASE_URL=http://192.168.56.102
OPNSENSE_API_KEY=sua_chave_api
OPNSENSE_API_SECRET=seu_segredo_api
```

## Uso do Serviço

### 1. Gerenciamento de Aliases (Grupos de IPs)

#### Adicionar IP a um Alias
```php
use App\Services\Opnsense\FirewallService;

$firewallService = new FirewallService();
$result = $firewallService->addIpToGroupAlias('alunos', '192.168.1.100');
$firewallService->applyChanges();
```

#### Remover IP de um Alias
```php
$result = $firewallService->removeIpFromGroupAlias('alunos', '192.168.1.100');
$firewallService->applyChanges();
```

#### Listar Aliases
```php
$aliases = $firewallService->getAliases();
```

#### Criar Alias
```php
$aliasData = [
    'enabled' => '1',
    'name' => 'alunos',
    'type' => 'host',
    'description' => 'IPs dos alunos',
    'content' => '192.168.1.100,192.168.1.101'
];
$result = $firewallService->createAlias($aliasData);
```

### 2. Gerenciamento de Regras de Firewall

#### Listar Regras
```php
$rules = $firewallService->getRules();
```

#### Obter Detalhes de uma Regra
```php
$rule = $firewallService->getRule($uuid);
```

#### Criar Regra
```php
$ruleData = [
    'enabled' => '1',
    'action' => 'pass',
    'interface' => 'lan',
    'protocol' => 'TCP',
    'source_net' => 'lan',
    'destination_net' => 'any',
    'destination_port' => '80',
    'description' => 'Permitir HTTP'
];
$result = $firewallService->createRule($ruleData);
```

#### Atualizar Regra
```php
$result = $firewallService->updateRule($uuid, $ruleData);
```

#### Deletar Regra
```php
$result = $firewallService->deleteRule($uuid);
```

#### Ativar/Desativar Regra
```php
// Ativar
$result = $firewallService->toggleRule($uuid, true);

// Desativar
$result = $firewallService->toggleRule($uuid, false);
```

### 3. Aplicar Mudanças

**IMPORTANTE**: Sempre que fizer alterações no firewall, você deve aplicar as mudanças:

```php
$result = $firewallService->applyChanges();
```

Este método:
1. Cria um savepoint (ponto de restauração)
2. Aplica as mudanças
3. Cancela o rollback automático (confirma as mudanças)

## Rotas da API

### Regras de Firewall

- **GET** `/api/firewall/rules` - Lista todas as regras
- **GET** `/api/firewall/rules/{uuid}` - Detalhes de uma regra
- **POST** `/api/firewall/rules` - Cria nova regra
- **PUT** `/api/firewall/rules/{uuid}` - Atualiza regra
- **DELETE** `/api/firewall/rules/{uuid}` - Deleta regra
- **POST** `/api/firewall/rules/{uuid}/toggle` - Ativa/Desativa regra

### Aliases

- **GET** `/api/firewall/aliases` - Lista todos os aliases
- **POST** `/api/firewall/aliases/add-ip` - Adiciona IP ao alias
- **POST** `/api/firewall/aliases/remove-ip` - Remove IP do alias

### Aplicar Mudanças

- **POST** `/api/firewall/apply` - Aplica todas as mudanças pendentes

## Exemplos de Uso via API

### Adicionar IP ao Alias (JavaScript)
```javascript
async function addIpToAlias(alias, ip) {
    const response = await fetch('/api/firewall/aliases/add-ip', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ alias, ip })
    });

    const data = await response.json();
    console.log(data);
}

// Uso
addIpToAlias('alunos', '192.168.1.100');
```

### Criar Regra de Firewall (JavaScript)
```javascript
async function createFirewallRule(ruleData) {
    const response = await fetch('/api/firewall/rules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(ruleData)
    });

    const data = await response.json();
    console.log(data);
}

// Uso
createFirewallRule({
    action: 'pass',
    interface: 'lan',
    protocol: 'TCP',
    source: 'any',
    destination: 'any',
    description: 'Nova regra'
});
```

## Tratamento de Erros

Todos os métodos lançam exceções em caso de erro. Use try-catch:

```php
try {
    $firewallService = new FirewallService();
    $result = $firewallService->addIpToGroupAlias('alunos', '192.168.1.100');
    $firewallService->applyChanges();
} catch (\Exception $e) {
    Log::error('Erro ao adicionar IP: ' . $e->getMessage());
    // Trate o erro apropriadamente
}
```

## Logs

Todos os métodos registram logs detalhados:
- **INFO**: Operações bem-sucedidas
- **ERROR**: Falhas nas operações
- **WARNING**: Avisos importantes

Verifique os logs em `storage/logs/laravel.log`.

## Segurança

⚠️ **IMPORTANTE**:
- A verificação SSL está desabilitada no `BaseService` para desenvolvimento
- Em produção, configure certificados SSL válidos
- Proteja suas credenciais da API no arquivo `.env`
- Nunca commite o arquivo `.env` no repositório

## Integração com BaseService

O `FirewallService` herda todas as funcionalidades do `BaseService`:

```php
class FirewallService extends BaseService
{
    // Acesso ao cliente Guzzle via $this->client
    // Autenticação automática (Basic Auth)
    // Headers JSON configurados
    // Verificação SSL desabilitada (desenvolvimento)
}
```

Outros serviços do projeto seguem o mesmo padrão:
- `UserService extends BaseService`
- `GroupService extends BaseService`
- `PermissionService extends BaseService`
- `FirewallService extends BaseService` ✨

## Fluxo de Trabalho Recomendado

1. Faça as alterações necessárias (criar/atualizar/deletar regras ou aliases)
2. Chame `applyChanges()` uma única vez ao final
3. Verifique o resultado nos logs
4. Teste as mudanças no firewall

## Rollback Automático

Se o método `applyChanges()` falhar durante a aplicação:
- O OPNsense automaticamente fará rollback para o savepoint
- As mudanças não serão aplicadas
- Você verá os detalhes do erro nos logs
