# ⚙️ Configurações Recomendadas - Sistema de Importação

## 📝 Configurações do Laravel

### 1. Limites de Upload (`.env`)

```env
# Aumentar limite de upload para arquivos Excel grandes
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
MAX_EXECUTION_TIME=300

# Configurações de sessão (para armazenar credenciais temporariamente)
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 2. Configurações do PHP (`php.ini`)

```ini
; Limite de memória para processar arquivos grandes
memory_limit = 256M

; Tempo máximo de execução (5 minutos)
max_execution_time = 300

; Tamanho máximo de upload
upload_max_filesize = 10M
post_max_size = 10M

; Tamanho máximo do input
max_input_vars = 5000
```

### 3. Configurações de PDF (`config/dompdf.php`)

Se o arquivo não existir, publique as configurações:
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

```php
return [
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    'options' => [
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_font' => 'serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => true,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],
];
```

---

## 🗄️ Otimizações de Banco de Dados

### Índices Recomendados

```sql
-- Índice para busca rápida por RA
CREATE INDEX idx_users_ra ON users(ra);

-- Índice para busca por tipo
CREATE INDEX idx_users_type ON users(user_type);

-- Índice para busca por status
CREATE INDEX idx_users_status ON users(status);

-- Índice composto para filtros combinados
CREATE INDEX idx_users_type_status ON users(user_type, status);

-- Verificar índices criados
SHOW INDEX FROM users;
```

### Otimização de Consultas

Para consultas frequentes, considere criar views:

```sql
-- View para usuários ativos
CREATE VIEW active_users AS
SELECT id, name, email, ra, user_type, status, created_at
FROM users
WHERE status = 'ativo';

-- View para estatísticas
CREATE VIEW user_statistics AS
SELECT
    user_type,
    status,
    COUNT(*) as total
FROM users
GROUP BY user_type, status;
```

---

## 🚀 Otimizações de Performance

### 1. Cache de Configurações

```bash
# Compilar configurações (produção)
php artisan config:cache

# Compilar rotas (produção)
php artisan route:cache

# Compilar views (produção)
php artisan view:cache
```

### 2. Queue para Importações Grandes

**Futuro:** Para arquivos muito grandes (>500 usuários), considere:

```php
// app/Jobs/ImportUsersJob.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\UserImportService;

class ImportUsersJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $userType;

    public function __construct($filePath, $userType)
    {
        $this->filePath = $filePath;
        $this->userType = $userType;
    }

    public function handle(UserImportService $importService)
    {
        $importService->importFromExcel($this->filePath, $this->userType);
    }
}
```

---

## 🔐 Segurança

### 1. Validação de Domínio de E-mail

Adicione em `config/app.php`:

```php
'allowed_email_domains' => [
    'escola.com',
    'faculdade.edu',
    'instituicao.edu.br',
],
```

E valide no Service:

```php
$domain = substr(strrchr($email, "@"), 1);
if (!in_array($domain, config('app.allowed_email_domains'))) {
    throw new \Exception("Domínio de e-mail não autorizado: {$domain}");
}
```

### 2. Rate Limiting

Em `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... outros middlewares
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
    ],
];
```

Em `routes/web.php`:

```php
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/users/import/excel/process', [UserController::class, 'processExcelImport']);
});
```

### 3. Sanitização de Arquivos

Adicione validação extra:

```php
// No UserImportService
private function validateExcelFile($filePath)
{
    $mimeType = mime_content_type($filePath);
    $allowedMimes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-excel', // .xls
    ];

    if (!in_array($mimeType, $allowedMimes)) {
        throw new \Exception('Arquivo não é um Excel válido');
    }

    return true;
}
```

---

## 📧 Notificações por E-mail (Futuro)

### Configurar SMTP no `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@escola.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Criar Notification

```bash
php artisan make:notification UserCredentialsNotification
```

```php
// app/Notifications/UserCredentialsNotification.php
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Suas Credenciais de Acesso - OPNsense')
        ->greeting("Olá, {$notifiable->name}!")
        ->line("Seu acesso ao sistema foi criado.")
        ->line("**Login:** {$notifiable->email}")
        ->line("**Senha temporária:** [será enviada separadamente]")
        ->action('Acessar Sistema', url('/login'))
        ->line('Por favor, altere sua senha no primeiro acesso.');
}
```

