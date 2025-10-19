@extends('layouts.main')

@section('title', 'Nossa Loja')

@section('content')
<div class="container py-5">
    <h1 class="text-center h1 fw-bold mb-5">Nossa Loja</h1>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse ($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none">
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $product->images->first()->path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/300" class="card-img-top" alt="Imagem indisponível" style="height: 250px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-truncate">{{ $product->name }}</h5>
                            <p class="card-text fw-bold" style="color: var(--sh-muted-gold);">
                                {{ 'R$ ' . number_format($product->price, 2, ',', '.') }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <p class="col-12 text-center">Nenhum produto encontrado.</p>
        @endforelse
    </div>
    <div class="mt-5 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection