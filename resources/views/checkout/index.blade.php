@extends('layouts.main')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-center text-3xl md:text-4xl font-bold mb-8 uppercase">Finalizar Compra</h1>

    <form action="{{ route('checkout.store') }}" method="POST" novalidate>
        @csrf
        <div class="grid lg:grid-cols-12 gap-8">

            {{-- Coluna Esquerda: Endereço, Frete e Pagamento --}}
            <div class="lg:col-span-7 space-y-6">
            
                {{-- Seção de Dados Pessoais (CPF/Celular) - Exibe apenas se faltando --}}
                @if(empty($user->cpf) || empty($user->celular))
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-amber-400">
                    <div class="bg-amber-50 px-6 py-4 border-b border-amber-200">
                        <h5 class="font-semibold text-lg flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Complete seus Dados
                        </h5>
                        <p class="text-sm text-amber-700 mt-1">Precisamos dessas informações para processar seu pagamento.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-4">
                            @if(empty($user->cpf))
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF*</label>
                                <input type="text" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                                       id="cpf" 
                                       name="cpf" 
                                       placeholder="000.000.000-00"
                                       maxlength="14"
                                       required
                                       value="{{ old('cpf') }}">
                                @error('cpf')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                            @if(empty($user->celular))
                            <div>
                                <label for="celular" class="block text-sm font-medium text-gray-700 mb-1">Celular*</label>
                                <input type="text" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                                       id="celular" 
                                       name="celular" 
                                       placeholder="(00) 00000-0000"
                                       maxlength="15"
                                       required
                                       value="{{ old('celular') }}">
                                @error('celular')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Seção de Endereço --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h5 class="font-semibold text-lg">Endereço de Entrega</h5>
                    </div>
                    <div class="p-6">
                        @if($addresses->isNotEmpty())
                            <div class="mb-6">
                                <label for="address_id" class="block text-sm font-medium text-gray-700 mb-2">Selecionar endereço existente:</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="address_id" name="address_id">
                                    <option value="">-- Selecione --</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" data-cep="{{ preg_replace('/\D/', '', $address->cep) }}">
                                            {{ $address->rua }}, {{ $address->numero }} - {{ $address->cidade }}/{{ $address->estado }}
                                        </option>
                                    @endforeach
                                    <option value="new">-- Cadastrar novo endereço --</option>
                                </select>
                            </div>
                            <hr class="my-6">
                            <p class="text-center text-gray-500 mb-4">Ou preencha um novo endereço abaixo:</p>
                        @else
                            <p class="text-gray-500 mb-4">Cadastre seu endereço de entrega:</p>
                        @endif

                        {{-- Formulário para Novo Endereço --}}
                        <div id="new-address-form" class="{{ $addresses->isNotEmpty() ? 'hidden' : '' }}">
                            <div class="grid md:grid-cols-12 gap-4">
                                <div class="md:col-span-9">
                                    <label for="rua" class="block text-sm font-medium text-gray-700 mb-1">Rua*</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="rua" name="rua" required>
                                </div>
                                <div class="md:col-span-3">
                                    <label for="numero" class="block text-sm font-medium text-gray-700 mb-1">Número*</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="numero" name="numero" required>
                                </div>
                                <div class="md:col-span-12">
                                    <label for="complemento" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="complemento" name="complemento">
                                </div>
                                <div class="md:col-span-6">
                                    <label for="cidade" class="block text-sm font-medium text-gray-700 mb-1">Cidade*</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="cidade" name="cidade" required>
                                </div>
                                <div class="md:col-span-3">
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado*</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="estado" name="estado" required>
                                </div>
                                <div class="md:col-span-3">
                                    <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP*</label>
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" id="cep" name="cep" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seção Calcular Frete --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h5 class="font-semibold text-lg">Calcular Entrega</h5>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 mb-4">Informe o CEP para calcular o valor da entrega.</p>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="cep-input" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                                <input type="text" id="cep-input" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Apenas 8 números" maxlength="8">
                            </div>
                            <div class="md:col-span-1 flex items-end">
                                <button type="button" id="calculate-shipping-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">Calcular</button>
                            </div>
                        </div>
                        
                        <div id="shipping-results-container" class="mt-4"></div>
                        
                        <input type="hidden" name="shipping_service" id="shipping_service_input">
                        <input type="hidden" name="shipping_cost" id="shipping_cost_input">
                    </div>
                </div>

                {{-- Seção de Pagamento --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h5 class="font-semibold text-lg">Forma de Pagamento</h5>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 mb-4">Selecione como deseja pagar:</p>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" id="payment_pix" value="pix" checked class="w-4 h-4 text-amber-500 focus:ring-amber-500">
                                <span>Pix (Opção Padrão por enquanto)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-not-allowed opacity-50">
                                <input type="radio" name="payment_method" id="payment_card" value="card" disabled class="w-4 h-4">
                                <span class="text-gray-400">Cartão de Crédito (Em breve)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna Direita: Resumo do Pedido --}}
            <div class="lg:col-span-5">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-24">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h5 class="font-semibold text-lg">Resumo do Pedido</h5>
                    </div>
                    <div class="p-6" data-subtotal="{{ $subTotal }}">
                        {{-- Listagem dos Itens --}}
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-3">
                                    @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/50'; @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="w-12 h-12 object-contain rounded">
                                    <div>
                                        <p class="text-sm font-semibold">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">Qtd: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="font-semibold text-sm">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <hr class="my-4">
                        
                        {{-- Subtotal --}}
                        <div class="flex justify-between mb-2">
                            <span>Subtotal</span>
                            <span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        {{-- Frete --}}
                        <div class="flex justify-between mb-4">
                            <span>Frete</span>
                            <span id="summary-shipping-cost">A calcular</span>
                        </div>
                        <hr class="my-4">
                        
                        {{-- Total --}}
                        <div class="flex justify-between font-bold text-xl mt-4">
                            <span>Total</span>
                            <span id="summary-total">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        {{-- Botão Finalizar --}}
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 px-6 rounded-lg uppercase transition-colors duration-200 text-lg">
                                Finalizar Pedido
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-4">Ao clicar em Finalizar Pedido, você concorda com nossos <a href="#" class="underline">Termos e Condições</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addressSelect = document.getElementById('address_id');
        const newAddressForm = document.getElementById('new-address-form');
        const requiredInputs = newAddressForm ? newAddressForm.querySelectorAll('input[required]') : [];

        if (addressSelect && newAddressForm) {
            addressSelect.addEventListener('change', function () {
                if (this.value === 'new' || this.value === '') {
                    newAddressForm.classList.remove('hidden');
                    requiredInputs.forEach(input => input.required = true);
                } else {
                    newAddressForm.classList.add('hidden');
                    requiredInputs.forEach(input => input.required = false);
                }
            });

            if (addressSelect.value !== 'new' && addressSelect.value !== '') {
                newAddressForm.classList.add('hidden');
                requiredInputs.forEach(input => input.required = false);
            } else {
                requiredInputs.forEach(input => input.required = true);
            }
        } else if (newAddressForm) {
            requiredInputs.forEach(input => input.required = true);
        }
        
        // Máscaras para CPF e Celular
        const cpfInput = document.getElementById('cpf');
        const celularInput = document.getElementById('celular');
        
        if (cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                // Formata: 000.000.000-00
                if (value.length > 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                } else if (value.length > 6) {
                    value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
                } else if (value.length > 3) {
                    value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
                }
                e.target.value = value;
            });
        }
        
        if (celularInput) {
            celularInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                // Formata: (00) 00000-0000
                if (value.length > 6) {
                    value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                } else if (value.length > 2) {
                    value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                } else if (value.length > 0) {
                    value = value.replace(/(\d{0,2})/, '($1');
                }
                e.target.value = value;
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const cepInput = document.getElementById('cep-input');
    const calculateBtn = document.getElementById('calculate-shipping-btn');
    const resultsContainer = document.getElementById('shipping-results-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const shippingServiceInput = document.getElementById('shipping_service_input');
    const shippingCostInput = document.getElementById('shipping_cost_input');
    const summaryShippingEl = document.getElementById('summary-shipping-cost');
    const summaryTotalEl = document.getElementById('summary-total');
    const summaryCard = document.querySelector('[data-subtotal]');
    const subtotal = parseFloat(summaryCard.dataset.subtotal);
    const addressSelect = document.getElementById('address_id');
    const newAddressCepInput = document.getElementById('cep');

    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    }

    calculateBtn.addEventListener('click', function() {
        let cep = cepInput.value.replace(/\D/g, '');

        if (cep.length !== 8) {
            showError('CEP inválido. Digite 8 números.');
            return;
        }

        showLoading();

        fetch('{{ route("checkout.shipping.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cep: cep })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { 
                    throw new Error(err.error || 'Erro ao calcular o frete.'); 
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.shipping_options && data.shipping_options.length > 0) {
                displayOptions(data.shipping_options);
            } else {
                showError('Nenhuma opção de frete encontrada para este CEP.');
            }
        })
        .catch(error => {
            console.error('Erro no fetch:', error);
            showError(error.message);
        });
    });

    resultsContainer.addEventListener('change', function(event) {
        if (event.target.name === 'shipping_option') {
            let selectedOption = event.target;
            let selectedPrice = parseFloat(selectedOption.getAttribute('data-price'));
            let newTotal = subtotal + selectedPrice;

            shippingServiceInput.value = selectedOption.getAttribute('data-service');
            shippingCostInput.value = selectedPrice;
            
            summaryShippingEl.innerText = formatCurrency(selectedPrice);
            summaryTotalEl.innerText = formatCurrency(newTotal);
        }
    });

    if (addressSelect) {
        addressSelect.addEventListener('change', function() {
            if (this.value !== 'new' && this.value !== '') {
                let selectedCep = this.options[this.selectedIndex].dataset.cep;
                if (selectedCep) {
                    cepInput.value = selectedCep;
                }
            } else {
                 cepInput.value = '';
            }
        });
    }

    if (newAddressCepInput) {
        newAddressCepInput.addEventListener('keyup', function() {
            cepInput.value = this.value.replace(/\D/g, '');
        });
    }

    function showLoading() {
        resultsContainer.innerHTML = '<div class="bg-blue-100 text-blue-800 px-4 py-2 rounded">Calculando...</div>';
    }

    function showError(message) {
        resultsContainer.innerHTML = `<div class="bg-red-100 text-red-800 px-4 py-2 rounded">${message}</div>`;
        clearShipping();
    }
    
    function displayOptions(options) {
        let html = '';
        options.forEach((option, index) => {
            let priceFormatted = formatCurrency(option.price);
            html += `
                <label class="flex items-center gap-3 p-4 border rounded-lg mb-2 cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="shipping_option" 
                           id="shipping_option_${index}" 
                           value="${option.service}"
                           data-service="${option.service} (${option.carrier})"
                           data-price="${option.price}" 
                           class="w-4 h-4 text-amber-500 focus:ring-amber-500" required>
                    <div class="flex-1">
                        <strong>${option.service} (${option.carrier})</strong><br>
                        <small class="text-gray-500">Prazo: ${option.deadline} dias úteis</small>
                    </div>
                    <strong class="text-green-600">${priceFormatted}</strong>
                </label>
            `;
        });
        resultsContainer.innerHTML = html;
    }

    function clearShipping() {
        shippingServiceInput.value = '';
        shippingCostInput.value = '';
        summaryShippingEl.innerText = 'A calcular';
        summaryTotalEl.innerText = formatCurrency(subtotal);
    }

    const checkoutForm = document.querySelector('form[action="{{ route("checkout.store") }}"]');
    let isSubmitting = false;
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            const addressId = document.getElementById('address_id')?.value;
            const newAddressForm = document.getElementById('new-address-form');
            const isNewAddressVisible = newAddressForm && !newAddressForm.classList.contains('hidden');
            
            if (!addressId || addressId === '' || addressId === 'new') {
                if (isNewAddressVisible) {
                    const rua = document.getElementById('rua')?.value.trim();
                    const numero = document.getElementById('numero')?.value.trim();
                    const cidade = document.getElementById('cidade')?.value.trim();
                    const estado = document.getElementById('estado')?.value.trim();
                    const cep = document.getElementById('cep')?.value.trim();
                    
                    if (!rua || !numero || !cidade || !estado || !cep) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios do endereço.');
                        document.querySelector('#new-address-form').scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        return false;
                    }
                } else {
                    e.preventDefault();
                    alert('Por favor, selecione um endereço existente ou preencha um novo endereço.');
                    document.querySelector('#new-address-form').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    return false;
                }
            }
            
            const shippingService = shippingServiceInput.value.trim();
            const shippingCost = shippingCostInput.value.trim();
            
            if (!shippingService || !shippingCost) {
                e.preventDefault();
                alert('Por favor, calcule e selecione uma opção de frete antes de finalizar o pedido.');
                document.querySelector('#shipping-results-container').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                return false;
            }
            
            const selectedShipping = document.querySelector('input[name="shipping_option"]:checked');
            if (!selectedShipping) {
                e.preventDefault();
                alert('Por favor, selecione uma opção de frete antes de finalizar o pedido.');
                document.querySelector('#shipping-results-container').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                return false;
            }
            
            isSubmitting = true;
            
            const submitBtn = checkoutForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processando...';
            }
        });
    }
});
</script>
@endpush
@endsection