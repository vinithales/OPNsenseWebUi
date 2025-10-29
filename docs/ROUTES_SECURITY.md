# Estrutura de Rotas e Segurança

## 🔒 Implementação do Middleware de Autenticação

Todas as rotas do sistema foram organizadas e protegidas com o middleware `auth` do Laravel, garantindo que apenas usuários autenticados possam acessar as funcionalidades.

---

## 📋 Estrutura das Rotas

### **Rotas Públicas (Sem Autenticação)**

Apenas as rotas de login são públicas:

```php
// Login
GET  /login  → showLoginForm()    // Exibe formulário de login
POST /login  → login()             // Processa credenciais
```

---

### **Rotas Protegidas (Middleware: `auth`)**

Todas as demais rotas estão dentro do grupo `Route::middleware(['auth'])->group()`:

```php
Route::middleware(['auth'])->group(function () {
    // Todas as rotas aqui requerem autenticação
});
```

---

## 📊 Mapa Completo de Rotas

### **1. Dashboard & Autenticação**

| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/` | Closure | `dashboard` | Página inicial |
| POST | `/logout` | LoginController | `logout` | Encerrar sessão |

---

### **2. Users - Gerenciamento de Usuários** 👥

#### **Views**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/users` | UserController@index | `users.index` | Lista de usuários |
| GET | `/users/create` | UserController@create | `users.create` | Formulário novo usuário |
| GET | `/users/{uuid}/edit` | UserController@edit | `users.edit` | Formulário editar usuário |
| GET | `/users/import` | UserController@importView | `users.import` | Página de importação |

#### **Actions**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| POST | `/user/create` | UserController@store | `users.api.create` | Criar usuário |
| PUT | `/users/{uuid}` | UserController@update | `users.update` | Atualizar usuário |
| DELETE | `/api/users/{user}` | UserController@destroy | `users.api.destroy` | Deletar usuário |

#### **Import - Excel**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/users/import/excel/template` | UserController@downloadExcelTemplate | `users.import.excel.template` | Download template Excel |
| POST | `/users/import/excel/process` | UserController@processExcelImport | `users.import.excel.process` | Processar importação |
| GET | `/users/import/excel/credentials-pdf` | UserController@downloadCredentialsPdf | `users.import.credentials.pdf` | Download PDF credenciais |

#### **Import - CSV (Legado)**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/users/import/template` | UserController@downloadTemplate | `users.import.template` | Download template CSV |
| POST | `/users/import/process` | UserController@processImport | `users.import.process` | Processar importação CSV |

#### **API**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/api/users` | UserController@apiIndex | `users.api.index` | API lista usuários |

---

### **3. Groups - Gerenciamento de Grupos** 👥

#### **Views**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/groups` | GroupController@indexView | `groups.index` | Lista de grupos |
| GET | `/groups/create` | GroupController@createView | `groups.create` | Formulário novo grupo |
| GET | `/groups/{group}/edit` | GroupController@edit | `groups.edit` | Formulário editar grupo |

#### **Actions**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| POST | `/groups` | GroupController@store | `groups.store` | Criar grupo |
| PUT | `/groups/{group}` | GroupController@update | `groups.update` | Atualizar grupo |
| DELETE | `/api/groups/{group}` | GroupController@destroy | `groups.api.destroy` | Deletar grupo |

#### **Export**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/groups/{group}/export-users` | GroupController@exportUsers | `groups.export.users` | Exportar usuários do grupo |

#### **API**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/api/groups` | GroupController@index | `groups.api.index` | API lista grupos |

---

### **4. Permissions - Gerenciamento de Permissões** 🔐

#### **Views**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/permissions` | PermissionController@indexView | `permissions.index` | Lista de permissões |

#### **Actions**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| POST | `/permissions/groups/{group}/assign` | PermissionController@assignPrivilegesToGroup | `permissions.assign` | Atribuir permissões |

#### **API**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/api/permissions` | PermissionController@index | `permission.api.index` | API lista permissões |

---

### **5. Firewall - Gerenciamento de Regras** 🛡️

#### **Views**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/firewall` | FirewallController@index | `firewall.index` | Página de regras |

#### **API - Regras**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/api/firewall/rules` | FirewallController@getRules | `firewall.api.rules` | Listar regras |
| GET | `/api/firewall/rules/{uuid}` | FirewallController@getRule | `firewall.api.rules.show` | Detalhes da regra |
| POST | `/api/firewall/rules` | FirewallController@createRule | `firewall.api.rules.create` | Criar regra |
| PUT | `/api/firewall/rules/{uuid}` | FirewallController@updateRule | `firewall.api.rules.update` | Atualizar regra |
| DELETE | `/api/firewall/rules/{uuid}` | FirewallController@deleteRule | `firewall.api.rules.delete` | Deletar regra |
| POST | `/api/firewall/rules/{uuid}/toggle` | FirewallController@toggleRule | `firewall.api.rules.toggle` | Ativar/Desativar |

#### **API - Aliases**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| GET | `/api/firewall/aliases` | FirewallController@getAliases | `firewall.api.aliases` | Listar aliases |
| POST | `/api/firewall/aliases/add-ip` | FirewallController@addIpToAlias | `firewall.api.aliases.add-ip` | Adicionar IP |
| POST | `/api/firewall/aliases/remove-ip` | FirewallController@removeIpFromAlias | `firewall.api.aliases.remove-ip` | Remover IP |

