@extends('layouts.main')

@section('title', 'Shava Haux - Headshop e Tabacaria em Rio Branco | Sedas, Pipes, Dichavadores')

@section('meta_description', 'Loja Shava Haux - Headshop e tabacaria em Rio Branco, Acre. Sedas, pipes, dichavadores, roupas de cânhamo e artigos exclusivos. Frete grátis em Rio Branco. Entrega rápida!')

@section('meta_keywords', 'headshop rio branco, tabacaria rio branco, tabacaria acre, sedas, pipes, dichavador, artigos de tabacaria, headshop acre, loja de seda rio branco, shava haux, vitrola, hempwear, roupas de cânhamo, comprar seda online')

@section('og_title', 'Shava Haux - Headshop e Tabacaria em Rio Branco')
@section('og_description', 'Headshop e tabacaria em Rio Branco. Sedas, pipes, dichavadores, vitrolas e artigos exclusivos. Frete grátis em Rio Branco!')

@push('styles')
{{-- JSON-LD Organization Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "Shava Haux",
    "description": "Headshop e tabacaria em Rio Branco, Acre. Sedas, pipes, dichavadores, roupas de cânhamo e artigos exclusivos.",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo_shava.png') }}",
    "image": "{{ asset('images/shava_banner.png') }}",
    "telephone": "+55-68-99999-9999",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Rio Branco",
        "addressRegion": "AC",
        "addressCountry": "BR"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": "-9.9747",
        "longitude": "-67.8249"
    },
    "openingHours": "Mo-Sa 09:00-18:00",
    "priceRange": "$$",
    "sameAs": [
        "https://instagram.com/shavahaux",
        "https://wa.me/5568999999999"
    ]
}
</script>
@endpush

@section('content')

{{-- HERO CARROSSEL - Transição suave automática --}}
<section id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false">
    <div class="carousel-inner">
        <div class="carousel-item active hero-slide">
            <div class="hero-bg" style="background-image: url('{{ asset('images/shava_banner.png') }}');"></div>
            <div class="hero-content">
                <span class="hero-label">EMPRESA GENUINAMENTE ACREANA</span>
                <h1 class="hero-title">A Tradição em Cada Detalhe</h1>
                <a href="{{ route('about') }}" class="hero-btn">CONHEÇA NOSSA CULTURA</a>
            </div>
        </div>
        <div class="carousel-item hero-slide">
            <div class="hero-bg" style="background-image: url('{{ asset('images/shava_banner2.png') }}');"></div>
            <div class="hero-content">
                <span class="hero-label">COLEÇÃO HEMPWEAR</span>
                <h1 class="hero-title">Vista a Sua Essência</h1>
                <a href="{{ route('products.index') }}" class="hero-btn">CONHEÇA A COLEÇÃO</a>
            </div>
        </div>
        <div class="carousel-item hero-slide">
            <div class="hero-bg" style="background-image: url('{{ asset('images/banner3.png') }}');"></div>
            <div class="hero-content">
                <h1 class="hero-title">Nosso Propósito</h1>
                <p class="hero-text d-none d-md-block">"Não vendemos produtos. Criamos símbolos com propósitos."</p>
                <a href="{{ route('about') }}" class="hero-btn">SAIBA MAIS</a>
            </div>
        </div>
    </div>
</section>

{{-- TICKER PROMOCIONAL - Marquee --}}
<div class="promo-ticker">
    <div class="ticker-track">
        <div class="ticker-content">
            <span class="ticker-item">✨ 5% OFF PARA PAGAMENTOS VIA PIX</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">🚀 ENTREGA RÁPIDA EM RIO BRANCO</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">💳 PARCELE EM ATÉ 3X SEM JUROS</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">🎁 FRETE GRÁTIS NA CIDADE DE RIO BRANCO</span>
            <span class="ticker-dot">•</span>
        </div>
        <div class="ticker-content" aria-hidden="true">
            <span class="ticker-item">✨ 5% OFF PARA PAGAMENTOS VIA PIX</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">🚀 ENTREGA RÁPIDA EM RIO BRANCO</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">💳 PARCELE EM ATÉ 3X SEM JUROS</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">🎁 FRETE GRÁTIS NA CIDADE DE RIO BRANCO</span>
            <span class="ticker-dot">•</span>
        </div>
    </div>
