<div>
    <div class="container mx-auto px-4 py-12 max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-900">Criar Conta</h1>

            {{-- Botão Registro com Google --}}
            <a href="{{ route('auth.google.redirect') }}" 
               class="w-full flex items-center justify-center gap-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 transition-all mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Cadastrar com Google
            </a>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
            </div>

            {{-- Toggle Pessoa Física / Pessoa Jurídica --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Tipo de Conta
                </label>
                <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                    <button 
                        type="button"
                        wire:click="setUserType('pf')"
                        class="flex-1 py-3 px-4 text-sm font-medium transition-all {{ $user_type === 'pf' ? 'bg-[var(--sh-muted-gold)] text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Pessoa Física
                        </span>
                    </button>
                    <button 
                        type="button"
                        wire:click="setUserType('pj')"
                        class="flex-1 py-3 px-4 text-sm font-medium transition-all border-l border-gray-300 {{ $user_type === 'pj' ? 'bg-[var(--sh-muted-gold)] text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Empresa
                        </span>
                    </button>
                </div>
            </div>

            {{-- Formulário de Registro --}}
            <form wire:submit.prevent="register" class="space-y-5">
                {{-- Nome (muda label baseado no tipo) --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ $user_type === 'pj' ? 'Nome do Responsável' : 'Nome Completo' }}
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                        placeholder="{{ $user_type === 'pj' ? 'Nome do responsável legal' : 'Seu nome' }}"
                    >
                    @error('name') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Campos específicos PF --}}
                @if($user_type === 'pf')
                    <div class="p-4 bg-blue-50 rounded-lg space-y-4 border border-blue-200">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            Dados Pessoais
                        </h3>
                        
                        {{-- CPF --}}
                        <div>
                            <label for="cpf" class="block text-sm font-semibold text-gray-700 mb-2">
                                CPF <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="cpf" 
                                wire:model="cpf"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="000.000.000-00"
                                x-data
                                x-mask="999.999.999-99"
                            >
                            @error('cpf') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        {{-- Celular --}}
                        <div>
                            <label for="celular" class="block text-sm font-semibold text-gray-700 mb-2">
                                Celular <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="celular" 
                                wire:model="celular"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="(00) 00000-0000"
                                x-data
                                x-mask="(99) 99999-9999"
                            >
                        </div>
                    </div>
                @endif

                {{-- Campos específicos PJ --}}
                @if($user_type === 'pj')
                    <div class="p-4 bg-gray-50 rounded-lg space-y-4 border border-gray-200">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[var(--sh-muted-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Dados da Empresa
                        </h3>
                        
                        {{-- CNPJ --}}
                        <div>
                            <label for="cnpj" class="block text-sm font-semibold text-gray-700 mb-2">
                                CNPJ <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="cnpj" 
                                wire:model="cnpj"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="00.000.000/0000-00"
                                x-data
                                x-mask="99.999.999/9999-99"
                            >
                            @error('cnpj') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        {{-- Razão Social --}}
                        <div>
                            <label for="razao_social" class="block text-sm font-semibold text-gray-700 mb-2">
                                Razão Social <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="razao_social" 
                                wire:model="razao_social"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="Razão Social da empresa"
                            >
                            @error('razao_social') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        {{-- Nome Fantasia --}}
                        <div>
                            <label for="nome_fantasia" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nome Fantasia <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="nome_fantasia" 
                                wire:model="nome_fantasia"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="Nome fantasia da empresa"
                            >
                        </div>

                        {{-- Inscrição Estadual --}}
                        <div>
                            <label for="inscricao_estadual" class="block text-sm font-semibold text-gray-700 mb-2">
                                Inscrição Estadual <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="inscricao_estadual" 
                                wire:model="inscricao_estadual"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="Inscrição Estadual"
                            >
                        </div>
                    </div>

                    {{-- Endereço da Empresa --}}
                    <div class="p-4 bg-green-50 rounded-lg space-y-4 border border-green-200">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Endereço da Empresa <span class="text-red-500">*</span>
                        </h3>
                        
                        {{-- CEP --}}
                        <div>
                            <label for="cep" class="block text-sm font-semibold text-gray-700 mb-2">
                                CEP <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="cep" 
                                wire:model="cep"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="00000-000"
                                x-data
                                x-mask="99999-999"
                            >
                            @error('cep') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        {{-- Rua --}}
                        <div>
                            <label for="rua" class="block text-sm font-semibold text-gray-700 mb-2">
                                Rua/Logradouro <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="rua" 
                                wire:model="rua"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="Rua, Avenida, etc."
                            >
                            @error('rua') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        {{-- Número e Complemento --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="numero" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Número <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="numero" 
                                    wire:model="numero"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    placeholder="123"
                                >
                                @error('numero') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                            <div>
                                <label for="complemento" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Complemento
                                </label>
                                <input 
                                    type="text" 
                                    id="complemento" 
                                    wire:model="complemento"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    placeholder="Sala, Andar"
                                >
                            </div>
                        </div>

                        {{-- Cidade e Estado --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label for="cidade" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Cidade <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="cidade" 
                                    wire:model="cidade"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                    placeholder="Cidade"
                                >
                                @error('cidade') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                            <div>
                                <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">
                                    UF <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="estado" 
                                    wire:model="estado"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all uppercase"
                                    placeholder="AC"
                                    maxlength="2"
                                >
                                @error('estado') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                        placeholder="{{ $user_type === 'pj' ? 'email@empresa.com' : 'seu@email.com' }}"
                    >
                    @error('email') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Senha --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Senha
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        wire:model="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                        placeholder="••••••••"
                    >
                    @error('password') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Confirmar Senha --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirmar Senha
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        wire:model="password_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                        placeholder="••••••••"
                    >
                </div>

                {{-- Botão Submit --}}
                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="register"
                    class="w-full bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                >
                    <span wire:loading.remove wire:target="register">
                        {{ $user_type === 'pj' ? 'Cadastrar Empresa' : 'Criar Conta' }}
                    </span>
                    <span wire:loading wire:target="register">Criando...</span>
                </button>
            </form>

            {{-- Link para Login --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Já tem uma conta? 
                    <a href="{{ route('login') }}" class="text-[var(--sh-muted-gold)] font-semibold hover:underline">
                        Entrar
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

