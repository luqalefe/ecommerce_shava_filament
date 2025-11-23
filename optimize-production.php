<?php
/**
 * Script de Otimização para Produção
 * 
 * Execute este arquivo UMA VEZ após o deploy na Hostinger
 * Acesse via navegador: https://seudominio.com.br/optimize-production.php
 * 
 * ⚠️ IMPORTANTE: Delete este arquivo após executar!
 */

// Verificar se está em produção
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    if (($env['APP_ENV'] ?? 'local') !== 'production') {
        die('❌ Este script só deve ser executado em produção!');
    }
}

// Carregar Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🚀 Otimizando Laravel para Produção</h1>";
echo "<pre>";

try {
    // Cache de configuração
    echo "📦 Criando cache de configuração...\n";
    Artisan::call('config:cache');
    echo "✅ Config cache criado\n\n";

    // Cache de rotas
    echo "📦 Criando cache de rotas...\n";
    Artisan::call('route:cache');
    echo "✅ Route cache criado\n\n";

    // Cache de views
    echo "📦 Criando cache de views...\n";
    Artisan::call('view:cache');
    echo "✅ View cache criado\n\n";

    // Otimização geral
    echo "📦 Executando otimização geral...\n";
    Artisan::call('optimize');
    echo "✅ Otimização concluída\n\n";

    // Verificar permissões
    echo "🔒 Verificando permissões...\n";
    $storagePath = __DIR__ . '/storage';
    $cachePath = __DIR__ . '/bootstrap/cache';
    
    if (is_writable($storagePath)) {
        echo "✅ Storage é gravável\n";
    } else {
        echo "⚠️ Storage NÃO é gravável - configure permissões 755\n";
    }
    
    if (is_writable($cachePath)) {
        echo "✅ Bootstrap/cache é gravável\n";
    } else {
        echo "⚠️ Bootstrap/cache NÃO é gravável - configure permissões 755\n";
    }
    
    echo "\n";
    
    // Verificar link simbólico de storage
    echo "🔗 Verificando link simbólico de storage...\n";
    $publicStorage = __DIR__ . '/public_html/storage';
    if (is_link($publicStorage) || file_exists($publicStorage)) {
        echo "✅ Link simbólico de storage existe\n";
    } else {
        echo "⚠️ Link simbólico de storage NÃO existe\n";
        echo "   Execute: php artisan storage:link\n";
    }
    
    echo "\n";
    echo "✅ <strong>Otimização concluída com sucesso!</strong>\n";
    echo "\n";
    echo "⚠️ <strong>IMPORTANTE: Delete este arquivo agora!</strong>\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";

