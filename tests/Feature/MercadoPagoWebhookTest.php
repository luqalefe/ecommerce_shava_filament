<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Endereco;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class MercadoPagoWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configura o webhook secret para os testes
        Config::set('services.mercadopago.webhook_secret', 'test-secret-key-12345');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Gera uma assinatura HMAC válida para webhook
     */
    protected function generateValidSignature(string $dataId, string $requestId, int $timestamp): string
    {
        $manifest = sprintf('id:%s;request-id:%s;ts:%s;', $dataId, $requestId, $timestamp);
        $hash = hash_hmac('sha256', $manifest, 'test-secret-key-12345');
        return "ts={$timestamp},v1={$hash}";
    }

    /**
     * Registra um mock do MercadoPagoService no container
     */
    protected function mockMercadoPagoService(array $paymentData): MockInterface
    {
        $mock = Mockery::mock(MercadoPagoService::class);
        $mock->shouldReceive('getPayment')->andReturn($paymentData);
        
        $this->app->bind(MercadoPagoService::class, function () use ($mock) {
            return $mock;
        });
        
        return $mock;
    }

    /**
     * Testa que webhook atualiza pedido para processing quando pagamento aprovado
     */
    public function test_webhook_updates_order_to_processing_on_approved_payment(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
        ]);

        $this->mockMercadoPagoService([
            'id' => '12345',
            'status' => 'approved',
            'external_reference' => (string) $order->id,
        ]);

        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('12345', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook?data.id=12345', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);
        
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    /**
     * Testa que webhook atualiza pedido para cancelled quando pagamento rejeitado
     */
    public function test_webhook_updates_order_to_cancelled_on_rejected_payment(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
        ]);

        $this->mockMercadoPagoService([
            'id' => '12345',
            'status' => 'rejected',
            'external_reference' => (string) $order->id,
        ]);

        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('12345', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook?data.id=12345', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    /**
     * Testa que webhook retorna ok quando pagamento não tem order_id
     */
    public function test_webhook_handles_payment_without_order_id(): void
    {
        $this->mockMercadoPagoService([
            'id' => '12345',
            'status' => 'approved',
            'external_reference' => null,
        ]);

        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('12345', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook?data.id=12345', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'ok');
        $response->assertJsonPath('message', 'No order ID in payment');
    }

    /**
     * Testa que webhook retorna erro quando pedido não existe
     */
    public function test_webhook_handles_nonexistent_order(): void
    {
        $this->mockMercadoPagoService([
            'id' => '12345',
            'status' => 'approved',
            'external_reference' => '99999',
        ]);

        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('12345', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook?data.id=12345', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('message', 'Order not found');
    }

    /**
     * Testa mapeamento de status: pending continua pending
     */
    public function test_webhook_keeps_order_pending_on_pending_payment(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
        ]);

        $this->mockMercadoPagoService([
            'id' => '12345',
            'status' => 'pending',
            'external_reference' => (string) $order->id,
        ]);

        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('12345', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook?data.id=12345', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    /**
     * Testa que HMAC é gerado corretamente com o manifest esperado
     */
    public function test_hmac_signature_generation(): void
    {
        $dataId = '12345';
        $requestId = 'test-request-id';
        $timestamp = 1704067200;
        
        $manifest = sprintf('id:%s;request-id:%s;ts:%s;', $dataId, $requestId, $timestamp);
        $expectedHash = hash_hmac('sha256', $manifest, 'test-secret-key-12345');
        
        $generatedSignature = $this->generateValidSignature($dataId, $requestId, $timestamp);
        
        $this->assertEquals("ts={$timestamp},v1={$expectedHash}", $generatedSignature);
    }

    /**
     * Testa que webhook retorna ok quando não há payment ID
     */
    public function test_webhook_handles_missing_payment_id(): void
    {
        $timestamp = time();
        $requestId = 'test-request-id';
        $signature = $this->generateValidSignature('', $requestId, $timestamp);

        $response = $this->postJson('/api/mercadopago/webhook', [], [
            'x-signature' => $signature,
            'x-request-id' => $requestId,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'ok');
        $response->assertJsonPath('message', 'No payment ID provided');
    }
}
