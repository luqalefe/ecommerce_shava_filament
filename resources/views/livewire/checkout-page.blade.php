<div class="min-h-screen bg-gray-50">
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
                
                // Formatação visual apenas - o wire:model vai sincronizar normalmente
                // O Livewire já remove formatação no método updatedCep()
                cepInput.addEventListener('input', function(e) {
                    const formatted = formatCep(e.target.value);
                    // Só atualiza se diferente para evitar loop
                    if (e.target.value !== formatted) {
                        e.target.value = formatted;
                    }
                });

                // Formata valor inicial se existir
                if (cepInput.value) {
                    cepInput.value = formatCep(cepInput.value);
                }
            }

            // Inicializa quando DOM estiver pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupCepInput);
            } else {
                setupCepInput();
            }

            // Listener para evento de endereço preenchido (Livewire 3)
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

            // Para elementos criados dinamicamente pelo Livewire
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setupCepInput();
                });
            });
        })();
    </script>
    {{-- Header Minimalista --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 max-w-6xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">Shava Haux</a>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="hidden sm:inline">Compra 100% Segura</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-6xl">
        {{-- Mensagens de Erro/Sucesso --}}
        @if(session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session()->has('message'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        {{-- Erros do Livewire --}}
        @error('payment')
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ $message }}</p>
                </div>
            </div>
        @enderror

        <form wire:submit.prevent="placeOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                
                {{-- Coluna Esquerda: Formulário (Desktop) / Primeira seção (Mobile) --}}
                <div class="lg:col-span-2 space-y-6 order-2 lg:order-1">
                    
                    {{-- Seção de Endereço --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-6 lg:p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Endereço de Entrega</h2>
                        
                        <div class="space-y-5">
                            {{-- CEP --}}
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">
                                    CEP <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="cep" 
                                        wire:model.live.debounce.800ms="cep"
                                        wire:blur="searchAddressOnBlur"
                                        placeholder="00000-000"
                                        maxlength="9"
                                        class="w-full h-12 px-4 border rounded-lg transition-all duration-200
                                               @error('cep') border-red-300 focus:border-red-500 focus:ring-red-500 
                                               @else border-gray-300 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]
                                               @enderror
                                               focus:ring-2 focus:outline-none text-base"
                                    >
                                    <div wire:loading wire:target="searchAddress,searchAddressOnBlur" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-5 w-5 text-[var(--sh-muted-gold)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    @if($loadingCep && !$errors->has('cep'))
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <svg class="animate-spin h-5 w-5 text-[var(--sh-muted-gold)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                @error('cep') 
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p> 
                                @enderror
                                <div wire:loading wire:target="searchAddress,searchAddressOnBlur" class="mt-1.5">
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-[var(--sh-muted-gold)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Buscando endereço...
                                    </p>
                                </div>
                            </div>

                            {{-- Rua e Número --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="rua" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rua <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="rua" 
                                        wire:model.blur="rua"
                                        class="w-full h-12 px-4 border rounded-lg transition-all duration-200
                                               @error('rua') border-red-300 focus:border-red-500 focus:ring-red-500 
                                               @else border-gray-300 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]
                                               @enderror
                                               focus:ring-2 focus:outline-none text-base"
                                    >
                                    @error('rua') 
                                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="numero" 
                                        wire:model.blur="numero"
                                        class="w-full h-12 px-4 border rounded-lg transition-all duration-200
                                               @error('numero') border-red-300 focus:border-red-500 focus:ring-red-500 
                                               @else border-gray-300 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]
                                               @enderror
                                               focus:ring-2 focus:outline-none text-base"
                                    >
                                    @error('numero') 
                                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>

                            {{-- Complemento e Bairro --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="complemento" class="block text-sm font-medium text-gray-700 mb-2">
                                        Complemento <span class="text-gray-400 text-xs font-normal">(opcional)</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="complemento" 
                                        wire:model="complemento"
                                        placeholder="Apto, Bloco, etc."
                                        class="w-full h-12 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)] focus:outline-none transition-all duration-200 text-base"
                                    >
                                </div>
                                <div>
                                    <label for="bairro" class="block text-sm font-medium text-gray-700 mb-2">
                                        Bairro <span class="text-gray-400 text-xs font-normal">(opcional)</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="bairro" 
                                        wire:model="bairro"
                                        placeholder="Bairro"
                                        class="w-full h-12 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)] focus:outline-none transition-all duration-200 text-base"
                                    >
                                </div>
                            </div>

                            {{-- Cidade e Estado --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="cidade" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cidade <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="cidade" 
                                        wire:model.blur="cidade"
                                        class="w-full h-12 px-4 border rounded-lg transition-all duration-200
                                               @error('cidade') border-red-300 focus:border-red-500 focus:ring-red-500 
                                               @else border-gray-300 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]
                                               @enderror
                                               focus:ring-2 focus:outline-none text-base"
                                    >
                                    @error('cidade') 
                                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div x-data="{
                                    estados: [
                                        { sigla: 'AC', nome: 'Acre' },
                                        { sigla: 'AL', nome: 'Alagoas' },
                                        { sigla: 'AP', nome: 'Amapá' },
                                        { sigla: 'AM', nome: 'Amazonas' },
                                        { sigla: 'BA', nome: 'Bahia' },
                                        { sigla: 'CE', nome: 'Ceará' },
                                        { sigla: 'DF', nome: 'Distrito Federal' },
                                        { sigla: 'ES', nome: 'Espírito Santo' },
                                        { sigla: 'GO', nome: 'Goiás' },
                                        { sigla: 'MA', nome: 'Maranhão' },
                                        { sigla: 'MT', nome: 'Mato Grosso' },
                                        { sigla: 'MS', nome: 'Mato Grosso do Sul' },
                                        { sigla: 'MG', nome: 'Minas Gerais' },
                                        { sigla: 'PA', nome: 'Pará' },
                                        { sigla: 'PB', nome: 'Paraíba' },
                                        { sigla: 'PR', nome: 'Paraná' },
                                        { sigla: 'PE', nome: 'Pernambuco' },
                                        { sigla: 'PI', nome: 'Piauí' },
                                        { sigla: 'RJ', nome: 'Rio de Janeiro' },
                                        { sigla: 'RN', nome: 'Rio Grande do Norte' },
                                        { sigla: 'RS', nome: 'Rio Grande do Sul' },
                                        { sigla: 'RO', nome: 'Rondônia' },
                                        { sigla: 'RR', nome: 'Roraima' },
                                        { sigla: 'SC', nome: 'Santa Catarina' },
                                        { sigla: 'SP', nome: 'São Paulo' },
                                        { sigla: 'SE', nome: 'Sergipe' },
                                        { sigla: 'TO', nome: 'Tocantins' }
                                    ],
                                    search: '',
                                    open: false,
                                    selectedEstado: null,
                                    get filteredEstados() {
                                        if (!this.search) return this.estados;
                                        const searchLower = this.search.toLowerCase();
                                        return this.estados.filter(estado => 
                                            estado.nome.toLowerCase().includes(searchLower) ||
                                            estado.sigla.toLowerCase().includes(searchLower)
                                        );
                                    },
                                    selectEstado(estado) {
                                        this.selectedEstado = estado;
                                        this.search = estado.sigla;
                                        @this.set('estado', estado.sigla);
                                        this.open = false;
                                    },
                                    init() {
                                        // Sincroniza com o valor do Livewire
                                        const currentEstado = @js($estado);
                                        if (currentEstado) {
                                            const estado = this.estados.find(e => e.sigla === currentEstado.toUpperCase());
                                            if (estado) {
                                                this.selectedEstado = estado;
                                                this.search = estado.sigla;
                                            }
                                        }
                                        
                                        // Observa mudanças no Livewire
                                        $watch('$wire.estado', (value) => {
                                            if (value) {
                                                const estado = this.estados.find(e => e.sigla === value.toUpperCase());
                                                if (estado) {
                                                    this.selectedEstado = estado;
                                                    this.search = estado.sigla;
                                                }
                                            }
                                        });
                                    }
                                }" class="relative">
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="estado" 
                                            x-model="search"
                                            @click="open = true"
                                            @focus="open = true"
                                            @keydown.escape="open = false"
                                            @keydown.enter.prevent="if (filteredEstados.length === 1) selectEstado(filteredEstados[0])"
                                            placeholder="Buscar estado..."
                                            class="w-full h-12 px-4 pr-10 border rounded-lg transition-all duration-200 uppercase
                                                   @error('estado') border-red-300 focus:border-red-500 focus:ring-red-500 
                                                   @else border-gray-300 focus:border-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)]
                                                   @enderror
                                                   focus:ring-2 focus:outline-none text-base"
                                        >
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        
                                        {{-- Dropdown --}}
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"
                                             style="display: none;">
                                            <template x-if="filteredEstados.length === 0">
                                                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                                    Nenhum estado encontrado
                                                </div>
                                            </template>
                                            <template x-for="estado in filteredEstados" :key="estado.sigla">
                                                <button 
                                                    type="button"
                                                    @click="selectEstado(estado)"
                                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                                                    :class="selectedEstado?.sigla === estado.sigla ? 'bg-[var(--sh-muted-gold)]/10' : ''"
                                                >
                                                    <div>
                                                        <span class="font-semibold text-gray-900 uppercase" x-text="estado.sigla"></span>
                                                        <span class="text-gray-600 ml-2" x-text="estado.nome"></span>
                                                    </div>
                                                    <svg x-show="selectedEstado?.sigla === estado.sigla" class="w-5 h-5 text-[var(--sh-muted-gold)]" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    @error('estado') 
                                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>

                            {{-- Opções de Frete --}}
                            @if($loading)
                                <div class="py-8 text-center">
                                    <svg class="animate-spin h-8 w-8 text-[var(--sh-muted-gold)] mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-600">Calculando opções de frete...</p>
                                </div>
                            @elseif(!empty($shippingOptions))
                                <div class="pt-6 border-t border-gray-200">
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">Selecione o frete:</h3>
                                    <div class="space-y-3">
                                        @foreach($shippingOptions as $index => $option)
                                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                                   {{ $selectedShipping === $index 
                                                       ? 'border-[var(--sh-muted-gold)] bg-[var(--sh-muted-gold)]/5 shadow-sm' 
                                                       : 'border-gray-200 hover:border-gray-300 bg-white' 
                                                   }}">
                                                <input 
                                                    type="radio" 
                                                    name="shipping_option" 
                                                    wire:click="selectShipping({{ $index }})"
                                                    class="mt-1 mr-4 w-5 h-5 text-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)] cursor-pointer"
                                                    {{ $selectedShipping === $index ? 'checked' : '' }}
                                                >
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-semibold text-gray-900 mb-1">{{ $option['service'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $option['carrier'] }} • {{ $option['deadline'] }} dias úteis</div>
                                                </div>
                                                <div class="ml-4 font-bold text-lg text-[var(--sh-muted-gold)] whitespace-nowrap">
                                                    R$ {{ number_format($option['price'], 2, ',', '.') }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('shippingService')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @elseif($cep && !$loading)
                                <div class="pt-6 border-t border-gray-200 text-center py-4">
                                    <p class="text-gray-500">Nenhuma opção de frete encontrada para este CEP.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Seção de Pagamento --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-6 lg:p-8">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <h2 class="text-xl font-semibold text-gray-900">Forma de Pagamento</h2>
                        </div>
                        <div class="space-y-3">
                            {{-- Opção PIX --}}
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                   {{ $paymentMethod === 'pix' ? 'border-[var(--sh-muted-gold)] bg-[var(--sh-muted-gold)]/5' : 'border-gray-200 hover:border-gray-300' }}">
                                <input 
                                    type="radio" 
                                    wire:model="paymentMethod"
                                    value="pix"
                                    class="mr-4 w-5 h-5 text-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)] cursor-pointer"
                                >
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-900">PIX</span>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded">Instantâneo</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Pagamento aprovado na hora</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </label>

                            {{-- Opção Mercado Pago --}}
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                                   {{ $paymentMethod === 'mercadopago' ? 'border-[var(--sh-muted-gold)] bg-[var(--sh-muted-gold)]/5' : 'border-gray-200 hover:border-gray-300' }}">
                                <input 
                                    type="radio" 
                                    wire:model="paymentMethod"
                                    value="mercadopago"
                                    class="mr-4 w-5 h-5 text-[var(--sh-muted-gold)] focus:ring-[var(--sh-muted-gold)] cursor-pointer"
                                >
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-900">Mercado Pago</span>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded">Cartão/Pix</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Pague com cartão de crédito ou Pix</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </label>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Seus dados estão protegidos e criptografados</span>
                        </div>
                    </div>
                </div>

                {{-- Coluna Direita: Resumo do Pedido --}}
                <div class="lg:col-span-1 order-1 lg:order-2">
                    {{-- Mobile: Resumo Colapsável --}}
                    <div class="lg:hidden bg-white rounded-lg border border-gray-200 mb-6">
                        <button 
                            type="button"
                            wire:click="toggleSummary"
                            class="w-full flex items-center justify-between p-4 text-left"
                        >
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Resumo do Pedido</h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ count($cartItems) }} {{ count($cartItems) === 1 ? 'item' : 'itens' }} • 
                                    R$ {{ number_format($total, 2, ',', '.') }}
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 {{ $summaryExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        @if($summaryExpanded)
                            <div class="px-4 pb-4 border-t border-gray-200 pt-4">
                                {{-- Lista de Itens --}}
                                <div class="space-y-3 mb-4 max-h-48 overflow-y-auto">
                                    @foreach($cartItems as $item)
                                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                                            @php 
                                                $imageUrl = $item->attributes->has('image') 
                                                    ? asset('storage/' . $item->attributes->image) 
                                                    : 'https://via.placeholder.com/60';
                                            @endphp
                                            <img 
                                                src="{{ $imageUrl }}" 
                                                alt="{{ $item->name }}" 
                                                class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-xs text-gray-900 line-clamp-2">{{ $item->name }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Qtd: {{ $item->quantity }}</p>
                                            </div>
                                            <span class="font-semibold text-gray-900 whitespace-nowrap text-xs">
                                                R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Totais --}}
                                <div class="space-y-2 pt-3 border-t border-gray-200">
                                    <div class="flex justify-between text-gray-700 text-sm">
                                        <span>Subtotal</span>
                                        <span class="font-semibold">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700 text-sm">
                                        <span>Frete</span>
                                        <span class="font-semibold">
                                            @if($shippingCost > 0)
                                                R$ {{ number_format($shippingCost, 2, ',', '.') }}
                                            @else
                                                <span class="text-gray-400 font-normal">A calcular</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                        <span class="font-semibold text-gray-900">Total</span>
                                        <span class="text-lg font-bold text-[var(--sh-muted-gold)]">
                                            R$ {{ number_format($total, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Desktop: Resumo Sticky --}}
                    <div class="hidden lg:block">
                        <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-20">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Resumo do Pedido</h2>
                            
                            {{-- Lista de Itens --}}
                            <div class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                                @foreach($cartItems as $item)
                                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        @php 
                                            $imageUrl = $item->attributes->has('image') 
                                                ? asset('storage/' . $item->attributes->image) 
                                                : 'https://via.placeholder.com/60';
                                        @endphp
                                        <img 
                                            src="{{ $imageUrl }}" 
                                            alt="{{ $item->name }}" 
                                            class="w-14 h-14 object-cover rounded-lg flex-shrink-0"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm text-gray-900 line-clamp-2">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Qtd: {{ $item->quantity }}</p>
                                        </div>
                                        <span class="font-semibold text-gray-900 whitespace-nowrap text-sm">
                                            R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Totais --}}
                            <div class="space-y-3 mb-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between text-gray-700">
                                    <span class="text-sm">Subtotal</span>
                                    <span class="font-semibold text-sm">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span class="text-sm">Frete</span>
                                    <span class="font-semibold text-sm">
                                        @if($shippingCost > 0)
                                            R$ {{ number_format($shippingCost, 2, ',', '.') }}
                                        @else
                                            <span class="text-gray-400 font-normal">A calcular</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="border-t-2 border-gray-200 pt-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-xl font-bold text-[var(--sh-muted-gold)]">
                                        R$ {{ number_format($total, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- CTA Button --}}
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="placeOrder"
                                class="w-full bg-[var(--sh-muted-gold)] hover:bg-[var(--sh-muted-gold)]/90 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg text-base"
                            >
                                <span wire:loading.remove wire:target="placeOrder" class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Finalizar Pedido
                                </span>
                                <span wire:loading wire:target="placeOrder" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processando...
                                </span>
                            </button>

                            <p class="text-xs text-gray-500 text-center mt-4">
                                Você será redirecionado para o pagamento seguro
                            </p>
                        </div>
                    </div>

                    {{-- Mobile: CTA Button Fixo na parte inferior --}}
                    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-50">
                        <div class="container mx-auto px-4 max-w-6xl">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-xs text-gray-500">Total</p>
                                    <p class="text-xl font-bold text-[var(--sh-muted-gold)]">
                                        R$ {{ number_format($total, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="placeOrder"
                                class="w-full bg-[var(--sh-muted-gold)] hover:bg-[var(--sh-muted-gold)]/90 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md text-base"
                            >
                                <span wire:loading.remove wire:target="placeOrder" class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Finalizar Pedido
                                </span>
                                <span wire:loading wire:target="placeOrder" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Espaçamento para o botão fixo no mobile --}}
    <div class="lg:hidden h-24"></div>
</div>