</div>

{{-- SEÇÃO: LANÇAMENTOS - Carrossel Infinito --}}
<section class="launches-section">
    <div class="launches-header">
        <h2>🔥 Lançamentos</h2>
        <p>Novidades que você vai amar</p>
    </div>
    
    <div class="launches-carousel-wrapper">
        <button class="launches-nav launches-prev" onclick="navigateLaunches(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
        </button>
        
        <div class="launches-carousel" id="launchesCarousel">
            @foreach($products->take(12) as $index => $product)
                <div class="launch-card" data-index="{{ $index }}">
                    <a href="{{ route('product.show', $product->slug) }}" class="launch-link">
                        <div class="launch-image">
                            @php
                                $imageUrl = $product->images->isNotEmpty() 
                                    ? asset('storage/' . $product->images->first()->path) 
                                    : 'https://via.placeholder.com/300x300/f5f5f5/ccc?text=Sem+Imagem';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                        </div>
                    </a>
                    <div class="launch-info">
                        <span class="launch-category">{{ $product->category->name ?? 'Produto' }}</span>
                        <h3 class="launch-name">{{ $product->name }}</h3>
                        <p class="launch-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <span class="launch-installment">ou 3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} Sem juros</span>
                    </div>
                    <div class="launch-action">
                        <a href="{{ route('product.show', $product->slug) }}" class="launch-btn">COMPRAR</a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <button class="launches-nav launches-next" onclick="navigateLaunches(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
    
    <div class="launches-dots" id="launchesDots">
        @foreach($products->take(12) as $index => $product)
            <button class="dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></button>
        @endforeach
    </div>
</section>

<script>
let currentSlide = 0;
const totalSlides = {{ min($products->count(), 12) }};

function navigateLaunches(direction) {
    currentSlide = Math.max(0, Math.min(totalSlides - 1, currentSlide + direction));
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

function updateCarousel() {
    const carousel = document.getElementById('launchesCarousel');
    const card = carousel.querySelector('.launch-card');
    if (!card) return;
    
    const cardWidth = card.offsetWidth + 16;
    carousel.scrollTo({ left: currentSlide * cardWidth, behavior: 'smooth' });
    
    // Update dots
    document.querySelectorAll('.launches-dots .dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === currentSlide);
    });
}

// Sync dots with scroll
document.getElementById('launchesCarousel')?.addEventListener('scroll', function() {
    const cardWidth = this.querySelector('.launch-card')?.offsetWidth + 16 || 300;
    const newSlide = Math.round(this.scrollLeft / cardWidth);
    if (newSlide !== currentSlide) {
        currentSlide = newSlide;
        document.querySelectorAll('.launches-dots .dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === currentSlide);
        });
    }
});
</script>


{{-- SEÇÃO: CATEGORIAS EM DESTAQUE --}}
@if(isset($globalCategories) && $globalCategories->isNotEmpty())
<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Navegue por Categoria</h2>
        <div class="categories-grid">
            @foreach($globalCategories->take(4) as $category)
                <a href="{{ route('category.show', $category->slug) }}" class="category-card">
                    <div class="category-icon">
                        @if(str_contains(strtolower($category->name), 'vitrola'))
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                        @elseif(str_contains(strtolower($category->name), 'ancestry') || str_contains(strtolower($category->name), 'rapé'))
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        @elseif(str_contains(strtolower($category->name), 'hemp'))
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2v20M18 2v20M6 12h12M6 7h12M6 17h12M3 12h3M18 12h3"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        @endif
                    </div>
                    <span class="category-name">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- SEÇÃO: MAIS VENDIDOS --}}
