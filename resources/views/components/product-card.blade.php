@props(['product'])

@php
    $imageUrl = $product->images->isNotEmpty() 
        ? asset('storage/' . $product->images->first()->path) 
        : 'https://via.placeholder.com/400x400/f5f5f5/ccc?text=Sem+Imagem';
@endphp

<div class="product-card">
    <a href="{{ route('product.show', $product->slug) }}">
        <div class="product-image">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
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
