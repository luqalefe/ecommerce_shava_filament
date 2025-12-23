<?php

namespace App\Livewire;

use App\Services\FrenetService;
use App\Services\MercadoPagoService;
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
    public $bairro = '';
    public $cidade = '';
    public $estado = '';
    public $cep = '';
    
    public $shippingOptions = [];
    public $selectedShipping = null;
    public $shippingCost = 0;
    public $shippingService = '';
    
    public $paymentMethod = 'pix';
    
    // Campos de perfil (CPF e Celular)
    public $cpf = '';
    public $celular = '';
    public $needsCpf = false;
    public $needsCelular = false;
    
    public $loading = false;
    public $loadingCep = false; // Loading específico para busca de CEP
    public $summaryExpanded = false; // Para controlar resumo no mobile
    
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
        
        // Verificar se o usuário precisa preencher CPF e/ou Celular
        $user = Auth::user();
        $this->needsCpf = empty($user->cpf);
        $this->needsCelular = empty($user->celular);
        
        // Pré-preencher se já existir
        $this->cpf = $user->cpf ?? '';
        $this->celular = $user->celular ?? '';
    }

    /**
     * Quando o CEP é atualizado, busca endereço automaticamente
     * Dispara quando o CEP atinge 8 dígitos
     */
    public function updatedCep()
    {
        $cep = preg_replace('/\D/', '', $this->cep);
        
        if (strlen($cep) === 8) {
            $this->searchAddress($cep);
        } else {
            // Limpa endereço se o CEP não estiver completo
            if (strlen($cep) < 8) {
                $this->clearAddress();
            }
        }
    }

    /**
     * Busca endereço via ViaCEP quando o usuário sai do campo CEP
     * Disparado via @blur no frontend
     */
    public function searchAddressOnBlur()
    {
        $cep = preg_replace('/\D/', '', $this->cep);
        
        if (strlen($cep) === 8) {
            $this->searchAddress($cep);
        } elseif (strlen($cep) > 0 && strlen($cep) < 8) {
            $this->addError('cep', 'CEP deve conter 8 dígitos.');
        }
    }

    /**
     * Busca endereço via BrasilAPI e preenche os campos automaticamente
     */
    public function searchAddress($cep = null)
    {
        if ($cep === null) {
            $cep = preg_replace('/\D/', '', $this->cep);
        }

        // Validação: CEP deve ter exatamente 8 dígitos
        if (strlen($cep) !== 8) {
            return;
        }

        $this->loadingCep = true;
        $this->resetErrorBag('cep');

        try {
            // Requisição GET para BrasilAPI (mais confiável que ViaCEP)
            $response = Http::timeout(10)->get("https://brasilapi.com.br/api/cep/v1/{$cep}");
            
            if (!$response->successful()) {
                // Fallback para ViaCEP se BrasilAPI falhar
                $response = Http::timeout(10)->get("https://viacep.com.br/ws/{$cep}/json/");
                
                if (!$response->successful()) {
                    throw new \Exception('Erro na requisição às APIs de CEP');
                }
                
                $data = $response->json();
                
                if (isset($data['erro']) && $data['erro'] === true) {
                    $this->addError('cep', 'CEP não encontrado. Verifique o CEP digitado.');
                    $this->loadingCep = false;
                    return;
                }
                
                // Mapear campos do ViaCEP
                $this->rua = $data['logradouro'] ?? '';
                $this->bairro = $data['bairro'] ?? '';
                $this->cidade = $data['localidade'] ?? '';
                $this->estado = strtoupper($data['uf'] ?? '');
                $this->complemento = $data['complemento'] ?? '';
            } else {
                $data = $response->json();
                
                // Mapear campos da BrasilAPI
                $this->rua = $data['street'] ?? '';
                $this->bairro = $data['neighborhood'] ?? '';
                $this->cidade = $data['city'] ?? '';
                $this->estado = strtoupper($data['state'] ?? '');
                $this->complemento = '';
            }

            // Normaliza o CEP no formato exibido
            $this->cep = substr($cep, 0, 5) . '-' . substr($cep, 5);

            // Calcula o frete automaticamente após preencher o endereço
            $this->calculateShipping($cep);

            // Dispara evento JavaScript para focar no campo número
            $this->dispatch('address-filled');

            $this->loadingCep = false;
            
        } catch (\Exception $e) {
            $this->addError('cep', 'Erro ao buscar CEP. Tente novamente.');
            $this->loadingCep = false;
            Log::error('Erro ao buscar CEP: ' . $e->getMessage(), [
                'cep' => $cep,
                'exception' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Limpa os campos de endereço
     */
    private function clearAddress()
    {
        $this->rua = '';
        $this->bairro = '';
        $this->cidade = '';
        $this->estado = '';
        $this->complemento = '';
        $this->clearShipping();
    }

    /**
     * Calcula o frete usando FrenetService
     * Chamado automaticamente após buscar o endereço
     * FRETE GRÁTIS para Rio Branco - AC
     */
    private function calculateShipping($cep)
    {
        $cartItems = Cart::getContent();
        $valorTotal = Cart::getSubTotal();
        
        if ($cartItems->isEmpty()) {
            $this->loading = false;
            return;
        }

        // Verificar se é Rio Branco - AC (frete grátis)
        $cidadeNormalizada = mb_strtolower(trim($this->cidade ?? ''));
        $estadoNormalizado = mb_strtoupper(trim($this->estado ?? ''));
        
        // Aceitar variações: "rio branco", "riobranco", "Rio Branco"
        $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
        $isAcre = $estadoNormalizado === 'AC';
        
        if ($isRioBranco && $isAcre) {
            // Frete grátis para Rio Branco - AC
            $this->shippingOptions = [
                [
                    'service' => 'Entrega Local',
                    'carrier' => 'Shava Haux',
                    'price' => 0,
                    'deadline' => 1,
                ]
            ];
            // Selecionar automaticamente o frete grátis
            $this->selectedShipping = 0;
            $this->shippingCost = 0;
            $this->shippingService = 'Entrega Local (Shava Haux) - GRÁTIS';
            $this->loading = false;
            return;
        }

        $this->loading = true;

        $itemsParaCotacao = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->associatedModel) {
                $this->loading = false;
                Log::warning("Item {$cartItem->name} não possui dados de produto para frete.");
                continue;
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

        if (empty($itemsParaCotacao)) {
            $this->loading = false;
            return;
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
        $this->loadingCep = false;
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
     * Toggle do resumo no mobile
     */
    public function toggleSummary()
    {
        $this->summaryExpanded = !$this->summaryExpanded;
    }

    /**
     * Finaliza o pedido e cria a cobrança na Abacate Pay
     */
    public function placeOrder()
    {
        // Validação customizada para CEP (remove caracteres não numéricos antes de validar)
        $cepClean = preg_replace('/\D/', '', $this->cep);
        
        // Regras dinâmicas para CPF e Celular
        $cpfRule = $this->needsCpf ? 'required|string|min:11|max:14' : 'nullable';
        $celularRule = $this->needsCelular ? 'required|string|min:10|max:15' : 'nullable';
        
        $this->validate([
            'rua' => 'required|string|min:3',
            'numero' => 'required|string',
            'bairro' => 'required|string|min:2',
            'cidade' => 'required|string|min:2',
            'estado' => 'required|string|size:2',
            'cep' => ['required', 'string', function ($attribute, $value, $fail) use ($cepClean) {
                if (strlen($cepClean) !== 8) {
                    $fail('O campo CEP deve conter 8 dígitos.');
                }
            }],
            'shippingService' => 'required|string',
            'shippingCost' => 'required|numeric|min:0',
            'paymentMethod' => 'required|string|in:pix,mercadopago',
            'cpf' => $cpfRule,
            'celular' => $celularRule,
        ], [
            'rua.required' => 'O campo rua é obrigatório.',
            'numero.required' => 'O campo número é obrigatório.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'estado.required' => 'O campo estado é obrigatório.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'shippingService.required' => 'Por favor, selecione uma opção de frete.',
            'shippingCost.required' => 'O custo do frete não foi selecionado.',
            'cpf.required' => 'Por favor, informe seu CPF para continuar.',
            'cpf.min' => 'CPF deve ter pelo menos 11 dígitos.',
            'celular.required' => 'Por favor, informe seu celular para continuar.',
            'celular.min' => 'Celular deve ter pelo menos 10 dígitos.',
        ]);

        $user = Auth::user();
        
        // Atualizar CPF e Celular do usuário se foram preenchidos
        $profileUpdated = false;
        if ($this->needsCpf && !empty($this->cpf)) {
            $user->cpf = preg_replace('/\D/', '', $this->cpf);
            $profileUpdated = true;
        }
        if ($this->needsCelular && !empty($this->celular)) {
            $user->celular = $this->celular;
            $profileUpdated = true;
        }
        if ($profileUpdated) {
            $user->save();
            $user->refresh();
        }
        
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
            $cartItemsArray = [];
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                $cartItemsArray[] = [
                    'id' => (string) $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                ];
            }

            // Processar pagamento baseado no método selecionado
            if ($this->paymentMethod === 'mercadopago') {
                // Processar com Mercado Pago
                return $this->processMercadoPagoPayment($order, $cartItemsArray, $shippingCost, $user, $endereco);
            } else {
                // Processar com Abacate Pay (PIX) - código original
                return $this->processAbacatePayPayment($order, $cartItems, $shippingCost, $user);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no Checkout: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'payment_method' => $this->paymentMethod ?? 'unknown',
            ]);
            
            // Exibir erro de forma mais visível no Livewire
            $this->addError('payment', 'Não foi possível processar seu pedido. ' . $e->getMessage());
            
            // Também adicionar na sessão para compatibilidade
            session()->flash('error', 'Não foi possível processar seu pedido. ' . $e->getMessage());
        }
    }

    /**
     * Processa pagamento via Mercado Pago
     */
    private function processMercadoPagoPayment($order, array $cartItemsArray, float $shippingCost, $user, $endereco)
    {
        try {
            $mercadopagoService = app(MercadoPagoService::class);

            // Preparar dados do pagador
            $payerData = [
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf ?? null,
                'phone' => $user->celular ?? null,
                'address' => [
                    'street' => $endereco->rua,
                    'number' => $endereco->numero,
                    'zip_code' => $endereco->cep,
                ],
            ];

            Log::info('Criando preferência Mercado Pago', [
                'order_id' => $order->id,
                'items_count' => count($cartItemsArray),
                'shipping_cost' => $shippingCost,
            ]);

            // Criar preferência no Mercado Pago
            $preference = $mercadopagoService->createPreference(
                $cartItemsArray,
                $shippingCost,
                $payerData,
                $order->id
            );

            if (empty($preference['init_point'])) {
                throw new \Exception('URL de checkout do Mercado Pago não foi retornada. Verifique os logs para mais detalhes.');
            }

            // Salvar o ID da preferência no pedido
            $order->update([
                'payment_id' => $preference['preference_id'],
            ]);

            DB::commit();
            Cart::clear();

            Log::info('Redirecionando para Mercado Pago', [
                'preference_id' => $preference['preference_id'],
                'init_point' => $preference['init_point'],
            ]);

            // Redirecionar para o checkout do Mercado Pago
            return redirect()->away($preference['init_point']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar pagamento Mercado Pago', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id ?? null,
            ]);
            throw $e; // Re-throw para ser capturado no método principal
        }
    }

    /**
     * Processa pagamento via Abacate Pay (PIX)
     */
    private function processAbacatePayPayment($order, $cartItems, float $shippingCost, $user)
    {
        try {
            // Preparar payload para Abacate Pay
            $productsPayload = [];
            foreach ($cartItems as $item) {
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

            // Verificar CPF e Celular (já devem ter sido atualizados no placeOrder)
            if (empty($user->cpf) || empty($user->celular)) {
                $this->addError('payment', 'Por favor, preencha seu CPF e Celular para continuar.');
                DB::rollBack();
                return;
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
            Log::error('Erro ao processar pagamento Abacate Pay: ' . $e->getMessage());
            throw $e;
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