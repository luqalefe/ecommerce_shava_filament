<div class="category-section">
    <div class="container mx-auto px-4">
        {{-- Section Header --}}
        <div class="category-section-header">
            <h2 class="category-section-title">{{ $title }}</h2>
            @if($category)
                <a href="{{ route('category.show', $category->slug) }}" class="category-section-link">Ver todos →</a>
            @endif
        </div>

        @if($category)
            <div class="category-section-layout {{ $showSidebar ? 'with-sidebar' : '' }}">
                {{-- Sidebar (optional) --}}
                @if($showSidebar && $subcategories->isNotEmpty())
                    <aside class="category-section-sidebar">
                        <div class="sidebar-card">
                            <h3 class="sidebar-title">Filtrar por</h3>
                            
                            <ul class="filter-list">
                                <li>
                                    <button 
                                        wire:click="clearFilter"
                                        class="filter-link {{ empty($selectedSubcategory) ? 'active' : '' }}"
                                    >
                                        <span>Todos</span>
                                    </button>
                                </li>
                                @foreach($subcategories as $subcat)
                                    <li>
                                        <button 
                                            wire:click="selectSubcategory('{{ $subcat->slug }}')"
                                            class="filter-link {{ $selectedSubcategory === $subcat->slug ? 'active' : '' }}"
                                        >
                                            <span>{{ $subcat->name }}</span>
                                            @if($subcat->products_count > 0)
                                                <span class="filter-count">({{ $subcat->products_count }})</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            @if($selectedSubcategory)
                                <button wire:click="clearFilter" class="clear-filter-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Limpar Filtro
                                </button>
                            @endif
                        </div>
                    </aside>
                @endif

                {{-- Products Grid --}}
                <div class="category-section-main">
                    @if($products->isNotEmpty())
                        <div class="products-grid">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p>Nenhum produto encontrado nesta categoria.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">Categoria não encontrada.</p>
        @endif
    </div>

    <style>
        /* Category Section Styles */
        .category-section { padding: 3rem 0; }
        
        .category-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .category-section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 600;
            color: #44403C;
        }
        
        .category-section-link {
            color: #1C4532;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .category-section-link:hover { color: #15372A; text-decoration: underline; }
        
        /* Layout with Sidebar */
        .category-section-layout { display: flex; flex-direction: column; gap: 2rem; }
        .category-section-layout.with-sidebar { }
        
        @media (min-width: 1024px) {
            .category-section-layout.with-sidebar { flex-direction: row; gap: 3rem; }
        }
        
        .category-section-sidebar { width: 100%; }
        @media (min-width: 1024px) {
            .category-section-sidebar { width: 240px; flex-shrink: 0; }
        }
        
        .category-section-main { flex: 1; min-width: 0; }
        
        /* Sidebar Card */
        .sidebar-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }
        
        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #44403C;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #E7E5E4;
        }
        
        .filter-list { list-style: none; padding: 0; margin: 0; }
        
        .filter-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 6px;
            color: #44403C;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
            text-align: left;
        }
        
        .filter-link:hover { background: #F5F5F4; color: #1C4532; }
        .filter-link.active { background: #1C4532; color: white; }
        .filter-link.active:hover { background: #15372A; }
        
        .filter-count {
            font-size: 0.75rem;
            color: #A8A29E;
        }
        .filter-link.active .filter-count { color: rgba(255,255,255,0.8); }
        
        .clear-filter-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.625rem;
            margin-top: 1rem;
            border-radius: 6px;
            color: #78716C;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px dashed #D6D3D1;
            background: none;
        }
        .clear-filter-btn:hover { background: #F5F5F4; color: #44403C; border-color: #A8A29E; }
        .clear-filter-btn svg { width: 14px; height: 14px; }
        
        /* Products Grid */
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 1rem; 
        }
        @media (min-width: 576px) { .products-grid { gap: 1.5rem; } }
        @media (min-width: 768px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { 
            .category-section-layout.with-sidebar .products-grid { grid-template-columns: repeat(3, 1fr); }
            .category-section-layout:not(.with-sidebar) .products-grid { grid-template-columns: repeat(4, 1fr); }
        }
        
        .empty-state { 
            text-align: center; 
            padding: 3rem 2rem; 
            color: #78716C; 
        }
        .empty-state svg { width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.5; }
    </style>
</div>
