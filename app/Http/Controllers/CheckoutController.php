<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\FrenetService; // <<< ADICIONADO
use Illuminate\Support\Facades\Validator; // <<< ADICIONADO

class CheckoutController extends Controller
{
    // <<< INÍCIO DO BLOCO ADICIONADO >>>
    protected $frenetService;

    /**
     * Injete o serviço no construtor
     */
    public function __construct(FrenetService $frenetService)
    {
        $this->frenetService = $frenetService;
    }
    // <<< FIM DO BLOCO ADICIONADO >>>

    /**
     * Exibe a página de checkout.
     * Rota: GET /checkout
     * Nome: checkout.index
     */
    public function index()
    {
        $cartItems = Cart::getContent();
        $subTotal = Cart::getSubTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio para finalizar a compra.');
        }

        $user = Auth::user();
        $addresses = $user->enderecos()->get();

        return view('checkout.index', compact('cartItems', 'subTotal', 'addresses', 'user'));
    }

    /**
     * Adiciona um produto específico ao carrinho (limpando-o antes)
     * e redireciona imediatamente para o checkout.
     * Rota: POST /comprar-agora/{product}
     * Nome: checkout.buyNow
     */
    public function buyNow(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        try {
            Cart::clear();

            Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'quantity' => (int)$request->input('quantity'),
                'attributes' => [
                    'image' => $product->images->isNotEmpty() ? $product->images->first()->path : null,
                    'slug' => $product->slug,
                ],
                'associatedModel' => $product
            ]);

            return redirect()->route('checkout.index');

        } catch (\Exception $e) {
            Log::error('Erro no Comprar Agora (CheckoutController@buyNow): ' . $e->getMessage());
            return back()->with('error', 'Não foi possível iniciar a compra direta. Por favor, tente adicionar ao carrinho.');
        }
    }

    /**
     * Processa o pedido, cria a cobrança na AbacatePay e redireciona para o pagamento.
     * Rota: POST /checkout
     * Nome: checkout.store
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'payment_method' => 'required|string|in:pix',
                'address_id' => 'required_without_all:rua,numero,cidade,estado,cep',
                'rua' => 'required_if:address_id,new,',
                'numero' => 'required_if:address_id,new,',
                'cidade' => 'required_if:address_id,new,',
                'estado' => 'required_if:address_id,new,',
                'cep' => 'required_if:address_id,new,',
                // Adicionar validação para o valor do frete e serviço
                'shipping_service' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
            ],
            [
                'address_id.required_without_all' => 'Você deve selecionar um endereço existente OU preencher um novo endereço.',
                'shipping_service.required' => 'Por favor, calcule e selecione uma opção de frete.',
                'shipping_cost.required' => 'O custo do frete não foi selecionado.',
            ]
        );

        $user = Auth::user();
        $cartItems = Cart::getContent();
        $cartSubTotal = Cart::getSubTotal(); // Total dos produtos
        $shippingCost = (float) $request->input('shipping_cost');
        
        // <<< MODIFICADO: O total agora inclui o frete >>>
        $totalAmount = $cartSubTotal + $shippingCost; 

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        try {
            DB::beginTransaction();

            $endereco = null;
            if ($request->input('address_id') && $request->input('address_id') !== 'new') {
                $endereco = $user->enderecos()->findOrFail($request->input('address_id'));
            } else {
                $endereco = $user->enderecos()->create([
                    'rua' => $request->input('rua'),
                    'numero' => $request->input('numero'),
                    'complemento' => $request->input('complemento'),
                    'cidade' => $request->input('cidade'),
                    'estado' => $request->input('estado'),
                    'cep' => $request->input('cep'),
                ]);
            }

            // <<< MODIFICADO: Salva o total com frete e o nome do serviço >>>
            $order = Order::create([
                'user_id' => $user->id,
                'endereco_id' => $endereco->id,
                'status' => 'pending',
                'total_amount' => $totalAmount, // Salva o total com frete
                'shipping_cost' => $shippingCost, // Salva o custo do frete
                'shipping_service' => $request->input('shipping_service'), // Salva o nome do serviço
                'payment_method' => $request->input('payment_method'),
            ]);

            // Adicionar itens ao pedido local
            $productsPayload = []; // Array para enviar à Abacate Pay
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price, // Preço unitário do item no carrinho
                ]);

                // Monta o array de produtos para a API Abacate Pay
                $productsPayload[] = [
                    'externalId' => (string) $item->id, // Garante que é string
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => (int) ($item->price * 100), // Preço UNITÁRIO em centavos
                ];
            }

            // <<< MODIFICADO: Adiciona o Frete como um produto na AbacatePay >>>
            if ($shippingCost > 0) {
                 $productsPayload[] = [
                    'externalId' => 'FRETE',
                    'name' => 'Taxa de Entrega (' . $request->input('shipping_service') . ')',
                    'quantity' => 1,
                    'price' => (int) ($shippingCost * 100), // Preço do frete em centavos
                ];
            }
            // <<< FIM DA MODIFICAÇÃO DO FRETE >>>


            // Verificar CPF e Celular
            if (empty($user->cpf) || empty($user->celular)) {
                throw new \Exception('Seu perfil está incompleto. Por favor, adicione seu CPF e Celular no seu perfil antes de finalizar a compra.');
            }

            // Dados do Cliente para Abacate Pay
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'cellphone' => preg_replace('/\D/', '', $user->celular), // Remove não-números do celular
                'taxId' => preg_replace('/\D/', '', $user->cpf), // Remove não-números do CPF
            ];

            // Configuração da API e URL
            $apiKey = config('services.abacatepay.key');
            $apiUrl = 'https://api.abacatepay.com/v1/billing/create';

            // Payload para /billing/create
            $billingData = [
                'frequency' => 'ONE_TIME',
                'methods' => ['PIX'],
                'products' => $productsPayload, // <<< AGORA INCLUI PRODUTOS + FRETE
                'returnUrl' => route('cart.index'),
                'completionUrl' => route('checkout.success'),
                'customer' => $customerData,
                'description' => "Pedido #" . $order->id,
            ];

            // *** ADICIONADO LOG DETALHADO DA REQUISIÇÃO ***
            Log::info('Requisição para AbacatePay (/billing/create):', [
                'url' => $apiUrl,
                'headers' => [
                    'Authorization' => 'Bearer ' . substr($apiKey, 0, 5) . '...',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => $billingData,
            ]);

            // Chamar a API da Abacate Pay
            $response = Http::withToken($apiKey)->post($apiUrl, $billingData);
            $paymentResponse = $response->json();

            // Verificar sucesso e salvar ID da cobrança (billing ID)
            if ($response->successful() && isset($paymentResponse['data']['id']) && isset($paymentResponse['data']['url'])) {
                $order->update([
                    'payment_id' => $paymentResponse['data']['id'],
                ]);

                DB::commit();
                Cart::clear();

                return redirect()->away($paymentResponse['data']['url']);

            } else {
                Log::error('Falha na API AbacatePay (/billing/create): ', $paymentResponse ?? ['raw_body' => $response->body()]);
                $errorMessage = $paymentResponse['message'] ?? ($paymentResponse['error'] ?? $response->reason());
                if (isset($paymentResponse['errors']) && is_array($paymentResponse['errors'])) {
                    $errorMessage .= ' Detalhes: ' . implode(', ', array_map(fn($err) => $err['message'] ?? json_encode($err), $paymentResponse['errors']));
                }
                throw new \Exception('Falha ao gerar cobrança PIX: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no Checkout: ' . $e->getMessage());
            return back()->with('error', 'Não foi possível processar seu pedido. Detalhe: ' . $e->getMessage());
        }
    }

    /**
     * Exibe a página de sucesso APÓS o pagamento ser confirmado
     * (O usuário será redirecionado para cá pela Abacate Pay via completionUrl).
     * Rota: GET /checkout/pedido-realizado
     * Nome: checkout.success
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        $lastOrder = $user->orders()->latest()->first();

        return view('checkout.success', [
            'order' => $lastOrder
        ]);
        // A view 'checkout.success' precisará ser adaptada.
    }


    // <<< INÍCIO DO NOVO MÉTODO PARA CALCULAR FRETE >>>

    /**
     * Calcula o frete usando o FrenetService.
     * Rota: POST /checkout/calculate-shipping
     * Nome: checkout.shipping.calculate
     */
    public function calculateShipping(Request $request)
    {
        // 1. Validar o CEP
        $validator = Validator::make($request->all(), [
            'cep' => 'required|string|regex:/^[0-9]{8}$/', // CEP com 8 dígitos
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'CEP inválido. Digite 8 números (ex: 01001000).'], 400);
        }

        $cepDestino = $request->input('cep');

        // 2. Preparar os dados do Carrinho
        // O seu controller já usa 'Darryldecode/Cart' e 'associatedModel'
        $cartItems = Cart::getContent();
        $valorTotal = Cart::getSubTotal();
        
        if ($cartItems->isEmpty()) {
             return response()->json(['error' => 'Seu carrinho está vazio.'], 400);
        }

        $itemsParaCotacao = [];
        foreach ($cartItems as $cartItem) {
            
            // Verifica se o modelo (Produto) está associado
            if (!$cartItem->associatedModel) {
                Log::error("Item do carrinho {$cartItem->id} sem associatedModel para cálculo de frete.");
                return response()->json(['error' => "Erro: O item {$cartItem->name} não possui dados de produto para frete."], 400);
            }

            $product = $cartItem->associatedModel;
            
            // !!! IMPORTANTE !!!
            // Seus produtos PRECISAM ter as colunas 'weight', 'height', 'width', 'length' no banco de dados.
            // Os valores de fallback (0.1, 10, 10, 10) são usados se forem nulos.
            $itemsParaCotacao[] = [
                'sku'       => $product->sku ?? $cartItem->id, // Usa o SKU do produto ou o ID do carrinho
                'quantity'  => (int) $cartItem->quantity,
                'weight'    => (float) ($product->weight ?? 0.1),  // Peso em KG (fallback: 100g)
                'height'    => (float) ($product->height ?? 10),   // Altura em CM (fallback: 10cm)
                'width'     => (float) ($product->width ?? 10),    // Largura em CM (fallback: 10cm)
                'length'    => (float) ($product->length ?? 10),   // Comprimento em CM (fallback: 10cm)
                'category'  => 'Geral' // Você pode customizar isso se tiver categorias
            ];
        }

        // 3. Chamar o Serviço
        $shippingOptions = $this->frenetService->calculate(
            $cepDestino,
            $valorTotal,
            $itemsParaCotacao
        );

        // 4. Retornar o JSON para o frontend
        return response()->json([
            'shipping_options' => $shippingOptions
        ]);
    }
    // <<< FIM DO NOVO MÉTODO >>>
}