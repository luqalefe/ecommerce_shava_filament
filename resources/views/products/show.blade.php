@extends('layouts.main')

@section('title', $product->name . ' | Shava Haux - Headshop Rio Branco')

@section('meta_description'){{ Str::limit(strip_tags($product->short_description ?? $product->long_description ?? 'Compre ' . $product->name . ' na Shava Haux. Headshop e tabacaria em Rio Branco com entrega rápida.'), 155) }}@endsection

@section('meta_keywords', $product->name . ', ' . ($product->category->name ?? 'headshop') . ', headshop rio branco, tabacaria acre, shava haux, comprar online')

@section('og_type', 'product')
@section('og_title', $product->name . ' | Shava Haux')
@section('og_description'){{ Str::limit(strip_tags($product->short_description ?? 'Compre na Shava Haux'), 100) }}@endsection
@section('og_image'){{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->path) : asset('images/shava_banner.png') }}@endsection

@push('styles')
{{-- JSON-LD Product Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ Str::limit(strip_tags($product->short_description ?? $product->long_description ?? ''), 200) }}",
    "image": "{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->path) : asset('images/shava_banner.png') }}",
    "sku": "{{ $product->sku ?? $product->id }}",
    "brand": {
        "@type": "Brand",
        "name": "Shava Haux"
    },
    "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "BRL",
        "price": "{{ number_format($product->price, 2, '.', '') }}",
        "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "seller": {
            "@type": "Organization",
            "name": "Shava Haux"
        }
    }
}
</script>
@endpush

