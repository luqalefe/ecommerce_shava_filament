<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$order = App\Models\Order::latest()->first();
if ($order) {
    $previousStatus = $order->status;
    $order->status = 'processing';
    $order->save();
    echo "Pedido {$order->id} atualizado de '{$previousStatus}' para 'processing'\n";
} else {
    echo "Nenhum pedido encontrado\n";
}
