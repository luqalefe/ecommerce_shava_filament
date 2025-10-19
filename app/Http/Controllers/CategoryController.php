<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product; // Importe o Model Product
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Mostra os produtos de uma categoria e suas subcategorias.
     */
    public function show(Category $category)
    {
        // 1. Pega o ID da categoria atual (pai)
        $categoryIds = [$category->id];

        // 2. Pega os IDs de todas as subcategorias
        $childCategoryIds = $category->children()->pluck('id')->toArray();

        // 3. Junta todos os IDs em um único array
        $allCategoryIds = array_merge($categoryIds, $childCategoryIds);

        // 4. Busca todos os produtos ativos que pertencem a qualquer um desses IDs
        $products = Product::whereIn('category_id', $allCategoryIds)
                           ->where('is_active', true)
                           ->latest()
                           ->paginate(12);

        // Retorna a view e passa os dados
        return view('categories.show', compact('category', 'products'));
    }
}