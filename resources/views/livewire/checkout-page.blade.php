<div class="checkout-page-wrapper">
    <script>
        // Formatação de CEP e listener para foco automático
        (function() {
            function formatCep(value) {
                const numbers = value.replace(/\D/g, '');
                if (numbers.length > 5) {
                    return numbers.substring(0, 5) + '-' + numbers.substring(5, 8);
                }
                return numbers;
            }

            function setupCepInput() {
                const cepInput = document.getElementById('cep');
                if (!cepInput || cepInput.dataset.setup === 'true') return;
                
                cepInput.dataset.setup = 'true';
                
                cepInput.addEventListener('input', function(e) {
                    const formatted = formatCep(e.target.value);
                    if (e.target.value !== formatted) {
                        e.target.value = formatted;
                    }
                });

                if (cepInput.value) {
                    cepInput.value = formatCep(cepInput.value);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupCepInput);
            } else {
                setupCepInput();
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('address-filled', () => {
                    setTimeout(() => {
                        const numeroField = document.getElementById('numero');
                        if (numeroField) {
                            numeroField.focus();
                        }
                    }, 100);
                });
            });

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setupCepInput();
                });
            });
        })();
    </script>

    {{-- Header Checkout --}}
    <div class="checkout-header">
        <div class="checkout-header-inner">
            <a href="{{ route('home') }}" class="checkout-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Shava Haux
            </a>
            <div class="checkout-secure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Checkout Seguro</span>
            </div>
        </div>
        {{-- Progress Steps --}}
        <div class="checkout-steps">
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-label">Endereço</span>
            </div>
            <div class="step-line"></div>
            <div class="step {{ $selectedShipping !== null ? 'active' : '' }}">
                <span class="step-number">2</span>
                <span class="step-label">Frete</span>
            </div>
            <div class="step-line"></div>
            <div class="step {{ $paymentMethod ? 'active' : '' }}">
                <span class="step-number">3</span>
                <span class="step-label">Pagamento</span>
            </div>
        </div>
    </div>

    <div class="checkout-container">
        {{-- Alertas --}}
        @if(session()->has('error'))
            <div class="checkout-alert error">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if(session()->has('message'))
            <div class="checkout-alert success">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>{{ session('message') }}</span>
                <button type="button" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @error('payment')
            <div class="checkout-alert error">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- PIX QR Code Modal --}}
        @if($showPixQrCode)
            <div class="pix-modal-overlay" x-data="{ copied: false, checkingStatus: false }" x-init="
                // Polling para verificar status do pagamento a cada 5 segundos
                setInterval(() => {
                    if (!checkingStatus) {
                        checkingStatus = true;
                        $wire.checkPixPaymentStatus().finally(() => checkingStatus = false);
                    }
                }, 5000);
            ">
                <div class="pix-modal">
                    <div class="pix-modal-header">
                        <div class="pix-success-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2>Pedido Criado!</h2>
                        <p>Escaneie o QR Code ou copie o código PIX</p>
                    </div>

                    <div class="pix-qr-container">
                        @if($pixQrCodeBase64)
                            <img src="data:image/png;base64,{{ $pixQrCodeBase64 }}" alt="QR Code PIX" class="pix-qr-image">
                        @else
                            <div class="pix-qr-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm12 4h4v-4h-4v4zM8 4h4v4H8V4zM4 8h4v4H4V8zm8-4h4v4h-4V4z"/>
                                </svg>
                                <p>QR Code</p>
                            </div>
                        @endif
                    </div>

                    <div class="pix-code-container">
                        <label>Código PIX (Copia e Cola)</label>
                        <div class="pix-code-wrapper">
                            <input type="text" readonly value="{{ $pixQrCode }}" id="pixCode" class="pix-code-input">
                            <button type="button" 
                                @click="
                                    navigator.clipboard.writeText('{{ $pixQrCode }}').then(() => {
                                        copied = true;
                                        setTimeout(() => copied = false, 3000);
                                    });
                                "
                                class="pix-copy-btn"
                                :class="copied ? 'copied' : ''"
                            >
                                <span x-show="!copied">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                    </svg>
                                    Copiar
                                </span>
                                <span x-show="copied">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                    Copiado!
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="pix-info">
                        <div class="pix-info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>O pagamento é processado em segundos</span>
                        </div>
                        <div class="pix-info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            <span>Transação 100% segura</span>
                        </div>
                    </div>

                    <div class="pix-status" wire:poll.5s="checkPixPaymentStatus">
                        <div class="pix-status-indicator">
                            <div class="pulse-dot"></div>
                            <span>Aguardando pagamento...</span>
                        </div>
                    </div>

                    <div class="pix-footer">
                        <p>Pedido #{{ $pixOrderId }}</p>
                        <a href="{{ route('orders.index') }}" class="pix-orders-link">Ver Meus Pedidos</a>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="placeOrder">
            <div class="checkout-layout">
                
                {{-- COLUNA FORMULÁRIOS --}}
                <div class="checkout-forms">
                    
                    {{-- Dados Pessoais (se necessário) --}}
                    @if($needsCpf || $needsCelular)
                    <div class="checkout-card highlight">
                        <div class="card-header warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h2>Complete seus Dados</h2>
                        </div>
                        <p class="card-subtitle">Precisamos dessas informações para processar seu pagamento.</p>
                        
                        <div class="form-grid">
                            @if($needsCpf)
                            <div class="form-group">
                                <label for="cpf">CPF <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="cpf" 
                                    wire:model.blur="cpf"
                                    placeholder="000.000.000-00"
                                    maxlength="14"
                                    x-data
                                    x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2')"
                                    class="form-input @error('cpf') error @enderror"
                                >
                                @error('cpf') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            @endif
                            
                            @if($needsCelular)
                            <div class="form-group">
                                <label for="celular">Celular <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="celular" 
                                    wire:model.blur="celular"
                                    placeholder="(00) 00000-0000"
                                    maxlength="15"
                                    x-data
                                    x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2')"
                                    class="form-input @error('celular') error @enderror"
                                >
                                @error('celular') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Endereço de Entrega --}}
                    <div class="checkout-card">
                        <div class="card-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <h2>Endereço de Entrega</h2>
                        </div>
                        
                        <div class="form-section">
                            {{-- CEP --}}
                            <div class="form-group">
                                <label for="cep">CEP <span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <input 
                                        type="text" 
                                        id="cep" 
                                        wire:model.live.debounce.800ms="cep"
                                        wire:blur="searchAddressOnBlur"
                                        placeholder="00000-000"
                                        maxlength="9"
                                        class="form-input @error('cep') error @enderror"
                                    >
                                    <div wire:loading wire:target="searchAddress,searchAddressOnBlur" class="input-spinner">
                                        <svg class="animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('cep') <p class="form-error">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="searchAddress,searchAddressOnBlur">
                                    <p class="form-hint loading">Buscando endereço...</p>
                                </div>
                            </div>

                            {{-- Rua e Número --}}
                            <div class="form-grid cols-3-1">
                                <div class="form-group">
                                    <label for="rua">Rua <span class="required">*</span></label>
                                    <input type="text" id="rua" wire:model.blur="rua" class="form-input @error('rua') error @enderror">
                                    @error('rua') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número <span class="required">*</span></label>
                                    <input type="text" id="numero" wire:model.blur="numero" class="form-input @error('numero') error @enderror">
                                    @error('numero') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Complemento e Bairro --}}
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="complemento">Complemento <span class="optional">(opcional)</span></label>
                                    <input type="text" id="complemento" wire:model="complemento" placeholder="Apto, Bloco, etc." class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro <span class="required">*</span></label>
                                    <input type="text" id="bairro" wire:model.blur="bairro" class="form-input @error('bairro') error @enderror">
                                    @error('bairro') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Cidade e Estado --}}
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="cidade">Cidade <span class="required">*</span></label>
                                    <input type="text" id="cidade" wire:model.blur="cidade" class="form-input @error('cidade') error @enderror">
                                    @error('cidade') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-group" x-data="{
                                    estados: [
                                        { sigla: 'AC', nome: 'Acre' }, { sigla: 'AL', nome: 'Alagoas' }, { sigla: 'AP', nome: 'Amapá' },
                                        { sigla: 'AM', nome: 'Amazonas' }, { sigla: 'BA', nome: 'Bahia' }, { sigla: 'CE', nome: 'Ceará' },
                                        { sigla: 'DF', nome: 'Distrito Federal' }, { sigla: 'ES', nome: 'Espírito Santo' }, { sigla: 'GO', nome: 'Goiás' },
                                        { sigla: 'MA', nome: 'Maranhão' }, { sigla: 'MT', nome: 'Mato Grosso' }, { sigla: 'MS', nome: 'Mato Grosso do Sul' },
                                        { sigla: 'MG', nome: 'Minas Gerais' }, { sigla: 'PA', nome: 'Pará' }, { sigla: 'PB', nome: 'Paraíba' },
                                        { sigla: 'PR', nome: 'Paraná' }, { sigla: 'PE', nome: 'Pernambuco' }, { sigla: 'PI', nome: 'Piauí' },
                                        { sigla: 'RJ', nome: 'Rio de Janeiro' }, { sigla: 'RN', nome: 'Rio Grande do Norte' }, { sigla: 'RS', nome: 'Rio Grande do Sul' },
                                        { sigla: 'RO', nome: 'Rondônia' }, { sigla: 'RR', nome: 'Roraima' }, { sigla: 'SC', nome: 'Santa Catarina' },
                                        { sigla: 'SP', nome: 'São Paulo' }, { sigla: 'SE', nome: 'Sergipe' }, { sigla: 'TO', nome: 'Tocantins' }
                                    ],
                                    search: '', open: false, selectedEstado: null,
                                    get filteredEstados() {
                                        if (!this.search) return this.estados;
                                        const s = this.search.toLowerCase();
                                        return this.estados.filter(e => e.nome.toLowerCase().includes(s) || e.sigla.toLowerCase().includes(s));
                                    },
                                    selectEstado(e) { this.selectedEstado = e; this.search = e.sigla; @this.set('estado', e.sigla); this.open = false; },
                                    init() {
                                        const c = @js($estado);
                                        if (c) { const e = this.estados.find(x => x.sigla === c.toUpperCase()); if (e) { this.selectedEstado = e; this.search = e.sigla; } }
                                        $watch('$wire.estado', (v) => { if (v) { const e = this.estados.find(x => x.sigla === v.toUpperCase()); if (e) { this.selectedEstado = e; this.search = e.sigla; } } });
                                    }
                                }">
                                    <label for="estado">Estado <span class="required">*</span></label>
                                    <div class="select-wrapper">
                                        <input 
                                            type="text" 
                                            id="estado" 
                                            x-model="search"
                                            @click="open = true"
                                            @focus="open = true"
                                            @keydown.escape="open = false"
                                            @keydown.enter.prevent="if (filteredEstados.length === 1) selectEstado(filteredEstados[0])"
                                            placeholder="UF"
                                            class="form-input uppercase @error('estado') error @enderror"
                                        >
                                        <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                                        
                                        <div x-show="open" @click.away="open = false" x-transition class="select-dropdown" style="display: none;">
                                            <template x-if="filteredEstados.length === 0">
                                                <div class="dropdown-empty">Nenhum estado encontrado</div>
                                            </template>
                                            <template x-for="estado in filteredEstados" :key="estado.sigla">
                                                <button type="button" @click="selectEstado(estado)" class="dropdown-item" :class="selectedEstado?.sigla === estado.sigla ? 'selected' : ''">
                                                    <span><strong x-text="estado.sigla"></strong> <span x-text="estado.nome"></span></span>
                                                    <svg x-show="selectedEstado?.sigla === estado.sigla" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    @error('estado') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Opções de Frete --}}
                        @if($loading)
                            <div class="shipping-loading">
                                <svg class="animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p>Calculando opções de frete...</p>
                            </div>
                        @elseif(!empty($shippingOptions))
                            <div class="shipping-options">
                                <h3>Selecione o frete:</h3>
                                <div class="shipping-list">
                                    @foreach($shippingOptions as $index => $option)
                                        <label class="shipping-option {{ $selectedShipping === $index ? 'selected' : '' }}">
                                            <input 
                                                type="radio" 
                                                name="shipping_option" 
                                                wire:click="selectShipping({{ $index }})"
                                                {{ $selectedShipping === $index ? 'checked' : '' }}
                                            >
                                            <div class="shipping-info">
                                                <span class="shipping-name">{{ $option['service'] }}</span>
                                                <span class="shipping-details">{{ $option['carrier'] }}</span>
                                            </div>
                                            <span class="shipping-price">R$ {{ number_format($option['price'], 2, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('shippingService') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        @elseif($cep && !$loading)
                            <div class="shipping-empty">
                                <p>Nenhuma opção de frete encontrada para este CEP.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Forma de Pagamento --}}
                    <div class="checkout-card">
                        <div class="card-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            <h2>Forma de Pagamento</h2>
                        </div>
                        
                        <div class="payment-options">
                            {{-- PIX via Mercado Pago --}}
                            <label class="payment-option {{ $paymentMethod === 'pix' ? 'selected' : '' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="pix">
                                <div class="payment-icon pix-icon">
                                    <svg viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M242.4 292.5c-2.3-2.3-6.1-2.3-8.5 0l-56.5 56.5c-15.7 15.7-41 15.7-56.6 0-15.6-15.6-15.6-41 0-56.6l56.5-56.5c2.3-2.3 2.3-6.1 0-8.5l-14.1-14.1c-2.3-2.3-6.1-2.3-8.5 0l-56.5 56.5c-28.1 28.1-28.1 73.6 0 101.7 28.1 28.1 73.6 28.1 101.7 0l56.5-56.5c2.3-2.3 2.3-6.1 0-8.5l-14-14zm-56.8-56.9l14.1-14.1c2.3-2.3 2.3-6.1 0-8.5l-56.5-56.5c-28.1-28.1-28.1-73.6 0-101.7 28.1-28.1 73.6-28.1 101.7 0l56.5 56.5c2.3 2.3 6.1 2.3 8.5 0l14.1-14.1c2.3-2.3 2.3-6.1 0-8.5l-56.5-56.5c-40.6-40.6-106.5-40.6-147.1 0-40.6 40.6-40.6 106.5 0 147.1l56.5 56.5c2.4 2.4 6.2 2.4 8.5 0zm198.3-84.4l-56.5 56.5c-2.3 2.3-2.3 6.1 0 8.5l14.1 14.1c2.3 2.3 6.1 2.3 8.5 0l56.5-56.5c15.7-15.7 41-15.7 56.6 0 15.6 15.6 15.6 41 0 56.6l-56.5 56.5c-2.3 2.3-2.3 6.1 0 8.5l14.1 14.1c2.3 2.3 6.1 2.3 8.5 0l56.5-56.5c28.1-28.1 28.1-73.6 0-101.7-28.1-28.1-73.7-28.1-101.8 0zm56.9 56.9l-14.1 14.1c-2.3 2.3-2.3 6.1 0 8.5l56.5 56.5c28.1 28.1 28.1 73.6 0 101.7-28.1 28.1-73.6 28.1-101.7 0l-56.5-56.5c-2.3-2.3-6.1-2.3-8.5 0l-14.1 14.1c-2.3 2.3-2.3 6.1 0 8.5l56.5 56.5c40.6 40.6 106.5 40.6 147.1 0 40.6-40.6 40.6-106.5 0-147.1l-56.5-56.5c-2.4-2.4-6.2-2.4-8.5 0z"/>
                                    </svg>
                                </div>
                                <div class="payment-info">
                                    <span class="payment-name">PIX</span>
                                    <span class="payment-badge pix-badge">Instantâneo</span>
                                    <span class="payment-desc">Pague instantaneamente com QR Code</span>
                                </div>
                            </label>

                            {{-- Cartão de Crédito via Mercado Pago Checkout Pro --}}
                            <label class="payment-option {{ $paymentMethod === 'card' ? 'selected' : '' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="card">
                                <div class="payment-icon card-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                        <line x1="1" y1="10" x2="23" y2="10"/>
                                    </svg>
                                </div>
                                <div class="payment-info">
                                    <span class="payment-name">Cartão de Crédito</span>
                                    <span class="payment-badge card-badge">Parcelado</span>
                                    <span class="payment-desc">Pague em até 12x via Mercado Pago</span>
                                </div>
                            </label>

                            {{-- Pagamento na Entrega (apenas PJ + Rio Branco) --}}
                            @if($canPayOnDelivery)
                            <label class="payment-option {{ $paymentMethod === 'delivery' ? 'selected' : '' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="delivery">
                                <div class="payment-icon delivery-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="3" width="15" height="13" rx="2"/>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                        <circle cx="5.5" cy="18.5" r="2.5"/>
                                        <circle cx="18.5" cy="18.5" r="2.5"/>
                                    </svg>
                                </div>
                                <div class="payment-info">
                                    <span class="payment-name">Pagamento na Entrega</span>
                                    <span class="payment-badge delivery-badge">Exclusivo PJ</span>
                                    <span class="payment-desc">Pague em dinheiro ou cartão na entrega</span>
                                </div>
                            </label>
                            @endif
                        </div>
                        
                        <div class="payment-security">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            <span>Seus dados estão protegidos e criptografados</span>
                        </div>
                    </div>
                </div>

                {{-- RESUMO DO PEDIDO --}}
                <div class="checkout-summary-section">
                    {{-- Mobile: Resumo Colapsável --}}
                    <div class="summary-mobile">
                        <button type="button" wire:click="toggleSummary" class="summary-toggle">
                            <div>
                                <h2>Resumo do Pedido</h2>
                                <p>{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'item' : 'itens' }} • R$ {{ number_format($total, 2, ',', '.') }}</p>
                            </div>
                            <svg class="{{ $summaryExpanded ? 'rotate' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        @if($summaryExpanded)
                        <div class="summary-content">
                            <div class="summary-items">
                                @foreach($cartItems as $item)
                                    <div class="summary-item">
                                        @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/60'; @endphp
                                        <img src="{{ $imageUrl }}" alt="{{ $item->name }}">
                                        <div class="item-details">
                                            <p class="item-name">{{ $item->name }}</p>
                                            <p class="item-qty">Qtd: {{ $item->quantity }}</p>
                                        </div>
                                        <span class="item-price">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="summary-totals">
                                <div class="total-row"><span>Subtotal</span><span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span></div>
                                <div class="total-row"><span>Frete</span><span>@if($shippingCost > 0) R$ {{ number_format($shippingCost, 2, ',', '.') }} @else A calcular @endif</span></div>
                                <div class="total-row final"><span>Total</span><span>R$ {{ number_format($total, 2, ',', '.') }}</span></div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Desktop: Resumo Sticky --}}
                    <div class="summary-desktop">
                        <div class="summary-card">
                            <h2>Resumo do Pedido</h2>
                            
                            <div class="summary-items">
                                @foreach($cartItems as $item)
                                    <div class="summary-item">
                                        @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/60'; @endphp
                                        <img src="{{ $imageUrl }}" alt="{{ $item->name }}">
                                        <div class="item-details">
                                            <p class="item-name">{{ $item->name }}</p>
                                            <p class="item-qty">Qtd: {{ $item->quantity }}</p>
                                        </div>
                                        <span class="item-price">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="summary-totals">
                                <div class="total-row"><span>Subtotal</span><span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span></div>
                                <div class="total-row"><span>Frete</span><span>@if($shippingCost > 0) R$ {{ number_format($shippingCost, 2, ',', '.') }} @else <span class="muted">A calcular</span> @endif</span></div>
                            </div>
                            
                            <div class="summary-final">
                                <span>Total</span>
                                <div class="final-value">
                                    <span class="total-price">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                    {{-- Desconto PIX removido --}}
                                </div>
                            </div>
                            
                            <button type="submit" wire:loading.attr="disabled" wire:target="placeOrder" class="btn-checkout">
                                <span wire:loading.remove wire:target="placeOrder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Finalizar Pedido
                                </span>
                                <span wire:loading wire:target="placeOrder">
                                    <svg class="animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Processando...
                                </span>
                            </button>
                            
                            <p class="checkout-redirect">Você será redirecionado para o pagamento seguro</p>
                            
                            <div class="trust-badges">
                                <div class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span>Seguro</span></div>
                                <div class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><span>Cartão</span></div>
                                <div class="badge pix"><svg viewBox="0 0 512 512" fill="currentColor"><path d="M242.4 292.5c-2.3-2.3-6.1-2.3-8.5 0l-56.5 56.5c-15.7 15.7-41 15.7-56.6 0-15.6-15.6-15.6-41 0-56.6l56.5-56.5c2.3-2.3 2.3-6.1 0-8.5l-14.1-14.1c-2.3-2.3-6.1-2.3-8.5 0l-56.5 56.5c-28.1 28.1-28.1 73.6 0 101.7z"/></svg><span>PIX</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Sticky Footer --}}
            <div class="checkout-sticky-footer">
                <div class="sticky-info">
                    <span class="sticky-label">Total</span>
                    <span class="sticky-price">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="placeOrder" class="sticky-btn">
                    <span wire:loading.remove wire:target="placeOrder">Finalizar</span>
                    <span wire:loading wire:target="placeOrder">...</span>
                </button>
            </div>
        </form>
    </div>

    <div class="checkout-spacer"></div>

    <style>
    /* ============================================
       CHECKOUT PAGE - AMAZON STYLE
       ============================================ */

    .checkout-page-wrapper {
        min-height: 100vh;
        background: #F5F5F4;
    }

    /* Header */
    .checkout-header {
        background: white;
        border-bottom: 1px solid #E7E5E4;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .checkout-header-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .checkout-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #1C1917;
        text-decoration: none;
        font-family: 'Playfair Display', serif;
    }

    .checkout-logo svg {
        width: 20px;
        height: 20px;
        color: #78716C;
    }

    .checkout-secure {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #059669;
    }

    .checkout-secure svg {
        width: 18px;
        height: 18px;
    }

    .checkout-secure span { display: none; }
    @media (min-width: 640px) { .checkout-secure span { display: inline; } }

    /* Progress Steps */
    .checkout-steps {
        max-width: 500px;
        margin: 0 auto;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #A8A29E;
    }

    .step.active { color: var(--sh-muted-gold, #A69067); }

    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #E7E5E4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .step.active .step-number {
        background: var(--sh-muted-gold, #A69067);
        color: white;
    }

    .step-label {
        font-size: 0.85rem;
        font-weight: 500;
        display: none;
    }

    @media (min-width: 640px) { .step-label { display: inline; } }

    .step-line {
        width: 40px;
        height: 2px;
        background: #E7E5E4;
        margin: 0 0.5rem;
    }

    @media (min-width: 640px) { .step-line { width: 60px; } }

    /* Container */
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    @media (min-width: 1024px) {
        .checkout-container { padding: 2rem; }
    }

    /* Alerts */
    .checkout-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .checkout-alert svg { width: 20px; height: 20px; flex-shrink: 0; }
    .checkout-alert span { flex: 1; }
    .checkout-alert button { background: none; border: none; font-size: 1.5rem; cursor: pointer; opacity: 0.7; }
    .checkout-alert.success { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .checkout-alert.error { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }

    /* Layout */
    .checkout-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    @media (min-width: 1024px) {
        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            align-items: start;
        }
    }

    /* Cards */
    .checkout-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }

    .checkout-card.highlight {
        border: 2px solid #F59E0B;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .card-header svg { width: 24px; height: 24px; color: var(--sh-muted-gold, #A69067); }
    .card-header.warning svg { color: #F59E0B; }
    .card-header h2 { font-size: 1.25rem; font-weight: 700; color: #1C1917; margin: 0; }
    .card-subtitle { font-size: 0.9rem; color: #78716C; margin-bottom: 1.5rem; }

    /* Form Elements */
    .form-section { display: flex; flex-direction: column; gap: 1.25rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-grid.cols-3-1 { grid-template-columns: 2fr 1fr; }
    @media (max-width: 640px) { .form-grid, .form-grid.cols-3-1 { grid-template-columns: 1fr; } }

    .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
    .form-group label { font-size: 0.9rem; font-weight: 500; color: #44403C; }
    .form-group .required { color: #DC2626; }
    .form-group .optional { font-size: 0.75rem; color: #A8A29E; font-weight: 400; }

    .form-input {
        width: 100%;
        height: 48px;
        padding: 0 1rem;
        border: 2px solid #E7E5E4;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--sh-muted-gold, #A69067);
        box-shadow: 0 0 0 3px rgba(166, 144, 103, 0.1);
    }

    .form-input.error { border-color: #DC2626; }
    .form-input.uppercase { text-transform: uppercase; }
    .form-error { font-size: 0.8rem; color: #DC2626; margin: 0; }
    .form-hint { font-size: 0.8rem; color: #78716C; }
    .form-hint.loading { color: var(--sh-muted-gold, #A69067); }

    .input-with-icon { position: relative; }
    .input-spinner { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
    .input-spinner svg { width: 20px; height: 20px; color: var(--sh-muted-gold, #A69067); }

    /* Select Dropdown */
    .select-wrapper { position: relative; }
    .select-arrow { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #78716C; pointer-events: none; }

    .select-dropdown {
        position: absolute;
        z-index: 50;
        width: 100%;
        margin-top: 0.5rem;
        background: white;
        border: 1px solid #E7E5E4;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        max-height: 250px;
        overflow-y: auto;
    }

    .dropdown-empty { padding: 1rem; text-align: center; color: #78716C; font-size: 0.9rem; }

    .dropdown-item {
        width: 100%;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: none;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
        text-align: left;
    }

    .dropdown-item:hover { background: #F5F5F4; }
    .dropdown-item.selected { background: rgba(166, 144, 103, 0.1); }
    .dropdown-item svg { width: 18px; height: 18px; color: var(--sh-muted-gold, #A69067); }

    /* Shipping Options */
    .shipping-loading {
        padding: 2rem;
        text-align: center;
        border-top: 1px solid #E7E5E4;
        margin-top: 1.5rem;
    }

    .shipping-loading svg { width: 32px; height: 32px; color: var(--sh-muted-gold, #A69067); margin: 0 auto 0.5rem; }
    .shipping-loading p { color: #78716C; }

    .shipping-options {
        border-top: 1px solid #E7E5E4;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
    }

    .shipping-options h3 { font-size: 1rem; font-weight: 600; color: #1C1917; margin-bottom: 1rem; }
    .shipping-list { display: flex; flex-direction: column; gap: 0.75rem; }

    .shipping-option {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 2px solid #E7E5E4;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .shipping-option:hover { border-color: #D6D3D1; }
    .shipping-option.selected { border-color: var(--sh-muted-gold, #A69067); background: rgba(166, 144, 103, 0.05); }
    .shipping-option input { width: 20px; height: 20px; accent-color: var(--sh-muted-gold, #A69067); }
    .shipping-info { flex: 1; }
    .shipping-name { font-weight: 600; color: #1C1917; display: block; }
    .shipping-details { font-size: 0.85rem; color: #78716C; }
    .shipping-price { font-size: 1.1rem; font-weight: 700; color: var(--sh-muted-gold, #A69067); }
    .shipping-empty { padding: 1.5rem; text-align: center; color: #78716C; border-top: 1px solid #E7E5E4; margin-top: 1.5rem; }

    /* Payment Options */
    .payment-options { display: flex; flex-direction: column; gap: 0.75rem; }

    .payment-option {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border: 2px solid #E7E5E4;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .payment-option:hover { border-color: #D6D3D1; }
    .payment-option.selected { border-color: var(--sh-muted-gold, #A69067); background: rgba(166, 144, 103, 0.05); }
    .payment-option input { width: 20px; height: 20px; margin-top: 2px; accent-color: var(--sh-muted-gold, #A69067); }

    .payment-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-icon svg { width: 24px; height: 24px; }
    .payment-icon.pix { background: #ECFDF5; }
    .payment-icon.pix svg { color: #059669; }
    .payment-icon.pix-icon { background: #ECFDF5; }
    .payment-icon.pix-icon svg { color: #059669; }
    .payment-icon.mp { background: #EFF6FF; }
    .payment-icon.mp svg { color: #2563EB; }
    .payment-icon.card-icon { background: #EFF6FF; }
    .payment-icon.card-icon svg { color: #2563EB; }

    .payment-info { flex: 1; }
    .payment-name { font-weight: 600; color: #1C1917; margin-right: 0.5rem; }
    .payment-badge { font-size: 0.7rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
    .payment-badge.instant { background: #ECFDF5; color: #059669; }
    .payment-badge.pix-badge { background: #ECFDF5; color: #059669; }
    .payment-badge.card { background: #EFF6FF; color: #2563EB; }
    .payment-badge.card-badge { background: #EFF6FF; color: #2563EB; }
    .payment-badge.delivery-badge { background: #FEF3C7; color: #D97706; }
    .payment-desc { display: block; font-size: 0.85rem; color: #78716C; margin-top: 0.25rem; }

    .payment-security {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #E7E5E4;
        font-size: 0.8rem;
        color: #78716C;
    }

    .payment-security svg { width: 16px; height: 16px; }

    /* Summary Section */
    .checkout-summary-section { order: -1; }
    @media (min-width: 1024px) { .checkout-summary-section { order: 0; } }

    .summary-mobile { display: block; }
    @media (min-width: 1024px) { .summary-mobile { display: none; } }

    .summary-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        text-align: left;
    }

    .summary-toggle h2 { font-size: 1rem; font-weight: 600; color: #1C1917; margin: 0; }
    .summary-toggle p { font-size: 0.85rem; color: #78716C; margin: 0.25rem 0 0 0; }
    .summary-toggle svg { width: 20px; height: 20px; color: #78716C; transition: transform 0.2s; }
    .summary-toggle svg.rotate { transform: rotate(180deg); }

    .summary-content {
        padding: 1rem 1.25rem;
        border-top: 1px solid #E7E5E4;
        background: white;
        border-radius: 0 0 12px 12px;
    }

    .summary-desktop { display: none; }
    @media (min-width: 1024px) { .summary-desktop { display: block; } }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        position: sticky;
        top: 140px;
    }

    .summary-card h2 { font-size: 1.25rem; font-weight: 700; color: #1C1917; margin: 0 0 1.5rem 0; }

    .summary-items {
        max-height: 250px;
        overflow-y: auto;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #E7E5E4;
    }

    .summary-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #F5F5F4;
    }

    .summary-item:last-child { border-bottom: none; }
    .summary-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
    .summary-item .item-details { flex: 1; min-width: 0; }
    .summary-item .item-name { font-size: 0.85rem; font-weight: 500; color: #1C1917; margin: 0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .summary-item .item-qty { font-size: 0.75rem; color: #78716C; margin: 0.25rem 0 0 0; }
    .summary-item .item-price { font-size: 0.85rem; font-weight: 600; color: #1C1917; white-space: nowrap; }

    .summary-totals { margin-bottom: 1rem; }
    .total-row { display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9rem; color: #57534E; }
    .total-row span:last-child { font-weight: 600; color: #1C1917; }
    .total-row .muted { color: #A8A29E; font-weight: 400; }

    .summary-final {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1rem 0;
        border-top: 2px solid #E7E5E4;
        margin-bottom: 1.5rem;
    }

    .summary-final > span { font-size: 1rem; font-weight: 600; color: #1C1917; }
    .final-value { text-align: right; }
    .total-price { font-size: 1.5rem; font-weight: 700; color: #1C1917; display: block; }
    .pix-discount { font-size: 0.8rem; color: #059669; display: block; margin-top: 0.25rem; }

    .btn-checkout {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #F59E0B;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        padding: 1rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-checkout:hover:not(:disabled) { background: #D97706; transform: translateY(-1px); }
    .btn-checkout:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-checkout svg { width: 20px; height: 20px; }

    .checkout-redirect { font-size: 0.8rem; color: #78716C; text-align: center; margin: 1rem 0; }

    .trust-badges {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #E7E5E4;
    }

    .trust-badges .badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .trust-badges .badge svg { width: 22px; height: 22px; color: var(--sh-muted-gold, #A69067); }
    .trust-badges .badge.pix svg { color: #059669; }
    .trust-badges .badge span { font-size: 0.7rem; color: #78716C; }

    /* Mobile Sticky Footer */
    .checkout-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #E7E5E4;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 100;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }

    @media (min-width: 1024px) { .checkout-sticky-footer { display: none; } }

    .sticky-info { display: flex; flex-direction: column; }
    .sticky-label { font-size: 0.8rem; color: #78716C; }
    .sticky-price { font-size: 1.25rem; font-weight: 700; color: #1C1917; }

    .sticky-btn {
        background: #F59E0B;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        padding: 1rem 2rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sticky-btn:hover:not(:disabled) { background: #D97706; }
    .sticky-btn:disabled { opacity: 0.6; }

    .checkout-spacer { height: 100px; }
    @media (min-width: 1024px) { .checkout-spacer { height: 0; } }

    .checkout-forms { order: 2; }
    @media (min-width: 1024px) { .checkout-forms { order: 1; } }

    /* ============================================
       PIX QR CODE MODAL
       ============================================ */
    .pix-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }

    .pix-modal {
        background: white;
        border-radius: 20px;
        max-width: 420px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .pix-modal-header {
        text-align: center;
        padding: 2rem 2rem 1rem;
    }

    .pix-success-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .pix-success-icon svg {
        width: 32px;
        height: 32px;
        color: white;
    }

    .pix-modal-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1C1917;
        margin: 0 0 0.5rem;
    }

    .pix-modal-header p {
        font-size: 0.95rem;
        color: #78716C;
        margin: 0;
    }

    .pix-qr-container {
        display: flex;
        justify-content: center;
        padding: 1rem 2rem;
    }

    .pix-qr-image {
        width: 200px;
        height: 200px;
        border-radius: 12px;
        border: 2px solid #E7E5E4;
        padding: 8px;
        background: white;
    }

    .pix-qr-placeholder {
        width: 200px;
        height: 200px;
        border-radius: 12px;
        border: 2px dashed #E7E5E4;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #A8A29E;
    }

    .pix-qr-placeholder svg {
        width: 48px;
        height: 48px;
    }

    .pix-code-container {
        padding: 0 2rem 1.5rem;
    }

    .pix-code-container label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #44403C;
        margin-bottom: 0.5rem;
    }

    .pix-code-wrapper {
        display: flex;
        gap: 0.5rem;
    }

    .pix-code-input {
        flex: 1;
        height: 48px;
        padding: 0 1rem;
        border: 2px solid #E7E5E4;
        border-radius: 10px;
        font-size: 0.85rem;
        color: #44403C;
        background: #F5F5F4;
        font-family: monospace;
        text-overflow: ellipsis;
    }

    .pix-copy-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        height: 48px;
        padding: 0 1.25rem;
        background: var(--sh-muted-gold, #A69067);
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .pix-copy-btn:hover {
        background: #8B7A5A;
    }

    .pix-copy-btn.copied {
        background: #059669;
    }

    .pix-copy-btn svg {
        width: 18px;
        height: 18px;
    }

    .pix-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem 2rem;
        background: #F5F5F4;
        border-top: 1px solid #E7E5E4;
        border-bottom: 1px solid #E7E5E4;
    }

    .pix-info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: #57534E;
    }

    .pix-info-item svg {
        width: 20px;
        height: 20px;
        color: #059669;
        flex-shrink: 0;
    }

    .pix-status {
        padding: 1.5rem 2rem;
        text-align: center;
    }

    .pix-status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        background: #FEF3C7;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #92400E;
    }

    .pulse-dot {
        width: 10px;
        height: 10px;
        background: #F59E0B;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.3);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .pix-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem 1.5rem;
    }

    .pix-footer p {
        font-size: 0.85rem;
        color: #78716C;
        margin: 0;
    }

    .pix-orders-link {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--sh-muted-gold, #A69067);
        text-decoration: none;
        transition: color 0.2s;
    }

    .pix-orders-link:hover {
        color: #8B7A5A;
        text-decoration: underline;
    }
    </style>
</div>
