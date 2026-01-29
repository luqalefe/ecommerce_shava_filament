<?php

namespace Tests\Unit;

use App\Services\FrenetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FrenetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FrenetService $frenetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->frenetService = new FrenetService();
    }

    /**
     * Testa que o cálculo de frete retorna opções válidas para CEP válido
     */
    public function test_calculate_returns_shipping_options_for_valid_cep(): void
    {
        // Mock da resposta da API Frenet
        Http::fake([
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'SEDEX',
                        'ShippingPrice' => 25.50,
                        'DeliveryTime' => 2,
                        'Error' => '',
                    ],
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'PAC',
                        'ShippingPrice' => 15.00,
                        'DeliveryTime' => 7,
                        'Error' => '',
                    ],
                ],
            ], 200),
        ]);

        $items = [
            [
                'sku' => 'SKU-001',
                'quantity' => 1,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Geral',
            ],
        ];

        $result = $this->frenetService->calculate('69900000', 100.00, $items);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // Verifica primeira opção (SEDEX)
        $this->assertEquals('Correios', $result[0]['carrier']);
        $this->assertEquals('SEDEX', $result[0]['service']);
        $this->assertEquals(25.50, $result[0]['price']);
        $this->assertEquals(2, $result[0]['deadline']);
        
        // Verifica segunda opção (PAC)
        $this->assertEquals('Correios', $result[1]['carrier']);
        $this->assertEquals('PAC', $result[1]['service']);
        $this->assertEquals(15.00, $result[1]['price']);
        $this->assertEquals(7, $result[1]['deadline']);
    }

    /**
     * Testa que o serviço retorna array vazio quando a API falha
     */
    public function test_calculate_returns_empty_array_for_api_failure(): void
    {
        // Mock de falha da API
        Http::fake([
            'api.frenet.com.br/*' => Http::response(null, 500),
        ]);

        $items = [
            [
                'sku' => 'SKU-001',
                'quantity' => 1,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Geral',
            ],
        ];

        $result = $this->frenetService->calculate('69900000', 100.00, $items);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Testa que serviços com erro são filtrados da resposta
     */
    public function test_calculate_filters_out_services_with_errors(): void
    {
        Http::fake([
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'SEDEX',
                        'ShippingPrice' => 25.50,
                        'DeliveryTime' => 2,
                        'Error' => '',
                    ],
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'PAC',
                        'ShippingPrice' => 0,
                        'DeliveryTime' => 0,
                        'Error' => 'CEP de destino não atendido',
                    ],
                ],
            ], 200),
        ]);

        $items = [
            [
                'sku' => 'SKU-001',
                'quantity' => 1,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Geral',
            ],
        ];

        $result = $this->frenetService->calculate('69900000', 100.00, $items);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('SEDEX', $result[0]['service']);
    }

    /**
     * Testa que array vazio de serviços é tratado corretamente
     */
    public function test_calculate_handles_empty_services_array(): void
    {
        Http::fake([
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [],
            ], 200),
        ]);

        $items = [
            [
                'sku' => 'SKU-001',
                'quantity' => 1,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Geral',
            ],
        ];

        $result = $this->frenetService->calculate('69900000', 100.00, $items);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Testa que múltiplos itens são incluídos na cotação
     */
    public function test_calculate_includes_multiple_items(): void
    {
        Http::fake([
            'api.frenet.com.br/*' => function ($request) {
                $body = json_decode($request->body(), true);
                
                // Verifica que o payload contém os itens esperados
                $this->assertArrayHasKey('ShippingItemArray', $body);
                $this->assertCount(2, $body['ShippingItemArray']);
                
                return Http::response([
                    'ShippingSevicesArray' => [
                        [
                            'Carrier' => 'Correios',
                            'ServiceDescription' => 'PAC',
                            'ShippingPrice' => 30.00,
                            'DeliveryTime' => 5,
                            'Error' => '',
                        ],
                    ],
                ], 200);
            },
        ]);

        $items = [
            [
                'sku' => 'SKU-001',
                'quantity' => 2,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Geral',
            ],
            [
                'sku' => 'SKU-002',
                'quantity' => 1,
                'weight' => 1.0,
                'height' => 15,
                'width' => 25,
                'length' => 35,
                'category' => 'Geral',
            ],
        ];

        $result = $this->frenetService->calculate('01310100', 200.00, $items);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
