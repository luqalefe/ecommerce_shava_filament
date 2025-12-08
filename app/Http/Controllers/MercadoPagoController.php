<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Services\MercadoPagoService;

class MercadoPagoController extends Controller
{
    protected $mercadopagoService;

    public function __construct(MercadoPagoService $mercadopagoService)
    {
        $this->mercadopagoService = $mercadopagoService;
    }

    /**
     * Recebe notificações de webhook do Mercado Pago
     * 
     * Rota: POST /api/mercadopago/webhook
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Log do payload recebido
            Log::info('Webhook Mercado Pago recebido', [
                'headers' => $request->headers->all(),
                'payload' => $request->all(),
                'query_params' => $request->query(),
            ]);

            // O Mercado Pago envia notificações de duas formas:
            // 1. Via query parameter 'data.id' (ID do pagamento)
            // 2. Via POST body com 'data.id'
            
            $paymentId = $request->query('data.id') ?? $request->input('data.id') ?? $request->input('id');

            if (empty($paymentId)) {
                Log::warning('Webhook Mercado Pago recebido sem ID de pagamento', [
                    'payload' => $request->all()
                ]);
                
                // Retornar 200 mesmo assim para evitar reenvios
                return response()->json(['status' => 'ok', 'message' => 'No payment ID provided'], 200);
            }

            Log::info('Processando webhook Mercado Pago', [
                'payment_id' => $paymentId
            ]);

            // Buscar informações do pagamento no Mercado Pago
            $payment = $this->mercadopagoService->getPayment($paymentId);

            if (!$payment) {
                Log::error('Pagamento não encontrado no Mercado Pago', [
                    'payment_id' => $paymentId
                ]);
                
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 200);
            }

            Log::info('Pagamento encontrado no Mercado Pago', [
                'payment_id' => $payment['id'],
                'status' => $payment['status'],
                'external_reference' => $payment['external_reference']
            ]);

            // Buscar o pedido pelo external_reference (que é o order_id)
            $orderId = $payment['external_reference'] ?? null;

            if (empty($orderId)) {
                Log::warning('Pagamento sem external_reference (order_id)', [
                    'payment_id' => $payment['id']
                ]);
                
                return response()->json(['status' => 'ok', 'message' => 'No order ID in payment'], 200);
            }

            // Buscar o pedido
            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Pedido não encontrado', [
                    'order_id' => $orderId,
                    'payment_id' => $payment['id']
                ]);
                
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 200);
            }

            // Atualizar status do pedido baseado no status do pagamento
            DB::beginTransaction();
            
            try {
                $newStatus = $this->mapPaymentStatusToOrderStatus($payment['status']);
                
                if ($newStatus) {
                    $order->status = $newStatus;
                    $order->save();
                    
                    Log::info('Status do pedido atualizado via webhook', [
                        'order_id' => $order->id,
                        'old_status' => $order->getOriginal('status'),
                        'new_status' => $newStatus,
                        'payment_status' => $payment['status']
                    ]);
                }

                DB::commit();
                
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Webhook processed successfully',
                    'order_id' => $order->id,
                    'payment_status' => $payment['status']
                ], 200);
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('Erro ao processar webhook Mercado Pago', [
                    'order_id' => $orderId,
                    'payment_id' => $payment['id'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
            }

        } catch (\Exception $e) {
            Log::error('Erro geral ao processar webhook Mercado Pago', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);
            
            // Sempre retornar 200 para evitar reenvios do Mercado Pago
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Mapeia o status do pagamento do Mercado Pago para o status do pedido
     * 
     * @param string $paymentStatus
     * @return string|null
     */
    private function mapPaymentStatusToOrderStatus(string $paymentStatus): ?string
    {
        $statusMap = [
            'approved' => 'processing',      // Pagamento aprovado -> Processando
            'authorized' => 'processing',   // Pagamento autorizado -> Processando
            'pending' => 'pending',          // Pagamento pendente -> Pendente
            'in_process' => 'pending',      // Em processamento -> Pendente
            'rejected' => 'cancelled',      // Pagamento rejeitado -> Cancelado
            'cancelled' => 'cancelled',     // Cancelado -> Cancelado
            'refunded' => 'cancelled',      // Reembolsado -> Cancelado
            'charged_back' => 'cancelled',  // Chargeback -> Cancelado
        ];

        return $statusMap[strtolower($paymentStatus)] ?? null;
    }
}

