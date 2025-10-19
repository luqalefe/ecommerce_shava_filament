@extends('layouts.main')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container py-5 my-4">
    <h1 class="text-center h1 fw-bold mb-5 text-uppercase">Finalizar Compra</h1>

    {{-- Formulário principal que englobará tudo --}}
    {{-- A action apontará para a rota 'checkout.store' que criaremos depois --}}
    <form action="#" method="POST">
        @csrf
        <div class="row gx-lg-5">

            {{-- Coluna Esquerda: Endereço e Pagamento --}}
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
                                        <option value="{{ $address->id }}">
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
                                {{-- Checkbox para salvar endereço (lógica virá depois) --}}
                                {{-- <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_address" name="save_address" value="1" checked>
                                        <label class="form-check-label" for="save_address">
                                            Salvar este endereço para futuras compras
                                        </label>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
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
                    <div class="card-body p-4">
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
                        {{-- Frete (Placeholder) --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete</span>
                            <span>A calcular</span>
                        </div>
                        <hr class="my-3">
                        {{-- Total --}}
                        <div class="d-flex justify-content-between fw-bold h5 mt-3">
                            <span>Total</span>
                            <span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span> {{-- Incluir frete aqui depois --}}
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
@endpush

@endsection