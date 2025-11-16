<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Livewire\Attributes\On;

class AddToCart extends Component
{
    public $productId;
    public $quantity = 1;

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function add()
    {
        $product = Product::findOrFail($this->productId);
        
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->price,
            'quantity' => (int) $this->quantity,
            'attributes' => [
                'image' => $product->images->isNotEmpty() ? $product->images->first()->path : null,
                'slug' => $product->slug,
            ],
            'associatedModel' => $product
        ]);

        $this->dispatch('cart-updated');
        
        session()->flash('message', $product->name . ' foi adicionado ao carrinho!');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}