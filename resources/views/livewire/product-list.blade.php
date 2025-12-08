<div class="shop-page">
    <style>
        /* SHOP PAGE - Organic Light Theme with Sidebar */
        .shop-page { background: #FAFAF9; min-height: 60vh; }
        .shop-header { text-align: center; margin-bottom: 2rem; }
        .shop-title { font-family: 'Playfair Display', serif; font-size: clamp(1.8rem, 5vw, 2.5rem); font-weight: 500; color: #44403C; margin-bottom: 0.5rem; }
        .shop-subtitle { color: #78716C; font-size: 0.95rem; }
        
        /* Layout com Sidebar */
        .shop-layout { display: flex; flex-direction: column; gap: 2rem; }
        @media (min-width: 1024px) { 
            .shop-layout { flex-direction: row; gap: 3rem; }
        }
        
        /* Sidebar */
        .shop-sidebar { width: 100%; }
        @media (min-width: 1024px) { 
            .shop-sidebar { width: 280px; flex-shrink: 0; }
        }
        
        .sidebar-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            position: sticky;
            top: 100px;
        }
        
        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: #44403C;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #E7E5E4;
        }
        
        .category-list { list-style: none; padding: 0; margin: 0; }
        
        .category-item { margin-bottom: 0.25rem; }
        
        .category-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #44403C;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        
        .category-link:hover { background: #F5F5F4; color: #1C4532; }
        .category-link.active { background: #1C4532; color: white; }
        .category-link.active:hover { background: #15372A; }
        
        .category-count {
            font-size: 0.8rem;
            color: #A8A29E;
            background: #F5F5F4;
            padding: 2px 8px;
            border-radius: 12px;
        }
        .category-link.active .category-count { background: rgba(255,255,255,0.2); color: white; }
        
        /* Subcategorias */
        .subcategory-list {
            list-style: none;
            padding: 0 0 0 1rem;
            margin: 0.25rem 0 0.5rem 0;
            border-left: 2px solid #E7E5E4;
        }
        
        .subcategory-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            color: #78716C;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        
        .subcategory-link:hover { background: #F5F5F4; color: #1C4532; }
        .subcategory-link.active { color: #1C4532; font-weight: 600; background: rgba(28, 69, 50, 0.08); }
        
        .subcategory-count {
            font-size: 0.75rem;
            color: #A8A29E;
        }
        
        /* Clear Filter Button */
        .clear-filter-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            margin-top: 1rem;
            border-radius: 8px;
            color: #78716C;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px dashed #D6D3D1;
            background: none;
            width: 100%;
            justify-content: center;
        }
        .clear-filter-btn:hover { background: #F5F5F4; color: #44403C; border-color: #A8A29E; }
        .clear-filter-btn svg { width: 16px; height: 16px; }
        
        /* Mobile Toggle */
        .mobile-filter-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            background: white;
            border: 1px solid #E7E5E4;
            border-radius: 50px;
            color: #44403C;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
        }
        .mobile-filter-toggle:hover { background: #F5F5F4; }
        .mobile-filter-toggle svg { width: 20px; height: 20px; }
        @media (min-width: 1024px) { .mobile-filter-toggle { display: none; } }
        
        /* Mobile Sidebar Hidden */
        .sidebar-mobile-hidden { display: none; }
        .sidebar-mobile-visible { display: block; }
        @media (min-width: 1024px) { 
            .sidebar-mobile-hidden { display: block; }
        }
        
        /* Main Content */
        .shop-main { flex: 1; min-width: 0; }
        
        .search-box { max-width: 100%; margin: 0 0 1.5rem 0; position: relative; }
        @media (min-width: 1024px) { .search-box { max-width: 400px; } }
        .search-box input { width: 100%; padding: 0.875rem 1rem 0.875rem 3rem; border: 1px solid #E7E5E4; border-radius: 50px; font-size: 0.95rem; background: white; color: #44403C; transition: all 0.3s ease; }
        .search-box input::placeholder { color: #A8A29E; }
        .search-box input:focus { outline: none; border-color: #1C4532; box-shadow: 0 0 0 3px rgba(28, 69, 50, 0.1); }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #A8A29E; }
        
        /* Active Filter Badge */
        .active-filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #1C4532;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        .active-filter-badge button {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            color: white;
            transition: background 0.2s;
        }
        .active-filter-badge button:hover { background: rgba(255,255,255,0.3); }
        .active-filter-badge button svg { width: 12px; height: 12px; }
        
        /* Products Grid */
        .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        @media (min-width: 576px) { .products-grid { gap: 1.5rem; } }
        @media (min-width: 768px) { .products-grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; } }
        @media (min-width: 1024px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1280px) { .products-grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; } }
        
        /* Enhanced Product Card - Estilo Lançamentos */
        .product-card {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
        
        .product-image { position: relative; aspect-ratio: 1; overflow: hidden; background: #F5F5F4; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .product-card:hover .product-image img { transform: scale(1.05); }
        
        .product-info { padding: 1.25rem; text-align: left; flex: 1; }
        
        .product-category {
            display: inline-block;
            color: #78716C;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        
        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #44403C;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1C4532;
            margin: 0;
        }
        
        .product-installment {
            display: block;
            font-size: 0.8rem;
            color: #78716C;
            margin-top: 4px;
        }
        
        .product-action { padding: 0 1.25rem 1.25rem; }
        
        .product-btn {
            display: block;
            text-align: center;
            background: #1C4532;
            color: white;
            padding: 0.875rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .product-btn:hover { background: #15372A; color: white; }
        
        .pagination-wrapper { margin-top: 3rem; display: flex; justify-content: center; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: #78716C; }
        .empty-state svg { width: 64px; height: 64px; margin-bottom: 1rem; opacity: 0.5; }
        
        /* Results count */
        .results-count { color: #78716C; font-size: 0.9rem; margin-bottom: 1rem; }
    </style>

    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="shop-header">
            <h1 class="shop-title">Nossa Loja</h1>
            <p class="shop-subtitle">Explore nossa coleção completa de produtos</p>
        </div>
        
        {{-- Mobile Filter Toggle --}}
        <button 
            x-data="{ open: false }" 
            @click="$dispatch('toggle-sidebar')"
            class="mobile-filter-toggle"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 4h18M3 12h18M3 20h18"/>
            </svg>
            Filtrar por Categoria
        </button>

        <div class="shop-layout" x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
            {{-- Sidebar --}}
            <aside class="shop-sidebar" :class="sidebarOpen ? 'sidebar-mobile-visible' : 'sidebar-mobile-hidden'">
                <div class="sidebar-card">
                    <h2 class="sidebar-title">Categorias</h2>
                    
                    <ul class="category-list">
                        {{-- Todas as categorias --}}
                        <li class="category-item">
                            <button 
                                wire:click="clearCategory"
                                class="category-link {{ empty($category) ? 'active' : '' }}"
                            >
                                <span>Todos os Produtos</span>
                            </button>
                        </li>
                        
                        @foreach($categories as $cat)
                            <li class="category-item">
                                <button 
                                    wire:click="selectCategory('{{ $cat->slug }}')"
                                    class="category-link {{ $category === $cat->slug ? 'active' : '' }}"
                                >
                                    <span>{{ $cat->name }}</span>
                                    @if($cat->products_count > 0 || $cat->children->sum('products_count') > 0)
                                        <span class="category-count">
                                            {{ $cat->products_count + $cat->children->sum('products_count') }}
                                        </span>
                                    @endif
                                </button>
                                
                                {{-- Subcategorias --}}
                                @if($cat->children->isNotEmpty())
                                    <ul class="subcategory-list">
                                        @foreach($cat->children as $subcat)
                                            <li>
                                                <button 
                                                    wire:click="selectCategory('{{ $subcat->slug }}')"
                                                    class="subcategory-link {{ $category === $subcat->slug ? 'active' : '' }}"
                                                >
                                                    <span>{{ $subcat->name }}</span>
                                                    @if($subcat->products_count > 0)
                                                        <span class="subcategory-count">({{ $subcat->products_count }})</span>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($category)
                        <button wire:click="clearCategory" class="clear-filter-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpar Filtro
                        </button>
                    @endif
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="shop-main">
                {{-- Search Box --}}
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar produtos..." 
                    >
                </div>
                
                {{-- Active Filter Badge --}}
                @if($selectedCategoryModel)
                    <div class="active-filter-badge">
                        <span>{{ $selectedCategoryModel->name }}</span>
                        <button wire:click="clearCategory" title="Remover filtro">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif
                
                {{-- Results Count --}}
                <p class="results-count">
                    {{ $products->total() }} {{ $products->total() === 1 ? 'produto encontrado' : 'produtos encontrados' }}
                    @if($search)
                        para "{{ $search }}"
                    @endif
                </p>

                {{-- Products Grid --}}
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach($products as $product)
                            <div class="product-card">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <div class="product-image">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $product->name }}" loading="lazy">
                                        @else
                                            <img src="https://via.placeholder.com/400x400/f5f5f5/ccc?text=Sem+Imagem" alt="Imagem indisponível" loading="lazy">
                                        @endif
                                    </div>
                                </a>
                                <div class="product-info">
                                    <span class="product-category">{{ $product->category->name ?? 'Produto' }}</span>
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                    <p class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                    <span class="product-installment">ou 3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} Sem juros</span>
                                </div>
                                <div class="product-action">
                                    <a href="{{ route('product.show', $product->slug) }}" class="product-btn">COMPRAR</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Paginação --}}
                    <div class="pagination-wrapper">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p>Nenhum produto encontrado.</p>
                        @if($category || $search)
                            <button wire:click="clearCategory" class="clear-filter-btn" style="max-width: 200px; margin: 1rem auto 0;">
                                Limpar Filtros
                            </button>
                        @endif
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>