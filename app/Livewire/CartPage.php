<?php

namespace App\Livewire;

use Livewire\Component;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CartPage extends Component
{
    public function render()
    {
        $cartItems = Cart::getContent();
        $subTotal = Cart::getSubTotal();
        
        return view('livewire.cart-page', [
            'cartItems' => $cartItems,
            'subTotal' => $subTotal,
        ]);
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        // Força a atualização do componente quando o carrinho é atualizado
        $this->render();
    }

    public function increment($itemId)
    {
        try {
            $item = Cart::get($itemId);
            if ($item) {
                Cart::update($itemId, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $item->quantity + 1
                    ]
                ]);
                
                $this->dispatch('cart-updated');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar quantidade do produto.');
        }
    }

    public function decrement($itemId)
    {
        try {
            $item = Cart::get($itemId);
            if ($item && $item->quantity > 1) {
                Cart::update($itemId, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $item->quantity - 1
                    ]
                ]);
                $this->dispatch('cart-updated');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao atualizar quantidade do produto.');
        }
    }

    public function remove($itemId)
    {
        try {
            Cart::remove($itemId);
            $this->dispatch('cart-updated');
            session()->flash('success', 'Produto removido do carrinho.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao remover produto do carrinho.');
        }
    }
}