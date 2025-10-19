@extends('layouts.main')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row g-5"> {{-- Gap maior entre as colunas --}}

        {{-- Coluna da Imagem --}}
        <div class="col-md-6">
            @php
                // Pega a primeira imagem ou usa placeholder
                $imageUrl = $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->path) : 'https://via.placeholder.com/600';
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-lg mb-3">
             {{-- Adicionar miniaturas aqui se houver mais imagens --}}
        </div>

        {{-- Coluna de Informações --}}
        <div class="col-md-6 d-flex flex-column"> {{-- Adicionado flex-column --}}
            <h1 class="h1 fw-bold text-uppercase mb-3">{{ $product->name }}</h1>

            {{-- Preço --}}
            <p class="h3 fw-bold my-4" style="color: var(--sh-muted-gold);">
                {{ 'R$ ' . number_format($product->price, 2, ',', '.') }}
            </p>

            {{-- Descrição Curta --}}
            @if($product->short_description)
                <div class="mb-4 lead">
                     {!! $product->short_description !!}
                </div>
            @endif

            {{-- Formulário Add Carrinho --}}
            <form action="{{ route('cart.store', $product) }}" method="POST" class="mb-3" id="add-to-cart-form"> {{-- Adicionado ID --}}
                @csrf
                <div class="d-flex align-items-center gap-3">
                    {{-- Input de Quantidade --}}
                    <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="form-control" style="width: 80px;" required>

                    <button type="submit" class="btn btn-outline-warning btn-lg fw-bold text-uppercase flex-grow-1"> {{-- flex-grow-1 para ocupar espaço --}}
                        <i class="bi bi-cart-plus-fill me-2"></i> Adicionar
                    </button>
                </div>
            </form>

            {{-- Formulário Comprar Agora --}}
            <form action="{{ route('checkout.buyNow', $product) }}" method="POST">
                @csrf
                {{-- Input oculto para quantidade (será atualizado via JS) --}}
                <input type="hidden" name="quantity" id="buy-now-quantity" value="1">

                <div class="d-grid"> {{-- Botão ocupa largura total --}}
                    <button type="submit" class="btn btn-warning btn-lg text-white fw-bold text-uppercase">
                        Comprar Agora
                    </button>
                </div>
            </form>

            {{-- Informações Adicionais (movidas um pouco para baixo) --}}
            <div class="small text-muted mt-5">
                @if($product->sku)
                    <p class="mb-1">SKU: {{ $product->sku }}</p>
                @endif
                @if($product->category)
                    <p>Categoria: <a href="{{ route('category.show', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></p>
                @endif
            </div>
        </div>
    </div>

    {{-- Descrição Completa --}}
    @if($product->long_description)
    <div class="row mt-5">
        <div class="col-12">
            <hr class="my-4">
            <h3 class="fw-bold mb-3 text-uppercase">Descrição Completa</h3>
            <div class="prose">
                {!! $product->long_description !!}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

{{-- Adiciona estilos para o conteúdo HTML da descrição longa --}}
@push('styles')
<style>
    .prose img { max-width: 100%; height: auto; margin-top: 1em; margin-bottom: 1em; }
    .prose p { margin-bottom: 1em; }
    .prose h1, .prose h2, .prose h3 { margin-top: 1.5em; margin-bottom: 0.5em; font-weight: bold; }
    .prose ul, .prose ol { margin-left: 1.5em; margin-bottom: 1em; }
    .prose li { margin-bottom: 0.5em; }
</style>
@endpush

{{-- Script para sincronizar a quantidade entre os formulários --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity-input');
        const buyNowQuantityInput = document.getElementById('buy-now-quantity');

        if (quantityInput && buyNowQuantityInput) {
            quantityInput.addEventListener('change', function() {
                buyNowQuantityInput.value = this.value;
            });
            // Garante que o valor inicial esteja sincronizado
             buyNowQuantityInput.value = quantityInput.value;
        }
    });
</script>
@endpush