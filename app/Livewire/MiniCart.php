<?php

namespace App\Livewire;

use Livewire\Component;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Livewire\Attributes\On;

class MiniCart extends Component
{
    #[On('cart-updated')]
    public function render()
    {
        $cartCount = Cart::getContent()->count();
        $cartTotal = Cart::getSubTotal();
        
        return view('livewire.mini-cart', [
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
        ]);
    }
}