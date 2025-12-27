<x-mail::message>
# Olá, {{ $order->user->name }}!

O status do seu pedido **#{{ $order->id }}** foi atualizado para: **Enviado**.

Transportadora: **{{ $order->carrier_name ?? 'Não informada' }}**

@if($order->tracking_code)
Código de Rastreio: **{{ $order->tracking_code }}**
@endif

@if($order->tracking_url)
<x-mail::button :url="$order->tracking_url">
Rastrear Pedido
</x-mail::button>
@else
Acesse sua conta para ver mais detalhes.
<x-mail::button :url="route('orders.index')">
Meus Pedidos
</x-mail::button>
@endif

Obrigado,<br>
Equipe {{ config('app.name') }}
</x-mail::message>
