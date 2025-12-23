<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMercadoPagoWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mp:test-webhook 
                            {--data-id=12345 : ID do pagamento simulado}
                            {--request-id= : Request ID (gerado automaticamente se não fornecido)}
                            {--invalid : Testar com assinatura inválida}
                            {--expired : Testar com timestamp expirado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a validação do webhook Mercado Pago enviando uma requisição simulada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookSecret = config('services.mercadopago.webhook_secret');
        
        if (empty($webhookSecret)) {
            $this->error('❌ MERCADOPAGO_WEBHOOK_SECRET não está configurada no .env');
            $this->info('Adicione: MERCADOPAGO_WEBHOOK_SECRET=sua_chave_secreta');
            return 1;
        }

        $dataId = $this->option('data-id');
        $requestId = $this->option('request-id') ?: 'test-' . uniqid();
        
        // Timestamp - atual ou expirado para teste
        if ($this->option('expired')) {
            $timestamp = time() - 600; // 10 minutos atrás (além do limite de 5 min)
            $this->warn('⏰ Usando timestamp EXPIRADO (10 min atrás)');
        } else {
            $timestamp = time();
        }

        // Construir o manifest conforme documentação do Mercado Pago
        $manifest = sprintf('id:%s;request-id:%s;ts:%s;', $dataId, $requestId, $timestamp);
        
        // Calcular hash HMAC
        if ($this->option('invalid')) {
            $hash = hash_hmac('sha256', $manifest, 'chave_errada_proposital');
            $this->warn('🔓 Usando assinatura INVÁLIDA');
        } else {
            $hash = hash_hmac('sha256', $manifest, $webhookSecret);
            $this->info('🔐 Usando assinatura VÁLIDA');
        }

        // Montar o header x-signature
        $xSignature = sprintf('ts=%s,v1=%s', $timestamp, $hash);

        $this->info('');
        $this->info('📤 Dados da Requisição:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['data.id', $dataId],
                ['x-request-id', $requestId],
                ['timestamp', $timestamp . ' (' . date('Y-m-d H:i:s', $timestamp) . ')'],
                ['manifest', $manifest],
                ['x-signature', substr($xSignature, 0, 50) . '...'],
            ]
        );

        // URL do webhook local
        $url = config('app.url') . '/api/mercadopago/webhook?data.id=' . $dataId;
        
        $this->info('');
        $this->info("🌐 Enviando para: {$url}");
        $this->info('');

        try {
            $response = Http::withHeaders([
                'x-signature' => $xSignature,
                'x-request-id' => $requestId,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'action' => 'payment.updated',
                'data' => ['id' => $dataId],
            ]);

            $statusCode = $response->status();
            $body = $response->json();

            if ($statusCode === 200) {
                $this->info('✅ Webhook ACEITO (Status 200)');
                $this->info('Resposta: ' . json_encode($body, JSON_PRETTY_PRINT));
            } elseif ($statusCode === 403) {
                $this->error('🚫 Webhook REJEITADO (Status 403 - Forbidden)');
                $this->error('A assinatura foi considerada inválida!');
                $this->info('Resposta: ' . json_encode($body, JSON_PRETTY_PRINT));
            } else {
                $this->warn("⚠️ Resposta inesperada (Status {$statusCode})");
                $this->info('Resposta: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar requisição: ' . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('📋 Verifique os logs em: storage/logs/laravel.log');
        
        return 0;
    }
}
