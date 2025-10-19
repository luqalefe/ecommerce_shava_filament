@extends('layouts.main')

@section('title', 'Categoria: ' . $category->name)

@section('content')
<div class="container py-5">
    <h1 class="text-center h1 fw-bold mb-5 uppercase">Categoria: {{ $category->name }}</h1>
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
            <p class="col-12 text-center">Nenhum produto encontrado nesta categoria.</p>
        @endforelse
    </div>
     {{-- Paginação estilizada pelo Bootstrap --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection