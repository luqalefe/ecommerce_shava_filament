<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProductList extends Component
{
    use WithPagination;

    #[Url(as: 'busca', keep: true)]
    public $search = '';

    #[Url(as: 'categoria', keep: true)]
    public $category = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('is_active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        $products = $query->latest()->paginate(12);

        return view('livewire.product-list', [
            'products' => $products,
        ]);
    }
}