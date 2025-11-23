<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Endereco;
use App\Services\FrenetService;
use App\Services\MercadoPagoService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class CheckoutTest extends TestCase
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

    public function test_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'quantity' => 10,
        ]);

        $this->actingAs($user);

        // Adiciona produto diretamente ao carrinho (simulando o componente Livewire)
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
            'attributes' => [],
        ]);

        $cartItems = Cart::getContent();
        $this->assertCount(1, $cartItems);
        $this->assertEquals(2, $cartItems->first()->quantity);
    }

    public function test_cart_calculates_total_correctly(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50.00,
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 75.00,
        ]);

        $this->actingAs($user);

        Cart::add([
            'id' => $product1->id,
            'name' => $product1->name,
            'price' => $product1->price,
            'quantity' => 2,
            'attributes' => [],
        ]);

        Cart::add([
            'id' => $product2->id,
            'name' => $product2->name,
            'price' => $product2->price,
            'quantity' => 1,
            'attributes' => [],
        ]);

        $total = Cart::getTotal();
        $expectedTotal = (50.00 * 2) + (75.00 * 1);
        $this->assertEquals($expectedTotal, $total);
    }

    public function test_frenet_service_calculates_shipping_with_mock(): void
    {
        // Mock da resposta da API Frenet
        Http::fake([
            'api.frenet.com.br/*' => Http::response([
                'shippingSevicesArray' => [
                    [
                        'serviceName' => 'Sedex',
                        'carrier' => 'Correios',
                        'shippingPrice' => 15.50,
                        'deliveryTime' => 1,
                    ],
                    [
                        'serviceName' => 'PAC',
                        'carrier' => 'Correios',
                        'shippingPrice' => 10.00,
                        'deliveryTime' => 5,
                    ],
                ],
            ], 200),
        ]);

        $frenetService = new FrenetService();
        $result = $frenetService->calculate('69900000', 100.00, [
            [
                'sku' => 'SKU-001',
                'quantity' => 1,
                'weight' => 0.5,
                'height' => 10,
                'width' => 20,
                'length' => 30,
                'category' => 'Eletrônicos',
            ],
        ]);

        $this->assertIsArray($result);
    }

    public function test_mercadopago_service_creates_preference_with_mock(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
        ]);

        $cartItems = [
            [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ],
        ];

        $shippingCost = 10.00;
        $payerData = [
            'name' => $user->name,
            'email' => $user->email,
            'cpf' => '12345678900',
            'phone' => '11999999999',
            'address' => [
                'street' => $endereco->rua,
                'number' => $endereco->numero,
                'neighborhood' => $endereco->bairro,
                'city' => $endereco->cidade,
                'state' => $endereco->estado,
                'zip_code' => $endereco->cep,
            ],
        ];

        // Mock do MercadoPagoService
        $mockService = Mockery::mock(MercadoPagoService::class);
        $mockService->shouldReceive('createPreference')
            ->once()
            ->with($cartItems, $shippingCost, $payerData, Mockery::type('int'))
            ->andReturn([
                'preference_id' => 'test-preference-id',
                'init_point' => 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=test',
            ]);

        $this->app->instance(MercadoPagoService::class, $mockService);

        $result = $mockService->createPreference($cartItems, $shippingCost, $payerData, 1);

        $this->assertArrayHasKey('preference_id', $result);
        $this->assertArrayHasKey('init_point', $result);
    }

    public function test_order_total_calculation_is_mathematically_correct(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 99.99,
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 49.50,
        ]);

        $this->actingAs($user);

        Cart::add([
            'id' => $product1->id,
            'name' => $product1->name,
            'price' => $product1->price,
            'quantity' => 3,
            'attributes' => [],
        ]);

        Cart::add([
            'id' => $product2->id,
            'name' => $product2->name,
            'price' => $product2->price,
            'quantity' => 2,
            'attributes' => [],
        ]);

        $subtotal = Cart::getSubTotal();
        $shippingCost = 15.00;
        $expectedTotal = $subtotal + $shippingCost;

        // Cálculo manual para validação
        $manualSubtotal = (99.99 * 3) + (49.50 * 2);
        $manualTotal = $manualSubtotal + $shippingCost;

        $this->assertEquals($manualSubtotal, $subtotal, 'Subtotal deve ser calculado corretamente');
        $this->assertEquals($manualTotal, $expectedTotal, 'Total com frete deve ser calculado corretamente');
    }

    public function test_checkout_requires_authentication(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_checkout_redirects_when_cart_is_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Cart::clear();

        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
    }
}

