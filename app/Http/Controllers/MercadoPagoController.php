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

    /**
     * Tolerância de tempo para validação do timestamp (em segundos)
     * Rejeita webhooks com mais de 5 minutos de idade para prevenir replay attacks
     */
    protected const TIMESTAMP_TOLERANCE_SECONDS = 300;

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
        // ===================================================================
        // ETAPA 1: VALIDAÇÃO DE ASSINATURA HMAC-SHA256
        // ===================================================================
        $signatureValidation = $this->validateWebhookSignature($request);
        
        if (!$signatureValidation['valid']) {
            // Logar tentativa de ataque
            Log::channel('daily')->warning('🚨 [SECURITY] Webhook Mercado Pago com assinatura inválida', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reason' => $signatureValidation['reason'],
                'headers' => $request->headers->all(),
                'payload' => $request->all(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature'
            ], 403);
        }

        // ===================================================================
        // ETAPA 2: PROCESSAMENTO DO WEBHOOK (código original)
        // ===================================================================
        try {
            Log::info('✅ Webhook Mercado Pago validado e recebido', [
                'payment_id' => $request->query('data.id') ?? $request->input('data.id'),
                'request_id' => $request->header('x-request-id'),
            ]);

            // O Mercado Pago envia notificações de duas formas:
            // 1. Via query parameter 'data.id' (ID do pagamento)
            // 2. Via POST body com 'data.id'
            
            $paymentId = $request->query('data.id') ?? $request->input('data.id') ?? $request->input('id');

            if (empty($paymentId)) {
                Log::warning('Webhook Mercado Pago recebido sem ID de pagamento', [
                    'payload' => $request->all()
                ]);
                
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
     * Valida a assinatura HMAC-SHA256 do webhook do Mercado Pago
     * 
     * @see https://www.mercadopago.com.br/developers/pt/docs/your-integrations/notifications/webhooks
     * 
     * @param Request $request
     * @return array ['valid' => bool, 'reason' => string|null]
     */
    protected function validateWebhookSignature(Request $request): array
    {
        // Obter a secret key do .env
        $webhookSecret = config('services.mercadopago.webhook_secret');
        
        // Obter headers necessários
        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');
        
        // Se não houver secret configurada, permitir em desenvolvimento (com warning)
        if (empty($webhookSecret)) {
            if (app()->environment('local', 'development')) {
                Log::warning('⚠️ MERCADOPAGO_WEBHOOK_SECRET não configurada - Validação desabilitada em ambiente de desenvolvimento');
                return ['valid' => true, 'reason' => null];
            }
            
            return ['valid' => false, 'reason' => 'Webhook secret not configured'];
        }

        // ===================================================================
        // Permitir testes do painel do Mercado Pago em ambiente local
        // O botão "Testar" do painel do MP não envia x-signature
        // ===================================================================
        if (empty($xSignature) && app()->environment('local', 'development')) {
            // Verificar se parece um teste do MP (tem os campos esperados)
            $payload = $request->all();
            if (isset($payload['type']) && isset($payload['data']) && isset($payload['action'])) {
                Log::info('🧪 Webhook de TESTE do Mercado Pago permitido (ambiente de desenvolvimento)', [
                    'action' => $payload['action'] ?? 'unknown',
                    'type' => $payload['type'] ?? 'unknown',
                ]);
                return ['valid' => true, 'reason' => 'Test webhook allowed in development'];
            }
        }
        
        if (empty($xSignature)) {
            return ['valid' => false, 'reason' => 'Missing x-signature header'];
        }
        
        if (empty($xRequestId)) {
            return ['valid' => false, 'reason' => 'Missing x-request-id header'];
        }

        // Extrair ts e v1 do header x-signature
        // Formato: "ts=1704067200,v1=abc123def456..."
        $signatureParts = [];
        foreach (explode(',', $xSignature) as $part) {
            $keyValue = explode('=', $part, 2);
            if (count($keyValue) === 2) {
                $signatureParts[trim($keyValue[0])] = trim($keyValue[1]);
            }
        }

        $timestamp = $signatureParts['ts'] ?? null;
        $receivedHash = $signatureParts['v1'] ?? null;

        if (empty($timestamp)) {
            return ['valid' => false, 'reason' => 'Missing timestamp (ts) in x-signature'];
        }

        if (empty($receivedHash)) {
            return ['valid' => false, 'reason' => 'Missing hash (v1) in x-signature'];
        }

        // Validar timestamp para prevenir replay attacks
        $currentTime = time();
        $webhookTime = (int) $timestamp;
        
        if (abs($currentTime - $webhookTime) > self::TIMESTAMP_TOLERANCE_SECONDS) {
            return [
                'valid' => false, 
                'reason' => sprintf(
                    'Timestamp expired (received: %d, current: %d, diff: %d seconds)',
                    $webhookTime,
                    $currentTime,
                    abs($currentTime - $webhookTime)
                )
            ];
        }

        // Obter o data.id do query parameter (usado pelo Mercado Pago no manifest)
        // Laravel pode parsear 'data.id' de duas formas:
        // 1. Como string direta: $request->query('data.id')
        // 2. Como array: $request->query('data')['id']
        $dataId = $request->query('data.id');
        
        // Se veio como array (ex: ?data[id]=123)
        if (empty($dataId)) {
            $dataArray = $request->query('data');
            if (is_array($dataArray) && isset($dataArray['id'])) {
                $dataId = $dataArray['id'];
            }
        }
        
        // Fallback: tentar pegar da query string bruta
        if (empty($dataId)) {
            $queryString = $request->getQueryString() ?? '';
            if (preg_match('/data\.id=([^&]+)/', $queryString, $matches)) {
                $dataId = urldecode($matches[1]);
            }
        }
        
        $dataId = $dataId ?? '';

        // Construir o "manifest" - string que será assinada
        // Formato do Mercado Pago: "id:[data.id];request-id:[x-request-id];ts:[ts];"
        $manifest = sprintf(
            'id:%s;request-id:%s;ts:%s;',
            $dataId,
            $xRequestId,
            $timestamp
        );

        // Calcular o HMAC-SHA256
        $calculatedHash = hash_hmac('sha256', $manifest, $webhookSecret);

        // Comparação segura contra timing attacks
        if (!hash_equals($calculatedHash, $receivedHash)) {
            Log::debug('Webhook signature mismatch', [
                'manifest' => $manifest,
                'calculated_hash' => $calculatedHash,
                'received_hash' => $receivedHash,
            ]);
            
            return ['valid' => false, 'reason' => 'Signature mismatch'];
        }

        return ['valid' => true, 'reason' => null];
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
