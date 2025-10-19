@extends('layouts.main')

@section('title', 'Shava Haux - Início')

@section('content')

{{-- SEÇÃO DO CARROSSEL --}}
<section id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000"> {{-- Adicionado data-bs-interval --}}
    {{-- Indicadores (bolinhas) --}}
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <div class="carousel-inner">
        {{-- Slide 1 --}}
        <div class="carousel-item active" style="height: 70vh;">
            <div class="hero-slide-image" style="background-image: url('{{ asset('images/shava_banner.png') }}');"></div>
            <div class="carousel-caption">
                <p class="text-uppercase small">EMPRESA GENUINAMENTE ACREANA</p>
                <h1 class="display-3 fw-bold text-uppercase">A Tradição em Cada Detalhe.</h1>
                <a href="#" class="btn btn-lg btn-warning rounded-pill mt-3 px-5 py-3">CONHEÇA NOSSA CULTURA</a>
            </div>
        </div>
        {{-- Slide 2 --}}
        <div class="carousel-item" style="height: 70vh;">
            <div class="hero-slide-image" style="background-image: url('{{ asset('images/shava_banner2.png') }}');"></div>
            <div class="carousel-caption">
                <p class="text-uppercase small">COLEÇÃO HEMPWEAR</p>
                <h1 class="display-3 fw-bold text-uppercase">Vista a Sua Essência.</h1>
                <a href="#" class="btn btn-lg btn-warning rounded-pill mt-3 px-5 py-3">VER PRODUTOS</a>
            </div>
        </div>
        {{-- Slide 3 --}}
        <div class="carousel-item" style="height: 70vh;">
            <div class="hero-slide-image" style="background-image: url('{{ asset('images/banner3.png') }}');"></div>
            <div class="carousel-caption">
                 <h1 class="display-4 fw-bold text-uppercase">Nosso Propósito</h1>
                 <p class="lead d-none d-md-block mt-3" style="max-width: 700px; margin-left: auto; margin-right: auto;">
                     "Não vendemos produtos. Criamos símbolos com propósitos. Nossas bandeiras carregam sentido e é isso que nos move."
                 </p>
                 <a href="#" class="btn btn-lg btn-warning rounded-pill mt-3 px-5 py-3">SAIBA MAIS</a>
            </div>
        </div>
    </div>
    {{-- Controles (Setas) --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</section>

{{-- SEÇÃO DOS PRODUTOS --}}
<div class="container py-5">
    <h2 class="text-center h1 fw-bold mb-5 text-uppercase">Nossos Produtos</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse ($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">
                        @php
                            $imageUrl = $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->path) : 'https://via.placeholder.com/300';
                        @endphp
                        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate">{{ $product->name }}</h5>
                            <p class="card-text fw-bold mt-auto" style="color: var(--sh-muted-gold);">
                                {{ 'R$ ' . number_format($product->price, 2, ',', '.') }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <p class="col-12 text-center">Nenhum produto encontrado no momento.</p>
        @endforelse
    </div>
</div>


{{-- Adicionando CSS para o carrossel --}}
@push('styles')
<style>
    .hero-slide-image { width: 100%; height: 100%; background-size: cover; background-position: center; }
    .carousel-caption { background: rgba(0, 0, 0, 0.4); inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; }
</style>
@endpush

@endsection