---

## 📊 Monitoramento

### 1. Logs Estruturados

Adicione em `config/logging.php`:

```php
'channels' => [
    'import' => [
        'driver' => 'daily',
        'path' => storage_path('logs/import.log'),
        'level' => 'info',
        'days' => 14,
    ],
],
```

Use no código:

```php
Log::channel('import')->info('Importação iniciada', [
    'user_id' => auth()->id(),
    'file_name' => $fileName,
    'user_type' => $userType,
]);
```

### 2. Métricas de Uso

```php
// app/Services/UserImportService.php

private function trackImportMetrics($result)
{
    DB::table('import_history')->insert([
        'user_id' => auth()->id(),
        'total_processed' => $result['total_processed'],
        'total_imported' => $result['total_imported'],
        'total_errors' => $result['total_errors'],
        'execution_time' => $result['execution_time'],
        'created_at' => now(),
    ]);
}
```

---

## 🎨 Personalização

### 1. Logo no PDF

Modifique `resources/views/pdf/user-credentials.blade.php`:

```html
<div class="header">
    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 50px;">
    <h1>🔐 Credenciais de Acesso</h1>
    <!-- ... -->
</div>
```

### 2. Cores Personalizadas

Crie `config/branding.php`:

```php
return [
    'primary_color' => '#4472C4',
    'secondary_color' => '#28a745',
    'institution_name' => 'Sua Instituição',
    'support_email' => 'suporte@instituicao.com',
];
```

---

## 🧹 Manutenção

### 1. Limpeza de Arquivos Temporários

Crie um command:

```bash
php artisan make:command CleanTempFiles
```

```php
// app/Console/Commands/CleanTempFiles.php
public function handle()
{
    $tempDir = sys_get_temp_dir();
    $files = glob($tempDir . '/template_importacao_*');

    foreach ($files as $file) {
        if (file_exists($file) && filemtime($file) < strtotime('-1 hour')) {
            unlink($file);
            $this->info("Deleted: {$file}");
        }
    }
}
```

Agende no `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('clean:temp-files')->hourly();
}
```

### 2. Backup Regular

```bash
# Adicione ao cron do servidor
0 2 * * * cd /caminho/do/projeto && php artisan backup:run
```

---

## 🌍 Ambiente de Produção

### Checklist Pré-Deploy

```bash
# 1. Rodar testes
php artisan test

# 2. Verificar configurações
php artisan config:clear
php artisan config:cache

# 3. Otimizar autoload
composer install --optimize-autoloader --no-dev

# 4. Cachear rotas e views
php artisan route:cache
php artisan view:cache

# 5. Rodar migrations
php artisan migrate --force

# 6. Definir permissões
chmod -R 755 storage bootstrap/cache

# 7. Verificar .env
# - APP_ENV=production
# - APP_DEBUG=false
# - Senhas de banco corretas
```

---

## 📈 Monitoramento Recomendado

### Ferramentas Sugeridas

1. **Laravel Telescope** (Dev/Staging)
   ```bash
   composer require laravel/telescope --dev
   php artisan telescope:install
   php artisan migrate
   ```

2. **Laravel Horizon** (Queues - se implementar)
   ```bash
   composer require laravel/horizon
   php artisan horizon:install
   ```

3. **Sentry** (Error Tracking)
   ```bash
   composer require sentry/sentry-laravel
   ```

---

## 📞 Contatos de Suporte

```php
// config/support.php
return [
    'email' => 'suporte@instituicao.com',
    'phone' => '+55 11 1234-5678',
    'hours' => 'Segunda a Sexta, 8h às 18h',
    'documentation' => 'https://docs.instituicao.com',
];
```

---

## ✅ Checklist de Configuração

- [ ] PHP.ini configurado (memory_limit, max_execution_time)
- [ ] .env configurado (limites de upload)
- [ ] Índices de banco criados
- [ ] Cache de configurações habilitado (produção)
- [ ] Logs configurados e rotacionados
- [ ] Backup automático configurado
- [ ] Limpeza de arquivos temporários agendada
- [ ] Rate limiting configurado
- [ ] Validação de domínio de e-mail (se aplicável)
- [ ] Monitoramento de erros configurado
- [ ] Documentação acessível para usuários

---

**Configurações concluídas! Sistema pronto para uso em produção. 🚀**
