<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class AbacatePayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Webhook AbacatePay Recebido:', $payload);

            // Tenta extrair o ID da cobrança e o status
            // A estrutura exata depende da API, mas geralmente vem em 'data' ou na raiz
            $billingId = $payload['data']['id'] ?? ($payload['id'] ?? null);
            $status = $payload['data']['status'] ?? ($payload['status'] ?? null);

            if (!$billingId) {
                Log::warning('Webhook AbacatePay sem ID de cobrança.');
                return response()->json(['message' => 'ID not found'], 400);
            }

            // Buscar o pedido pelo payment_id
            $order = Order::where('payment_id', $billingId)->first();

            if (!$order) {
                Log::warning("Webhook AbacatePay: Pedido não encontrado para o ID {$billingId}");
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Atualizar status do pedido
            // Status possíveis da Abacate: PENDING, PAID, CANCELED, EXPIRED (suposição)
            // Mapeando para o sistema: pending, processing, cancelled
            
            $oldStatus = $order->status;
            
            if ($status === 'PAID') {
                if ($order->status !== 'processing' && $order->status !== 'shipped' && $order->status !== 'delivered') {
                    $order->update(['status' => 'processing']);
                    Log::info("Pedido #{$order->id} atualizado para PROCESSANDO via Webhook.");
                }
            } elseif ($status === 'CANCELED' || $status === 'EXPIRED') {
                if ($order->status !== 'cancelled' && $order->status !== 'shipped' && $order->status !== 'delivered') {
                    $order->update(['status' => 'cancelled']);
                    Log::info("Pedido #{$order->id} atualizado para CANCELADO via Webhook.");
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Erro no Webhook AbacatePay: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Error'], 500);
        }
    }
}
