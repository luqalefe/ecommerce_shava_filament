<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class HomePage extends Component
{
    public function render()
    {
        // Fetch 8 most recent active products (same as HomeController)
        // Note: The view actually handles pagination/lists, but controller passed 8.
        // The view looks like it iterates $products->take(12) and also all products.
        // Let's pass all active products to be safe, or enough for the carousel (12).
        // The original controller passed `take(8)->get()`.
        // BUT the view uses `$products->take(12)` in one place and `$products` (all?) in another section?
        // Original controller: $products = Product::where('is_active', true)->latest()->take(8)->get();
        // View line 116: @foreach($products->take(12)... 
        // View line 266: @forelse ($products as $product)...
        // If controller only sent 8, take(12) takes 8. 
        // I will fetch 12 to populate the carousel better, or even more if needed.
        // However, fetching ALL products might be heavy if there are thousands. 
        // Let's stick to a reasonable limit, say 24, to populate the "products-section" too.
        
        $products = Product::where('is_active', true)
                           ->latest()
                           ->take(24) 
                           ->get();

        // Fetch parent categories with children for the "Navegue por Categoria" section
        $globalCategories = Category::whereNull('parent_id')
                                    ->with('children')
                                    ->get();

        return view('livewire.home-page', [
            'products' => $products,
            'globalCategories' => $globalCategories,
        ])->extends('layouts.main')->section('content'); 
        // Explicitly extending layouts.main to match original behavior
    }
}
