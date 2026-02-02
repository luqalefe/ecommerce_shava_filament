<?php

namespace App\Livewire;

use App\Services\FrenetService;
use App\Services\MercadoPagoService;
use App\Models\Endereco;
use App\Models\Order;
use App\Models\Product;
use App\Rules\Cpf;
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
    
    public $paymentMethod = 'pix'; // 'pix', 'card' ou 'delivery' (Pagamento na Entrega)
    public $canPayOnDelivery = false; // Elegível para pagamento na entrega (PJ + Rio Branco)
    
    // Campos de perfil (CPF e Celular)
    public $cpf = '';
    public $celular = '';
    public $needsCpf = false;
    public $needsCelular = false;
    
    // PIX QR Code display
    public $showPixQrCode = false;
    public $pixQrCode = '';        // Código copia e cola
    public $pixQrCodeBase64 = '';  // Imagem Base64 do QR Code
    public $pixPaymentId = '';
    public $pixOrderId = null;
    
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

            // Verifica se é elegível para pagamento na entrega (PJ + Rio Branco)
            $this->checkPaymentOnDeliveryEligibility();

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

        // Verificar se é Rio Branco - AC (frete especial)
        $cidadeNormalizada = mb_strtolower(trim($this->cidade ?? ''));
        $estadoNormalizado = mb_strtoupper(trim($this->estado ?? ''));
        
        // Aceitar variações: "rio branco", "riobranco", "Rio Branco"
        $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
        $isAcre = $estadoNormalizado === 'AC';
        
        if ($isRioBranco && $isAcre) {
            // Rio Branco - AC: Frete grátis acima de R$100, senão R$10
            $freteGratis = $valorTotal >= 100;
            $precoFrete = $freteGratis ? 0 : 10;
            $labelFrete = $freteGratis ? 'GRÁTIS' : 'R$ 10,00';
            
            $this->shippingOptions = [
                [
                    'service' => 'Entrega Local',
                    'carrier' => 'Shava Haux',
                    'price' => $precoFrete,
                    'deadline' => 1,
                ]
            ];
            // Selecionar automaticamente
            $this->selectedShipping = 0;
            $this->shippingCost = $precoFrete;
            $this->shippingService = "Entrega Local (Shava Haux) - {$labelFrete}";
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
     * Verifica se o usuário é elegível para pagamento na entrega
     * Requisitos: Pessoa Jurídica (PJ) + Rio Branco - AC
     */
    private function checkPaymentOnDeliveryEligibility(): void
    {
        $user = Auth::user();
        
        // Verificar se é Pessoa Jurídica
        $isPJ = $user && method_exists($user, 'isPessoaJuridica') && $user->isPessoaJuridica();
        
        // Verificar se é Rio Branco - AC
        $cidadeNormalizada = mb_strtolower(trim($this->cidade ?? ''));
        $estadoNormalizado = mb_strtoupper(trim($this->estado ?? ''));
        $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
        $isAcre = $estadoNormalizado === 'AC';
        
        $this->canPayOnDelivery = $isPJ && $isRioBranco && $isAcre;
        
        // Se não é mais elegível mas tinha selecionado delivery, volta para pix
        if (!$this->canPayOnDelivery && $this->paymentMethod === 'delivery') {
            $this->paymentMethod = 'pix';
        }
        
        Log::info('Verificação de elegibilidade para pagamento na entrega', [
            'user_id' => $user?->id,
            'is_pj' => $isPJ,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'is_rio_branco' => $isRioBranco && $isAcre,
            'can_pay_on_delivery' => $this->canPayOnDelivery,
        ]);
    }

    /**
     * Finaliza o pedido e cria a cobrança na Abacate Pay
     */
    public function placeOrder()
    {
        // Validação customizada para CEP (remove caracteres não numéricos antes de validar)
        $cepClean = preg_replace('/\D/', '', $this->cep);
        
        // Regras dinâmicas para CPF e Celular
        $cpfRule = $this->needsCpf ? ['required', 'string', new Cpf] : 'nullable';
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

        if ($cartItems->isEmpty()) {
            session()->flash('error', 'Seu carrinho está vazio.');
            return redirect()->route('cart.index');
        }

        // ========================================================
        // VALIDAÇÃO: Produtos da categoria CNPJ só podem ser comprados por PJ
        // ========================================================
        foreach ($cartItems as $item) {
            $product = Product::with('category')->find($item->id);
            
            if ($product && $product->isCnpjOnly() && !$user->isPessoaJuridica()) {
                $this->addError('payment', "O produto '{$product->name}' é exclusivo para clientes Pessoa Jurídica (CNPJ). Por favor, atualize seu cadastro ou remova este produto do carrinho.");
                return;
            }
        }

        // ========================================================
        // SEGURANÇA: Recalcular frete no servidor para evitar manipulação
        // ========================================================
        $shippingCost = $this->recalculateAndValidateShipping($cartItems, $cartSubTotal);
        if ($shippingCost === false) {
            return; // Erro já foi adicionado no método
        }
        
        $totalAmount = $cartSubTotal + $shippingCost;


        try {
            DB::beginTransaction();

            // Criar endereço
            // Criar endereço
            $endereco = $user->enderecos()->create([
                'rua' => $this->rua,
                'numero' => $this->numero,
                'complemento' => $this->complemento,
                'bairro' => $this->bairro, // <-- Adicionado
                'cidade' => $this->cidade,
                'estado' => strtoupper($this->estado),
                'cep' => preg_replace('/\D/', '', $this->cep),
            ]);

            // Criar pedido preliminar (será atualizado com valores reais)
            $order = Order::create([
                'user_id' => $user->id,
                'endereco_id' => $endereco->id,
                'status' => 'pending',
                'total_amount' => 0, // Será recalculado
                'shipping_cost' => $shippingCost,
                'shipping_service' => $this->shippingService,
                'payment_method' => $this->paymentMethod,
            ]);

            // Adicionar itens ao pedido com validação de estoque e preço
            $cartItemsArray = [];
            $serverSubTotal = 0;

            foreach ($cartItems as $item) {
                // SEGURANÇA: Lock pessimista para evitar race condition (Bug #10)
                $product = Product::lockForUpdate()->find($item->id);

                if (!$product) {
                    throw new \Exception("Produto '{$item->name}' não encontrado ou indisponível.");
                }

                // SEGURANÇA: Validação de Estoque (Bug #9)
                if ($product->quantity < $item->quantity) {
                    throw new \Exception("Estoque insuficiente para o produto '{$product->name}'. Restam apenas {$product->quantity} unidades.");
                }

                // SEGURANÇA: Validação de Preço (Bug #3) - Usa preço do banco, não da sessão
                $realPrice = $product->sale_price ?? $product->price;
                
                // Opcional: Verificar discrepância absurda de preço se necessário
                // if (abs($realPrice - $item->price) > 0.1) { Warning... }

                $serverSubTotal += $realPrice * $item->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $realPrice,
                ]);

                // SEGURANÇA: Decrementar Estoque (Bug #9)
                $product->decrement('quantity', $item->quantity);

                $cartItemsArray[] = [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'quantity' => $item->quantity,
                    'price' => (float) $realPrice,
                ];
            }

            // Atualiza o total do pedido com o subtotal recalculado + frete validado
            $validatedTotal = $serverSubTotal + $shippingCost;
            $order->update(['total_amount' => $validatedTotal]);

            // Processar pagamento baseado no método selecionado
            if ($this->paymentMethod === 'pix') {
                return $this->processPixPayment($order, $cartItemsArray, $shippingCost, $user, $endereco);
            } elseif ($this->paymentMethod === 'delivery') {
                // Pagamento na Entrega - Apenas para PJ + Rio Branco
                // Verificar elegibilidade novamente por segurança
                $this->checkPaymentOnDeliveryEligibility();
                if (!$this->canPayOnDelivery) {
                    throw new \Exception('Pagamento na entrega disponível apenas para clientes PJ em Rio Branco - AC.');
                }
                return $this->processPaymentOnDelivery($order, $cartItemsArray, $shippingCost, $user, $endereco);
            } else {
                // Cartão de Crédito via Checkout Pro (redireciona para Mercado Pago)
                return $this->processMercadoPagoCheckoutPro($order, $cartItemsArray, $shippingCost, $user, $endereco);
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
     * Processa pagamento PIX via Mercado Pago (QR Code direto)
     */
    private function processPixPayment($order, array $cartItemsArray, float $shippingCost, $user, $endereco)
    {
        try {
            $mercadopagoService = app(MercadoPagoService::class);

            // Preparar dados do pagador
            $payerData = [
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf ?? null,
                'phone' => $user->celular ?? null,
            ];

            // Calcular valor total
            $totalAmount = $order->total_amount;

            Log::info('Criando pagamento PIX', [
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
            ]);

            // Criar pagamento PIX no Mercado Pago
            $pixPayment = $mercadopagoService->createPixPayment(
                $totalAmount,
                $payerData,
                $order->id,
                'Pedido Shava Haux'
            );

            if (empty($pixPayment['qr_code']) && empty($pixPayment['qr_code_base64'])) {
                throw new \Exception('QR Code PIX não foi gerado. Tente novamente.');
            }

            // Salvar o ID do pagamento no pedido
            $order->update([
                'payment_id' => $pixPayment['payment_id'],
                'payment_method' => 'pix',
            ]);

            DB::commit();
            Cart::clear();

            // Setar propriedades para exibir QR Code na interface
            $this->showPixQrCode = true;
            $this->pixQrCode = $pixPayment['qr_code'];
            $this->pixQrCodeBase64 = $pixPayment['qr_code_base64'];
            $this->pixPaymentId = $pixPayment['payment_id'];
            $this->pixOrderId = $order->id;

            Log::info('QR Code PIX gerado com sucesso', [
                'payment_id' => $pixPayment['payment_id'],
                'order_id' => $order->id,
            ]);

            // Disparar evento para UI (opcional)
            $this->dispatch('pix-qr-code-ready');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar pagamento PIX', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id ?? null,
            ]);
            throw $e;
        }
    }

    /**
     * Copia o código PIX para a área de transferência (chamado via JS)
     */
    public function copyPixCode()
    {
        // Este método é chamado apenas para logging, a cópia real é feita via JS
        Log::info('Código PIX copiado', ['order_id' => $this->pixOrderId]);
    }

    /**
     * Processa pedido com Pagamento na Entrega (apenas PJ + Rio Branco)
     * O pedido fica com status 'awaiting_delivery_payment' até o entregador confirmar
     */
    private function processPaymentOnDelivery($order, array $cartItemsArray, float $shippingCost, $user, $endereco)
    {
        try {
            // Atualizar status do pedido para aguardando pagamento na entrega
            $order->update([
                'status' => 'pending',
                'payment_method' => 'delivery',
                'payment_status' => 'awaiting_delivery_payment',
            ]);

            Log::info('Pedido criado com Pagamento na Entrega', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'cnpj' => $user->cnpj ?? 'N/A',
                'cidade' => $this->cidade,
                'total' => $order->total_amount,
            ]);

            // Commit da transação
            DB::commit();

            // Limpar carrinho
            Cart::clear();

            // Redirecionar para página de sucesso
            session()->flash('message', 'Pedido realizado com sucesso! O pagamento será feito na entrega.');
            return redirect()->route('checkout.success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar Pagamento na Entrega', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
            ]);
            throw $e;
        }
    }

    /**
     * Verifica o status do pagamento PIX
     */
    public function checkPixPaymentStatus()
    {
        if (!$this->pixPaymentId) {
            return;
        }

        try {
            $mercadopagoService = app(MercadoPagoService::class);
            $payment = $mercadopagoService->getPayment($this->pixPaymentId);

            if ($payment && $payment['status'] === 'approved') {
                // Atualizar status do pedido para 'processing'
                if ($this->pixOrderId) {
                    $order = Order::find($this->pixOrderId);
                    if ($order && $order->status !== 'processing') {
                        $order->update([
                            'status' => 'processing',
                            'payment_status' => 'approved',
                        ]);
                        
                        Log::info('Pagamento PIX aprovado via polling', [
                            'order_id' => $this->pixOrderId,
                            'payment_id' => $this->pixPaymentId,
                        ]);
                    }
                }
                
                // Pagamento aprovado - redirecionar para página de sucesso
                session()->flash('message', 'Pagamento confirmado com sucesso!');
                return redirect()->route('checkout.success');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status do PIX', [
                'payment_id' => $this->pixPaymentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Processa pagamento via Mercado Pago Checkout Pro (Cartão de Crédito)
     * Redireciona para a página do Mercado Pago
     */
    private function processMercadoPagoCheckoutPro($order, array $cartItemsArray, float $shippingCost, $user, $endereco)
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

            Log::info('Criando preferência Mercado Pago (Checkout Pro)', [
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
                'payment_method' => 'card',
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
            throw $e;
        }
    }

    /**
     * Recalcula e valida o frete no servidor para evitar manipulação
     * Retorna o valor do frete validado ou false em caso de erro
     */
    private function recalculateAndValidateShipping($cartItems, $cartSubTotal)
    {
        // 1. Validar se uma opção de frete foi selecionada
        if ($this->selectedShipping === null || !isset($this->shippingOptions[$this->selectedShipping])) {
            $this->addError('shippingService', 'Por favor, selecione uma opção de frete válida.');
            return false;
        }

        $selectedOption = $this->shippingOptions[$this->selectedShipping];
        
        // 2. Verificar se a opção selecionada corresponde ao serviço esperado
        // Isso previne que o usuário manipule o array shippingOptions via JS
        // Recalculamos as opções de frete com os dados atuais do servidor
        
        // Verificar se é Rio Branco - AC (frete especial) - Lógica deve ser IDÊNTICA ao calculateShipping
        $cidadeNormalizada = mb_strtolower(trim($this->cidade ?? ''));
        $estadoNormalizado = mb_strtoupper(trim($this->estado ?? ''));
        $isRioBranco = in_array($cidadeNormalizada, ['rio branco', 'riobranco']);
        $isAcre = $estadoNormalizado === 'AC';
        
        if ($isRioBranco && $isAcre) {
            // Rio Branco - AC: Frete grátis acima de R$100, senão R$10
            return $cartSubTotal >= 100 ? 0.0 : 10.0;
        }

        // Se não for Rio Branco, recotamos na Frenet para garantir que o valor está correto
        
        $itemsParaCotacao = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->associatedModel) continue;
            
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

        // Se não tem itens válidos, algo está errado
        if (empty($itemsParaCotacao)) {
             $this->addError('payment', 'Erro ao validar itens do carrinho para cálculo de frete.');
             return false;
        }

        try {
            // Recalcula opções
            $cep = preg_replace('/\D/', '', $this->cep);
            
            // Reutiliza o serviço Frenet injetado
            $validOptions = $this->frenetService->calculate(
                $cep,
                $cartSubTotal,
                $itemsParaCotacao
            );
            
            // Procura a opção selecionada nas opções válidas retornadas agora pela API
            $found = false;
            $validatedCost = 0.0;
            
            $selectedServiceName = $selectedOption['service'] . ' (' . $selectedOption['carrier'] . ')';
            
            foreach ($validOptions as $option) {
                $optionName = $option['service'] . ' (' . $option['carrier'] . ')';
                
                // Compara nome do serviço
                if ($optionName === $selectedServiceName) {
                    $found = true;
                    $validatedCost = (float) $option['price'];
                    break;
                }
            }
            
            if (!$found) {
                // Se a opção selecionada não existe mais
                $this->addError('shippingService', 'A opção de frete selecionada não é mais válida. Por favor, selecione novamente.');
                $this->calculateShipping($cep); // Recarrega as opções na tela
                return false;
            }
            
            // Verifica se o valor enviado pelo cliente bate com o valor recalculado
            // Margem de erro de 1 centavo
            if (abs((float)$this->shippingCost - $validatedCost) > 0.01) {
                $this->addError('shippingCost', 'O valor do frete mudou. Por favor, revise o pedido.');
                $this->calculateShipping($cep); // Atualiza os valores na tela
                return false;
            }
            
            return $validatedCost;

        } catch (\Exception $e) {
            Log::error('Erro ao revalidar frete no checkout: ' . $e->getMessage());
            // Em caso de erro na API de frete no momento do checkout, bloqueia por segurança
            $this->addError('shippingService', 'Não foi possível validar o frete. Tente novamente em instantes.');
            return false;
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