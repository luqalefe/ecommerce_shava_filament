<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class StripeService
{
    private \Stripe\StripeClient $client;

    public function __construct()
    {
        $this->client = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Cria um PaymentIntent para processar o pagamento.
     *
     * @param float $amount Valor total em BRL (ex: 150.50)
     * @param array $metadata Dados extras (order_id, etc.)
     * @return array ['client_secret' => '...', 'payment_intent_id' => '...']
     */
    public function createPaymentIntent(float $amount, array $metadata = []): array
    {
        try {
            $paymentIntent = $this->client->paymentIntents->create([
                'amount' => (int) round($amount * 100), // Stripe trabalha em centavos
                'currency' => 'brl',
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            Log::info('Stripe PaymentIntent criado', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'metadata' => $metadata,
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Erro ao criar PaymentIntent Stripe', [
                'error' => $e->getMessage(),
                'amount' => $amount,
            ]);
            throw $e;
        }
    }

    /**
     * Busca o status de um PaymentIntent.
     */
    public function getPaymentIntent(string $paymentIntentId): ?\Stripe\PaymentIntent
    {
        try {
            return $this->client->paymentIntents->retrieve($paymentIntentId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Erro ao buscar PaymentIntent Stripe', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
