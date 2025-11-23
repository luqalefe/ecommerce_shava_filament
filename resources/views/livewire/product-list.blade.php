<div>
    <x-layouts.app title="Nossa Loja">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-center text-4xl font-bold mb-8">Nossa Loja</h1>
            
            {{-- Filtros --}}
            <div class="mb-6 flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar produtos..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-transparent"
                    >
                </div>
            </div>

            {{-- Grid de Produtos --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                            <a href="{{ route('product.show', $product->slug) }}" class="block">
                                @if($product->images->isNotEmpty())
                                    <img 
                                        src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-full h-64 object-cover"
                                    >
                                @else
                                    <img 
                                        src="https://via.placeholder.com/300" 
                                        alt="Imagem indisponível" 
                                        class="w-full h-64 object-cover"
                                    >
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                                    <p class="text-[var(--sh-muted-gold)] font-bold text-xl">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </p>
                                </div>
                            </a>
                            <div class="px-4 pb-4">
                                <livewire:add-to-cart :product-id="$product->id" :key="'cart-' . $product->id" />
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Paginação --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <p class="text-center text-gray-500 py-12">Nenhum produto encontrado.</p>
            @endif
        </div>
    </x-layouts.app>
</div>