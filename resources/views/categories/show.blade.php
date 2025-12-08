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
                <span class="current">{{ $category->name }}</span>
            </nav>
            <h1 class="category-title">{{ $category->name }}</h1>
            <p class="category-count">{{ $products->total() }} produto(s) encontrado(s)</p>
        </div>

        {{-- Grid de Produtos --}}
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach ($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}" class="product-card">
                        <div class="product-image">
                            @php
                                $imageUrl = $product->images->isNotEmpty() 
                                    ? asset('storage/' . $product->images->first()->path) 
                                    : 'https://via.placeholder.com/400x400/f5f5f5/ccc?text=Sem+Imagem';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <p class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </a>
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
    </div>
</div>
@endsection

@push('styles')
<style>
/* =============================================
   CATEGORY PAGE - Design Consistente com Home
   ============================================= */

.category-page {
    background: #FAFAFA;
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
}

.breadcrumb a {
    color: var(--sh-muted-gold);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #888;
}

.breadcrumb .current {
    color: var(--sh-dark-text);
    font-weight: 500;
}

.category-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 5vw, 2.5rem);
    font-weight: 500;
    color: var(--sh-dark-text, #403A30);
    margin-bottom: 0.5rem;
}

.category-count {
    color: #888;
    font-size: 0.9rem;
}

/* Products Grid - Mesmo da Home */
.products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 576px) { .products-grid { gap: 1.5rem; } }
@media (min-width: 768px) { .products-grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; } }
@media (min-width: 992px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

/* Product Card - Mesmo da Home */
.product-card {
    display: block;
    text-decoration: none;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.product-image {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    background: #f5f5f5;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-info {
    padding: 1rem;
    text-align: center;
}

@media (min-width: 768px) { .product-info { padding: 1.25rem; } }

.product-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--sh-dark-text, #403A30);
    margin-bottom: 0.5rem;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

@media (min-width: 768px) { .product-name { font-size: 1rem; } }

.product-price {
    font-size: 1rem;
    font-weight: 600;
    color: var(--sh-muted-gold, #B3AF8F);
    margin: 0;
}

@media (min-width: 768px) { .product-price { font-size: 1.1rem; } }

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
    color: #888;
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
@endpush