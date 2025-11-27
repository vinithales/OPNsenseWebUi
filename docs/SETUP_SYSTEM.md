# Sistema de Configuração Inicial

## Funcionalidade

Este sistema implementa uma tela de configuração inicial que é exibida na primeira execução da aplicação.

## Como Funciona

### 1. Primeira Execução

Quando o sistema detecta `APP_FIRST_RUN=true` no arquivo `.env`:
- Todas as rotas são redirecionadas para `/setup`
- O usuário é apresentado com uma tela de configuração em 3 abas

### 2. Telas de Configuração

#### Aba 1: Administrador
- Nome completo do administrador
- Email
- Senha (mínimo 8 caracteres)
- Confirmação de senha

#### Aba 2: OPNsense
- URL do firewall OPNsense
- API Key
- API Secret
- Instruções de como obter as credenciais

#### Aba 3: Avançado (Opcional)
- Nome da aplicação (`APP_NAME`)
- URL da aplicação (`APP_URL`)
- Porta do banco de dados (`DB_PORT`)

### 3. Após Configuração

Quando o formulário é submetido:
1. Valida todos os campos obrigatórios
2. Atualiza o arquivo `.env` com as configurações
3. Executa as migrations do banco de dados
4. Cria o usuário administrador
5. Define `APP_FIRST_RUN=false`
6. Redireciona para a tela de login

### 4. Execuções Subsequentes

Com `APP_FIRST_RUN=false`:
- O middleware `CheckFirstRun` permite acesso normal
- Tentativas de acessar `/setup` são redirecionadas para login

## Arquivos Criados

### Controllers
- `app/Http/Controllers/SetupController.php` - Gerencia o processo de configuração

### Middleware
- `app/Http/Middleware/CheckFirstRun.php` - Verifica se é primeira execução

### Views
- `resources/views/setup/index.blade.php` - Interface de configuração

### Configuração
- `.env` - Adicionada flag `APP_FIRST_RUN`
- `.env.example` - Atualizado com novas variáveis

## Rotas

```php
// Configuração inicial (sem middleware)
GET  /setup  - Exibe formulário de configuração
POST /setup  - Processa configuração

// Todas as outras rotas usam middleware 'check.first.run'
```

## Reset do Sistema

Para forçar a tela de configuração novamente:

1. No arquivo `.env`, altere:
```
APP_FIRST_RUN=false
```
para:
```
APP_FIRST_RUN=true
```

2. Opcionalmente, limpe o banco de dados:
```bash
php artisan migrate:fresh
```

## Validações

### Campos Obrigatórios
- Nome do administrador
- Email do administrador (formato email válido)
- Senha (mínimo 8 caracteres)
- Confirmação de senha (deve coincidir)
- URL do OPNsense (formato URL válido)
- API Key
- API Secret

### Campos Opcionais
- Nome da aplicação
- URL da aplicação
- Porta do banco de dados (1-65535)

## Segurança

- Senhas são hash usando `Hash::make()` do Laravel
- Validação de CSRF em todos os formulários
- Sanitização automática de inputs
- Escape de caracteres especiais ao gravar no `.env`

## Exemplo de Uso

### Nova Instalação

1. Clone o repositório
2. Copie `.env.example` para `.env`
3. Execute `php artisan key:generate`
4. Acesse a aplicação
5. Será automaticamente redirecionado para `/setup`
6. Preencha as 3 abas de configuração
7. Clique em "Concluir Configuração"
8. Faça login com as credenciais criadas

### Sistema Já Configurado

- O acesso a `/setup` será bloqueado
- Login e outras rotas funcionam normalmente