<section class="bestsellers-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Mais Vendidos</h2>
            <a href="{{ route('products.index') }}" class="section-link">Ver todos →</a>
        </div>
        <div class="enhanced-products-grid">
            @forelse ($products->take(4) as $product)
                <div class="enhanced-product-card">
                    <a href="{{ route('product.show', $product->slug) }}" class="enhanced-product-link">
                        <div class="enhanced-product-image">
                            @php
                                $imageUrl = $product->images->isNotEmpty() 
                                    ? asset('storage/' . $product->images->first()->path) 
                                    : 'https://via.placeholder.com/400x400/f5f5f5/ccc?text=Sem+Imagem';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                        </div>
                    </a>
                    <div class="enhanced-product-info">
                        <span class="enhanced-product-category">{{ $product->category->name ?? 'Produto' }}</span>
                        <h3 class="enhanced-product-name">{{ $product->name }}</h3>
                        <p class="enhanced-product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <span class="enhanced-product-installment">ou 3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} Sem juros</span>
                    </div>
                    <div class="enhanced-product-action">
                        <a href="{{ route('product.show', $product->slug) }}" class="enhanced-product-btn">COMPRAR</a>
                    </div>
                </div>
            @empty
                <p class="no-products">Nenhum produto encontrado.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- SEÇÃO: TODOS OS PRODUTOS --}}
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nossos Produtos</h2>
        </div>
        <div class="enhanced-products-grid">
            @forelse ($products as $product)
                <div class="enhanced-product-card">
                    <a href="{{ route('product.show', $product->slug) }}" class="enhanced-product-link">
                        <div class="enhanced-product-image">
                            @php
                                $imageUrl = $product->images->isNotEmpty() 
                                    ? asset('storage/' . $product->images->first()->path) 
                                    : 'https://via.placeholder.com/400x400/f5f5f5/ccc?text=Sem+Imagem';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                        </div>
                    </a>
                    <div class="enhanced-product-info">
                        <span class="enhanced-product-category">{{ $product->category->name ?? 'Produto' }}</span>
                        <h3 class="enhanced-product-name">{{ $product->name }}</h3>
                        <p class="enhanced-product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <span class="enhanced-product-installment">ou 3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} Sem juros</span>
                    </div>
                    <div class="enhanced-product-action">
                        <a href="{{ route('product.show', $product->slug) }}" class="enhanced-product-btn">COMPRAR</a>
                    </div>
                </div>
            @empty
                <p class="no-products">Nenhum produto encontrado.</p>
            @endforelse
        </div>
        @if($products->count() >= 8)
            <div class="text-center mt-5">
                <a href="{{ route('products.index') }}" class="btn-view-all">VER TODOS OS PRODUTOS</a>
            </div>
        @endif
    </div>
</section>

{{-- SEÇÃO: TRUST BADGES / SEGURANÇA --}}
<section class="trust-section">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item">
                <svg class="trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="trust-label">Loja Segura</span>
            </div>
            <div class="trust-item">
                <svg class="trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="trust-label">Pague com Cartão</span>
            </div>
            <div class="trust-item">
                <svg class="trust-icon pix" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.59 10.59L12 16.17l-5.59-5.58L8 9l4 4 4-4 1.59 1.59z"/>
                </svg>
                <span class="trust-label">PIX com Desconto</span>
            </div>
            <div class="trust-item">
                <svg class="trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span class="trust-label">Entrega Rápida</span>
            </div>
        </div>
    </div>
</section>

{{-- BOTÕES FLUTUANTES: WHATSAPP E INSTAGRAM --}}
<div class="floating-buttons">
    <a href="https://wa.me/5568999999999" target="_blank" class="floating-btn whatsapp" title="WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    <a href="https://instagram.com/shavahaux" target="_blank" class="floating-btn instagram" title="Instagram">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
        </svg>
    </a>
</div>

@endsection

