@extends('layouts.main')

@section('title', 'Nossa Loja')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-center text-3xl md:text-4xl font-bold mb-8">Nossa Loja</h1>
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <a href="{{ route('product.show', $product->slug) }}" class="block">
                    @if($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" class="w-full h-48 md:h-56 object-cover" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/300" class="w-full h-48 md:h-56 object-cover" alt="Imagem indisponível">
                    @endif
                    <div class="p-4">
                        <h5 class="text-sm md:text-base font-medium text-gray-800 truncate">{{ $product->name }}</h5>
                        <p class="text-lg font-bold mt-2" style="color: var(--sh-muted-gold);">
                            {{ 'R$ ' . number_format($product->price, 2, ',', '.') }}
                        </p>
                    </div>
                </a>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">Nenhum produto encontrado.</p>
        @endforelse
    </div>
    <div class="mt-12 flex justify-center">
        {{ $products->links() }}
    </div>
</div>
@endsection