@section('content')
<div class="product-page" 
    x-data="{ 
        currentImage: 0,
        images: {{ Js::from($product->images->map(fn($img) => asset('storage/' . $img->path))->toArray() ?: ['https://via.placeholder.com/600']) }},
        zoomed: false,
        zoomX: 50,
        zoomY: 50
    }">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="separator">›</span>
        @if($product->category)
            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            <span class="separator">›</span>
        @endif
        <span class="current">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- Container Principal --}}
    <div class="product-main">
        
        {{-- GALERIA DE IMAGENS --}}
        <div class="product-gallery">
            {{-- Thumbnails (Desktop: lateral, Mobile: horizontal) --}}
            <div class="gallery-thumbnails">
                <template x-for="(img, index) in images" :key="index">
                    <button 
                        @click="currentImage = index"
                        @mouseenter="currentImage = index"
                        :class="{ 'active': currentImage === index }"
                        class="thumbnail-btn"
                    >
                        <img :src="img" :alt="'Imagem ' + (index + 1)">
                    </button>
                </template>
            </div>
            
            {{-- Imagem Principal --}}
            <div class="gallery-main">
                <div 
                    class="main-image-container"
                    @mouseenter="zoomed = true"
                    @mouseleave="zoomed = false"
                    @mousemove="zoomX = ($event.offsetX / $el.offsetWidth) * 100; zoomY = ($event.offsetY / $el.offsetHeight) * 100"
                >
                    <img 
                        :src="images[currentImage]" 
                        :alt="'{{ $product->name }}'"
                        class="main-image"
                        :style="zoomed ? `transform-origin: ${zoomX}% ${zoomY}%; transform: scale(2)` : ''"
                    >
                </div>
                
                {{-- Navigation Arrows --}}
                <button 
                    @click="currentImage = (currentImage - 1 + images.length) % images.length"
                    class="gallery-arrow gallery-prev"
                    x-show="images.length > 1"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button 
                    @click="currentImage = (currentImage + 1) % images.length"
                    class="gallery-arrow gallery-next"
                    x-show="images.length > 1"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                </button>
                
                {{-- Image Counter (Mobile) --}}
                <div class="image-counter" x-show="images.length > 1">
                    <span x-text="currentImage + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>

        {{-- INFORMAÇÕES DO PRODUTO --}}
        <div class="product-info">
            {{-- Category Badge --}}
            @if($product->category)
                <a href="{{ route('category.show', $product->category->slug) }}" class="category-badge">
                    {{ $product->category->name }}
                </a>
            @endif

            {{-- Product Title --}}
            <h1 class="product-title">{{ $product->name }}</h1>

            {{-- Rating Placeholder (future feature) --}}
            <div class="rating-section">
                <div class="stars">
                    @for($i = 0; $i < 5; $i++)
                        <svg viewBox="0 0 24 24" fill="currentColor" class="star {{ $i < 4 ? 'filled' : '' }}">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    @endfor
                </div>
                <span class="rating-count">(12 avaliações)</span>
            </div>

            {{-- Price Box --}}
            <div class="price-box">
                @php
                    $pixPrice = $product->price * 0.95; // 5% off for PIX
                @endphp
                
                <div class="price-main">
                    <span class="currency">R$</span>
                    <span class="price-value">{{ number_format($product->price, 2, ',', '.') }}</span>
                </div>
                
                <div class="pix-price">
                    <span class="pix-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                            <path d="M13.365 16.673l-3.192-3.192a1.5 1.5 0 010-2.121l3.192-3.192a2.001 2.001 0 012.828 0l3.192 3.192a1.5 1.5 0 010 2.121l-3.192 3.192a2.001 2.001 0 01-2.828 0z"/>
                            <path d="M5.615 16.673l3.192-3.192a1.5 1.5 0 000-2.121L5.615 8.168a2.001 2.001 0 00-2.828 0L-.405 11.36a1.5 1.5 0 000 2.121l3.192 3.192a2.001 2.001 0 002.828 0z" transform="translate(4.5)"/>
                        </svg>
                    </span>
                    <span class="pix-label">R$ {{ number_format($pixPrice, 2, ',', '.') }} no PIX</span>
                    <span class="pix-discount">(5% OFF)</span>
                </div>
                
                <div class="installment">
                    ou 3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} sem juros
                </div>
            </div>

            {{-- Stock Status --}}
            <div class="stock-status {{ $product->quantity > 0 ? ($product->quantity < 5 ? 'low' : 'available') : 'unavailable' }}">
                @if($product->quantity > 10)
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    <span>Em estoque</span>
                @elseif($product->quantity > 0)
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Apenas {{ $product->quantity }} em estoque - peça já!</span>
                @else
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                    <span>Produto indisponível</span>
                @endif
            </div>

            {{-- Short Description --}}
            @if($product->short_description)
                <div class="short-description">
                    {!! $product->short_description !!}
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="action-section">
                {{-- Livewire AddToCart --}}
                <livewire:add-to-cart :product-id="$product->id" :key="'cart-' . $product->id" />

                {{-- Buy Now Button --}}
                <form action="{{ route('checkout.buyNow', $product) }}" method="POST" class="buy-now-form">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-buy-now" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Comprar Agora
                    </button>
                </form>
            </div>

            {{-- Trust Badges --}}
            <div class="trust-badges">
                <div class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span>Entrega Rápida<br><small>Rio Branco</small></span>
                </div>
                <div class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Loja 100%<br><small>Segura</small></span>
                </div>
                <div class="badge">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13.365 16.673l-3.192-3.192a1.5 1.5 0 010-2.121l3.192-3.192a2.001 2.001 0 012.828 0l3.192 3.192a1.5 1.5 0 010 2.121l-3.192 3.192a2.001 2.001 0 01-2.828 0z"/>
                    </svg>
                    <span>5% OFF<br><small>no PIX</small></span>
                </div>
            </div>

            {{-- Product Meta --}}
            <div class="product-meta">
                @if($product->sku)
                    <p><strong>SKU:</strong> {{ $product->sku }}</p>
                @endif
                @if($product->category)
                    <p><strong>Categoria:</strong> 
                        <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- DESCRIÇÃO COMPLETA --}}
    @if($product->long_description)
    <div class="product-details">
        <div class="details-tabs" x-data="{ activeTab: 'description' }">
            <div class="tabs-header">
                <button 
                    @click="activeTab = 'description'" 
                    :class="{ 'active': activeTab === 'description' }"
                    class="tab-btn"
                >
                    Descrição
                </button>
                <button 
                    @click="activeTab = 'delivery'" 
                    :class="{ 'active': activeTab === 'delivery' }"
                    class="tab-btn"
                >
                    Entrega
                </button>
            </div>
            
            <div class="tabs-content">
                <div x-show="activeTab === 'description'" class="tab-panel">
                    <div class="prose">
                        {!! $product->long_description !!}
                    </div>
                </div>
                
                <div x-show="activeTab === 'delivery'" x-cloak class="tab-panel">
                    <h3>Informações de Entrega</h3>
                    <ul>
                        <li><strong>Rio Branco:</strong> Entrega em até 24h úteis</li>
                        <li><strong>Interior do Acre:</strong> 2-5 dias úteis</li>
                        <li><strong>Outras regiões:</strong> 5-10 dias úteis via Correios</li>
                    </ul>
                    <p>Frete grátis para Rio Branco em compras acima de R$ 100,00</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PRODUTOS RELACIONADOS --}}
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="related-products">
        <h2>Quem viu também gostou</h2>
        <div class="related-grid">
            @foreach($relatedProducts->take(4) as $related)
                <x-product-card :product="$related" />
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Mobile Sticky Footer --}}
<div class="mobile-sticky-footer">
    <div class="sticky-price">
        <span class="sticky-price-value">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
        <span class="sticky-pix">R$ {{ number_format($product->price * 0.95, 2, ',', '.') }} PIX</span>
    </div>
    <div class="sticky-actions">
        <button 
            onclick="document.querySelector('.action-section button[wire\\:click]').click()"
            class="sticky-btn sticky-cart"
            {{ $product->stock <= 0 ? 'disabled' : '' }}
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
            </svg>
        </button>
        <form action="{{ route('checkout.buyNow', $product) }}" method="POST" class="sticky-form">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="sticky-btn sticky-buy" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                Comprar
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ============================================
   PRODUCT PAGE - AMAZON STYLE
   ============================================ */

