# 🚀 Guia de Deploy - Hostinger (Hospedagem Compartilhada)

Este guia detalha o processo completo de deploy do e-commerce Laravel 10 na hospedagem compartilhada da Hostinger.

---

## 📋 Pré-requisitos

### No seu computador local:
- ✅ Git instalado
- ✅ Composer instalado
- ✅ Node.js 16+ e npm instalados
- ✅ Acesso FTP/SFTP ou File Manager da Hostinger
- ✅ Credenciais de acesso ao painel da Hostinger

### Na Hostinger:
- ✅ Plano de hospedagem compartilhada ativo
- ✅ PHP 8.1 ou superior (verificar no painel)
- ✅ MySQL/MariaDB disponível
- ✅ Acesso ao File Manager ou FTP

---

## 🔧 Passo 1: Preparação Local

### 1.1. Build dos Assets (Vite)

**IMPORTANTE**: Em hospedagem compartilhada, você precisa compilar os assets localmente antes de fazer upload.

```bash
# No diretório do projeto
npm install
npm run build
```

Isso criará os arquivos em `public/build/` que precisam ser enviados para o servidor.

### 1.2. Otimizar para Produção

```bash
# Instalar dependências de produção (sem dev)
composer install --optimize-autoloader --no-dev

# Gerar chave da aplicação (se ainda não tiver)
php artisan key:generate

# Limpar caches antigos
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 1.3. Preparar Arquivo .env para Produção

Crie um arquivo `.env.production` com as configurações da Hostinger:

```env
APP_NAME="Shava Haux"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seudominio.com.br

# Banco de Dados (Hostinger)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_ecommerce
DB_USERNAME=u123456789_admin
DB_PASSWORD=sua_senha_aqui

# Cache e Sessão (usar file em hospedagem compartilhada)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mercado Pago (PRODUÇÃO - substituir por chaves reais)
MERCADOPAGO_ACCESS_TOKEN=APP_USR-...
MERCADOPAGO_PUBLIC_KEY=APP_USR-...

# Abacate Pay (PRODUÇÃO)
ABACATEPAY_API_KEY=sua_chave_producao

# Frenet
FRENET_API_TOKEN=seu_token
FRENET_CEP_ORIGEM=69921248

# Google OAuth
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=https://seudominio.com.br/auth/google/callback

# Email (SMTP da Hostinger)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@seudominio.com.br
MAIL_PASSWORD=sua_senha_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="${APP_NAME}"

# Logs
LOG_CHANNEL=daily
LOG_LEVEL=error
```

**⚠️ IMPORTANTE**: 
- Substitua `APP_DEBUG=false` em produção
- Use chaves de **PRODUÇÃO** do Mercado Pago (não sandbox)
- Use `APP_URL` com HTTPS e seu domínio real

---

## 📤 Passo 2: Upload dos Arquivos

### 2.1. Estrutura de Diretórios na Hostinger

Na Hostinger, a estrutura típica é:
```
/home/u123456789/
├── public_html/          ← Arquivos públicos (equivalente ao /public)
├── domains/
└── ...
```

### 2.2. Opção A: Via File Manager (Recomendado)

1. Acesse o **hPanel** da Hostinger
2. Vá em **File Manager**
3. Navegue até `public_html`
4. **Delete todos os arquivos padrão** (index.html, etc.)

#### Upload via ZIP (Mais Rápido)

```bash
# No seu computador, crie um arquivo ZIP com os arquivos necessários
# Exclua arquivos desnecessários:
zip -r deploy.zip . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x "tests/*" \
  -x "*.md" \
  -x ".env*" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*"
