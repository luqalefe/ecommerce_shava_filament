# 🐳 Docker Setup - Shava Haux E-commerce

Configuração Docker completa para desenvolvimento e produção.

## 📦 Estrutura de Arquivos Docker

```
.
├── Dockerfile                    # Imagem PHP 8.2 + FPM para produção
├── Dockerfile.dev                # Imagem PHP 8.2 + FPM + Node.js para desenvolvimento
├── docker-compose.yml            # Orquestração dos serviços
├── docker-compose.override.yml.example  # Exemplo de override para customizações
├── .dockerignore                 # Arquivos ignorados no build
└── docker/
    ├── nginx/
    │   └── default.conf          # Configuração do Nginx
    ├── php/
    │   └── local.ini             # Configurações PHP (upload, memory, etc.)
    └── mysql/
        └── my.cnf                # Configurações MySQL
```

## 🚀 Quick Start

### 1. Configurar variáveis de ambiente

```bash
cp .env.example .env
```

Edite o `.env` e configure:

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

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# APIs (configure suas chaves)
FRENET_API_TOKEN=
FRENET_CEP_ORIGEM=69921248
ABACATEPAY_API_KEY=

STRIPE_KEY=
STRIPE_SECRET=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 2. Construir e iniciar

```bash
# Construir imagens
docker-compose build

# Iniciar containers
docker-compose up -d

# Ver logs
docker-compose logs -f
```

### 3. Configurar aplicação

```bash
# Entrar no container
docker-compose exec app bash

# Dentro do container:
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan optimize
```

### 4. Build de assets

```bash
# Opção 1: Usar container Node
docker-compose run --rm node npm install
docker-compose run --rm node npm run build

# Opção 2: Localmente (se tiver Node instalado)
npm install
npm run build
```

### 5. Acessar

- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## 🛠️ Comandos Úteis

### Gerenciar containers

```bash
# Iniciar
docker-compose up -d

# Parar
docker-compose stop

# Parar e remover
docker-compose down

# Parar, remover e limpar volumes
docker-compose down -v

# Rebuild sem cache
docker-compose build --no-cache
```

### Artisan Commands

```bash
# Via docker-compose
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan tinker

# Ou entrar no container
docker-compose exec app bash
php artisan migrate
```

### Logs

```bash
# Todos os serviços
docker-compose logs -f

# Serviço específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db

# Logs do Laravel
docker-compose exec app tail -f storage/logs/laravel.log
```

### Banco de dados

```bash
# Acessar MySQL
docker-compose exec db mysql -u root -proot shava_ecommerce

# Backup
docker-compose exec db mysqldump -u root -proot shava_ecommerce > backup.sql

# Restore
docker-compose exec -T db mysql -u root -proot shava_ecommerce < backup.sql
```

### Permissões

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## 🔧 Serviços

### app (PHP 8.2 + FPM)
- PHP 8.2 com extensões: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip, intl
- Composer instalado
- Porta interna: 9000

### nginx
- Servidor web Nginx Alpine
- Porta: 8000 → 80
- Proxy reverso para PHP-FPM

### db (MySQL 8.0)
- MySQL 8.0
- Porta: 3306
- Volume persistente: `db_data`
- Healthcheck configurado

### redis
- Redis 7 Alpine
- Porta: 6379
- Volume persistente: `redis_data`
- Healthcheck configurado

### node (build only)
- Node.js 18 Alpine
- Usado apenas para build de assets
- Profile: `build` (não inicia automaticamente)

## 📝 Variáveis de Ambiente Docker

O `docker-compose.yml` usa variáveis do `.env`:

- `DB_DATABASE` - Nome do banco (padrão: shava_ecommerce)
- `DB_PASSWORD` - Senha do MySQL (padrão: root)
- `DB_USERNAME` - Usuário do MySQL (padrão: root)
- `DB_PORT` - Porta do MySQL (padrão: 3306)
- `REDIS_PORT` - Porta do Redis (padrão: 6379)

## 🐛 Troubleshooting

### Container não inicia

```bash
# Ver logs de erro
docker-compose logs app

# Verificar status
docker-compose ps

# Rebuild completo
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Erro de conexão com banco

```bash
# Verificar se MySQL está rodando
docker-compose ps db

# Ver logs do MySQL
docker-compose logs db

# Verificar variáveis de ambiente
docker-compose exec app env | grep DB_
```

### Erro de permissões

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Limpar tudo e começar do zero

```bash
# Parar e remover tudo
docker-compose down -v

# Remover imagens
docker-compose rm -f
docker rmi shava_ecommerce_app

# Rebuild
docker-compose build --no-cache
docker-compose up -d

# Reconfigurar
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```

## 🚀 Deploy em Produção

Para produção, considere:

1. **Segurança**:
   - Usar senhas fortes no `.env`
   - Configurar SSL/TLS
   - Desabilitar `APP_DEBUG=false`
   - Usar secrets do Docker ou variáveis de ambiente seguras

2. **Performance**:
   - Usar `Dockerfile` otimizado (sem dev dependencies)
   - Configurar cache do OPcache
   - Usar CDN para assets estáticos
   - Configurar limites de recursos

3. **Monitoramento**:
   - Configurar healthchecks
   - Usar ferramentas de monitoramento (Prometheus, Grafana)
   - Configurar logs centralizados

4. **Backup**:
   - Backup automático do banco de dados
   - Backup de arquivos de storage
   - Estratégia de retenção de backups

## 📚 Recursos Adicionais

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Docker](https://laravel.com/docs/10.x)

