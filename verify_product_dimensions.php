&lt;?php

// Script para verificar se os campos de dimensões foram adicionados corretamente
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "Verificando campos de dimensões na tabela products...\n\n";

$product = Product::first();

if ($product) {
    echo "✓ Produto encontrado: {$product->name}\n";
    echo "  - ID: {$product->id}\n";
    echo "  - Peso: {$product->weight} kg\n";
    echo "  - Altura: {$product->height} cm\n";
    echo "  - Largura: {$product->width} cm\n";
    echo "  - Comprimento: {$product->length} cm\n";
    echo "\n✓ Campos adicionados com sucesso!\n";
} else {
    echo "⚠ Nenhum produto encontrado no banco de dados.\n";
    echo "  Criando um produto de teste...\n\n";
    
    $testProduct = Product::create([
        'category_id' => 1,
        'name' => 'Produto Teste - Dimensões',
        'slug' => 'produto-teste-dimensoes',
        'sku' => 'TEST-001',
        'price' => 99.99,
        'weight' => 0.5,
        'height' => 10,
        'width' => 10,
        'length' => 10,
    ]);
    
    echo "✓ Produto de teste criado:\n";
    echo "  - Nome: {$testProduct->name}\n";
    echo "  - Peso: {$testProduct->weight} kg\n";
    echo "  - Altura: {$testProduct->height} cm\n";
    echo "  - Largura: {$testProduct->width} cm\n";
    echo "  - Comprimento: {$testProduct->length} cm\n";
}
