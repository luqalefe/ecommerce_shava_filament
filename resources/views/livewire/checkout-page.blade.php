<div>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-center text-4xl font-bold mb-8">Finalizar Compra</h1>

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded">
                <p class="font-medium">{{ session('message') }}</p>
            </div>
        @endif

        <form wire:submit.prevent="placeOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Coluna Esquerda: Endereço, Frete e Pagamento --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Seção de Endereço e Frete (Unificada) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Endereço de Entrega e Frete</h2>
                        
                        <div class="space-y-4">
                            {{-- CEP Único - Busca endereço e calcula frete automaticamente --}}
                            <div>
                                <label for="cep" class="block text-sm font-semibold text-gray-700 mb-2">
                                    CEP *
                                </label>
                                <input 
                                    type="text" 
                                    id="cep" 
                                    wire:model.live.debounce.800ms="cep"
                                    placeholder="00000000"
                                    maxlength="9"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                >
                                @error('cep') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                                @if($loading)
                                    <p class="mt-1 text-sm text-gray-500 flex items-center gap-2">
                                        <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-[var(--sh-muted-gold)]"></span>
                                        Buscando endereço e calculando frete...
                                    </p>
                                @endif
                            </div>

                            {{-- Campos de Endereço (preenchidos automaticamente) --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label for="rua" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Rua *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="rua" 
                                        wire:model="rua"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    >
                                    @error('rua') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="numero" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Número *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="numero" 
                                        wire:model="numero"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    >
                                    @error('numero') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="complemento" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Complemento
                                </label>
                                <input 
                                    type="text" 
                                    id="complemento" 
                                    wire:model="complemento"
                                    placeholder="Apto, Bloco, etc."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                >
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cidade" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Cidade *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="cidade" 
                                        wire:model="cidade"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    >
                                    @error('cidade') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Estado *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="estado" 
                                        wire:model="estado"
                                        maxlength="2"
                                        placeholder="SP"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all uppercase"
                                    >
                                    @error('estado') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>

                            {{-- Opções de Frete (aparecem automaticamente após calcular) --}}
                            @if($loading)
                                <div class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--sh-muted-gold)]"></div>
                                    <p class="mt-2 text-gray-600">Calculando opções de frete...</p>
                                </div>
                            @elseif(!empty($shippingOptions))
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Selecione uma opção de frete:</h3>
                                    <div class="space-y-3">
                                        @foreach($shippingOptions as $index => $option)
                                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $selectedShipping === $index ? 'border-[var(--sh-muted-gold)] bg-yellow-50 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                                                <input 
                                                    type="radio" 
                                                    name="shipping_option" 
                                                    wire:click="selectShipping({{ $index }})"
                                                    class="mr-4 w-5 h-5 text-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]"
                                                    {{ $selectedShipping === $index ? 'checked' : '' }}
                                                >
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-900">{{ $option['service'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $option['carrier'] }} • Prazo: {{ $option['deadline'] }} dias úteis</div>
                                                </div>
                                                <div class="font-bold text-lg text-[var(--sh-muted-gold)] ml-4">
                                                    R$ {{ number_format($option['price'], 2, ',', '.') }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($cep && !$loading)
                                <div class="mt-6 pt-6 border-t border-gray-200 text-center py-4 text-gray-500">
                                    <p>Nenhuma opção de frete encontrada. Verifique o CEP e tente novamente.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Seção de Pagamento --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Forma de Pagamento</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-all">
                                <input 
                                    type="radio" 
                                    wire:model="paymentMethod"
                                    value="pix"
                                    class="mr-4 w-5 h-5 text-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]"
                                    checked
                                >
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-900">PIX</span>
                                    <p class="text-sm text-gray-600">Pagamento instantâneo</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Coluna Direita: Resumo do Pedido --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Resumo do Pedido</h2>
                        
                        {{-- Lista de Itens --}}
                        <div class="space-y-4 mb-6">
                            @foreach($cartItems as $item)
                                <div class="flex items-center gap-3 pb-4 border-b border-gray-200">
                                    @php 
                                        $imageUrl = $item->attributes->has('image') 
                                            ? asset('storage/' . $item->attributes->image) 
                                            : 'https://via.placeholder.com/60';
                                    @endphp
                                    <img 
                                        src="{{ $imageUrl }}" 
                                        alt="{{ $item->name }}" 
                                        class="w-16 h-16 object-cover rounded-lg"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-600">Qtd: {{ $item->quantity }}</p>
                                    </div>
                                    <span class="font-bold text-gray-900 whitespace-nowrap">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span class="font-semibold">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Frete</span>
                                <span class="font-semibold">
                                    @if($shippingCost > 0)
                                        R$ {{ number_format($shippingCost, 2, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">A calcular</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="border-t-2 border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-[var(--sh-muted-gold)]">R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="placeOrder"
                            class="w-full bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-4 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                        >
                            <span wire:loading.remove wire:target="placeOrder">Finalizar Pedido</span>
                            <span wire:loading wire:target="placeOrder">Processando...</span>
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            Ao finalizar, você será redirecionado para o pagamento.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
