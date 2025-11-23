# Documentação Completa - E-commerce Shava

## Índice

1. [Visão Geral](#visão-geral)
2. [Requisitos do Sistema](#requisitos-do-sistema)
3. [Instalação e Configuração](#instalação-e-configuração)
4. [Arquitetura do Sistema](#arquitetura-do-sistema)
5. [Estrutura de Diretórios](#estrutura-de-diretórios)
6. [Funcionalidades Principais](#funcionalidades-principais)
7. [Integrações Externas](#integrações-externas)
8. [API Endpoints](#api-endpoints)
9. [Painel Administrativo](#painel-administrativo)
10. [Deploy em Produção](#deploy-em-produção)
11. [Manutenção e Suporte](#manutenção-e-suporte)
12. [Troubleshooting](#troubleshooting)

---

## Visão Geral

### Descrição do Projeto

O **E-commerce Shava** é uma plataforma completa de vendas online desenvolvida em Laravel 10 com Livewire 3, oferecendo uma experiência moderna e responsiva tanto para clientes quanto para administradores.

### Tecnologias Utilizadas

- **Backend**: Laravel 10.0
- **Frontend**: Livewire 3.2, TailwindCSS, Blade Templates
- **Painel Admin**: Filament 3.2
- **Banco de Dados**: MySQL/MariaDB
- **Cache**: Redis (opcional)
- **Queue**: Redis/Database
- **Pagamentos**: Abacate Pay (PIX), Mercado Pago (Cartão/Pix)
- **Frete**: Frenet API
- **Autenticação**: Laravel Sanctum + Socialite (Google)

### Características Principais

- ✅ Sistema completo de e-commerce
- ✅ Interface responsiva e moderna
- ✅ Painel administrativo intuitivo
- ✅ Múltiplos métodos de pagamento
- ✅ Cálculo de frete em tempo real
- ✅ Sistema de avaliação de produtos
- ✅ Gestão de estoque e pedidos
- ✅ Autenticação social
- ✅ Carrinho de compras persistente

---

## Requisitos do Sistema

### Requisitos Mínimos

- **PHP**: 8.1 ou superior
- **MySQL**: 5.7+ ou MariaDB 10.3+
- **Web Server**: Apache 2.4+ ou Nginx 1.18+
- **Memória RAM**: Mínimo 2GB, recomendado 4GB+
- **Espaço em Disco**: Mínimo 5GB disponíveis

### Extensões PHP Obrigatórias

```bash
php -m | grep -E "(mbstring|openssl|pdo|tokenizer|xml|ctype|fileinfo|json|bcmath|curl|dom|filter|hash|iconv|intl|session|simplexml|sodium)"
```

- bcmath
- ctype
- curl
- dom
- fileinfo
- filter
- hash
- iconv
- intl
- json
- mbstring
- openssl
- pdo_mysql
- posix
- session
- sodium
- tokenizer
- xml

### Dependências Node.js

- **Node.js**: 16.0+ (para compilação de assets)
- **npm**: 8.0+ ou **yarn**: 1.22+

---

## Instalação e Configuração

### 1. Clonar o Repositório

```bash
git clone <repository-url>
cd ecommerce_shava
```

### 2. Configurar Ambiente

```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### 3. Configurar Variáveis de Ambiente

Editar o arquivo `.env` com suas credenciais:

```env
# Configurações Básicas
APP_NAME="E-commerce Shava"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Configurações de Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_shava
DB_USERNAME=root
DB_PASSWORD=

# Configurações de Pagamento
MERCADOPAGO_ACCESS_TOKEN=APP_USR-...
MERCADOPAGO_PUBLIC_KEY=APP_USR-...
ABACATEPAY_API_KEY=abc_dev_...

# Configurações de Frete
FRENET_CEP_ORIGEM=69921248
FRENET_API_TOKEN=962CB46FRDB06R45A2RABC2R608CDC9EBEA0

# Configurações de Email
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Configurações do Google (Login Social)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 4. Instalar Dependências

```bash
# Dependências PHP
composer install --optimize-autoloader --no-dev

# Dependências Node.js
npm install
npm run build
```

### 5. Configurar Banco de Dados

```bash
# Criar banco de dados
mysql -u root -p
CREATE DATABASE ecommerce_shava CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Rodar migrações
php artisan migrate --force

# Popular dados iniciais (opcional)
php artisan db:seed --class=DatabaseSeeder
```

### 6. Configurar Permissões

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (IIS)
# Dar permissão de escrita nas pastas storage e bootstrap/cache
```

### 7. Otimizações de Produção

```bash
# Cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize
```

### 8. Configurar Queue Worker (Opcional)

```bash
# Iniciar worker de filas
php artisan queue:work --daemon --sleep=3 --tries=3

# Ou usar supervisor para gestão automática
```

---

## Arquitetura do Sistema

### Padrão Arquitetural

O sistema segue o padrão **MVC (Model-View-Controller)** com componentes **Livewire** para interatividade em tempo real.

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend        │    │   Database      │
│                 │    │                  │    │                 │
│ • Blade Views   │◄──►│ • Controllers    │◄──►│ • MySQL/MariaDB │
│ • Livewire      │    │ • Livewire       │    │ • Models        │
│ • TailwindCSS   │    │ • Services       │    │ • Migrations    │
│ • Alpine.js     │    │ • Middleware     │    │ • Seeders       │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

### Componentes Principais

#### 1. Models (Camada de Dados)
- **Product**: Gestão de produtos
- **Category**: Categorias de produtos
- **Order**: Pedidos dos clientes
- **OrderItem**: Itens dos pedidos
- **User**: Usuários do sistema
- **Cart**: Carrinho de compras
- **Review**: Avaliações de produtos

#### 2. Controllers (Camada de Lógica)
- **ProductController**: Listagem e detalhes de produtos
- **CartController**: Gestão do carrinho
- **CheckoutController**: Processamento de pedidos
- **Auth Controllers**: Autenticação e registro
- **ProfileController**: Gestão de perfil

#### 3. Livewire Components (Interatividade)
- **ProductList**: Listagem de produtos com filtros
- **AddToCart**: Adicionar produtos ao carrinho
- **CartPage**: Página do carrinho
- **CheckoutPage**: Finalização de compra
- **MiniCart**: Carrinho flutuante

#### 4. Services (Integrações Externas)
- **FrenetService**: Cálculo de frete
- **PaymentService**: Processamento de pagamentos
- **EmailService**: Envio de emails transacionais

---

## Estrutura de Diretórios

```
ecommerce_shava/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controllers HTTP
│   │   ├── Middleware/           # Middleware da aplicação
│   │   └── Requests/             # Form Requests
│   ├── Livewire/                 # Componentes Livewire
│   │   ├── Auth/                # Componentes de autenticação
│   │   ├── CheckoutPage.php     # Checkout
│   │   ├── ProductList.php      # Listagem de produtos
│   │   └── CartPage.php         # Carrinho
│   ├── Models/                   # Models Eloquent
│   ├── Providers/                # Service Providers
│   └── Services/                 # Classes de serviço
├── bootstrap/                    # Bootstrapping da aplicação
├── config/                       # Arquivos de configuração
├── database/
│   ├── migrations/              # Migrações do banco
│   ├── seeders/                 # Dados iniciais
│   └── factories/               # Factories para testes
├── public/                       # Arquivos públicos
│   ├── assets/                  # Assets compilados
│   ├── storage/                 # Uploads de arquivos
│   └── index.php               # Entry point
├── resources/
│   ├── views/                   # Templates Blade
│   │   ├── livewire/           # Views dos componentes
│   │   ├── components/         # Componentes Blade
│   │   └── layouts/            # Layouts principais
│   ├── css/                     # Arquivos CSS
│   └── js/                      # Arquivos JavaScript
├── routes/                       # Definição de rotas
│   ├── web.php                 # Rotas web
│   ├── api.php                 # Rotas API
│   └── auth.php                # Rotas de autenticação
├── storage/                      # Armazenamento da aplicação
│   ├── app/                    # Logs e cache
│   ├── framework/              # Cache do framework
│   └── logs/                   # Logs da aplicação
└── tests/                       # Testes automatizados
```

---

## Funcionalidades Principais

### 1. Gestão de Produtos

#### Cadastro de Produtos
- Nome, descrição, preço
- Upload de múltiplas imagens
- Categorias e atributos
- Controle de estoque
- Status (ativo/inativo)

#### Variáveis de Produtos
- Cores, tamanhos, modelos
- Preços diferenciados
- Estoque individual por variação

### 2. Carrinho de Compras

#### Funcionalidades
- Adicionar/remover produtos
- Atualizar quantidades
- Cálculo automático de subtotal
- Persistência de sessão
- Mini-cart flutuante

#### Comportamento
- Sessão para visitantes
- Persistência para usuários logados
- Atualização em tempo real com Livewire

### 3. Checkout e Pagamentos

#### Processo de Checkout
1. **Identificação**: Login ou cadastro rápido
2. **Endereço**: Busca automática de CEP
3. **Frete**: Cálculo em tempo real
4. **Pagamento**: PIX ou Cartão de crédito
5. **Confirmação**: Redirecionamento para pagamento

#### Métodos de Pagamento
- **PIX (Abacate Pay)**: Geração de QR Code
- **Mercado Pago (Cartão/Pix)**: Checkout Pro com redirect
- **Boleto**: Futura implementação

### 4. Cálculo de Frete

#### Integração Frenet
- Múltiplas transportadoras
- Prazos de entrega
- Valores em tempo real
- CEP de origem configurável

### 5. Gestão de Pedidos

#### Fluxo do Pedido
1. **Pendente**: Aguardando pagamento
2. **Pago**: Pagamento confirmado
3. **Em Processamento**: Separação do produto
4. **Enviado**: Despachado para transportadora
5. **Entregue**: Cliente recebeu
6. **Cancelado**: Pedido cancelado

#### Notificações
- Email de confirmação
- Status atualizado
- Tracking de entrega

### 6. Sistema de Avaliações

#### Funcionalidades
- Avaliação de 1-5 estrelas
- Comentários textuais
- Aprovação moderada
- Média de avaliações

---

## Integrações Externas

### 1. Abacate Pay (PIX)

#### Configuração
```php
// config/services.php
'abacatepay' => [
    'api_key' => env('ABACATEPAY_API_KEY'),
    'sandbox' => env('APP_ENV') === 'local',
],
```

#### Funcionalidades
- Geração de cobranças PIX
- Webhook para confirmação
- QR Code dinâmico
- Status em tempo real

#### Webhook Setup
```bash
# URL do Webhook
https://seusite.com/webhook/abacatepay
```

### 2. Mercado Pago (Cartão/Pix)

#### Configuração
```php
// config/services.php
'mercadopago' => [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
],
```

#### Funcionalidades
- Checkout Pro (redirect)
- Suporte para Cartão de Crédito e Pix
- API de Orders
- Webhooks para confirmação

### 3. Frenet (Frete)

#### Configuração
```php
// config/services.php
'frenet' => [
    'api_token' => env('FRENET_API_TOKEN'),
    'cep_origem' => env('FRENET_CEP_ORIGEM'),
],
```

#### Funcionalidades
- Cotação em tempo real
- Múltiplos serviços
- Prazos de entrega
- Cálculo automático

### 4. Google OAuth

#### Configuração
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

#### Funcionalidades
- Login social
- Importação de dados básicos
- Sessão única

---

## API Endpoints

### Rotas Web Principais

```php
// Página inicial
GET /                     -> HomeController@index

// Produtos
GET /produtos             -> ProductController@index
GET /produto/{slug}       -> ProductController@show

// Carrinho
GET /carrinho             -> CartPage::class
POST /cart/add            -> AddToCart::class
DELETE /cart/remove       -> CartController@remove

// Checkout
GET /checkout             -> CheckoutPage::class
POST /checkout/process    -> CheckoutController@process

// Autenticação
GET /login                -> LoginPage::class
POST /login               -> Auth\AuthenticatedSessionController@store
GET /register             -> RegisterPage::class
POST /register            -> Auth\RegisteredUserController@store
GET /auth/google          -> GoogleLoginController@redirectToGoogle
GET /auth/google/callback -> GoogleLoginController@handleGoogleCallback

// Perfil
GET /perfil               -> ProfilePage::class
GET /meus-pedidos         -> MyOrdersList::class
GET /pedido/{id}          -> ViewOrderDetails::class

// Painel Admin
/admin                    -> Filament Admin Panel
```

### API Routes

```php
// API de produtos
GET /api/products         -> ProductController@apiIndex
GET /api/products/{id}    -> ProductController@apiShow

// API de carrinho
GET /api/cart             -> CartController@apiIndex
POST /api/cart/add        -> CartController@apiAdd
DELETE /api/cart/{id}     -> CartController@apiRemove

// Webhooks
POST /api/webhook/mercadopago -> MercadoPagoWebhookController
POST /api/webhook/abacate     -> AbacateWebhookController
```

### Livewire Components

#### ProductList
- Filtros por categoria
- Ordenação (preço, nome, popularidade)
- Paginação infinita
- Busca em tempo real

#### CartPage
- Lista de itens
- Atualização de quantidades
- Cálculo de totais
- Redirecionamento para checkout

#### CheckoutPage
- Formulário de endereço
- Cálculo de frete
- Seleção de pagamento
- Processamento do pedido

---

## Painel Administrativo

### Acesso ao Painel

```
URL: /admin
Email: admin@example.com
Senha: password (alterar após primeiro acesso)
```

### Recursos Disponíveis

#### 1. Dashboard
- Estatísticas de vendas
- Pedidos recentes
- Produtos mais vendidos
- Visitas do site

#### 2. Gestão de Produtos
- **CRUD completo**: Criar, ler, atualizar, deletar
- **Upload de imagens**: Múltiplas imagens por produto
- **Categorias**: Hierarquia de categorias
- **Atributos**: Cores, tamanhos, especificações
- **Estoque**: Controle de quantidade
- **Preços**: Preços promocionais, descontos

#### 3. Gestão de Pedidos
- **Listagem**: Todos os pedidos com filtros
- **Detalhes**: Informações completas do pedido
- **Status**: Atualização de status manual
- **Notificações**: Email automáticos
- **Exportação**: CSV/Excel

#### 4. Gestão de Usuários
- **Clientes**: Lista de todos os clientes
- **Admins**: Gestão de administradores
- **Permissões**: Controle de acesso
- **Ativação**: Ativar/desativar usuários

#### 5. Configurações
- **Loja**: Nome, contato, redes sociais
- **Pagamentos**: Configurações de gateways
- **Frete**: Configurações de transportadoras
- **Email**: Templates de email

#### 6. Relatórios
- **Vendas**: Por período, produto, categoria
- **Clientes**: Novos clientes, retenção
- **Produtos**: Mais vendidos, menos vendidos
- **Financeiro**: Faturamento, comissões

### Customizações do Filament

#### Resources Personalizados
```php
// app/Filament/Resources/ProductResource.php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    // Formulários personalizados
    // Tabelas customizadas
    // Filtros avançados
}
```

#### Pages Customizadas
- Dashboard personalizado
- Relatórios customizados
- Páginas de configuração

---

## Deploy em Produção

### 1. Servidor Compartilhado (Hospedagem Compartilhada)

#### Requisitos
- PHP 8.1+
- MySQL 5.7+
- cPanel/Plesk (opcional)
- Acesso SSH (recomendado)

#### Passos de Deploy

```bash
# 1. Upload dos arquivos
scp -r . user@server:/public_html/ecommerce/

# 2. Configurar .env
cp .env.example .env
# Editar com credenciais de produção

# 3. Instalar dependências
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 4. Rodar migrações
php artisan migrate --force

# 5. Otimizar produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Configurar cron job
# Adicionar ao crontab:
# * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Configuração Apache (.htaccess)

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

# Configurações de segurança
<Files .env>
    Order allow,deny
    Deny from all
</Files>

<Files composer.json>
    Order allow,deny
    Deny from all
</Files>
```

### 2. Servidor Dedicado/VPS

#### Configuração do Servidor

```bash
# Instalar PHP e extensões
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-intl

# Instalar Nginx
sudo apt install nginx

# Instalar MySQL
sudo apt install mysql-server

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

#### Configuração Nginx

```nginx
server {
    listen 80;
    server_name seusite.com www.seusite.com;
    root /var/www/ecommerce/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Configurações de cache
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 3. Docker (Containerização)

#### Dockerfile
```dockerfile
FROM php:8.1-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código da aplicação
WORKDIR /var/www
COPY . /var/www

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Configurar permissões
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

#### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: ecommerce_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - ecommerce

  webserver:
    image: nginx:alpine
    container_name: ecommerce_webserver
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/:/etc/nginx/conf.d/
    networks:
      - ecommerce

  db:
    image: mysql:5.7
    container_name: ecommerce_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ecommerce
      MYSQL_ROOT_PASSWORD: root
      MYSQL_PASSWORD: password
      MYSQL_USER: ecommerce
    volumes:
      - dbdata:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - ecommerce

networks:
  ecommerce:
    driver: bridge

volumes:
  dbdata:
    driver: local
```

### 4. CI/CD (GitHub Actions)

#### .github/workflows/deploy.yml
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extension-coverage: none
        
    - name: Install Dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Build Assets
      run: npm install && npm run build
      
    - name: Deploy to Server
      uses: appleboy/scp-action@master
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        source: "."
        target: "/var/www/ecommerce"
```

---

## Manutenção e Suporte

### 1. Backup Automático

#### Banco de Dados
```bash
#!/bin/bash
# backup_database.sh

DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="ecommerce_shava"
DB_USER="root"
DB_PASS="password"
BACKUP_DIR="/var/backups/ecommerce"

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "db_backup_*.sql" -mtime +7 -delete
```

#### Arquivos
```bash
#!/bin/bash
# backup_files.sh

DATE=$(date +%Y%m%d_%H%M%S)
SOURCE_DIR="/var/www/ecommerce"
BACKUP_DIR="/var/backups/ecommerce"

tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz -C $SOURCE_DIR public/storage
```

#### Cron Jobs
```bash
# Backup diário às 2h
0 2 * * * /path/to/backup_database.sh

# Backup semanal de arquivos
0 3 * * 0 /path/to/backup_files.sh
```

### 2. Monitoramento

#### Logs da Aplicação
```bash
# Verificar logs em tempo real
tail -f storage/logs/laravel.log

# Logs de erro
grep -i "error" storage/logs/laravel.log

# Logs específicos de hoje
grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log
```

#### Monitoramento de Performance
```bash
# Instalar Laravel Telescope (dev)
composer require laravel/telescope --dev
php artisan telescope:install

# Monitoramento em produção
composer require laravel/horizon
php artisan horizon:install
```

### 3. Atualizações de Segurança

#### Atualizar Dependências
```bash
# Verificar dependências desatualizadas
composer outdated

# Atualizar composer
composer update

# Atualizar npm
npm update

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Security Patches
```bash
# Verificar vulnerabilidades
composer audit

# Corrigir automaticamente quando possível
composer audit --fix
```

### 4. Otimização de Performance

#### Cache Strategy
```bash
# Configurar Redis para cache
composer require predis/predis

# Configurar no .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Otimizar queries
php artisan model:show Product
```

#### Asset Optimization
```bash
# Minificar CSS/JS
npm run build --production

# Configurar CDN (opcional)
# Usar Cloudflare ou Amazon CloudFront
```

### 5. SSL e Segurança

#### Configurar HTTPS
```bash
# Usar Let's Encrypt (gratuito)
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d seusite.com -d www.seusite.com

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### Security Headers
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    
    return $response;
}
```

---

## Troubleshooting

### 1. Problemas Comuns

#### Erro 500 - Internal Server Error
```bash
# Verificar permissões
ls -la storage/
ls -la bootstrap/cache/

# Corrigir permissões
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Verificar logs
tail -f storage/logs/laravel.log
```

#### Erro de Conexão com Banco
```bash
# Testar conexão MySQL
mysql -u username -p -h host database_name

# Verificar configuração .env
grep DB_ .env

# Resetar cache de configuração
php artisan config:clear
php artisan config:cache
```

#### Problemas com Assets
```bash
# Limpar e recompilar
php artisan view:clear
rm -rf public/build
npm install
npm run build
```

#### Queue não processando
```bash
# Verificar status do queue
php artisan queue:failed

# Reiniciar worker
php artisan queue:restart

# Testar queue manualmente
php artisan queue:work --timeout=60
```

### 2. Debug Mode

#### Ativar Debug
```env
# .env
APP_DEBUG=true
LOG_LEVEL=debug
```

#### Telescope para Debug
```bash
# Instalar em ambiente de desenvolvimento
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Performance Issues

#### Identificar Queries Lentas
```php
// Habilitar query log
DB::enableQueryLog();
// ... seu código
$queries = DB::getQueryLog();
dd($queries);
```

#### Otimizar Cache
```bash
# Limpar caches antigos
php artisan cache:clear
php artisan view:clear

# Recriar caches otimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Email Não Enviando

#### Configurar Mailpit (Desenvolvimento)
```bash
# Instalar Mailpit
curl -sL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh | sh

# Configurar .env
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

#### Testar Email
```bash
# Enviar email de teste
php artisan tinker
>>> Mail::raw('Test email', function($message) {
...     $message->to('test@example.com')->subject('Test');
... });
```

### 5. Pagamento Não Processando

#### Verificar Webhooks
```bash
# Testar webhook local
ngrok http 8000

# Verificar logs de webhook
grep "webhook" storage/logs/laravel.log
```

#### Debug de Pagamento
```php
// Adicionar logs no controller
Log::info('Payment attempt:', [
    'amount' => $amount,
    'payment_method' => $paymentMethod,
    'user_id' => auth()->id()
]);
```

### 6. Contato de Suporte

#### Informações para Suporte Técnico
- **Versão PHP**: `php -v`
- **Versão Laravel**: `php artisan --version`
- **Versão MySQL**: `mysql --version`
- **Logs recentes**: `tail -n 100 storage/logs/laravel.log`
- **Configuração .env** (remover senhas)
- **Erro específico**: Mensagem completa do erro

#### Ferramentas Úteis
```bash
# Sistema info
php -i | grep -E "(memory_limit|max_execution_time|post_max_size|upload_max_filesize)"

# Laravel info
php artisan about

# Database info
php artisan db:show
```

---

## Licença e Termos de Uso

### Licença MIT

Este software está licenciado sob a Licença MIT. Consulte o arquivo LICENSE para mais detalhes.

### Termos de Uso

1. **Uso Comercial**: Permitido com atribuição
2. **Modificação**: Permitida
3. **Distribuição**: Permitida
4. **Responsabilidade**: Sem garantia, uso por conta e risco

### Suporte e Manutenção

- **Suporte Básico**: 30 dias após compra
- **Manutenção**: Contrato anual (opcional)
- **Customizações**: Sob consulta

---

## Contato

- **Desenvolvedor**: [Seu Nome]
- **Email**: seu.email@exemplo.com
- **Telefone**: (XX) XXXXX-XXXX
- **Website**: https://seusite.com

---

*Última atualização: Novembro 2025*
*Versão: 1.0.0*