@push('styles')
<style>
    /* ============================================
       DESIGN - INSPIRADO BEM BOLADO
       ============================================ */
    
    /* HERO CAROUSEL - Responsivo estilo Bem Bolado */
    #heroCarousel { 
        overflow: hidden; 
        position: relative;
        z-index: 1;
    }
    
    /* Mobile: banner mais curto, estilo Bem Bolado */
    .hero-slide { 
        height: 55vh; 
        min-height: 280px;
        max-height: 380px;
    }
    
    /* Tablet: transição */
    @media (min-width: 768px) { 
        .hero-slide { 
            height: 60vh; 
            min-height: 350px;
            max-height: 450px;
        } 
    }
    
    /* Desktop: banner mais horizontal/wide */
    @media (min-width: 1200px) {
        .hero-slide {
            height: 65vh;
            min-height: 400px;
            max-height: 550px;
        }
    }
    
    .hero-bg { 
        position: absolute; 
        inset: 0; 
        background-size: cover; 
        background-position: center;
    }
    
    .hero-content {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        text-align: center; padding: 2rem;
        background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.35) 50%, rgba(0,0,0,0.5) 100%);
        z-index: 1;
    }
    
    .hero-label { color: rgba(255,255,255,0.9); font-size: 0.75rem; letter-spacing: 3px; margin-bottom: 1rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .hero-title { color: white; font-family: 'Playfair Display', serif; font-size: clamp(1.8rem, 5vw, 3.5rem); font-weight: 600; margin-bottom: 1.5rem; max-width: 600px; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .hero-text { color: rgba(255,255,255,0.95); font-size: 1.1rem; max-width: 500px; margin-bottom: 1.5rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .hero-btn { display: inline-block; background: #1C4532; color: white; padding: 1rem 2.5rem; font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; text-decoration: none; transition: all 0.3s ease; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .hero-btn:hover { background: #15372A; color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }
    
    .carousel-indicators { bottom: 20px; }
    .carousel-indicators button { width: 30px; height: 3px; border-radius: 0; background: rgba(255,255,255,0.5); border: none; margin: 0 5px; }
    .carousel-indicators .active { background: white; }
    .carousel-control-prev, .carousel-control-next { width: 60px; opacity: 0.5; }
    .carousel-control-prev:hover, .carousel-control-next:hover { opacity: 1; }
    
    /* ============================================
       TICKER PROMOCIONAL - Marquee Animation
       ============================================ */
    .promo-ticker {
        background: #1C4532;
        color: white;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
    }
    
    .ticker-track {
        display: flex;
        width: max-content;
        animation: ticker-scroll 35s linear infinite;
    }
    
    .ticker-content {
        display: flex;
        align-items: center;
        padding: 14px 0;
    }
    
    .ticker-item {
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .ticker-dot {
        margin: 0 2rem;
        opacity: 0.5;
        font-size: 0.75rem;
    }
    
    @keyframes ticker-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    /* Pause on hover */
    .promo-ticker:hover .ticker-track {
        animation-play-state: paused;
    }
    
    /* LAUNCHES SECTION - Organic Light Theme */
    .launches-section {
        background: linear-gradient(135deg, #F5F5F4 0%, #FAFAF9 50%, #FFF7ED 100%);
        padding: 3rem 0;
        overflow: hidden;
    }
    
    .launches-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    
    .launches-header h2 {
        color: #44403C;
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .launches-header p {
        color: #78716C;
        font-size: 1rem;
    }
    
    .launches-carousel-wrapper {
        position: relative;
        width: 100%;
        padding: 0;
    }
    
    .launches-carousel {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 1rem 1rem 1rem calc(50vw - 700px);
    }
    
    @media (max-width: 1440px) {
        .launches-carousel { padding: 1rem 1.5rem; }
    }
    
    .launches-carousel::-webkit-scrollbar { display: none; }
    
    /* Mobile: 1 card centralizado */
    .launch-card {
        flex: 0 0 280px;
        min-width: 280px;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        scroll-snap-align: start;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .launch-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    
    @media (min-width: 576px) { .launch-card { flex: 0 0 260px; min-width: 260px; } }
    @media (min-width: 768px) { .launch-card { flex: 0 0 280px; min-width: 280px; } }
    @media (min-width: 992px) { .launch-card { flex: 0 0 300px; min-width: 300px; } }
    
    /* Badges */
    .launch-badges {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 5;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .badge-new {
        background: #1C4532;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .launch-link { text-decoration: none; color: inherit; display: block; }
    
    .launch-image {
        aspect-ratio: 1;
        overflow: hidden;
        background: #F5F5F4;
    }
    
    .launch-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .launch-card:hover .launch-image img { transform: scale(1.1); }
    
    .launch-info { padding: 1rem 1.25rem; }
    
    .launch-category {
        display: inline-block;
        color: #78716C;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    
    .launch-name {
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
    
    .launch-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1C4532;
        margin: 0;
    }
    
    .launch-installment {
        display: block;
        font-size: 0.8rem;
        color: #78716C;
        margin-top: 4px;
    }
    
    .launch-action {
        padding: 0 1.25rem 1.25rem;
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    
    /* Quantity Selector */
    .quantity-selector {
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 50px;
        overflow: hidden;
    }
    
    .quantity-selector button {
        width: 36px;
        height: 36px;
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #555;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .quantity-selector button:hover { background: #f0f0f0; }
    
    .quantity-selector span {
        min-width: 24px;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .launch-btn {
        flex: 1;
        text-align: center;
        background: #1C4532;
        color: white;
        padding: 0.875rem 1.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .launch-btn:hover {
        background: #15372A;
        color: white;
    }
    
    /* Navigation Arrows */
    .launches-nav {
        display: none;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        background: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        z-index: 10;
        transition: all 0.3s ease;
        color: #44403C;
    }
    
    .launches-nav:hover { background: #1C4532; color: white; }
    .launches-nav svg { width: 24px; height: 24px; }
    .launches-nav.launches-prev { left: 0; }
    .launches-nav.launches-next { right: 0; }
    .launches-nav svg { width: 24px; height: 24px; }
    .launches-prev { left: 0; }
    .launches-next { right: 0; }
    
    @media (min-width: 768px) { .launches-nav { display: flex; align-items: center; justify-content: center; } }
    
    /* Dots Navigation */
    .launches-dots {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 1.5rem;
    }
    
    .launches-dots .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #D6D3D1;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .launches-dots .dot:hover { background: #A8A29E; }
    .launches-dots .dot.active {
        background: #1C4532;
        width: 32px;
        border-radius: 6px;
    }

    .launch-action { padding: 0 1rem 1rem; }
    
    .launch-btn {
        display: block;
        width: 100%;
        text-align: center;
        background: var(--sh-muted-gold, #B3AF8F);
        color: white;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .launch-btn:hover {
        background: var(--sh-dark-text, #403A30);
        color: white;
    }
    
    .launches-nav {
        display: none;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        background: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10;
        transition: all 0.3s ease;
    }
    
    .launches-nav:hover { background: var(--sh-muted-gold); color: white; }
    .launches-nav svg { width: 24px; height: 24px; }
    .launches-prev { left: 1rem; }
    .launches-next { right: 1rem; }
    
    @media (min-width: 768px) { .launches-nav { display: flex; align-items: center; justify-content: center; } }
    
    .launches-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 1.5rem;
    }

    /* SECTION COMMON STYLES */
    .section-title { text-align: center; font-family: 'Playfair Display', serif; font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 500; color: #44403C; margin-bottom: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
    .section-header .section-title { margin-bottom: 0; text-align: left; }
    .section-link { color: #1C4532; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
    .section-link:hover { text-decoration: underline; }

    /* CATEGORIES SECTION */
    .categories-section { padding: 3rem 0; background: #FAFAF9; }
    .categories-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    @media (min-width: 768px) { .categories-grid { grid-template-columns: repeat(4, 1fr); gap: 1.5rem; } }
    
    .category-card {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 2rem 1rem; background: white; border-radius: 12px;
        text-decoration: none; color: #44403C; transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }
    .category-card:hover { background: #1C4532; color: white; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .category-icon { width: 48px; height: 48px; margin-bottom: 1rem; color: #1C4532; }
    .category-card:hover .category-icon { color: white; }
    .category-icon svg { width: 100%; height: 100%; }
    .category-name { font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

    /* BESTSELLERS SECTION */
    .bestsellers-section { padding: 3rem 0; background: #F5F5F4; }
    
    /* PRODUCTS SECTION */
    .products-section { padding: 3rem 0; background: #FAFAF9; }
    @media (min-width: 768px) { .products-section, .bestsellers-section { padding: 4rem 0; } }

    /* PRODUCT GRID */
    .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    @media (min-width: 576px) { .products-grid { gap: 1.5rem; } }
    @media (min-width: 768px) { .products-grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; } }
    @media (min-width: 992px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

    /* PRODUCT CARD */
    .product-card { display: block; text-decoration: none; background: white; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
    
    .product-image { position: relative; aspect-ratio: 1; overflow: hidden; background: #F5F5F4; }
    .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover .product-image img { transform: scale(1.05); }
    
    .product-badge { position: absolute; top: 10px; left: 10px; background: #1C4532; color: white; font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 4px; }
    
    .product-info { padding: 1rem; text-align: center; }
    @media (min-width: 768px) { .product-info { padding: 1.25rem; } }
    
    .product-name { font-size: 0.9rem; font-weight: 500; color: #44403C; margin-bottom: 0.5rem; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    @media (min-width: 768px) { .product-name { font-size: 1rem; } }
    
    .product-price { font-size: 1rem; font-weight: 600; color: #1C4532; margin: 0; }
    @media (min-width: 768px) { .product-price { font-size: 1.1rem; } }
    
    .no-products { grid-column: 1 / -1; text-align: center; color: #78716C; padding: 3rem; }
    .btn-view-all { display: inline-block; padding: 1rem 3rem; border: 2px solid #1C4532; color: #1C4532; font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; text-decoration: none; transition: all 0.3s ease; border-radius: 6px; }
    .btn-view-all:hover { background: #1C4532; color: white; }

    /* ENHANCED PRODUCT GRID - Estilo Lançamentos */
    .enhanced-products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    @media (min-width: 576px) { .enhanced-products-grid { gap: 1.5rem; } }
    @media (min-width: 768px) { .enhanced-products-grid { grid-template-columns: repeat(3, 1fr); gap: 1.5rem; } }
    @media (min-width: 992px) { .enhanced-products-grid { grid-template-columns: repeat(4, 1fr); gap: 2rem; } }

    /* ENHANCED PRODUCT CARD */
    .enhanced-product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .enhanced-product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }

    .enhanced-product-link { text-decoration: none; }
    
    .enhanced-product-image {
        aspect-ratio: 1;
        overflow: hidden;
        background: #F5F5F4;
    }
    .enhanced-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .enhanced-product-card:hover .enhanced-product-image img { transform: scale(1.05); }

    .enhanced-product-info {
        padding: 1.25rem;
        text-align: left;
        flex: 1;
    }
    
    .enhanced-product-category {
        display: inline-block;
        color: #78716C;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    
    .enhanced-product-name {
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
    
    .enhanced-product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1C4532;
        margin: 0;
    }
    
    .enhanced-product-installment {
        display: block;
        font-size: 0.8rem;
        color: #78716C;
        margin-top: 4px;
    }
    
    .enhanced-product-action {
        padding: 0 1.25rem 1.25rem;
    }
    
    .enhanced-product-btn {
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
    .enhanced-product-btn:hover {
        background: #15372A;
        color: white;
    }

    /* TRUST SECTION */
    .trust-section { padding: 2rem 0; background: #1C4532; }
    .trust-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    @media (min-width: 768px) { .trust-grid { grid-template-columns: repeat(4, 1fr); } }
    
    .trust-item { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .trust-icon { width: 32px; height: 32px; color: #A8947A; margin-bottom: 0.5rem; }
    .trust-icon.pix { color: #A8947A; }
    .trust-label { color: white; font-size: 0.8rem; font-weight: 500; }

    /* FLOATING BUTTONS - Organic Minimal */
    .floating-buttons { position: fixed; right: 20px; bottom: 100px; display: flex; flex-direction: column; gap: 12px; z-index: 999; }
    @media (min-width: 768px) { .floating-buttons { bottom: 30px; } }
    
    .floating-btn {
        width: 56px; height: 56px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    .floating-btn:hover { transform: scale(1.1); box-shadow: 0 6px 25px rgba(0,0,0,0.2); }
    .floating-btn svg { width: 28px; height: 28px; }
    
    .floating-btn.whatsapp { background: #1C4532; color: white; }
    .floating-btn.whatsapp:hover { background: #15372A; }
    .floating-btn.instagram { background: white; color: #44403C; border: 1px solid #E7E5E4; }
    .floating-btn.instagram:hover { background: #F5F5F4; color: #1C4532; }
</style>
@endpush