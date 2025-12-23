@extends('layouts.main')

@section('title', 'Categoria: ' . $category->name)

@section('content')
<div class="category-page">
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="category-header">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Início</a>
                <span>/</span>
                <a href="{{ route('products.index') }}">Loja</a>
                <span>/</span>
                @if($category->parent)
                    <a href="{{ route('category.show', $category->parent->slug) }}">{{ $category->parent->name }}</a>
                    <span>/</span>
                @endif
                <span class="current">{{ $category->name }}</span>
            </nav>
            <h1 class="category-title">{{ $category->name }}</h1>
            <p class="category-count">{{ $products->total() }} produto(s) encontrado(s)</p>
        </div>

        {{-- Layout com Sidebar --}}
        <div class="category-layout" x-data="{ sidebarOpen: false }">
            {{-- Mobile Filter Toggle --}}
            @if($category->children->isNotEmpty() || $category->parent)
            <button @click="sidebarOpen = !sidebarOpen" class="mobile-filter-toggle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 4h18M3 12h18M3 20h18"/>
                </svg>
                Filtrar por Subcategoria
            </button>
            @endif

            {{-- Sidebar --}}
            @if($category->children->isNotEmpty() || $category->parent)
            <aside class="category-sidebar" :class="sidebarOpen ? 'sidebar-open' : ''">
                <div class="sidebar-card">
                    <h2 class="sidebar-title">
                        @if($category->parent)
                            {{ $category->parent->name }}
                        @else
                            {{ $category->name }}
                        @endif
                    </h2>
                    
                    <ul class="filter-list">
                        @if($category->parent)
                            {{-- Estamos em uma subcategoria, mostrar irmãos --}}
                            <li>
                                <a href="{{ route('category.show', $category->parent->slug) }}" 
                                   class="filter-link">
                                    <span>Todos</span>
                                </a>
                            </li>
                            @foreach($category->parent->children as $sibling)
                                <li>
                                    <a href="{{ route('category.show', $sibling->slug) }}" 
                                       class="filter-link {{ $sibling->id === $category->id ? 'active' : '' }}">
                                        <span>{{ $sibling->name }}</span>
                                        <span class="filter-count">({{ $sibling->products()->where('is_active', true)->count() }})</span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            {{-- Estamos na categoria pai, mostrar filhos --}}
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="filter-link active">
                                    <span>Todos</span>
                                </a>
                            </li>
                            @foreach($category->children as $child)
                                <li>
                                    <a href="{{ route('category.show', $child->slug) }}" 
                                       class="filter-link">
                                        <span>{{ $child->name }}</span>
                                        <span class="filter-count">({{ $child->products()->where('is_active', true)->count() }})</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </aside>
            @endif

            {{-- Main Content --}}
            <main class="category-main {{ ($category->children->isNotEmpty() || $category->parent) ? 'with-sidebar' : '' }}">
                {{-- Products Grid --}}
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
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
                        <p>Nenhum produto encontrado nesta categoria.</p>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* =============================================
   CATEGORY PAGE - Design com Sidebar
   ============================================= */

.category-page {
    background: #FAFAF9;
    min-height: 60vh;
}

.category-header {
    text-align: center;
    margin-bottom: 2rem;
}

.breadcrumb {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: #A8947A;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #888;
}

.breadcrumb .current {
    color: #44403C;
    font-weight: 500;
}

.category-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 5vw, 2.5rem);
    font-weight: 500;
    color: #44403C;
    margin-bottom: 0.5rem;
}

.category-count {
    color: #78716C;
    font-size: 0.9rem;
}

/* Layout com Sidebar */
.category-layout {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .category-layout {
        flex-direction: row;
        gap: 3rem;
    }
}

/* Mobile Filter Toggle */
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

/* Sidebar */
.category-sidebar {
    display: none;
    width: 100%;
}

.category-sidebar.sidebar-open {
    display: block;
}

@media (min-width: 1024px) {
    .category-sidebar {
        display: block;
        width: 260px;
        flex-shrink: 0;
    }
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

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-list li {
    margin-bottom: 0.25rem;
}

.filter-link {
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
}

.filter-link:hover {
    background: #F5F5F4;
    color: #1C4532;
}

.filter-link.active {
    background: #1C4532;
    color: white;
}

.filter-link.active:hover {
    background: #15372A;
}

.filter-count {
    font-size: 0.8rem;
    color: #A8A29E;
}

.filter-link.active .filter-count {
    color: rgba(255,255,255,0.8);
}

/* Main Content */
.category-main {
    flex: 1;
    min-width: 0;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 576px) { .products-grid { gap: 1.5rem; } }
@media (min-width: 768px) { .products-grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; } }
@media (min-width: 1024px) { 
    .category-main.with-sidebar .products-grid { grid-template-columns: repeat(3, 1fr); }
    .category-main:not(.with-sidebar) .products-grid { grid-template-columns: repeat(4, 1fr); }
}

/* Product Card - Matching Home Page */
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

.product-info { padding: 1rem 1.25rem; text-align: left; flex: 1; }

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

/* Pagination */
.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #78716C;
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
@endpush