# Guia de Teste - Sistema de Configuração Inicial

## Como Testar a Primeira Execução

### 1. Preparar Ambiente para Teste

#### Opção A: Simular Primeira Execução (Recomendado)
```bash
# No arquivo .env, altere:
APP_FIRST_RUN=false
# para:
APP_FIRST_RUN=true
```

#### Opção B: Reset Completo do Sistema
```bash
# 1. Limpar banco de dados
php artisan migrate:fresh

# 2. Alterar flag no .env
APP_FIRST_RUN=true

# 3. Limpar cache
php artisan config:clear
php artisan cache:clear
```

### 2. Iniciar Servidor

```bash
php artisan serve
```

### 3. Acessar a Aplicação

Abra o navegador e acesse: `http://localhost:8000`

**Resultado Esperado:**
- Você será automaticamente redirecionado para `/setup`
- Verá a tela de configuração inicial com 3 abas

### 4. Preencher o Formulário de Configuração

#### Aba 1: Administrador
- **Nome Completo**: Digite seu nome (ex: João Silva)
- **Email**: Digite um email válido (ex: admin@exemplo.com)
- **Senha**: Mínimo 8 caracteres (ex: senha123)
- **Confirmar Senha**: Repita a senha

#### Aba 2: OPNsense
- **URL do OPNsense**: Digite a URL do seu firewall (ex: http://192.168.1.1)
- **API Key**: Cole a chave API do OPNsense
- **API Secret**: Cole o secret API do OPNsense

**Como obter as credenciais API:**
1. Acesse seu OPNsense
2. Vá em **System → Access → Users**
3. Edite seu usuário
4. Na aba **API Keys**, clique em **+** para gerar

#### Aba 3: Avançado (Opcional)
- **Nome da Aplicação**: Ex: "OPNsense Web UI"
- **URL da Aplicação**: Ex: http://localhost:8000
- **Porta do Banco**: Ex: 3307 ou 3306

### 5. Concluir Configuração

Clique em **"Concluir Configuração"**

**Resultado Esperado:**
- Sistema valida os dados
- Atualiza o arquivo `.env`
- Executa migrations
- Cria usuário administrador
- Define `APP_FIRST_RUN=false`
- Redireciona para tela de login

### 6. Fazer Login

Use as credenciais que você criou na Aba 1:
- **Email**: O email que você cadastrou
- **Senha**: A senha que você criou

### 7. Testar a Aba de Configurações

Após o login:

1. **Acesse a Sidebar**: No menu lateral esquerdo
2. **Clique em "Configurações"**: Ícone de engrenagem no final do menu
3. **Você verá a tela de configurações** com 2 abas:
   - **Conexão OPNsense**: Edite URL, API Key, API Secret
   - **Configurações Avançadas**: Edite nome da app, URL, porta do banco

4. **Faça uma alteração**: Por exemplo, mude o nome da aplicação
5. **Clique em "Salvar Configurações"**
6. **Verifique**: As alterações devem ser salvas no `.env`

### 8. Verificar Proteção contra Re-configuração

Tente acessar: `http://localhost:8000/setup`

**Resultado Esperado:**
- Você será redirecionado para o dashboard
- Verá mensagem: "Sistema já configurado"

## Casos de Teste

### Teste 1: Validação de Campos Obrigatórios
1. Acesse `/setup`
2. Deixe campos obrigatórios vazios
3. Tente submeter
4. **Esperado**: Mensagens de erro em vermelho

### Teste 2: Validação de Email
1. Digite email inválido (ex: "teste")
2. **Esperado**: Erro "Digite um email válido"

### Teste 3: Validação de Senha
1. Digite senha com menos de 8 caracteres
2. **Esperado**: Erro "A senha deve ter no mínimo 8 caracteres"

### Teste 4: Confirmação de Senha
1. Digite senhas diferentes
2. **Esperado**: Erro "As senhas não conferem"

### Teste 5: Validação de URL
1. Digite URL inválida (ex: "teste")
2. **Esperado**: Erro "Digite uma URL válida"

### Teste 6: Edição de Configurações
1. Faça login
2. Vá em Configurações
3. Altere qualquer campo
4. Salve
5. **Esperado**: Mensagem de sucesso verde

### Teste 7: Verificar Persistência
1. Altere configurações
2. Feche o navegador
3. Abra novamente e faça login
4. Vá em Configurações
5. **Esperado**: Valores atualizados devem estar salvos

## Verificações no Arquivo .env

Após configuração, verifique se o `.env` contém:

```env
APP_NAME=seu_nome_escolhido
APP_URL=sua_url
DB_PORT=sua_porta
OPNSENSE_API_BASE_URL=http://seu.opnsense
OPNSENSE_API_KEY=sua_chave
OPNSENSE_API_SECRET=seu_secret
APP_FIRST_RUN=false
```

## Troubleshooting

### Problema: Loop infinito de redirecionamento
**Solução**: 
```bash
php artisan config:clear
php artisan cache:clear
```

### Problema: Erro ao salvar no .env
**Solução**: Verifique permissões do arquivo:
```bash
# Windows (PowerShell como Admin)
icacls .env /grant Users:F

# Linux/Mac
chmod 666 .env
```

### Problema: Migrations não executam
**Solução**: 
```bash
php artisan migrate:fresh
```

### Problema: Cache não limpa
**Solução**: 
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Checklist Completo

- [ ] Sistema redireciona para `/setup` quando `APP_FIRST_RUN=true`
- [ ] Formulário possui 3 abas funcionando
- [ ] Validações de campos obrigatórios funcionam
- [ ] Validações de formato (email, URL) funcionam
- [ ] Senha e confirmação devem coincidir
- [ ] Sistema cria usuário administrador no banco
- [ ] Arquivo `.env` é atualizado corretamente
- [ ] Flag `APP_FIRST_RUN` muda para `false`
- [ ] Redirect para login após configuração
- [ ] Login funciona com credenciais criadas
- [ ] Acesso a `/setup` é bloqueado após configuração
- [ ] Aba "Configurações" aparece na sidebar
- [ ] Página de configurações carrega valores atuais
- [ ] Edição de configurações funciona
- [ ] Alterações são salvas no `.env`
- [ ] Cache é limpo após salvar configurações

## Prints Esperados

### 1. Tela de Setup (Primeira Execução)
- Logo/ícone centralizado
- Título "Bem-vindo ao OPNsense Web UI"
- 3 abas: Administrador, OPNsense, Avançado
- Botão "Concluir Configuração"

### 2. Sidebar com Configurações
- Dashboard
- Aliases
- Usuários
- Grupos
- **--- (divisor) ---**
- **Configurações** (ícone de engrenagem)

### 3. Página de Configurações
- Título "Configurações do Sistema"
- 2 abas: Conexão OPNsense, Configurações Avançadas
- Campos preenchidos com valores atuais
- Botões "Cancelar" e "Salvar Configurações"
