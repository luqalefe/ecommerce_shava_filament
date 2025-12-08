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

    public function selectCategory($slug)
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function clearCategory()
    {
        $this->category = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('is_active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            // Buscar a categoria selecionada
            $selectedCategory = Category::where('slug', $this->category)->first();
            
            if ($selectedCategory) {
                // Se for uma categoria pai, incluir produtos das subcategorias também
                $categoryIds = [$selectedCategory->id];
                
                if ($selectedCategory->children->isNotEmpty()) {
                    $categoryIds = array_merge($categoryIds, $selectedCategory->children->pluck('id')->toArray());
                }
                
                $query->whereIn('category_id', $categoryIds);
            }
        }

        $products = $query->latest()->paginate(12);

        // Buscar categorias com subcategorias para o sidebar
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }]);
            }])
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        // Encontrar a categoria selecionada atual
        $selectedCategoryModel = $this->category 
            ? Category::where('slug', $this->category)->first() 
            : null;

        return view('livewire.product-list', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryModel' => $selectedCategoryModel,
        ]);
    }
}