#### **API - Apply Changes**
| Método | Rota | Controller | Nome | Descrição |
|--------|------|------------|------|-----------|
| POST | `/api/firewall/apply` | FirewallController@applyChanges | `firewall.api.apply` | Aplicar mudanças |

---

## 🔐 Segurança Implementada

### **1. Middleware de Autenticação**

```php
Route::middleware(['auth'])->group(function () {
    // Todas as rotas protegidas
});
```

**Funcionalidade:**
- ✅ Verifica se o usuário está autenticado
- ✅ Redireciona para `/login` se não autenticado
- ✅ Permite acesso apenas se houver sessão válida

---

### **2. Proteção CSRF**

Todas as rotas POST, PUT, DELETE são automaticamente protegidas pelo middleware CSRF do Laravel.

**No Frontend:**
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

**No JavaScript:**
```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

---

### **3. Validação de Dados**

Todos os controllers implementam validação de dados:

```php
$validated = $request->validate([
    'action' => 'required|string|in:pass,block,reject',
    'interface' => 'required|string',
    // ...
]);
```

---

### **4. Autorização (Futuro)**

Para implementar controle de acesso baseado em funções:

```php
// Exemplo de middleware personalizado
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Rotas apenas para administradores
});

// Ou usando Gates
Route::middleware(['auth'])->group(function () {
    Route::delete('/users/{user}', function () {
        if (Gate::denies('delete-user')) {
            abort(403);
        }
        // ...
    });
});
```

---

## 🔄 Fluxo de Autenticação

```
┌──────────────┐
│   Browser    │
└──────┬───────┘
       │
       │ GET /users
       ▼
┌──────────────────┐
│  Middleware Auth │
└──────┬───────────┘
       │
       ├──── Não autenticado ────▶ Redirect /login
       │
       └──── Autenticado ─────────▶ UserController@index
                                           │
                                           ▼
                                    Retorna View
```

---

## 📝 Melhorias Futuras

### **Alta Prioridade**
- [ ] Implementar middleware de autorização (roles/permissions)
- [ ] Rate limiting em rotas de API
- [ ] Logs de auditoria para ações sensíveis
- [ ] Validação de entrada mais rigorosa

### **Média Prioridade**
- [ ] API Tokens para acesso programático
- [ ] Two-Factor Authentication (2FA)
- [ ] Session timeout configurável
- [ ] IP whitelist/blacklist

### **Baixa Prioridade**
- [ ] OAuth2 integration
- [ ] API versioning
- [ ] Documentação Swagger/OpenAPI
- [ ] GraphQL endpoint

---

## 🧪 Testando a Segurança

### **1. Testar Acesso Sem Autenticação**

```bash
# Deve redirecionar para /login
curl -I http://localhost:8000/users
# Location: http://localhost:8000/login
```

### **2. Testar CSRF Protection**

```bash
# Deve retornar 419 (CSRF Token Mismatch)
curl -X POST http://localhost:8000/users
```

### **3. Testar Acesso Autenticado**

```bash
# Login primeiro
curl -c cookies.txt -X POST http://localhost:8000/login \
  -d "email=admin@example.com" \
  -d "password=password"

# Depois acesse rota protegida
curl -b cookies.txt http://localhost:8000/users
# Deve retornar a página
```

---

## 📊 Estatísticas das Rotas

| Categoria | Quantidade | Protegidas | Públicas |
|-----------|------------|------------|----------|
| **Dashboard** | 2 | 1 | 1 |
| **Users** | 14 | 14 | 0 |
| **Groups** | 9 | 9 | 0 |
| **Permissions** | 3 | 3 | 0 |
| **Firewall** | 12 | 12 | 0 |
| **TOTAL** | **40** | **39** | **1** |

---

## ✅ Checklist de Segurança

- [x] ✅ Middleware de autenticação implementado
- [x] ✅ Proteção CSRF ativa
- [x] ✅ Rotas organizadas por funcionalidade
- [x] ✅ Validação de entrada nos controllers
- [x] ✅ HTTPS recomendado (produção)
- [ ] ⏳ Autorização baseada em roles (futuro)
- [ ] ⏳ Rate limiting (futuro)
- [ ] ⏳ Logs de auditoria (futuro)

---

**Status:** ✅ **Segurança Implementada**  
**Última Atualização:** 22 de Outubro de 2025  
**Versão:** 1.0.0

---

## 🚀 Como Usar

### **Adicionar Nova Rota Protegida**

```php
Route::middleware(['auth'])->group(function () {
    
    // ... rotas existentes ...
    
    // Nova rota
    Route::get('/nova-funcionalidade', [NovoController::class, 'index'])
        ->name('nova.index');
});
```

### **Verificar Autenticação em Controller**

```php
public function index()
{
    // auth() já está disponível
    $user = auth()->user();
    
    // Verificar permissões
    if (!$user->can('view-users')) {
        abort(403);
    }
    
    // ...
}
```

### **Redirecionar Usuário Autenticado**

```php
// No LoginController
protected $redirectTo = '/dashboard';

// Ou dinâmico
protected function redirectTo()
{
    return auth()->user()->isAdmin() ? '/admin' : '/dashboard';
}
```
