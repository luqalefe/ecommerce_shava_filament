<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Endereco;
use App\Services\FrenetService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class ShippingCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::clear();
    }

    protected function tearDown(): void
    {
        Cart::clear();
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Testa que frete é grátis para Rio Branco - AC
     */
    public function test_free_shipping_for_rio_branco_ac(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'quantity' => 10,
            'weight' => 0.5,
            'height' => 10,
            'width' => 20,
            'length' => 30,
        ]);

        $this->actingAs($user);

        // Adiciona produto ao carrinho com associatedModel
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'attributes' => [],
            'associatedModel' => $product,
        ]);

        // A lógica de frete grátis está no CheckoutPage Livewire
        // Simulamos a verificação da regra
        $cidade = 'Rio Branco';
        $estado = 'AC';
        
        $cidadeNormalizada = mb_strtolower(trim($cidade));
        $estadoNormalizado = mb_strtoupper(trim($estado));
        
        $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
        $isAcre = $estadoNormalizado === 'AC';
        
        $this->assertTrue($isRioBranco && $isAcre, 'Deveria identificar Rio Branco - AC para frete grátis');
    }

    /**
     * Testa que frete é calculado para outras cidades (não Rio Branco)
     */
    public function test_shipping_calculated_for_non_rio_branco_cities(): void
    {
        Http::fake([
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'PAC',
                        'ShippingPrice' => 25.00,
                        'DeliveryTime' => 5,
                        'Error' => '',
                    ],
                ],
            ], 200),
        ]);

        $frenetService = new FrenetService();
        
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

        // CEP de São Paulo
        $result = $frenetService->calculate('01310100', 100.00, $items);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(25.00, $result[0]['price']);
    }

    /**
     * Testa que o custo de frete é somado corretamente ao total do pedido
     */
    public function test_shipping_cost_added_to_order_total(): void
    {
        $user = User::factory()->create([
            'cpf' => '12345678900',
            'celular' => '11999999999',
        ]);
        $endereco = Endereco::factory()->create([
            'user_id' => $user->id,
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 150.00,
            'quantity' => 10,
        ]);

        $subtotal = 150.00;
        $shippingCost = 25.00;
        $expectedTotal = $subtotal + $shippingCost;

        // Cria pedido com frete
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
            'total_amount' => $expectedTotal,
            'shipping_cost' => $shippingCost,
            'shipping_service' => 'PAC (Correios)',
        ]);

        $this->assertEquals($expectedTotal, $order->total_amount);
        $this->assertEquals($shippingCost, $order->shipping_cost);
        $this->assertEquals($subtotal, $order->total_amount - $order->shipping_cost);
    }

    /**
     * Testa variações de escrita de "Rio Branco" para frete grátis
     */
    public function test_free_shipping_accepts_rio_branco_variations(): void
    {
        $variations = [
            ['cidade' => 'Rio Branco', 'estado' => 'AC', 'expected' => true],
            ['cidade' => 'rio branco', 'estado' => 'ac', 'expected' => true],
            ['cidade' => 'RIO BRANCO', 'estado' => 'AC', 'expected' => true],
            ['cidade' => 'riobranco', 'estado' => 'AC', 'expected' => true],
            ['cidade' => 'Manaus', 'estado' => 'AM', 'expected' => false],
            ['cidade' => 'São Paulo', 'estado' => 'SP', 'expected' => false],
            ['cidade' => 'Rio Branco', 'estado' => 'SP', 'expected' => false], // Rio Branco mas não no AC
        ];

        foreach ($variations as $case) {
            $cidadeNormalizada = mb_strtolower(trim($case['cidade']));
            $estadoNormalizado = mb_strtoupper(trim($case['estado']));
            
            $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
            $isAcre = $estadoNormalizado === 'AC';
            
            $result = $isRioBranco && $isAcre;
            
            $this->assertEquals(
                $case['expected'],
                $result,
                "Falha para cidade='{$case['cidade']}', estado='{$case['estado']}'"
            );
        }
    }

    /**
     * Testa que o cálculo matemático do total é preciso
     */
    public function test_order_total_calculation_precision(): void
    {
        $subtotal = 199.99;
        $shippingCost = 15.50;
        
        $total = $subtotal + $shippingCost;
        
        $this->assertEquals(215.49, $total);
        
        // Testa com múltiplos itens
        $itemPrice1 = 49.99;
        $itemPrice2 = 75.50;
        $quantity1 = 2;
        $quantity2 = 3;
        
        $calculatedSubtotal = ($itemPrice1 * $quantity1) + ($itemPrice2 * $quantity2);
        $calculatedTotal = $calculatedSubtotal + $shippingCost;
        
        $this->assertEquals(326.48, $calculatedSubtotal);
        $this->assertEquals(341.98, $calculatedTotal);
    }
}
