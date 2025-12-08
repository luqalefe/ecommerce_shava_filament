<?php

/**
 * Script para adicionar imagens placeholder a todos os produtos
 * 
 * COMO USAR:
 * php add_images_to_products.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;

echo "=== Adicionando Imagens aos Produtos ===\n\n";

// Buscar todos os produtos
$products = Product::all();
echo "Total de produtos encontrados: " . $products->count() . "\n\n";

// Caminho das imagens (relativo ao storage/app/public)
$image1Path = 'products/product_image_1.png';
$image2Path = 'products/product_image_2.png';

$contadorCriados = 0;
$contadorPulados = 0;

foreach ($products as $product) {
    // Verificar se o produto já tem imagens
    if ($product->images()->count() > 0) {
        echo "Produto #{$product->id} ({$product->name}) já tem imagens - pulando\n";
        $contadorPulados++;
        continue;
    }
    
    // Criar imagem 1 (principal)
    ProductImage::create([
        'product_id' => $product->id,
        'path' => $image1Path,
        'is_main' => true,
    ]);
    
    // Criar imagem 2 (secundária)
    ProductImage::create([
        'product_id' => $product->id,
        'path' => $image2Path,
        'is_main' => false,
    ]);
    
    echo "✅ Produto #{$product->id} ({$product->name}) - 2 imagens adicionadas\n";
    $contadorCriados++;
}

echo "\n=== RESUMO ===\n";
echo "Produtos atualizados: {$contadorCriados}\n";
echo "Produtos pulados (já tinham imagens): {$contadorPulados}\n";
echo "\n✅ Concluído!\n";
