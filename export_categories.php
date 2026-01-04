<?php

use App\Models\Category;
use Illuminate\Support\Facades\File;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = Category::with('children')->whereNull('parent_id')->get();

$output = "<?php\n\n";
$output .= "use App\Models\Category;\n\n";
$output .= "echo \"Iniciando importacao de categorias...\\n\";\n\n";

foreach ($categories as $category) {
    // Cria a categoria pai
    $output .= "\$parent = Category::updateOrCreate(\n";
    $output .= "    ['slug' => '" . $category->slug . "'],\n";
    $output .= "    ['name' => '" . addslashes($category->name) . "', 'parent_id' => null]\n";
    $output .= ");\n";
    $output .= "echo \"Categoria Pai Criada/Atualizada: " . $category->name . "\\n\";\n\n";

    // Cria os filhos
    foreach ($category->children as $child) {
        $output .= "Category::updateOrCreate(\n";
        $output .= "    ['slug' => '" . $child->slug . "'],\n";
        $output .= "    ['name' => '" . addslashes($child->name) . "', 'parent_id' => \$parent->id]\n";
        $output .= ");\n";
        $output .= "echo \"  - Subcategoria Criada: " . $child->name . "\\n\";\n";
    }
    $output .= "\n";
}

$output .= "echo \"Importacao concluida com sucesso!\\n\";\n";

// Salva o arquivo
file_put_contents('import_categories.php', $output);

echo "Arquivo 'import_categories.php' gerado com sucesso! Agora envie este arquivo para a Hostinger e execute-o.\n";
