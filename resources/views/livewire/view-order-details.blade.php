<div>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Botão Voltar --}}
        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-[var(--sh-muted-gold)] mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para Meus Pedidos
        </a>

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Pedido #{{ $order->id }}</h1>
            <p class="text-gray-600 mt-1">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
        </div>

        {{-- A. Status Tracker (O mais importante) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold mb-6 text-gray-900">Status do Pedido</h2>
            
            @if($order->status === 'cancelled')
                {{-- Status Cancelado --}}
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            Pedido Cancelado
                        </span>
                    </div>
                </div>
            @else
                {{-- Tracker de Status Visual --}}
                <div class="relative">
                    {{-- Linha de conexão --}}
                    @php
                        $activeIndex = null;
                        foreach($this->getStatusSteps() as $index => $step) {
                            if($step['isActive']) {
                                $activeIndex = $index;
                                break;
                            }
                        }
                        $progressWidth = $activeIndex !== null 
                            ? ($activeIndex === 3 ? '100%' : ($activeIndex * 33.33) . '%')
                            : '0%';
                    @endphp
                    <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200">
                        <div class="h-full bg-[var(--sh-muted-gold)] transition-all duration-500" 
                             style="width: {{ $progressWidth }}">
                        </div>
                    </div>

                    <div class="relative flex justify-between">
                        @foreach($this->getStatusSteps() as $step)
                            <div class="flex flex-col items-center flex-1">
                                {{-- Ícone do Status --}}
                                <div class="relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-2 transition-all
                                    {{ $step['isCompleted'] 
                                        ? 'bg-[var(--sh-muted-gold)] border-[var(--sh-muted-gold)]' 
                                        : ($step['isActive'] 
                                            ? 'bg-white border-[var(--sh-muted-gold)] ring-4 ring-[var(--sh-muted-gold)] ring-opacity-20' 
                                            : 'bg-white border-gray-300') }}">
                                    
                                    @if($step['isCompleted'])
                                        {{-- Checkmark para status completos --}}
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif($step['isActive'])
                                        {{-- Ícone específico para status ativo --}}
                                        @if($step['icon'] === 'clock')
                                            <svg class="w-6 h-6 text-[var(--sh-muted-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($step['icon'] === 'cog')
                                            <svg class="w-6 h-6 text-[var(--sh-muted-gold)] animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        @elseif($step['icon'] === 'truck')
                                            <svg class="w-6 h-6 text-[var(--sh-muted-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-[var(--sh-muted-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    @else
                                        {{-- Círculo vazio para status não alcançados --}}
                                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                    @endif
                                </div>

                                {{-- Label do Status --}}
                                <div class="mt-3 text-center">
                                    <p class="text-sm font-semibold 
                                        {{ $step['isActive'] || $step['isCompleted'] ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $step['label'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Badge do Status Atual --}}
                <div class="mt-6 text-center">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $this->getStatusBadgeClass() }}">
                        Status Atual: {{ $this->getStatusLabel() }}
                    </span>
                </div>

                {{-- Seção de Rastreamento (se disponível) --}}
                @if($order->tracking_code)
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <h3 class="font-bold text-blue-900">Informações de Rastreamento</h3>
                        </div>
                        
                        @if($order->carrier_name)
                            <p class="text-sm text-blue-800 mb-2">
                                <span class="font-semibold">Transportadora:</span> {{ $order->carrier_name }}
                            </p>
                        @endif
                        
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-sm text-blue-800 font-semibold">Código:</span>
                            <code class="px-3 py-1 bg-white border border-blue-300 rounded font-mono text-blue-900 select-all">{{ $order->tracking_code }}</code>
                            <button 
                                onclick="navigator.clipboard.writeText('{{ $order->tracking_code }}').then(() => { this.innerHTML = '✓ Copiado!'; setTimeout(() => this.innerHTML = 'Copiar', 2000); })"
                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition"
                            >
                                Copiar
                            </button>
                        </div>
                        
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Rastrear Pedido
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Coluna Esquerda: Endereço e Itens --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- B. Endereço de Entrega --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">Endereço de Entrega</h2>
                    @if($order->endereco)
                        <div class="text-gray-700 space-y-1">
                            <p class="font-semibold">{{ $order->endereco->rua }}, {{ $order->endereco->numero }}</p>
                            @if($order->endereco->complemento)
                                <p>{{ $order->endereco->complemento }}</p>
                            @endif
                            <p>{{ $order->endereco->cidade }} - {{ $order->endereco->estado }}</p>
                            <p>CEP: {{ $order->endereco->cep }}</p>
                        </div>
                    @else
                        <p class="text-gray-500">Endereço não disponível</p>
                    @endif
                </div>

                {{-- C. Itens do Pedido --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">Itens do Pedido</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-start gap-4 pb-4 border-b border-gray-200 last:border-0">
                                @php
                                    $product = $item->product;
                                    $imageUrl = $product && $product->images->isNotEmpty() 
                                        ? asset('storage/' . $product->images->first()->path) 
                                        : 'https://via.placeholder.com/100';
                                @endphp
                                <img 
                                    src="{{ $imageUrl }}" 
                                    alt="{{ $item->product->name ?? 'Produto' }}" 
                                    class="w-20 h-20 object-cover rounded-lg"
                                >
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 mb-1">
                                        {{ $item->product->name ?? 'Produto não encontrado' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">Quantidade: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">Preço unitário: R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">
                                        R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Coluna Direita: Resumo do Pagamento --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">Resumo do Pagamento</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span class="font-semibold">
                                R$ {{ number_format($order->total_amount - ($order->shipping_cost ?? 0), 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Frete</span>
                            <span class="font-semibold">
                                R$ {{ number_format($order->shipping_cost ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                        @if(isset($order->shipping_service) && $order->shipping_service)
                            <p class="text-xs text-gray-500">{{ $order->shipping_service }}</p>
                        @endif
                    </div>

                    <div class="border-t-2 border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between text-xl font-bold">
                            <span class="text-gray-900">Total Pago</span>
                            <span class="text-[var(--sh-muted-gold)]">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Forma de Pagamento:</span>
                            <span class="font-semibold uppercase">{{ $order->payment_method }}</span>
                        </div>
                        @if($order->payment_id)
                            <div class="flex justify-between text-gray-600">
                                <span>ID do Pagamento:</span>
                                <span class="font-mono text-xs">{{ $order->payment_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