.product-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    padding-bottom: 100px; /* Space for mobile sticky footer */
}

/* Breadcrumb */
.breadcrumb {
    font-size: 0.85rem;
    color: #78716C;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.breadcrumb a {
    color: var(--sh-muted-gold, #A69067);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb .separator {
    color: #D6D3D1;
}

.breadcrumb .current {
    color: #44403C;
}

/* Main Layout */
.product-main {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 768px) {
    .product-main {
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
    }
}

@media (min-width: 1024px) {
    .product-main {
        grid-template-columns: 55% 45%;
    }
}

/* ============================================
   GALLERY
   ============================================ */

.product-gallery {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (min-width: 768px) {
    .product-gallery {
        flex-direction: row;
        gap: 1rem;
    }
}

/* Thumbnails */
.gallery-thumbnails {
    display: flex;
    gap: 0.5rem;
    order: 2;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

@media (min-width: 768px) {
    .gallery-thumbnails {
        flex-direction: column;
        order: 1;
        width: 80px;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 500px;
    }
}

.thumbnail-btn {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 4px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

@media (min-width: 768px) {
    .thumbnail-btn {
        width: 70px;
        height: 70px;
    }
}

.thumbnail-btn:hover,
.thumbnail-btn.active {
    border-color: var(--sh-muted-gold, #A69067);
}

.thumbnail-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

/* Main Image */
.gallery-main {
    position: relative;
    flex: 1;
    order: 1;
}

@media (min-width: 768px) {
    .gallery-main {
        order: 2;
    }
}

.main-image-container {
    position: relative;
    background: #F5F5F4;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 1;
    cursor: zoom-in;
}

.main-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

/* Gallery Arrows */
.gallery-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
    transition: all 0.2s ease;
}

.gallery-arrow:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.gallery-arrow svg {
    width: 20px;
    height: 20px;
    color: #44403C;
}

.gallery-prev { left: 10px; }
.gallery-next { right: 10px; }

/* Image Counter (Mobile) */
.image-counter {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
}

@media (min-width: 768px) {
    .image-counter {
        display: none;
    }
}

/* ============================================
   PRODUCT INFO
   ============================================ */

.product-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Category Badge */
.category-badge {
    display: inline-block;
    background: #F5F5F4;
    color: #78716C;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 6px 12px;
    border-radius: 20px;
    text-decoration: none;
    width: fit-content;
    transition: all 0.2s ease;
}

.category-badge:hover {
    background: var(--sh-muted-gold, #A69067);
    color: white;
}

/* Product Title */
.product-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1C1917;
    line-height: 1.3;
    margin: 0;
}

@media (min-width: 768px) {
    .product-title {
        font-size: 1.75rem;
    }
}

/* Rating */
.rating-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    width: 18px;
    height: 18px;
    color: #D6D3D1;
}

.star.filled {
    color: #F59E0B;
}

.rating-count {
    font-size: 0.85rem;
    color: #78716C;
}

/* Price Box */
.price-box {
    background: #FAFAF9;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #E7E5E4;
}

.price-main {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 0.75rem;
}

.currency {
    font-size: 1rem;
    color: #44403C;
}

.price-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1C1917;
}

.pix-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #ECFDF5;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.pix-icon svg {
    color: #059669;
}

.pix-label {
    font-weight: 600;
    color: #059669;
}

.pix-discount {
    font-size: 0.8rem;
    color: #059669;
    font-weight: 500;
}

.installment {
    font-size: 0.9rem;
    color: #78716C;
}

/* Stock Status */
.stock-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.stock-status svg {
    width: 20px;
    height: 20px;
}

.stock-status.available {
    color: #059669;
}

.stock-status.low {
    color: #D97706;
}

.stock-status.unavailable {
    color: #DC2626;
}

/* Short Description */
.short-description {
    font-size: 0.95rem;
    color: #57534E;
    line-height: 1.6;
}

/* Action Section */
.action-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-section > div {
    display: flex;
    gap: 0.5rem;
}

.action-section input[type="number"] {
    width: 70px;
    padding: 0.75rem;
    border: 2px solid #E7E5E4;
    border-radius: 8px;
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
}

.action-section button[wire\:click] {
    flex: 1;
    background: var(--sh-muted-gold, #A69067);
    color: white;
    font-weight: 600;
    padding: 0.875rem 1.5rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
}

.action-section button[wire\:click]:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.buy-now-form {
    width: 100%;
}

.btn-buy-now {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: #F59E0B;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-buy-now:hover:not(:disabled) {
    background: #D97706;
    transform: translateY(-1px);
}

.btn-buy-now:disabled {
    background: #D6D3D1;
    cursor: not-allowed;
}

.btn-buy-now svg {
    width: 20px;
    height: 20px;
}

/* Trust Badges */
.trust-badges {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem 0;
    border-top: 1px solid #E7E5E4;
    border-bottom: 1px solid #E7E5E4;
}

.trust-badges .badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.5rem;
}

.trust-badges .badge svg {
    width: 28px;
    height: 28px;
    color: var(--sh-muted-gold, #A69067);
}

.trust-badges .badge span {
    font-size: 0.75rem;
    font-weight: 600;
    color: #44403C;
    line-height: 1.3;
}

.trust-badges .badge small {
    font-weight: 400;
    color: #78716C;
}

/* Product Meta */
.product-meta {
    font-size: 0.85rem;
    color: #78716C;
}

.product-meta p {
    margin: 0.25rem 0;
}

.product-meta a {
    color: var(--sh-muted-gold, #A69067);
    text-decoration: none;
}

.product-meta a:hover {
    text-decoration: underline;
}

/* ============================================
   PRODUCT DETAILS / TABS
   ============================================ */

.product-details {
    margin-top: 3rem;
    border-top: 1px solid #E7E5E4;
    padding-top: 2rem;
}

.tabs-header {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #E7E5E4;
    margin-bottom: 1.5rem;
}

.tab-btn {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #78716C;
    cursor: pointer;
    position: relative;
    transition: color 0.2s ease;
}

.tab-btn:hover {
    color: #44403C;
}

.tab-btn.active {
    color: var(--sh-muted-gold, #A69067);
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--sh-muted-gold, #A69067);
}

.tab-panel {
    padding: 1rem 0;
}

.tab-panel h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1C1917;
    margin-bottom: 1rem;
}

.tab-panel ul {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.tab-panel li {
    margin-bottom: 0.5rem;
    color: #57534E;
}

/* Prose for description */
.prose {
    color: #57534E;
    line-height: 1.7;
}

.prose img {
    max-width: 100%;
    height: auto;
    margin: 1em 0;
    border-radius: 8px;
}

.prose p {
    margin-bottom: 1em;
}

.prose h1, .prose h2, .prose h3 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: bold;
    color: #1C1917;
}

.prose ul, .prose ol {
    margin-left: 1.5em;
    margin-bottom: 1em;
}

.prose li {
    margin-bottom: 0.5em;
}

/* ============================================
   RELATED PRODUCTS
   ============================================ */

.related-products {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #E7E5E4;
}

.related-products h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1C1917;
    margin-bottom: 1.5rem;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 768px) {
    .related-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* ============================================
   MOBILE STICKY FOOTER
   ============================================ */

.mobile-sticky-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #E7E5E4;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    z-index: 100;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}

@media (min-width: 768px) {
    .mobile-sticky-footer {
        display: none;
    }
    
    .product-page {
        padding-bottom: 2rem;
    }
}

.sticky-price {
    display: flex;
    flex-direction: column;
}

.sticky-price-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1C1917;
}

.sticky-pix {
    font-size: 0.8rem;
    color: #059669;
    font-weight: 500;
}

.sticky-actions {
    display: flex;
    gap: 0.5rem;
}

.sticky-form {
    flex: 1;
}

.sticky-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.sticky-cart {
    width: 48px;
    height: 48px;
    background: #F5F5F4;
    color: #44403C;
}

.sticky-cart svg {
    width: 24px;
    height: 24px;
}

.sticky-buy {
    width: 100%;
    height: 48px;
    background: #F59E0B;
    color: white;
    font-size: 1rem;
    padding: 0 1.5rem;
}

.sticky-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endpush