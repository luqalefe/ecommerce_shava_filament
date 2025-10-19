<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
// Importa a facade principal do pacote do carrinho
use Darryldecode\Cart\Facades\CartFacade as Cart;
// Importa a facade de Log para registrar possíveis erros
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Adiciona um produto ao carrinho.
     * Rota: POST /carrinho/adicionar/{product}
     * Nome da Rota: cart.store
     */
    public function store(Request $request, Product $product)
    {
        // Valida se a quantidade enviada é um número e pelo menos 1
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try {
            // Adiciona o item ao carrinho usando o pacote darryldecode/cart
            Cart::add([
                'id' => $product->id, // ID único do item no carrinho (geralmente o ID do produto)
                'name' => $product->name, // Nome do produto
                'price' => $product->sale_price ?? $product->price, // Preço (usa o promocional se houver, senão o normal)
                'quantity' => (int)$request->input('quantity'), // Quantidade vinda do formulário
                'attributes' => [ // Atributos customizados que queremos guardar junto com o item
                    'image' => $product->images->isNotEmpty() ? $product->images->first()->path : null, // Caminho da imagem principal
                    'slug' => $product->slug, // Slug para gerar o link de volta ao produto na view do carrinho
                ],
                'associatedModel' => $product // Opcional: Associa o Model Product original ao item do carrinho
            ]);

            // Retorna para a página anterior com uma mensagem de sucesso
            return back()->with('success', $product->name . ' foi adicionado ao carrinho!');

        } catch (\Exception $e) {
            // Se qualquer erro ocorrer durante a adição
            Log::error('Erro ao adicionar ao carrinho: ' . $e->getMessage()); // Registra o erro detalhado no log do Laravel
            // Retorna para a página anterior com uma mensagem de erro genérica para o usuário
            return back()->with('error', 'Erro ao adicionar o produto ao carrinho. Por favor, tente novamente.');
        }
    }

    /**
     * Exibe a página do carrinho de compras.
     * Rota: GET /carrinho
     * Nome da Rota: cart.index
     */
    public function index()
    {
        // Pega todos os itens que estão atualmente na sessão do carrinho
        $cartItems = Cart::getContent();

        // Calcula o subtotal (soma dos preços x quantidades)
        $subTotal = Cart::getSubTotal();

        // Retorna a view 'cart.index' passando os itens e o subtotal para serem exibidos
        return view('cart.index', compact('cartItems', 'subTotal'));
    }

    /**
     * Atualiza a quantidade de um item específico no carrinho.
     * Rota: PATCH /carrinho/atualizar/{item}
     * Nome da Rota: cart.update
     */
    public function update(Request $request, $itemId) // Recebe o ID do item pela URL
    {
        // Valida se a nova quantidade enviada é um número e pelo menos 1
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try {
            // Usa o método update() do pacote para alterar a quantidade do item
            Cart::update($itemId, [
                'quantity' => [
                    'relative' => false, // false = define a quantidade exata (valor absoluto)
                    'value' => (int)$request->input('quantity') // A nova quantidade
                ],
            ]);

            // Retorna para a página anterior (a do carrinho) com mensagem de sucesso
            return back()->with('success', 'Quantidade do item atualizada!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar carrinho: ' . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar a quantidade. Tente novamente.');
        }
    }

    /**
     * Remove um item específico do carrinho.
     * Rota: DELETE /carrinho/remover/{item}
     * Nome da Rota: cart.destroy
     */
    public function destroy($itemId) // Recebe o ID do item pela URL
    {
        try {
            // Usa o método remove() do pacote para tirar o item do carrinho
            Cart::remove($itemId);

            // Retorna para a página anterior (a do carrinho) com mensagem de sucesso
            return back()->with('success', 'Produto removido do carrinho!');

        } catch (\Exception $e) {
            Log::error('Erro ao remover do carrinho: ' . $e->getMessage());
            return back()->with('error', 'Erro ao remover o produto. Tente novamente.');
        }
    }

    // --- Métodos Futuros Possíveis ---
    // public function clear() { // Para limpar o carrinho inteiro }
}