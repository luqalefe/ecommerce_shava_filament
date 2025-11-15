@extends('layouts.main')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container py-5 my-4">
    <h1 class="text-center h1 fw-bold mb-5 text-uppercase">Finalizar Compra</h1>

    {{-- Formulário principal que englobará tudo --}}
    {{-- Adicionado 'novalidate' para pular a validação HTML dos campos escondidos --}}
    <form action="{{ route('checkout.store') }}" method="POST" novalidate>
        @csrf
        <div class="row gx-lg-5">

            {{-- Coluna Esquerda: Endereço, Frete e Pagamento --}}
            <div class="col-lg-7">
                {{-- Seção de Endereço --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Endereço de Entrega</h5>
                    </div>
                    <div class="card-body p-4">
                        {{-- Opção de selecionar endereço existente (se houver) --}}
                        @if($addresses->isNotEmpty())
                            <div class="mb-4">
                                <label for="address_id" class="form-label">Selecionar endereço existente:</label>
                                <select class="form-select" id="address_id" name="address_id">
                                    <option value="">-- Selecione --</option>
                                    @foreach($addresses as $address)
                                        {{-- MODIFICADO: Adicionado data-cep para o JS --}}
                                        <option value="{{ $address->id }}" data-cep="{{ preg_replace('/\D/', '', $address->cep) }}">
                                            {{ $address->rua }}, {{ $address->numero }} - {{ $address->cidade }}/{{ $address->estado }}
                                        </option>
                                    @endforeach
                                    <option value="new">-- Cadastrar novo endereço --</option>
                                </select>
                            </div>
                            <hr class="my-4">
                            <p class="text-center text-muted">Ou preencha um novo endereço abaixo:</p>
                        @else
                            <p class="text-muted">Cadastre seu endereço de entrega:</p>
                        @endif

                        {{-- Formulário para Novo Endereço (inicialmente escondido se houver endereços) --}}
                        <div id="new-address-form" class="{{ $addresses->isNotEmpty() ? 'd-none' : '' }}">
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <label for="rua" class="form-label">Rua*</label>
                                    <input type="text" class="form-control" id="rua" name="rua" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="numero" class="form-label">Número*</label>
                                    <input type="text" class="form-control" id="numero" name="numero" required>
                                </div>
                                <div class="col-12">
                                    <label for="complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento">
                                </div>
                                <div class="col-md-6">
                                    <label for="cidade" class="form-label">Cidade*</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="estado" class="form-label">Estado*</label>
                                    <input type="text" class="form-control" id="estado" name="estado" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="cep" class="form-label">CEP*</label>
                                    <input type="text" class="form-control" id="cep" name="cep" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Calcular Entrega</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted">Informe o CEP para calcular o valor da entrega.</p>
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label for="cep-input" class="form-label">CEP</label>
                                <input type="text" id="cep-input" class="form-control" placeholder="Apenas 8 números" maxlength="8">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="button" id="calculate-shipping-btn" class="btn btn-primary btn-block w-100">Calcular</button>
                            </div>
                        </div>
                        
                        <div id="shipping-results-container" class="mt-3">
                            </div>
                        
                        {{-- MODIFICADO: Removido 'required' e adicionado validação customizada --}}
                        <input type="hidden" name="shipping_service" id="shipping_service_input">
                        <input type="hidden" name="shipping_cost" id="shipping_cost_input">
                    </div>
                </div>
                {{-- Seção de Pagamento (Visual Placeholder) --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Forma de Pagamento</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted">Selecione como deseja pagar:</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_pix" value="pix" checked>
                            <label class="form-check-label" for="payment_pix">
                                Pix (Opção Padrão por enquanto)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card" disabled>
                            <label class="form-check-label text-muted" for="payment_card">
                                Cartão de Crédito (Em breve)
                            </label>
                        </div>
                        {{-- Adicione outras opções visuais aqui --}}
                    </div>
                </div>
            </div>

            {{-- Coluna Direita: Resumo do Pedido --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    {{-- MODIFICADO: Adicionado data-subtotal para o JS --}}
                    <div class="card-body p-4" data-subtotal="{{ $subTotal }}">
                        {{-- Listagem dos Itens --}}
                        @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/50'; @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: contain;">
                                    <div>
                                        <p class="mb-0 small fw-bold">{{ $item->name }}</p>
                                        <p class="mb-0 small text-muted">Qtd: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="fw-bold small">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <hr class="my-3">
                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        {{-- MODIFICADO: Frete agora é dinâmico --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete</span>
                            <span id="summary-shipping-cost">A calcular</span>
                        </div>
                        <hr class="my-3">
                        
                        {{-- MODIFICADO: Total agora é dinâmico --}}
                        <div class="d-flex justify-content-between fw-bold h5 mt-3">
                            <span>Total</span>
                            <span id="summary-total">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        {{-- Botão Finalizar --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg text-white fw-bold text-uppercase">
                                Finalizar Pedido
                            </button>
                        </div>
                        <p class="small text-muted text-center mt-3">Ao clicar em Finalizar Pedido, você concorda com nossos <a href="#">Termos e Condições</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Script para mostrar/esconder o formulário de novo endereço --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addressSelect = document.getElementById('address_id');
        const newAddressForm = document.getElementById('new-address-form');
        const requiredInputs = newAddressForm.querySelectorAll('input[required]');

        if (addressSelect && newAddressForm) {
            addressSelect.addEventListener('change', function () {
                if (this.value === 'new' || this.value === '') {
                    newAddressForm.classList.remove('d-none');
                    requiredInputs.forEach(input => input.required = true); // Torna os campos obrigatórios
                } else {
                    newAddressForm.classList.add('d-none');
                    requiredInputs.forEach(input => input.required = false); // Torna os campos não obrigatórios
                }
            });

            // Dispara o evento change na carga da página para ajustar o estado inicial
            if (addressSelect.value !== 'new' && addressSelect.value !== '') {
                newAddressForm.classList.add('d-none');
                requiredInputs.forEach(input => input.required = false);
            } else {
                requiredInputs.forEach(input => input.required = true);
            }
        } else if (newAddressForm) {
            // Caso não haja select (nenhum endereço salvo), garante que os inputs são required
            requiredInputs.forEach(input => input.required = true);
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Elementos do Frete ---
    const cepInput = document.getElementById('cep-input');
    const calculateBtn = document.getElementById('calculate-shipping-btn');
    const resultsContainer = document.getElementById('shipping-results-container');
    
    // ESTA LINHA AGORA É SEGURA, pois a tag meta está no layouts/main.blade.php
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // --- Inputs Escondidos do Formulário ---
    const shippingServiceInput = document.getElementById('shipping_service_input');
    const shippingCostInput = document.getElementById('shipping_cost_input');

    // --- Elementos do Resumo do Pedido ---
    const summaryShippingEl = document.getElementById('summary-shipping-cost');
    const summaryTotalEl = document.getElementById('summary-total');
    const summaryCard = document.querySelector('.card-body[data-subtotal]');
    const subtotal = parseFloat(summaryCard.dataset.subtotal);

    // --- Elementos do Endereço ---
    const addressSelect = document.getElementById('address_id');
    const newAddressCepInput = document.getElementById('cep'); // CEP do *novo* endereço

    // --- Helper de Formatação ---
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    }

    // 1. AÇÃO: Calcular Frete (Botão)
    calculateBtn.addEventListener('click', function() {
        let cep = cepInput.value.replace(/\D/g, ''); // Limpa CEP

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

    // 2. AÇÃO: Selecionar Opção de Frete (Radio Button)
    resultsContainer.addEventListener('change', function(event) {
        if (event.target.name === 'shipping_option') {
            let selectedOption = event.target;
            let selectedPrice = parseFloat(selectedOption.getAttribute('data-price'));
            let newTotal = subtotal + selectedPrice;

            // Preenche os inputs escondidos para o formulário
            shippingServiceInput.value = selectedOption.getAttribute('data-service');
            shippingCostInput.value = selectedPrice;
            
            // Atualiza o Resumo do Pedido (na direita)
            summaryShippingEl.innerText = formatCurrency(selectedPrice);
            summaryTotalEl.innerText = formatCurrency(newTotal);
        }
    });

    // 3. (BÔNUS) AÇÃO: Selecionar Endereço Salvo
    if (addressSelect) {
        addressSelect.addEventListener('change', function() {
            if (this.value !== 'new' && this.value !== '') {
                // Tenta pegar o 'data-cep' do option selecionado
                let selectedCep = this.options[this.selectedIndex].dataset.cep;
                if (selectedCep) {
                    cepInput.value = selectedCep; // Preenche o CEP do frete
                }
            } else {
                 cepInput.value = ''; // Limpa se for 'novo' ou 'selecione'
            }
        });
    }

    // 4. (BÔNUS) AÇÃO: Digitar no CEP do Novo Endereço
    if (newAddressCepInput) {
        newAddressCepInput.addEventListener('keyup', function() {
            cepInput.value = this.value.replace(/\D/g, ''); // Espelha o CEP no campo de frete
        });
    }

    // --- Funções Auxiliares de UI ---
    function showLoading() {
        resultsContainer.innerHTML = '<div class="alert alert-info py-2">Calculando...</div>';
    }

    function showError(message) {
        resultsContainer.innerHTML = `<div class="alert alert-danger py-2">${message}</div>`;
        clearShipping();
    }
    
    function displayOptions(options) {
        let html = '';
        options.forEach((option, index) => {
            let priceFormatted = formatCurrency(option.price);
            html += `
                <div class="form-check border p-3 mb-2 rounded">
                    <input class="form-check-input" type="radio" name="shipping_option" 
                           id="shipping_option_${index}" 
                           value="${option.service}"
                           data-service="${option.service} (${option.carrier})"
                           data-price="${option.price}" required>
                           
                    <label class="form-check-label w-100" for="shipping_option_${index}">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${option.service} (${option.carrier})</strong><br>
                                <small class="text-muted">Prazo: ${option.deadline} dias úteis</small>
                            </div>
                            <strong class="text-success ms-3">${priceFormatted}</strong>
                        </div>
                    </label>
                </div>
            `;
        });
        resultsContainer.innerHTML = html;
        // REMOVIDO: clearShipping() - não limpar mais aqui, apenas quando necessário
    }

    function clearShipping() {
        // Limpa os inputs escondidos
        shippingServiceInput.value = '';
        shippingCostInput.value = '';
        
        // Reseta o Resumo do Pedido
        summaryShippingEl.innerText = 'A calcular';
        summaryTotalEl.innerText = formatCurrency(subtotal);
    }

    // --- VALIDAÇÃO ANTES DO SUBMIT ---
    const checkoutForm = document.querySelector('form[action="{{ route("checkout.store") }}"]');
    let isSubmitting = false; // Flag para evitar submit duplo
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Previne múltiplos submits
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Validação do endereço
            const addressId = document.getElementById('address_id')?.value;
            const newAddressForm = document.getElementById('new-address-form');
            const isNewAddressVisible = newAddressForm && !newAddressForm.classList.contains('d-none');
            
            if (!addressId || addressId === '' || addressId === 'new') {
                // Se não há endereço selecionado, verifica se o formulário novo está visível e preenchido
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
            
            // Validação do frete
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
            
            // Valida se um radio foi selecionado
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
            
            // Se passou todas as validações, marca como submetendo e permite o submit
            isSubmitting = true;
            
            // Desabilita o botão para evitar múltiplos cliques
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