<div>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-center text-4xl font-bold mb-8" style="font-family: 'Playfair Display', serif;">Meu Carrinho</h1>

        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Lista de Itens --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-[var(--sh-border)] p-6 flex gap-4 hover:shadow-md transition-shadow">
                            @php
                                $imageUrl = $item->attributes->has('image') 
                                    ? asset('storage/' . $item->attributes->image) 
                                    : 'https://via.placeholder.com/100';
                            @endphp
                            <img 
                                src="{{ $imageUrl }}" 
                                alt="{{ $item->name }}" 
                                class="w-24 h-24 object-cover rounded-lg"
                            >
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-[var(--sh-dark-text)] mb-1">{{ $item->name }}</h3>
                                <p class="text-[var(--sh-text-light)] mb-4">R$ {{ number_format($item->price, 2, ',', '.') }} cada</p>
                                
                                <div class="flex items-center gap-3">
                                    <button 
                                        wire:click="decrement('{{ $item->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="decrement('{{ $item->id }}')"
                                        class="w-10 h-10 bg-[var(--sh-cream)] hover:bg-[var(--sh-muted-gold)] hover:text-white rounded-lg transition-colors flex items-center justify-center font-bold disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="decrement('{{ $item->id }}')">-</span>
                                        <span wire:loading wire:target="decrement('{{ $item->id }}')" class="animate-spin">⟳</span>
                                    </button>
                                    <span class="w-12 text-center font-semibold text-[var(--sh-dark-text)]">{{ $item->quantity }}</span>
                                    <button 
                                        wire:click="increment('{{ $item->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="increment('{{ $item->id }}')"
                                        class="w-10 h-10 bg-[var(--sh-cream)] hover:bg-[var(--sh-muted-gold)] hover:text-white rounded-lg transition-colors flex items-center justify-center font-bold disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="increment('{{ $item->id }}')">+</span>
                                        <span wire:loading wire:target="increment('{{ $item->id }}')" class="animate-spin">⟳</span>
                                    </button>
                                    <button 
                                        wire:click="remove('{{ $item->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="remove('{{ $item->id }}')"
                                        class="ml-auto text-red-500 hover:text-red-700 font-semibold transition-colors disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="remove('{{ $item->id }}')">Remover</span>
                                        <span wire:loading wire:target="remove('{{ $item->id }}')">Removendo...</span>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-xl text-[var(--sh-muted-gold)]">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Resumo --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-[var(--sh-border)] p-6 h-fit sticky top-4">
                        <h2 class="text-2xl font-bold mb-6 text-[var(--sh-dark-text)]" style="font-family: 'Playfair Display', serif;">Resumo</h2>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-[var(--sh-dark-text)]">
                                <span>Subtotal</span>
                                <span class="font-bold">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <a 
                            href="{{ route('checkout.index') }}" 
                            class="block w-full bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-4 px-6 rounded-lg text-center transition-all shadow-md hover:shadow-lg"
                        >
                            Finalizar Compra
                        </a>
                        <a 
                            href="{{ route('products.index') }}" 
                            class="block w-full mt-3 text-center text-[var(--sh-muted-gold)] hover:underline font-semibold"
                        >
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-[var(--sh-text-light)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-xl text-[var(--sh-text-light)] mb-4">Seu carrinho está vazio</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all">
                    Continuar Comprando
                </a>
            </div>
        @endif
    </div>
</div>