<div>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Mensagem de sucesso após pagamento --}}
        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Meus Pedidos</h1>
            @if($unreadCount > 0)
                <button 
                    wire:click="markAllAsRead"
                    class="text-sm text-[var(--sh-muted-gold)] hover:underline font-semibold"
                >
                    Marcar todas como lidas
                </button>
            @endif
        </div>

        {{-- Notificações --}}
        @if($notifications->isNotEmpty())
            <div class="mb-6 space-y-3">
                @foreach($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isRead = $notification->read_at !== null;
                    @endphp
                    <div 
                        wire:click="markAsRead('{{ $notification->id }}')"
                        class="bg-white rounded-lg border-l-4 {{ $isRead ? 'border-gray-300' : 'border-[var(--sh-muted-gold)]' }} shadow-sm p-4 cursor-pointer hover:shadow-md transition-all {{ !$isRead ? 'bg-yellow-50' : '' }}"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900 mb-1">
                                    {{ $data['message'] ?? 'Atualização do pedido' }}
                                </p>
                                <p class="text-xs text-gray-600">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if(!$isRead)
                                <span class="ml-4 w-2 h-2 bg-[var(--sh-muted-gold)] rounded-full"></span>
                            @endif
                        </div>
                        @if(isset($data['url']))
                            <a href="{{ $data['url'] }}" class="text-xs text-[var(--sh-muted-gold)] hover:underline mt-2 inline-block">
                                Ver detalhes →
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Nenhum pedido encontrado</h2>
                <p class="text-gray-600 mb-6">Você ainda não realizou nenhum pedido.</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-semibold py-3 px-6 rounded-lg transition-all">
                    Ir para a Loja
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Nº do Pedido
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Data
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">#{{ $order->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $order->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'processing' => ['label' => 'Processando', 'class' => 'bg-blue-100 text-blue-800'],
                                                'shipped' => ['label' => 'Enviado', 'class' => 'bg-indigo-100 text-indigo-800'],
                                                'delivered' => ['label' => 'Entregue', 'class' => 'bg-green-100 text-green-800'],
                                                'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-red-100 text-red-800'],
                                            ];
                                            $config = $statusConfig[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('order.show', $order) }}" class="text-[var(--sh-muted-gold)] hover:text-opacity-80 font-semibold">
                                            Ver Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
