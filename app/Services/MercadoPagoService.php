<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Resources\Preference;
use MercadoPago\Resources\Payment;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected $accessToken;
    protected $preferenceClient;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        
        if (empty($this->accessToken)) {
            throw new \Exception('Mercado Pago Access Token não configurado. Verifique o arquivo .env');
        }

        // Configura o SDK do Mercado Pago (nova API v3.7)
        MercadoPagoConfig::setAccessToken($this->accessToken);
        
        // Inicializa o cliente de preferências
        $this->preferenceClient = new PreferenceClient();
    }

    /**
     * Cria uma preferência de pagamento (Checkout Pro) usando a API de Orders
     * 
     * @param array $cartItems Itens do carrinho
     * @param float $shippingCost Custo do frete
     * @param array $payerData Dados do pagador (user, endereco)
     * @param int $orderId ID do pedido criado no sistema
     * @return array Retorna ['init_point' => url, 'preference_id' => id]
     */
    public function createPreference(array $cartItems, float $shippingCost, array $payerData, int $orderId)
    {
        try {
            // Preparar itens do carrinho
            $items = [];
            foreach ($cartItems as $item) {
                // Validar dados do item
                if (empty($item['name']) || !isset($item['quantity']) || !isset($item['price'])) {
                    throw new \Exception('Item do carrinho inválido: dados incompletos');
                }
                
                $unitPrice = (float) $item['price'];
                if ($unitPrice <= 0) {
                    throw new \Exception('Item do carrinho inválido: preço deve ser maior que zero');
                }
                
                $items[] = [
                    'title' => mb_substr($item['name'], 0, 256), // Limitar tamanho do título
                    'quantity' => max(1, (int) $item['quantity']), // Garantir quantidade mínima
                    'unit_price' => round($unitPrice, 2), // Arredondar para 2 casas decimais
                    'currency_id' => 'BRL',
                ];
            }

            // Adicionar frete como item separado
            if ($shippingCost > 0) {
                $items[] = [
                    'title' => 'Frete',
                    'quantity' => 1,
                    'unit_price' => round((float) $shippingCost, 2),
                    'currency_id' => 'BRL',
                ];
            }
            
            // Validar que há pelo menos um item
            if (empty($items)) {
                throw new \Exception('Não é possível criar preferência sem itens');
            }

            // Configurar pagador
            $payer = [
                'name' => $payerData['name'],
                'email' => $payerData['email'],
            ];
            
            // Adicionar CPF se disponível
            if (!empty($payerData['cpf'])) {
                $cpf = preg_replace('/\D/', '', $payerData['cpf']);
                if (strlen($cpf) === 11) {
                    $payer['identification'] = [
                        'type' => 'CPF',
                        'number' => $cpf
                    ];
                }
            }

            // Adicionar telefone se disponível
            if (!empty($payerData['phone'])) {
                $phone = preg_replace('/\D/', '', $payerData['phone']);
                if (strlen($phone) >= 10) {
                    $payer['phone'] = [
                        'area_code' => substr($phone, 0, 2),
                        'number' => substr($phone, 2)
                    ];
                }
            }

            // Adicionar endereço se disponível
            if (!empty($payerData['address'])) {
                $payer['address'] = [
                    'street_name' => $payerData['address']['street'] ?? '',
                    'street_number' => (int) ($payerData['address']['number'] ?? 0),
                    'zip_code' => preg_replace('/\D/', '', $payerData['address']['zip_code'] ?? ''),
                ];
            }

            // Garantir URLs absolutas para o Mercado Pago
            $rootUrl = config('app.url');
            
            // Validação de Segurança: Rejeitar localhost/127.0.0.1
            if (empty($rootUrl)) {
                throw new \Exception('APP_URL não está configurado no .env. Configure com a URL do túnel (ex: https://seu-tunel.serveo.net)');
            }
            
            $rootUrl = trim($rootUrl);
            
            // Verificar se contém localhost ou 127.0.0.1 (Mercado Pago rejeita)
            if (preg_match('/localhost|127\.0\.0\.1/i', $rootUrl)) {
                Log::error('APP_URL contém localhost/127.0.0.1 - Mercado Pago rejeitará', [
                    'app_url' => $rootUrl,
                    'message' => 'Configure APP_URL com a URL do túnel público (Serveo, ngrok, etc)'
                ]);
                throw new \Exception('APP_URL não pode ser localhost ou 127.0.0.1. Configure com a URL do túnel público (ex: https://seu-tunel.serveo.net)');
            }
            
            // Remover barra final do rootUrl se existir para evitar //
            $rootUrl = rtrim($rootUrl, '/');
            
            // Construir URLs explicitamente concatenando com APP_URL
            // Usando as rotas definidas em routes/web.php
            $backUrls = [
                'success' => $rootUrl . '/checkout/pedido-realizado',
                'failure' => $rootUrl . '/checkout?status=failure',
                'pending' => $rootUrl . '/checkout/pedido-realizado?status=pending',
            ];
            
            // Validar que todas as URLs são válidas
            foreach ($backUrls as $key => $url) {
                if (empty($url)) {
                    throw new \Exception("URL de retorno '{$key}' está vazia. Verifique a configuração APP_URL.");
                }
                
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    throw new \Exception("URL de retorno '{$key}' é inválida: {$url}");
                }
                
                // Garantir que é HTTPS (Mercado Pago prefere)
                if (!preg_match('/^https:/i', $url)) {
                    Log::warning("URL de retorno '{$key}' não usa HTTPS: {$url}");
                }
            }
            
            // Montar array da preferência
            $preferenceData = [
                'items' => $items,
                'payer' => $payer,
                'back_urls' => $backUrls,
                'auto_return' => 'approved',
                'external_reference' => (string) $orderId,
            ];
            
            // Log detalhado do payload antes de enviar
            Log::info('Payload completo para Mercado Pago', [
                'order_id' => $orderId,
                'app_url' => $rootUrl,
                'back_urls' => $backUrls,
                'items_count' => count($items),
                'payer_email' => $payerData['email'] ?? 'N/A',
            ]);
            
            // Log específico das back_urls (conforme solicitado)
            Log::info('Payload Back URLs:', $preferenceData['back_urls'] ?? []);

            Log::info('Criando preferência Mercado Pago', [
                'order_id' => $orderId,
                'items_count' => count($items),
                'preference_data' => $preferenceData,
            ]);

            // Criar a preferência usando o cliente
            $preference = $this->preferenceClient->create($preferenceData);

            if (!$preference->id) {
                Log::error('Preferência não criada - resposta completa', [
                    'preference' => json_encode($preference),
                    'order_id' => $orderId
                ]);
                throw new \Exception('Falha ao criar preferência do Mercado Pago. ID não retornado.');
            }

            // Obter URL de checkout
            $checkoutUrl = $preference->init_point ?? null;
            
            if (empty($checkoutUrl)) {
                Log::error('URL de checkout não retornada', [
                    'preference_id' => $preference->id,
                    'preference' => json_encode($preference),
                ]);
                throw new \Exception('URL de checkout do Mercado Pago não foi retornada.');
            }

            Log::info('Preferência Mercado Pago criada com sucesso', [
                'preference_id' => $preference->id,
                'init_point' => $checkoutUrl,
                'order_id' => $orderId
            ]);
            
            return [
                'preference_id' => $preference->id,
                'init_point' => $checkoutUrl,
            ];

        } catch (MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $errorContent = $apiResponse->getContent();
            
            // Extrair mensagens de erro mais detalhadas
            $errorMessages = [];
            if (isset($errorContent['message'])) {
                $errorMessages[] = $errorContent['message'];
            }
            if (isset($errorContent['error'])) {
                $errorMessages[] = $errorContent['error'];
            }
            if (isset($errorContent['cause']) && is_array($errorContent['cause'])) {
                foreach ($errorContent['cause'] as $cause) {
                    if (isset($cause['description'])) {
                        $errorMessages[] = $cause['description'];
                    }
                    if (isset($cause['code'])) {
                        $errorMessages[] = "Código: " . $cause['code'];
                    }
                }
            }
            
            $detailedError = !empty($errorMessages) 
                ? implode(' | ', $errorMessages) 
                : $e->getMessage();
            
            Log::error('Erro da API Mercado Pago', [
                'error' => $e->getMessage(),
                'status' => $e->getStatusCode(),
                'content' => $errorContent,
                'detailed_error' => $detailedError,
            ]);
            
            throw new \Exception('Erro ao criar preferência de pagamento: ' . $detailedError);
        } catch (\Exception $e) {
            Log::error('Erro ao criar preferência Mercado Pago', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro ao criar preferência de pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Busca informações de um pagamento pelo ID
     * 
     * @param string $paymentId ID do pagamento no Mercado Pago
     * @return array|null
     */
    public function getPayment(string $paymentId)
    {
        try {
            $paymentClient = new PaymentClient();
            $payment = $paymentClient->get($paymentId);
            
            if ($payment) {
                return [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail ?? null,
                    'external_reference' => $payment->external_reference ?? null,
                    'transaction_amount' => $payment->transaction_amount ?? null,
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar pagamento Mercado Pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

