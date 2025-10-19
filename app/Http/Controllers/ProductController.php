<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Exibe a página de listagem de todos os produtos ativos.
     * Corresponde à rota GET /loja
     */
    public function index()
    {
        // Busca no banco de dados todos os produtos que estão ativos,
        // ordenados pelos mais recentes e com paginação.
        $products = Product::where('is_active', true)
                           ->latest()
                           ->paginate(12); // Mostra 12 produtos por página

        // Retorna a view 'products.index' e passa a variável 'products' para ela
        return view('products.index', compact('products'));
    }

    /**
     * Exibe a página de detalhes de um único produto.
     * Corresponde à rota GET /produto/{product:slug}
     */
    public function show(Product $product)
    {
        // Verifica se o produto está ativo. Se não estiver, retorna um erro 404 (Não Encontrado).
        // Isso impede que clientes acessem URLs de produtos inativos.
        if (!$product->is_active) {
            abort(404);
        }
        
        // Retorna a view 'products.show' e passa a variável 'product' para ela
        return view('products.show', compact('product'));
    }
}