```

1. Faça upload do `deploy.zip` no File Manager
2. Extraia o arquivo ZIP
3. Mova o conteúdo para `public_html`

### 2.3. Opção B: Via FTP/SFTP

Use um cliente FTP como **FileZilla** ou **WinSCP**:

1. Conecte-se ao servidor FTP da Hostinger
2. Navegue até `public_html`
3. Faça upload de todos os arquivos (exceto os listados acima)

---

## 🔄 Passo 3: Reorganizar Estrutura (Hospedagem Compartilhada)

Na hospedagem compartilhada, precisamos mover os arquivos públicos para `public_html` e o resto para um nível acima.

### 3.1. Estrutura Final na Hostinger

```
/home/u123456789/
├── public_html/              ← Arquivos públicos
│   ├── index.php
│   ├── .htaccess
│   ├── build/               ← Assets compilados
│   ├── css/
│   ├── js/
│   └── images/
│
├── app/                     ← Código da aplicação (fora do public_html)
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                     ← Arquivo de configuração
├── artisan
├── composer.json
└── ...
```

### 3.2. Modificar public/index.php

Edite `public/index.php` para apontar para o diretório correto:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

**⚠️ IMPORTANTE**: O caminho `__DIR__.'/../` assume que o Laravel está um nível acima do `public_html`.

### 3.3. Criar .htaccess no public_html

**Opção A: Copiar arquivo de exemplo**

Copie o arquivo `.htaccess.production` para `public_html/.htaccess`:

```bash
# Via File Manager: copie .htaccess.production para public_html/.htaccess
# Ou renomeie public/.htaccess para public_html/.htaccess
```

**Opção B: Criar manualmente**

Crie/edite `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger arquivos sensíveis
<FilesMatch "^(\.env|\.git|composer\.(json|lock)|package\.(json|lock))$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Otimizações de Performance
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Compressão GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

---

## 🗄️ Passo 4: Configurar Banco de Dados

### 4.1. Criar Banco de Dados na Hostinger

1. Acesse o **hPanel** → **MySQL Databases**
2. Crie um novo banco de dados (ex: `u123456789_ecommerce`)
3. Crie um usuário e associe ao banco
4. Anote as credenciais (host geralmente é `localhost`)

### 4.2. Importar Estrutura do Banco

**Opção A: Via phpMyAdmin**

1. Acesse **phpMyAdmin** no hPanel
2. Selecione seu banco de dados
3. Vá em **Importar**
4. Execute as migrations manualmente ou importe um SQL

**Opção B: Via SSH (se disponível)**

```bash
# Conectar via SSH
ssh u123456789@seudominio.com.br

# Navegar até o diretório
cd ~/public_html/../

# Executar migrations
php artisan migrate --force
php artisan db:seed --force
```

**Opção C: Via Artisan Tinker (se disponível)**

Se tiver acesso ao terminal, execute:

```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## ⚙️ Passo 5: Configuração Final

### 5.1. Configurar .env

1. No File Manager, navegue até o diretório raiz (fora do `public_html`)
2. Renomeie `.env.example` para `.env` (ou crie um novo)
3. Edite o `.env` com as configurações da Hostinger (veja Passo 1.3)

### 5.2. Configurar Permissões

Via File Manager ou SSH:

```bash
# Dar permissões corretas
chmod -R 755 storage bootstrap/cache
chmod -R 755 public_html
chown -R u123456789:u123456789 storage bootstrap/cache
```

**Via File Manager:**
1. Selecione as pastas `storage` e `bootstrap/cache`
2. Clique com botão direito → **Change Permissions**
3. Defina como `755` ou `775`

### 5.3. Criar Link Simbólico de Storage

```bash
# Via SSH (se disponível)
php artisan storage:link
```

**Se não tiver SSH**, crie manualmente no File Manager:
1. Vá em `public_html`
2. Crie um link simbólico chamado `storage` apontando para `../storage/app/public`

### 5.4. Otimizar para Produção

```bash
# Via SSH (se disponível)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Se não tiver SSH**, use o arquivo `optimize-production.php` incluído no projeto:

1. Faça upload do arquivo `optimize-production.php` para a raiz do projeto (fora do `public_html`)
2. Acesse via navegador: `https://seudominio.com.br/optimize-production.php`
3. **IMPORTANTE**: Delete o arquivo `optimize-production.php` após executar!

