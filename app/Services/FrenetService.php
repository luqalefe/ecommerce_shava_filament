<?php

namespace App\Services;

use Illuminate\Support\Facades\Http; // <-- Importante!
use Illuminate\Support\Facades\Log; // Para registrar erros

class FrenetService
{
    protected $baseUrl;
    protected $token;
    protected $sellerPostcode;

    public function __construct()
    {
        // Pega os dados do config/services.php
        $this->baseUrl = 'https://api.frenet.com.br'; // URL base da API
        $this->token = config('services.frenet.token');
        $this->sellerPostcode = config('services.frenet.seller_postcode');
        
        // Log temporário para debug (remova depois)
        Log::info('FrenetService inicializado', [
            'token_length' => $this->token ? strlen($this->token) : 0,
            'token_preview' => $this->token ? substr($this->token, 0, 10) . '...' : 'NULL',
            'seller_postcode' => $this->sellerPostcode,
        ]);
    }

    /**
     * Calcula o frete.
     *
     * @param string $recipientPostcode CEP de destino
     * @param float $totalValue Valor total (declarado) dos produtos
     * @param array $items Array de itens para cotação
     * @return array
     */
    public function calculate(string $recipientPostcode, float $totalValue, array $items)
    {
        // 1. Formatar os itens para o padrão JSON da API Frenet
        $shippingItems = [];
        foreach ($items as $item) {
            $shippingItems[] = [
                "SKU" => $item['sku'],
                "Quantity" => (int) $item['quantity'],
                "Weight" => (float) $item['weight'],     // Peso em kg
                "Height" => (float) $item['height'],     // Altura em cm
                "Width" => (float) $item['width'],      // Largura em cm
                "Length" => (float) $item['length'],     // Comprimento em cm
                "Category" => $item['category'],
            ];
        }

        // 2. Montar o corpo (payload) da requisição
        $payload = [
            'SellerCEP' => $this->sellerPostcode,
            'RecipientCEP' => $recipientPostcode,
            'ShipmentInvoiceValue' => $totalValue,
            'ShippingItemArray' => $shippingItems,
            'RecipientCountry' => 'BR',
        ];

        try {
            // 3. Fazer a chamada HTTP
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'token' => $this->token, // O token vai no cabeçalho
            ])->post($this->baseUrl . '/shipping/quote', $payload);

            // 4. Verificar se a requisição falhou
            if (!$response->successful()) {
                Log::error('Falha na API Frenet', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return []; // Retorna vazio em caso de falha
            }
            
            // 5. Processar a resposta de sucesso
            $data = $response->json();

            // A resposta da Frenet vem dentro de 'ShippingSevicesArray'
            $services = $data['ShippingSevicesArray'] ?? []; 
            
            $formattedServices = [];
            foreach ($services as $service) {
                // 'Error' é uma string vazia se não houver erro
                if (empty($service['Error'])) {
                    $formattedServices[] = [
                        'carrier' => $service['Carrier'],
                        'service' => $service['ServiceDescription'],
                        'price'   => (float) $service['ShippingPrice'],
                        'deadline' => (int) $service['DeliveryTime'], // Prazo em dias
                    ];
                }
            }
            
            return $formattedServices;

        } catch (\Exception $e) {
            Log::error('Exceção ao chamar API Frenet: ' . $e->getMessage());
            return [];
        }
    }
}