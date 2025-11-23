# Docker Setup - Shava Haux E-commerce

Este projeto está configurado para rodar com Docker e Docker Compose.

## 📋 Pré-requisitos

- Docker Desktop (Windows/Mac) ou Docker Engine + Docker Compose (Linux)
- Git

## 🚀 Como usar

### 1. Clonar o repositório

```bash
git clone <seu-repositorio>
cd ecommerce_shava
```

### 2. Configurar variáveis de ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Edite o arquivo `.env` e configure:

```env
APP_NAME="Shava Haux"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=shava_ecommerce
DB_USERNAME=root
DB_PASSWORD=root

# APIs
FRENET_API_TOKEN=seu_token_aqui
FRENET_CEP_ORIGEM=69921248
ABACATEPAY_API_KEY=sua_chave_aqui

# Stripe (Test Mode)
STRIPE_KEY=pk_test_sua_chave_publica
STRIPE_SECRET=sk_test_sua_chave_secreta

# Google OAuth
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Mail (opcional)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@shavahaux.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Construir e iniciar os containers

```bash
# Construir as imagens
docker-compose build

# Iniciar os containers
docker-compose up -d

# Ver logs
docker-compose logs -f
```

### 4. Instalar dependências e configurar

```bash
# Entrar no container da aplicação
docker-compose exec app bash

# Dentro do container:
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Build dos assets (Vite)

```bash
# Opção 1: Usar o serviço Node do docker-compose
docker-compose run --rm node npm install
docker-compose run --rm node npm run build

# Opção 2: Instalar Node localmente e rodar
npm install
npm run build
```

### 6. Acessar a aplicação

- **Frontend**: http://localhost:8000
- **Admin Panel (Filament)**: http://localhost:8000/admin
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## 🛠️ Comandos úteis

### Gerenciar containers

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose stop

# Parar e remover containers
docker-compose down

# Parar, remover containers e volumes
docker-compose down -v

# Ver logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Executar comandos Artisan

```bash
# Via docker-compose
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Ou entrar no container
docker-compose exec app bash
php artisan migrate
```

### Acessar banco de dados

```bash
# Via MySQL client
docker-compose exec db mysql -u root -proot shava_ecommerce

# Ou usar ferramenta externa (DBeaver, MySQL Workbench, etc.)
# Host: localhost
# Port: 3306
# User: root
# Password: root
# Database: shava_ecommerce
```

### Rebuild completo

```bash
# Parar tudo
docker-compose down -v

# Rebuild sem cache
docker-compose build --no-cache

# Iniciar novamente
docker-compose up -d

# Reconfigurar
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```

## 📁 Estrutura Docker

```
.
├── Dockerfile              # Imagem PHP/Laravel
├── docker-compose.yml     # Orquestração dos serviços
├── .dockerignore          # Arquivos ignorados no build
└── docker/
    ├── nginx/
    │   └── default.conf   # Configuração do Nginx
    ├── php/
    │   └── local.ini      # Configurações PHP
    └── mysql/
        └── my.cnf         # Configurações MySQL
```

## 🔧 Serviços

### app (PHP 8.2 + FPM)
- PHP 8.2 com extensões necessárias
- Composer instalado
- Porta interna: 9000

### nginx
- Servidor web Nginx
- Porta: 8000 (mapeada para 80 do container)
- Proxy reverso para PHP-FPM

### db (MySQL 8.0)
- Banco de dados MySQL
- Porta: 3306
- Volume persistente para dados

### redis
- Cache Redis
- Porta: 6379
- Volume persistente para dados

### node (apenas para build)
- Node.js 18
- Usado apenas para build de assets
- Profile: build (não inicia automaticamente)

## 🐛 Troubleshooting

### Erro de permissões

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Limpar cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

### Ver logs de erro

```bash
# Logs da aplicação
docker-compose logs app

# Logs do Nginx
docker-compose logs nginx

# Logs do MySQL
docker-compose logs db

# Logs do Laravel
docker-compose exec app tail -f storage/logs/laravel.log
```

### Reinstalar dependências

```bash
docker-compose exec app composer install --no-interaction
docker-compose exec app npm install  # se tiver Node no container
```

## 📝 Notas

- Os volumes são persistidos, então dados do banco não são perdidos ao parar os containers
- Para desenvolvimento, os arquivos são sincronizados via volumes
- Para produção, ajuste as configurações de segurança no `.env` e `docker-compose.yml`

## 🚀 Deploy em Produção

Para produção, considere:

1. Usar variáveis de ambiente seguras
2. Configurar SSL/TLS (HTTPS)
3. Ajustar limites de recursos no `docker-compose.yml`
4. Usar imagens otimizadas (multi-stage build)
5. Configurar backup automático do banco de dados
6. Usar um serviço de fila (Redis/SQS) para jobs assíncronos

