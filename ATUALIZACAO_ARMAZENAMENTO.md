# 🔄 ATUALIZAÇÃO IMPORTANTE - Armazenamento de Dados

## ⚠️ Mudança de Arquitetura

### Antes (Incorreto)
❌ Usuários eram salvos no banco de dados local do Laravel
❌ Dados ficavam duplicados (OPNsense + MySQL)
❌ Necessidade de sincronização entre sistemas

### Agora (Correto)
✅ Usuários são criados **apenas no OPNsense**
✅ RA e Tipo de Usuário armazenados no campo `comment` do OPNsense
✅ Fonte única de verdade (Single Source of Truth)

---

## 📝 Como Funciona Agora

### 1. Formato do Campo Comment no OPNsense

```
RA: 123456 | Tipo: aluno | Criado: 2025-10-18 15:30:00
```

Ou para cadastro manual com observação:

```
RA: 123456 | Tipo: professor | Aluno do curso de TI | Criado: 2025-10-18 15:30:00
```

### 2. Estrutura dos Dados

**No OPNsense:**
- `name`: RA do usuário (username para login)
- `fullname`: Nome completo ou e-mail
- `email`: E-mail do usuário
- `password`: Senha criptografada
- `comment`: **Metadados estruturados** (RA, Tipo, observações)

---

## 🔧 Implementação Técnica

### Salvando Dados (Importação/Cadastro)

```php
// No UserImportService.php
$comment = "RA: {$ra} | Tipo: {$userType} | Importado: " . now()->format('Y-m-d H:i:s');

$userData = [
    'user' => [
        'name' => $ra,           // RA como username
        'fullname' => $email,     
        'email' => $email,
        'password' => $password,
        'comment' => $comment,    // Metadados aqui!
        // ... outros campos
    ]
];

$this->opnsenseUserService->createUser($userData);
```

### Recuperando Dados (Listagem)

```php
// No UserService.php

// Função auxiliar para extrair dados do comment
public function parseComment($comment)
{
    $data = ['ra' => null, 'user_type' => null];
    
    // Extrai RA
    if (preg_match('/RA:\s*([^\|]+)/', $comment, $matches)) {
        $data['ra'] = trim($matches[1]);
    }
    
    // Extrai Tipo
    if (preg_match('/Tipo:\s*([^\|]+)/', $comment, $matches)) {
        $data['user_type'] = trim($matches[1]);
    }
    
    return $data;
}

// Enriquece a lista de usuários
public function enrichUsersWithMetadata(array $users)
{
    foreach ($users as &$user) {
        $metadata = $this->parseComment($user['comment'] ?? '');
        $user['ra'] = $metadata['ra'];
        $user['user_type'] = $metadata['user_type'];
    }
    return $users;
}
```

### No Controller (API)

```php
public function apiIndex()
{
    $users = $this->userService->getUsers();
    
    // Extrai RA e tipo do comment
    $users = $this->userService->enrichUsersWithMetadata($users);
    
    return response()->json([
        'status' => 'success',
        'data' => $users // Agora inclui 'ra' e 'user_type'
    ]);
}
```

---

## 🎯 Vantagens desta Abordagem

### 1. Simplicidade
- ✅ Não há duplicação de dados
- ✅ Não precisa sincronizar dois bancos
- ✅ OPNsense é a fonte única de verdade

### 2. Consistência
- ✅ Se o usuário existe no OPNsense, ele existe no sistema
- ✅ Não há risco de inconsistência entre bancos
- ✅ Deletar no OPNsense = deletar do sistema

### 3. Compatibilidade
- ✅ Usa campos nativos do OPNsense
- ✅ Campo `comment` é padrão e aceita texto livre
- ✅ Funciona sem modificações no OPNsense

### 4. Manutenibilidade
- ✅ Menos código para manter
- ✅ Menos tabelas no banco
- ✅ Fácil de debugar

---

## 🔍 Validação de Duplicatas

### RA Duplicado

```php
// Verifica se RA (usado como username) já existe
$existingUser = $this->userService->findUserByName($ra);
if ($existingUser) {
    // Erro: RA já cadastrado
}
```

### E-mail Duplicado

O OPNsense já valida e-mails duplicados nativamente, então não precisamos fazer verificação extra.

---

## 📊 Impacto nas Funcionalidades

### ✅ Funcionam Normalmente

1. **Importação Excel**
   - RA e tipo vão para o `comment`
   - Senhas geradas e armazenadas no OPNsense
   - PDF gerado com credenciais

2. **Cadastro Manual**
   - RA opcional
   - Tipo obrigatório
   - Tudo salvo no OPNsense

3. **Listagem de Usuários**
   - RA e tipo extraídos do `comment` automaticamente
   - Filtros funcionam normalmente
   - Busca funciona

4. **Geração de PDF**
   - Credenciais temporárias na sessão
   - Não armazena no banco

### ⚠️ O que NÃO funciona mais

