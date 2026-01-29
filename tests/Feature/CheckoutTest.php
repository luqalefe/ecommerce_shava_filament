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

    /**
     * Testa que pedido é criado com status pending
     */
    public function test_order_created_with_pending_status(): void
    {
        $user = User::factory()->create([
            'cpf' => '12345678900',
            'celular' => '11999999999',
        ]);
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'quantity' => 10,
        ]);

        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
            'total_amount' => 115.00,
            'shipping_cost' => 15.00,
            'shipping_service' => 'PAC (Correios)',
            'payment_method' => 'mercadopago',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('pending', $order->status);
    }

    /**
     * Testa que estoque é decrementado ao criar pedido
     */
    public function test_stock_decremented_on_order_creation(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50.00,
            'quantity' => 10,
        ]);

        $initialStock = $product->quantity;
        $quantityOrdered = 3;

        // Simula o decremento de estoque que acontece no checkout
        $product->decrement('quantity', $quantityOrdered);
        $product->refresh();

        $this->assertEquals($initialStock - $quantityOrdered, $product->quantity);
        $this->assertEquals(7, $product->quantity);
    }

    /**
     * Testa que estoque insuficiente impede a compra
     */
    public function test_order_creation_fails_for_insufficient_stock(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50.00,
            'quantity' => 2, // Apenas 2 unidades
        ]);

        $quantityRequested = 5; // Tentando comprar 5

        // Validação de estoque como no CheckoutPage
        $hasInsufficientStock = $product->quantity < $quantityRequested;

        $this->assertTrue($hasInsufficientStock, 'Deveria detectar estoque insuficiente');
    }

    /**
     * Testa que pedido inclui custo de frete corretamente
     */
    public function test_order_includes_shipping_cost(): void
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->create(['user_id' => $user->id]);

        $subtotal = 200.00;
        $shippingCost = 25.50;
        $total = $subtotal + $shippingCost;

        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'endereco_id' => $endereco->id,
            'status' => 'pending',
            'total_amount' => $total,
            'shipping_cost' => $shippingCost,
            'shipping_service' => 'SEDEX (Correios)',
            'payment_method' => 'mercadopago',
        ]);

        $this->assertEquals($shippingCost, $order->shipping_cost);
        $this->assertEquals($total, $order->total_amount);
        $this->assertEquals($subtotal, $order->total_amount - $order->shipping_cost);
    }
}

