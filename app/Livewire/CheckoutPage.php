<?php

namespace App\Livewire;

use App\Services\FrenetService;
use App\Models\Endereco;
use App\Models\Order;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CheckoutPage extends Component
{
    public $rua = '';
    public $numero = '';
    public $complemento = '';
    public $cidade = '';
    public $estado = '';
    public $cep = '';
    
    public $shippingOptions = [];
    public $selectedShipping = null;
    public $shippingCost = 0;
    public $shippingService = '';
    
    public $paymentMethod = 'pix';
    
    public $loading = false;
    
    protected $frenetService;

    public function boot(FrenetService $frenetService)
    {
        $this->frenetService = $frenetService;
    }

    public function mount()
    {
        $cartItems = Cart::getContent();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio para finalizar a compra.');
        }
    }

    /**
     * Quando o CEP é atualizado, busca endereço e calcula frete automaticamente
     */
    public function updatedCep()
    {
        $cep = preg_replace('/\D/', '', $this->cep);
        
        if (strlen($cep) === 8) {
            $this->loading = true;
            $this->resetErrorBag('cep');
            
            // Busca endereço e calcula frete em paralelo
            $this->searchAddressAndCalculateShipping($cep);
        } else {
            // Limpa tudo se o CEP não estiver completo
            $this->clearShipping();
        }
    }

    /**
     * Busca endereço e calcula frete automaticamente
     */
    private function searchAddressAndCalculateShipping($cep)
    {
        try {
            // 1. Buscar endereço via ViaCEP
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            $data = $response->json();

            if (isset($data['erro']) || !isset($data['logradouro'])) {
                $this->addError('cep', 'CEP não encontrado.');
                $this->loading = false;
                return;
            }

            // Preenche os campos automaticamente
            $this->rua = $data['logradouro'] ?? '';
            $this->cidade = $data['localidade'] ?? '';
            $this->estado = $data['uf'] ?? '';
            $this->complemento = $data['complemento'] ?? '';

            // 2. Calcula o frete automaticamente
            $this->calculateShipping($cep);
            
        } catch (\Exception $e) {
            $this->addError('cep', 'Erro ao buscar CEP. Tente novamente.');
            $this->loading = false;
            Log::error('Erro ao buscar CEP: ' . $e->getMessage());
        }
    }

    /**
     * Calcula o frete usando FrenetService
     */
    private function calculateShipping($cep)
    {
        $cartItems = Cart::getContent();
        $valorTotal = Cart::getSubTotal();
        
        if ($cartItems->isEmpty()) {
            $this->addError('cep', 'Carrinho vazio');
            $this->loading = false;
            return;
        }

        $itemsParaCotacao = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->associatedModel) {
                $this->addError('cep', "Erro: O item {$cartItem->name} não possui dados de produto para frete.");
                $this->loading = false;
                return;
            }
            
            $product = $cartItem->associatedModel;
            $itemsParaCotacao[] = [
                'sku' => $product->sku ?? $cartItem->id,
                'quantity' => (int) $cartItem->quantity,
                'weight' => (float) ($product->weight ?? 0.1),
                'height' => (float) ($product->height ?? 10),
                'width' => (float) ($product->width ?? 10),
                'length' => (float) ($product->length ?? 10),
                'category' => 'Geral'
            ];
        }

        try {
            $this->shippingOptions = $this->frenetService->calculate(
                $cep,
                $valorTotal,
                $itemsParaCotacao
            );
            
            if (empty($this->shippingOptions)) {
                $this->addError('cep', 'Nenhuma opção de frete encontrada para este CEP.');
            }
            
            $this->loading = false;
        } catch (\Exception $e) {
            $this->addError('cep', 'Erro ao calcular frete. Tente novamente.');
            $this->loading = false;
            Log::error('Erro ao calcular frete: ' . $e->getMessage());
        }
    }

    /**
     * Limpa as opções de frete
     */
    private function clearShipping()
    {
        $this->shippingOptions = [];
        $this->selectedShipping = null;
        $this->shippingCost = 0;
        $this->shippingService = '';
        $this->loading = false;
    }

    /**
     * Seleciona uma opção de frete
     */
    public function selectShipping($index)
    {
        if (!isset($this->shippingOptions[$index])) {
            return;
        }
        
        $option = $this->shippingOptions[$index];
        $this->selectedShipping = $index;
        $this->shippingCost = $option['price'];
        $this->shippingService = $option['service'] . ' (' . $option['carrier'] . ')';
    }

    /**
     * Finaliza o pedido e cria a cobrança na Abacate Pay
     */
    public function placeOrder()
    {
        $this->validate([
            'rua' => 'required|string|min:3',
            'numero' => 'required|string',
            'cidade' => 'required|string|min:2',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
            'shippingService' => 'required|string',
            'shippingCost' => 'required|numeric|min:0',
            'paymentMethod' => 'required|string|in:pix',
        ], [
            'rua.required' => 'O campo rua é obrigatório.',
            'numero.required' => 'O campo número é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'estado.required' => 'O campo estado é obrigatório.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'shippingService.required' => 'Por favor, selecione uma opção de frete.',
            'shippingCost.required' => 'O custo do frete não foi selecionado.',
        ]);

        $user = Auth::user();
        $cartItems = Cart::getContent();
        $cartSubTotal = Cart::getSubTotal();
        $shippingCost = (float) $this->shippingCost;
        $totalAmount = $cartSubTotal + $shippingCost;

        if ($cartItems->isEmpty()) {
            session()->flash('error', 'Seu carrinho está vazio.');
            return redirect()->route('cart.index');
        }

        try {
            DB::beginTransaction();

            // Criar endereço
            $endereco = $user->enderecos()->create([
                'rua' => $this->rua,
                'numero' => $this->numero,
                'complemento' => $this->complemento,
                'cidade' => $this->cidade,
                'estado' => strtoupper($this->estado),
                'cep' => preg_replace('/\D/', '', $this->cep),
            ]);

            // Criar pedido
            $order = Order::create([
                'user_id' => $user->id,
                'endereco_id' => $endereco->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'shipping_service' => $this->shippingService,
                'payment_method' => $this->paymentMethod,
            ]);

            // Adicionar itens ao pedido
            $productsPayload = [];
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                $productsPayload[] = [
                    'externalId' => (string) $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => (int) ($item->price * 100),
                ];
            }

            // Adicionar frete como produto
            if ($shippingCost > 0) {
                $shippingServiceName = mb_substr($this->shippingService, 0, 50);
                $shippingServiceName = preg_replace('/[^\w\s\-\(\)]/', '', $shippingServiceName);
                
                $productsPayload[] = [
                    'externalId' => 'SHIPPING_' . $order->id,
                    'name' => 'Taxa de Entrega (' . $shippingServiceName . ')',
                    'quantity' => 1,
                    'price' => (int) ($shippingCost * 100),
                ];
            }

            // Verificar CPF e Celular
            if (empty($user->cpf) || empty($user->celular)) {
                throw new \Exception('Seu perfil está incompleto. Por favor, adicione seu CPF e Celular no seu perfil antes de finalizar a compra.');
            }

            // Dados do Cliente para Abacate Pay
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'cellphone' => preg_replace('/\D/', '', $user->celular),
                'taxId' => preg_replace('/\D/', '', $user->cpf),
            ];

            // Validações
            $apiKey = config('services.abacatepay.key');
            if (empty($apiKey)) {
                throw new \Exception('Chave da API Abacate Pay não configurada. Verifique o arquivo .env');
            }

            if (strlen($customerData['taxId']) !== 11) {
                throw new \Exception('CPF inválido: deve ter 11 dígitos');
            }

            if (strlen($customerData['cellphone']) < 10) {
                throw new \Exception('Celular inválido: deve ter pelo menos 10 dígitos');
            }

            // Payload para Abacate Pay
            $billingData = [
                'frequency' => 'ONE_TIME',
                'methods' => ['PIX'],
                'products' => $productsPayload,
                'returnUrl' => route('cart.index'),
                'completionUrl' => route('checkout.success'),
                'customer' => $customerData,
                'description' => "Pedido #" . $order->id,
            ];

            // Validação do payload
            foreach ($billingData['products'] as $product) {
                if (!isset($product['price']) || $product['price'] <= 0) {
                    throw new \Exception('Produto com preço inválido: ' . ($product['name'] ?? 'Desconhecido'));
                }
            }

            $apiUrl = 'https://api.abacatepay.com/v1/billing/create';

            Log::info('Requisição para AbacatePay:', ['body' => $billingData]);

            // Chamar API Abacate Pay
            $response = Http::withToken($apiKey)->post($apiUrl, $billingData);
            $paymentResponse = $response->json();

            Log::info('Resposta da API AbacatePay:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful() && isset($paymentResponse['data']['id']) && isset($paymentResponse['data']['url'])) {
                $order->update([
                    'payment_id' => $paymentResponse['data']['id'],
                ]);

                DB::commit();
                Cart::clear();

                return redirect()->away($paymentResponse['data']['url']);
            } else {
                $errorMessage = $paymentResponse['message'] ?? ($paymentResponse['error'] ?? $response->reason());
                if (isset($paymentResponse['errors']) && is_array($paymentResponse['errors'])) {
                    $errorMessage .= ' Detalhes: ' . implode(', ', array_map(fn($err) => $err['message'] ?? json_encode($err), $paymentResponse['errors']));
                }
                throw new \Exception('Falha ao gerar cobrança PIX: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no Checkout: ' . $e->getMessage());
            session()->flash('error', 'Não foi possível processar seu pedido. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $cartItems = Cart::getContent();
        $subTotal = Cart::getSubTotal();
        $total = $subTotal + $this->shippingCost;
        
        return view('livewire.checkout-page', [
            'cartItems' => $cartItems,
            'subTotal' => $subTotal,
            'total' => $total,
        ]);
    }
}