1. **Tabela `users` do Laravel**
   - Campos `ra`, `user_type`, `status` NÃO são mais usados
   - A tabela pode ser mantida apenas para o usuário admin do sistema web
   - **Recomendação**: Manter a migration para não quebrar o sistema, mas não usar esses campos

2. **Queries diretas no banco**
   - Consultas como `User::where('ra', '123456')` NÃO funcionam
   - Deve-se buscar no OPNsense via API

---

## 🗄️ Sobre a Tabela `users` do Laravel

### Decisão: Manter mas Não Usar

A tabela `users` do Laravel continua existindo, mas será usada apenas para:

1. **Autenticação do sistema web** (admin que acessa a interface)
2. **Sessões e remember_token**
3. Não mais para alunos/professores do OPNsense

### Migration

A migration foi criada mas os campos não serão populados:
```php
// Campos existem mas ficam vazios para usuários do OPNsense
$table->string('ra')->nullable()->unique();
$table->enum('user_type', ['aluno', 'professor', 'admin'])->default('aluno');
$table->enum('status', ['ativo', 'inativo', 'bloqueado'])->default('ativo');
```

---

## 🔄 Fluxo de Dados Completo

### Importação
```
Excel → UserImportService
    ↓
Valida dados (RA único, e-mail válido)
    ↓
Monta comment: "RA: xxx | Tipo: yyy | ..."
    ↓
OPNsense API (createUser)
    ↓
Usuário criado no OPNsense
    ↓
Credenciais para PDF (sessão temporária)
```

### Listagem
```
Frontend solicita usuários
    ↓
UserController::apiIndex()
    ↓
UserService::getUsers() → OPNsense API
    ↓
UserService::enrichUsersWithMetadata()
    ↓ (extrai RA e tipo do comment via regex)
JSON com 'ra' e 'user_type' preenchidos
    ↓
Frontend renderiza tabela
```

### Filtros
```
JavaScript filtra localmente com base em:
- user['ra']
- user['user_type'] 
- user['status'] (do OPNsense: disabled/enabled)
```

---

## 🧪 Testando a Nova Implementação

### 1. Teste de Importação

```bash
# 1. Baixar template
# 2. Adicionar linha:
RA      | E-mail
123456  | teste@escola.com

# 3. Importar como "Aluno"
# 4. Verificar no OPNsense:
#    - Username deve ser "123456"
#    - Comment deve conter "RA: 123456 | Tipo: aluno"
```

### 2. Teste de Listagem

```bash
# Acessar /users
# Verificar que a tabela mostra:
# - Coluna com RA
# - Badges de tipo (Aluno/Professor)
# - Filtros funcionando
```

### 3. Teste de Duplicata

```bash
# Tentar importar o mesmo RA novamente
# Deve dar erro: "RA 123456 já existe no sistema"
```

---

## 📌 Pontos de Atenção

### 1. Campo "name" vs "ra"
- No OPNsense, o campo `name` é o **username** para login
- Estamos usando o **RA como username**
- Portanto: `name` do OPNsense = RA do aluno

### 2. Parsing do Comment
- O regex busca padrões específicos: `RA: xxx` e `Tipo: yyy`
- **NÃO altere o formato** do comment sem atualizar o regex
- Formato atual:
  ```
  RA: 123456 | Tipo: aluno | Observação qualquer | Criado: data
  ```

### 3. Compatibilidade Reversa
- Se já existem usuários sem metadados no comment:
  - `ra` será `null`
  - `user_type` será `null`
  - Sistema não quebra, mas filtros não funcionarão para esses usuários

---

## 🔧 Manutenção e Debug

### Ver Comentários no OPNsense

Acesse via SSH ou interface web do OPNsense:
```bash
# Via CLI (se disponível)
configctl system show user
```

Ou pela API (já implementado):
```php
$user = $userService->getUser($uuid);
echo $user['comment']; // "RA: 123456 | Tipo: aluno | ..."
```

### Logs

```bash
# Ver logs de importação
tail -f storage/logs/laravel.log | grep "importado com sucesso"

# Ver metadados extraídos
# (adicione Log::debug no enrichUsersWithMetadata se necessário)
```

---

## ✅ Checklist de Verificação

Após esta atualização, verifique:

- [ ] Importação Excel cria usuários no OPNsense
- [ ] Campo `comment` contém "RA: xxx | Tipo: yyy"
- [ ] Listagem extrai e exibe RA e tipo corretamente
- [ ] Filtros por tipo funcionam
- [ ] Validação de RA duplicado funciona
- [ ] PDF é gerado com as credenciais
- [ ] Cadastro manual salva no OPNsense com metadados

---

## 🚀 Deploy

**Não é necessário rodar migrations novamente!**

A migration já foi executada, mas os campos não serão mais usados para usuários do OPNsense.

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Pronto para usar!
```

---

**Atualização concluída! Sistema agora usa OPNsense como fonte única de verdade. ✅**
