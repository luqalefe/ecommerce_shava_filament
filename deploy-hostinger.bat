@echo off
REM Script de Deploy para Hostinger (Windows)
REM Uso: deploy-hostinger.bat

echo 🚀 Iniciando preparação para deploy na Hostinger...
echo.

REM Verificar se está no diretório correto
if not exist "artisan" (
    echo ❌ Erro: Execute este script na raiz do projeto Laravel
    pause
    exit /b 1
)

REM 1. Build dos assets
echo 📦 Compilando assets (Vite)...
call npm run build
if errorlevel 1 (
    echo ❌ Erro ao compilar assets
    pause
    exit /b 1
)

REM 2. Instalar dependências de produção
echo 📦 Instalando dependências de produção...
call composer install --optimize-autoloader --no-dev
if errorlevel 1 (
    echo ❌ Erro ao instalar dependências
    pause
    exit /b 1
)

REM 3. Limpar caches
echo 🧹 Limpando caches...
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
call php artisan cache:clear

REM 4. Criar arquivo ZIP para upload
echo 📦 Criando arquivo ZIP para upload...

REM Criar nome do arquivo com data/hora
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set datetime=%datetime:~0,8%-%datetime:~8,6%
set ZIP_NAME=deploy-%datetime%.zip

REM Usar PowerShell para criar ZIP (mais confiável)
powershell -Command "Get-ChildItem -Path . -Exclude '.git','node_modules','tests','*.md','.env*','storage\logs\*','storage\framework\cache\*','storage\framework\sessions\*','storage\framework\views\*','deploy-temp','deploy-hostinger.*','GUIA_DEPLOY_HOSTINGER.md' | Compress-Archive -DestinationPath %ZIP_NAME% -Force"

if errorlevel 1 (
    echo ❌ Erro ao criar arquivo ZIP
    echo 💡 Dica: Use o 7-Zip ou WinRAR para criar o ZIP manualmente
    pause
    exit /b 1
)

echo.
echo ✅ Arquivo ZIP criado: %ZIP_NAME%
echo.
echo 📤 Próximos passos:
echo 1. Faça upload do arquivo %ZIP_NAME% para o File Manager da Hostinger
echo 2. Extraia o arquivo ZIP no diretório public_html
echo 3. Siga as instruções do GUIA_DEPLOY_HOSTINGER.md
echo.
echo ✨ Preparação concluída!
echo.
pause