O script irá:
- ✅ Criar cache de configuração
- ✅ Criar cache de rotas
- ✅ Criar cache de views
- ✅ Executar otimização geral
- ✅ Verificar permissões
- ✅ Verificar link simbólico de storage

---

## 🔒 Passo 6: Segurança

### 6.1. Proteger Arquivos Sensíveis

Certifique-se de que o `.htaccess` está protegendo:
- `.env`
- `composer.json` / `composer.lock`
- `.git/` (se houver)

### 6.2. Desabilitar Debug

No `.env`:
```env
APP_DEBUG=false
APP_ENV=production
```

### 6.3. Configurar HTTPS

1. No hPanel, ative o **SSL gratuito** (Let's Encrypt)
2. Force HTTPS no `.htaccess`:

```apache
# Forçar HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## ✅ Passo 7: Verificação e Testes

### 7.1. Verificar se o Site Carrega

1. Acesse `https://seudominio.com.br`
2. Verifique se a página inicial carrega
3. Verifique o console do navegador (F12) por erros

### 7.2. Testar Funcionalidades

- [ ] Página inicial carrega
- [ ] Listagem de produtos funciona
- [ ] Carrinho funciona
- [ ] Checkout funciona
- [ ] Login/Registro funciona
- [ ] Admin Panel (Filament) acessível em `/admin`
- [ ] Upload de imagens funciona
- [ ] Pagamentos (Mercado Pago / Abacate Pay) funcionam

### 7.3. Verificar Logs

Se algo der errado, verifique os logs:

```bash
# Via SSH
tail -f storage/logs/laravel.log
```

Ou via File Manager: `storage/logs/laravel.log`

---

## 🐛 Troubleshooting

### Erro 500 (Internal Server Error)

1. **Verifique permissões** de `storage` e `bootstrap/cache`
2. **Verifique o `.env`** - todas as variáveis estão corretas?
3. **Verifique os logs** em `storage/logs/laravel.log`
4. **Verifique PHP version** - deve ser 8.1+

### Erro "Class not found"

1. Execute `composer dump-autoload` (se tiver SSH)
2. Verifique se `vendor/` foi enviado corretamente

### Assets não carregam (CSS/JS)

1. Verifique se `npm run build` foi executado localmente
2. Verifique se `public/build/` foi enviado
3. Verifique permissões de `public/build/`

### Erro de Conexão com Banco

1. Verifique credenciais no `.env`
2. Verifique se o host é `localhost` (não `127.0.0.1`)
3. Verifique se o banco foi criado no hPanel

### Admin Panel (Filament) não acessível

1. Verifique se o usuário tem `is_admin=1` e `role='admin'`
2. Verifique rotas em `routes/web.php`
3. Verifique middleware `EnsureUserIsAdmin`

---

## 📝 Checklist Final

- [ ] Arquivos enviados para o servidor
- [ ] `.env` configurado com credenciais corretas
- [ ] Banco de dados criado e migrations executadas
- [ ] Permissões de `storage` e `bootstrap/cache` configuradas (755)
- [ ] Link simbólico `storage` criado em `public_html`
- [ ] Assets compilados (`npm run build`) e enviados
- [ ] `APP_DEBUG=false` no `.env`
- [ ] SSL/HTTPS configurado
- [ ] Cache otimizado (`php artisan optimize`)
- [ ] Testes realizados (páginas, checkout, admin)
- [ ] Logs verificados (sem erros críticos)

---

## 🚀 Próximos Passos

1. **Backup Regular**: Configure backups automáticos do banco via hPanel
2. **Monitoramento**: Configure alertas de erro (Sentry, Bugsnag, etc.)
3. **Performance**: Considere CDN para assets estáticos
4. **Segurança**: Mantenha dependências atualizadas (`composer update`)

---

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs em `storage/logs/laravel.log`
2. Consulte a documentação da Hostinger
3. Entre em contato com o suporte da Hostinger

---

**Última atualização**: Novembro 2024

