<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class CategorySection extends Component
{
    // Props passed from parent
    public string $categorySlug;
    public string $title;
    public bool $showSidebar = false;
    public int $limit = 8;

    // Internal state
    public string $selectedSubcategory = '';

    public function mount(string $categorySlug, string $title, bool $showSidebar = false, int $limit = 8)
    {
        $this->categorySlug = $categorySlug;
        $this->title = $title;
        $this->showSidebar = $showSidebar;
        $this->limit = $limit;
    }

    public function selectSubcategory(string $slug)
    {
        $this->selectedSubcategory = $slug;
    }

    public function clearFilter()
    {
        $this->selectedSubcategory = '';
    }

    public function render()
    {
        // Get the main category
        $category = Category::where('slug', $this->categorySlug)
            ->with(['children' => function ($query) {
                $query->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }]);
            }])
            ->first();

        if (!$category) {
            return view('livewire.category-section', [
                'products' => collect(),
                'category' => null,
                'subcategories' => collect(),
            ]);
        }

        // Build query for products
        $query = Product::where('is_active', true);

        if ($this->selectedSubcategory) {
            // Filter by selected subcategory
            $subcategory = Category::where('slug', $this->selectedSubcategory)->first();
            if ($subcategory) {
                $query->where('category_id', $subcategory->id);
            }
        } else {
            // Get all products from category and its children
            $categoryIds = [$category->id];
            if ($category->children->isNotEmpty()) {
                $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
            }
            $query->whereIn('category_id', $categoryIds);
        }

        $products = $query->latest()->take($this->limit)->get();

        return view('livewire.category-section', [
            'products' => $products,
            'category' => $category,
            'subcategories' => $category->children ?? collect(),
        ]);
    }
}
