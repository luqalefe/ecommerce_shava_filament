#!/bin/bash

# Script de Deploy para Hostinger
# Uso: ./deploy-hostinger.sh

echo "🚀 Iniciando preparação para deploy na Hostinger..."

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erro: Execute este script na raiz do projeto Laravel${NC}"
    exit 1
fi

# 1. Build dos assets
echo -e "${YELLOW}📦 Compilando assets (Vite)...${NC}"
npm run build

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao compilar assets${NC}"
    exit 1
fi

# 2. Instalar dependências de produção
echo -e "${YELLOW}📦 Instalando dependências de produção...${NC}"
composer install --optimize-autoloader --no-dev

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao instalar dependências${NC}"
    exit 1
fi

# 3. Limpar caches
echo -e "${YELLOW}🧹 Limpando caches...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Criar arquivo ZIP para upload
echo -e "${YELLOW}📦 Criando arquivo ZIP para upload...${NC}"

# Criar diretório temporário
TEMP_DIR="deploy-temp"
mkdir -p $TEMP_DIR

# Copiar arquivos necessários (excluindo desnecessários)
rsync -av \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='*.md' \
    --exclude='.env*' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='deploy-temp' \
    --exclude='deploy-hostinger.sh' \
    --exclude='GUIA_DEPLOY_HOSTINGER.md' \
    . $TEMP_DIR/

# Criar ZIP
ZIP_NAME="deploy-$(date +%Y%m%d-%H%M%S).zip"
cd $TEMP_DIR
zip -r ../$ZIP_NAME . -q
cd ..

# Limpar diretório temporário
rm -rf $TEMP_DIR

echo -e "${GREEN}✅ Arquivo ZIP criado: $ZIP_NAME${NC}"
echo -e "${GREEN}✅ Tamanho: $(du -h $ZIP_NAME | cut -f1)${NC}"
echo ""
echo -e "${YELLOW}📤 Próximos passos:${NC}"
echo "1. Faça upload do arquivo $ZIP_NAME para o File Manager da Hostinger"
echo "2. Extraia o arquivo ZIP no diretório public_html"
echo "3. Siga as instruções do GUIA_DEPLOY_HOSTINGER.md"
echo ""
echo -e "${GREEN}✨ Preparação concluída!${NC}"

