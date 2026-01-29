<?php

namespace Tests\Feature;

use App\Livewire\CheckoutPage;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Services\FrenetService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class CheckoutLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::clear();
        
        // Criar usuário e produto base para os testes
        $this->user = User::factory()->create([
            'cpf' => '12345678900',
            'celular' => '11999999999',
        ]);
        
        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'quantity' => 10,
            'weight' => 0.5,
            'height' => 10,
            'width' => 20,
            'length' => 30,
        ]);
    }

    protected function tearDown(): void
    {
        Cart::clear();
        Mockery::close();
        parent::tearDown();
    }

    protected function addProductToCart(): void
    {
        Cart::add([
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => $this->product->price,
            'quantity' => 1,
            'attributes' => [],
            'associatedModel' => $this->product,
        ]);
    }

    /**
     * Testa que campos de endereço são preenchidos após busca de CEP
     */
    public function test_address_fields_are_filled_after_cep_search(): void
    {
        $this->addProductToCart();
        
        // Mock da API BrasilAPI
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Rua das Flores',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
            ], 200),
        ]);

        Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '01310100')
            ->call('searchAddress', '01310100')
            ->assertSet('rua', 'Rua das Flores')
            ->assertSet('bairro', 'Centro')
            ->assertSet('cidade', 'São Paulo')
            ->assertSet('estado', 'SP');
    }

    /**
     * Testa que opções de frete são carregadas após busca de CEP
     */
    public function test_shipping_options_update_after_cep_change(): void
    {
        $this->addProductToCart();
        
        // Mock da BrasilAPI
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Av. Paulista',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ], 200),
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'PAC',
                        'ShippingPrice' => 20.00,
                        'DeliveryTime' => 5,
                        'Error' => '',
                    ],
                ],
            ], 200),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '01310100')
            ->call('searchAddress', '01310100')
            ->assertSet('cidade', 'São Paulo');
        
        // Verify shipping options were populated
        $this->assertNotEmpty($component->get('shippingOptions'), 'Shipping options should not be empty after CEP search');
    }


    /**
     * Testa frete grátis para Rio Branco - AC
     */
    public function test_free_shipping_for_rio_branco(): void
    {
        $this->addProductToCart();
        
        // Mock da BrasilAPI retornando Rio Branco
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Rua Principal',
                'neighborhood' => 'Centro',
                'city' => 'Rio Branco',
                'state' => 'AC',
            ], 200),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '69900000')
            ->call('searchAddress', '69900000');
        
        // Deve ter frete grátis automaticamente
        $component->assertSet('cidade', 'Rio Branco')
            ->assertSet('estado', 'AC')
            ->assertSet('shippingCost', 0);
    }

    /**
     * Testa que o total atualiza quando frete é selecionado
     */
    public function test_total_updates_when_shipping_selected(): void
    {
        $this->addProductToCart();
        
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Av. Paulista',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ], 200),
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'PAC',
                        'ShippingPrice' => 15.00,
                        'DeliveryTime' => 5,
                        'Error' => '',
                    ],
                    [
                        'Carrier' => 'Correios',
                        'ServiceDescription' => 'SEDEX',
                        'ShippingPrice' => 30.00,
                        'DeliveryTime' => 2,
                        'Error' => '',
                    ],
                ],
            ], 200),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '01310100')
            ->call('searchAddress', '01310100');
        
        // Seleciona primeira opção de frete (PAC - R$15)
        $component->call('selectShipping', 0)
            ->assertSet('shippingCost', 15.00)
            ->assertSet('selectedShipping', 0);
        
        // Muda para segunda opção (SEDEX - R$30)
        $component->call('selectShipping', 1)
            ->assertSet('shippingCost', 30.00)
            ->assertSet('selectedShipping', 1);
    }

    /**
     * Testa validação de campos obrigatórios ao finalizar pedido
     */
    public function test_place_order_validates_required_fields(): void
    {
        $this->addProductToCart();

        Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->call('placeOrder')
            ->assertHasErrors(['rua', 'numero', 'bairro', 'cidade', 'estado', 'cep', 'shippingService']);
    }

    /**
     * Testa que usuário sem CPF precisa informá-lo
     */
    public function test_user_without_cpf_must_provide_it(): void
    {
        $userWithoutCpf = User::factory()->create([
            'cpf' => null,
            'celular' => '11999999999',
        ]);
        
        $this->addProductToCart();

        $component = Livewire::actingAs($userWithoutCpf)
            ->test(CheckoutPage::class);
        
        $component->assertSet('needsCpf', true);
    }

    /**
     * Testa que usuário com CPF não precisa informá-lo novamente
     */
    public function test_user_with_cpf_does_not_need_to_provide_it(): void
    {
        $this->addProductToCart();

        $component = Livewire::actingAs($this->user)
            ->test(CheckoutPage::class);
        
        $component->assertSet('needsCpf', false);
    }

    /**
     * Testa toggle do resumo mobile
     */
    public function test_toggle_summary_works(): void
    {
        $this->addProductToCart();

        Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->assertSet('summaryExpanded', false)
            ->call('toggleSummary')
            ->assertSet('summaryExpanded', true)
            ->call('toggleSummary')
            ->assertSet('summaryExpanded', false);
    }

    /**
     * Testa fallback para ViaCEP quando BrasilAPI falha
     */
    public function test_fallback_to_viacep_when_brasilapi_fails(): void
    {
        $this->addProductToCart();
        
        // Mock: BrasilAPI falha, ViaCEP funciona
        Http::fake([
            'brasilapi.com.br/*' => Http::response(null, 500),
            'viacep.com.br/*' => Http::response([
                'logradouro' => 'Rua Teste',
                'bairro' => 'Bairro Teste',
                'localidade' => 'Cidade Teste',
                'uf' => 'SP',
                'complemento' => '',
            ], 200),
            'api.frenet.com.br/*' => Http::response([
                'ShippingSevicesArray' => [],
            ], 200),
        ]);

        Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '01310100')
            ->call('searchAddress', '01310100')
            ->assertSet('rua', 'Rua Teste')
            ->assertSet('cidade', 'Cidade Teste');
    }

    /**
     * Testa erro para CEP não encontrado
     */
    public function test_error_shown_for_invalid_cep(): void
    {
        $this->addProductToCart();
        
        // Mock: Ambas APIs retornam erro
        Http::fake([
            'brasilapi.com.br/*' => Http::response(null, 404),
            'viacep.com.br/*' => Http::response([
                'erro' => true,
            ], 200),
        ]);

        Livewire::actingAs($this->user)
            ->test(CheckoutPage::class)
            ->set('cep', '00000000')
            ->call('searchAddress', '00000000')
            ->assertHasErrors('cep');
    }

    /**
     * Testa que produtos da categoria CNPJ são bloqueados para usuários PF
     */
    public function test_cnpj_products_blocked_for_pf_users(): void
    {
        // Criar categoria CNPJ
        $cnpjCategory = Category::factory()->create(['slug' => 'cnpj', 'name' => 'Produtos CNPJ']);
        
        // Criar produto na categoria CNPJ
        $cnpjProduct = Product::factory()->create([
            'category_id' => $cnpjCategory->id,
            'price' => 500.00,
            'quantity' => 10,
            'weight' => 0.5,
            'height' => 10,
            'width' => 20,
            'length' => 30,
        ]);
        
        // Usuário PF (Pessoa Física)
        $pfUser = User::factory()->create([
            'user_type' => 'pf',
            'cpf' => '12345678900',
            'celular' => '11999999999',
        ]);
        
        // Adicionar produto CNPJ ao carrinho
        Cart::add([
            'id' => $cnpjProduct->id,
            'name' => $cnpjProduct->name,
            'price' => $cnpjProduct->price,
            'quantity' => 1,
            'attributes' => [],
            'associatedModel' => $cnpjProduct,
        ]);

        // Mock APIs
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Rua Teste', 
                'neighborhood' => 'Centro',
                'city' => 'Rio Branco', // Frete grátis para simplificar
                'state' => 'AC',
            ], 200),
        ]);

        Livewire::actingAs($pfUser)
            ->test(CheckoutPage::class)
            ->set('cep', '69900000')
            ->call('searchAddress', '69900000')
            ->set('numero', '123')
            ->call('selectShipping', 0)
            ->call('placeOrder')
            ->assertHasErrors('payment'); // Deve bloquear com erro
    }

    /**
     * Testa que produtos da categoria CNPJ são permitidos para usuários PJ
     */
    public function test_cnpj_products_allowed_for_pj_users(): void
    {
        // Criar categoria CNPJ
        $cnpjCategory = Category::factory()->create(['slug' => 'cnpj', 'name' => 'Produtos CNPJ']);
        
        // Criar produto na categoria CNPJ
        $cnpjProduct = Product::factory()->create([
            'category_id' => $cnpjCategory->id,
            'price' => 500.00,
            'quantity' => 10,
            'weight' => 0.5,
            'height' => 10,
            'width' => 20,
            'length' => 30,
        ]);
        
        // Usuário PJ (Pessoa Jurídica)
        $pjUser = User::factory()->create([
            'user_type' => 'pj',
            'cnpj' => '12345678000190',
            'razao_social' => 'Empresa Teste LTDA',
            'celular' => '11999999999',
        ]);
        
        // Adicionar produto CNPJ ao carrinho
        Cart::add([
            'id' => $cnpjProduct->id,
            'name' => $cnpjProduct->name,
            'price' => $cnpjProduct->price,
            'quantity' => 1,
            'attributes' => [],
            'associatedModel' => $cnpjProduct,
        ]);

        // Mock APIs
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                'street' => 'Rua Teste', 
                'neighborhood' => 'Centro',
                'city' => 'Rio Branco',
                'state' => 'AC',
            ], 200),
        ]);

        // Não deve ter erro de 'payment' relacionado a CNPJ
        // (pode ter outros erros de validação, mas não o de restrição CNPJ)
        Livewire::actingAs($pjUser)
            ->test(CheckoutPage::class)
            ->set('cep', '69900000')
            ->call('searchAddress', '69900000')
            ->set('numero', '123')
            ->call('selectShipping', 0)
            ->call('placeOrder')
            // Usuário PJ não deve ver erro de restrição CNPJ
            ->assertDontSee('exclusivo para clientes Pessoa Jurídica');
    }
}
