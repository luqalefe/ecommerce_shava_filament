<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Str;

// Verificar se as imagens existem no storage
$image1Path = 'products/shava_haux_1.png';
$image2Path = 'products/shava_haux_2.png';

// Função para criar produto com imagens
function createProduct($categoryId, $name, $price, $weight, $height, $width, $length, $quantity, $shortDesc, $image1, $image2) {
    $slug = Str::slug($name);
    
    // Garantir slug único
    $originalSlug = $slug;
    $counter = 1;
    while (Product::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    // Gerar SKU único
    $sku = 'SH-' . strtoupper(Str::random(6));
    while (Product::where('sku', $sku)->exists()) {
        $sku = 'SH-' . strtoupper(Str::random(6));
    }
    
    $product = Product::create([
        'category_id' => $categoryId,
        'name' => $name,
        'slug' => $slug,
        'sku' => $sku,
        'short_description' => $shortDesc,
        'long_description' => $shortDesc . ' - Produto de alta qualidade da marca Shava Haux.',
        'price' => $price,
        'sale_price' => null,
        'is_active' => true,
        'quantity' => $quantity,
        'weight' => $weight,
        'height' => $height,
        'width' => $width,
        'length' => $length,
    ]);
    
    // Criar imagens para o produto
    ProductImage::create([
        'product_id' => $product->id,
        'path' => $image1,
        'is_main' => true,
    ]);
    
    ProductImage::create([
        'product_id' => $product->id,
        'path' => $image2,
        'is_main' => false,
    ]);
    
    return $product;
}

echo "=== Iniciando criação de produtos ===\n\n";

// ==============================
// CATEGORIA: Piteiras (ID: 2)
// ==============================
echo "Criando produtos para PITEIRAS...\n";
$piteirasProducts = [
    ['Piteira de Vidro Premium Shava Haux', 29.90, 0.05, 2, 8, 2, 50, 'Piteira de vidro artesanal de alta qualidade.'],
    ['Piteira de Madeira Natural Shava Haux', 19.90, 0.03, 2, 6, 2, 80, 'Piteira de madeira 100% natural e sustentável.'],
    ['Piteira de Pedra Sabão Shava Haux', 39.90, 0.08, 3, 8, 3, 40, 'Piteira artesanal em pedra sabão mineira.'],
    ['Piteira de Bambu Ecológica Shava Haux', 14.90, 0.02, 2, 5, 2, 100, 'Piteira ecológica feita com bambu renovável.'],
    ['Piteira de Cerâmica Artesanal Shava Haux', 34.90, 0.06, 3, 7, 3, 60, 'Piteira de cerâmica feita à mão por artesãos.'],
];
foreach ($piteirasProducts as $p) {
    $product = createProduct(2, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: Sedas (ID: 3)
// ==============================
echo "\nCriando produtos para SEDAS...\n";
$sedasProducts = [
    ['Seda King Size Slim Shava Haux', 12.90, 0.02, 1, 11, 4, 200, 'Seda ultrafina king size de alta qualidade.'],
    ['Seda 1 1/4 Shava Haux', 9.90, 0.02, 1, 8, 4, 250, 'Seda clássica tamanho 1 1/4.'],
    ['Seda Hemp Orgânica Shava Haux', 14.90, 0.02, 1, 11, 4, 150, 'Seda feita com cânhamo orgânico certificado.'],
    ['Seda Brown Natural Shava Haux', 11.90, 0.02, 1, 11, 4, 180, 'Seda não branqueada, 100% natural.'],
    ['Seda Slim + Piteiras Shava Haux', 16.90, 0.04, 1, 11, 5, 120, 'Kit completo com sedas slim e piteiras.'],
];
foreach ($sedasProducts as $p) {
    $product = createProduct(3, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: Rapé indígena (ID: 5)
// ==============================
echo "\nCriando produtos para RAPÉ INDÍGENA...\n";
$rapeProducts = [
    ['Rapé Cumaru Shava Haux', 49.90, 0.10, 5, 5, 5, 30, 'Rapé tradicional com semente de cumaru.'],
    ['Rapé Murici Shava Haux', 45.90, 0.10, 5, 5, 5, 35, 'Rapé preparado com casca de murici.'],
    ['Rapé Tsunu Shava Haux', 54.90, 0.10, 5, 5, 5, 25, 'Rapé feito com cinzas de tsunu.'],
    ['Rapé Paricá Shava Haux', 52.90, 0.10, 5, 5, 5, 28, 'Rapé tradicional com paricá amazônico.'],
    ['Rapé Caneleiro Shava Haux', 47.90, 0.10, 5, 5, 5, 32, 'Rapé com notas aromáticas de caneleiro.'],
];
foreach ($rapeProducts as $p) {
    $product = createProduct(5, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: Artesanatos indígenas (ID: 6)
// ==============================
echo "\nCriando produtos para ARTESANATOS INDÍGENAS...\n";
$artesanatosProducts = [
    ['Kuripe de Madeira Entalhada Shava Haux', 89.90, 0.15, 5, 15, 5, 20, 'Kuripe artesanal entalhado à mão.'],
    ['Tepi de Bambu Cerimonial Shava Haux', 129.90, 0.20, 5, 25, 5, 15, 'Tepi tradicional para cerimônias.'],
    ['Colar de Sementes Indígena Shava Haux', 69.90, 0.08, 2, 20, 2, 25, 'Colar artesanal com sementes da floresta.'],
    ['Pulseira de Miçangas Indígena Shava Haux', 39.90, 0.03, 2, 8, 2, 40, 'Pulseira com padrões tradicionais indígenas.'],
    ['Bolsa de Palha Indígena Shava Haux', 149.90, 0.30, 25, 30, 10, 12, 'Bolsa trançada à mão com palha natural.'],
];
foreach ($artesanatosProducts as $p) {
    $product = createProduct(6, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: Roupas (ID: 8)
// ==============================
echo "\nCriando produtos para ROUPAS...\n";
$roupasProducts = [
    ['Camiseta Hemp Básica Shava Haux', 89.90, 0.25, 2, 30, 25, 50, 'Camiseta básica feita com fibra de cânhamo.'],
    ['Calça Hemp Masculina Shava Haux', 189.90, 0.45, 3, 40, 30, 30, 'Calça masculina confortável em hemp.'],
    ['Vestido Hemp Feminino Shava Haux', 199.90, 0.35, 3, 35, 28, 25, 'Vestido leve e elegante em tecido hemp.'],
    ['Shorts Hemp Unissex Shava Haux', 99.90, 0.20, 2, 25, 20, 40, 'Shorts casual unissex em hemp sustentável.'],
    ['Jaqueta Hemp Shava Haux', 299.90, 0.60, 5, 45, 35, 20, 'Jaqueta premium feita com cânhamo orgânico.'],
];
foreach ($roupasProducts as $p) {
    $product = createProduct(8, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: Acessórios (ID: 9)
// ==============================
echo "\nCriando produtos para ACESSÓRIOS...\n";
$acessoriosProducts = [
    ['Bone Hemp Shava Haux', 59.90, 0.12, 8, 20, 15, 60, 'Boné estiloso feito com tecido hemp.'],
    ['Mochila Hemp Shava Haux', 199.90, 0.50, 40, 35, 15, 25, 'Mochila resistente e ecológica em hemp.'],
    ['Carteira Hemp Shava Haux', 69.90, 0.08, 2, 12, 10, 80, 'Carteira compacta em tecido hemp.'],
    ['Cinto Hemp Natural Shava Haux', 49.90, 0.10, 4, 100, 4, 50, 'Cinto durável feito com fibra de cânhamo.'],
    ['Pochete Hemp Shava Haux', 79.90, 0.15, 8, 20, 8, 45, 'Pochete prática e sustentável em hemp.'],
];
foreach ($acessoriosProducts as $p) {
    $product = createProduct(9, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

// ==============================
// CATEGORIA: HEADSHOP (ID: 10)
// ==============================
echo "\nCriando produtos para HEADSHOP...\n";
$headshopProducts = [
    ['Dichavador Metálico Premium Shava Haux', 79.90, 0.20, 3, 6, 6, 40, 'Dichavador de alta qualidade em metal.'],
    ['Bandeja de Rolagem Shava Haux', 49.90, 0.15, 2, 25, 15, 50, 'Bandeja para rolagem com design exclusivo.'],
    ['Isqueiro Clipper Shava Haux', 14.90, 0.04, 8, 2, 2, 100, 'Isqueiro Clipper recarregável.'],
    ['Pote de Silicone Shava Haux', 24.90, 0.05, 3, 4, 4, 80, 'Pote de silicone para armazenamento.'],
    ['Kit Completo Fumante Shava Haux', 149.90, 0.40, 8, 30, 20, 25, 'Kit completo com todos os acessórios essenciais.'],
];
foreach ($headshopProducts as $p) {
    $product = createProduct(10, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $image1Path, $image2Path);
    echo "  ✓ Criado: {$product->name}\n";
}

echo "\n=== Criação de produtos concluída! ===\n";
echo "Total de produtos criados: 35 (5 por categoria)\n";
