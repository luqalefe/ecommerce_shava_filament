<?php

namespace App\Http\Controllers;

use App\Models\Product; // Importe o Model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para pegar o usuário logado
use Darryldecode\Cart\Facades\CartFacade as Cart; // Para usar o carrinho
use Illuminate\Support\Facades\Log; // Para registrar erros

class CheckoutController extends Controller
{
    /**
     * Exibe a página de checkout.
     * Rota: GET /checkout
     * Nome: checkout.index
     */
    public function index()
    {
        $cartItems = Cart::getContent();
        $subTotal = Cart::getSubTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio para finalizar a compra.');
        }

        $user = Auth::user();
        $addresses = $user->enderecos()->get(); // Assume que o relacionamento é 'enderecos' no Model User

        return view('checkout.index', compact('cartItems', 'subTotal', 'addresses', 'user'));
    }

    /**
     * Adiciona um produto específico ao carrinho (limpando-o antes)
     * e redireciona imediatamente para o checkout.
     * Rota: POST /comprar-agora/{product}
     * Nome: checkout.buyNow
     */
    public function buyNow(Request $request, Product $product)
    {
        // Valida a quantidade recebida do formulário
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try {
            // 1. Limpa o carrinho atual antes de adicionar o item 'Comprar Agora'
            Cart::clear();

            // 2. Adiciona o produto selecionado
            Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'quantity' => (int)$request->input('quantity'),
                'attributes' => [
                    'image' => $product->images->isNotEmpty() ? $product->images->first()->path : null,
                    'slug' => $product->slug,
                ],
                'associatedModel' => $product
            ]);

            // 3. Redireciona o usuário para a página de checkout
            return redirect()->route('checkout.index');

        } catch (\Exception $e) {
            // Em caso de erro, registra no log e volta para a página do produto
            Log::error('Erro no Comprar Agora (CheckoutController@buyNow): ' . $e->getMessage());
            return back()->with('error', 'Não foi possível iniciar a compra direta. Por favor, tente adicionar ao carrinho.');
        }
    }

    // O método store() para processar o pedido final virá aqui depois
    // public function store(Request $request) { ... }
}