FROM php:8.2-fpm

# Definir argumentos de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar apenas arquivos de configuração primeiro (para cache do Docker)
COPY composer.json composer.lock ./

# Instalar dependências PHP (cache layer)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar resto dos arquivos
COPY . /var/www/html

# Executar scripts do Composer
RUN composer dump-autoload --optimize

# Definir permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expor porta
EXPOSE 9000

CMD ["php-fpm"]

