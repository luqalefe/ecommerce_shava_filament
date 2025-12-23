<div>
    <div class="container mx-auto px-4 py-12 max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            
            {{-- Título --}}
            <div class="text-center mb-8">
                <div class="mb-4">
                    <img src="{{ asset('images/logo_shava.png') }}" alt="Shava Haux" class="h-16 mx-auto">
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Recuperar Senha</h1>
                @if($step === 1)
                    <p class="mt-2 text-sm text-gray-600">
                        Digite seu email para receber o código de recuperação
                    </p>
                @else
                    <p class="mt-2 text-sm text-gray-600">
                        Enviamos um código de 6 dígitos para <strong class="text-[var(--sh-muted-gold)]">{{ $email }}</strong>
                    </p>
                @endif
            </div>

            {{-- Mensagens de Sucesso/Erro --}}
            @if($success)
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ $success }}</p>
                </div>
            @endif

            @if($error)
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">{{ $error }}</p>
                </div>
            @endif

            @if($step === 1)
                {{-- Etapa 1: Solicitar Email --}}
                <form wire:submit.prevent="requestCode" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            E-mail
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            wire:model="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="seu@email.com"
                        >
                        @error('email') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="requestCode"
                        class="w-full bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                    >
                        <span wire:loading.remove wire:target="requestCode">Enviar Código</span>
                        <span wire:loading wire:target="requestCode">Enviando...</span>
                    </button>
                </form>
            @else
                {{-- Etapa 2: Código + Nova Senha --}}
                <form wire:submit.prevent="resetPassword" class="space-y-6">
                    {{-- Código de Verificação --}}
                    <div>
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                            Código de Verificação
                        </label>
                        <input 
                            type="text" 
                            id="code" 
                            wire:model="code"
                            maxlength="6"
                            class="w-full px-4 py-4 text-center text-2xl tracking-[0.5em] font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="000000"
                        >
                        @error('code') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Nova Senha --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nova Senha
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
                            Confirmar Nova Senha
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            wire:model="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="••••••••"
                        >
                    </div>

                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="resetPassword"
                        class="w-full bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                    >
                        <span wire:loading.remove wire:target="resetPassword">Redefinir Senha</span>
                        <span wire:loading wire:target="resetPassword">Redefinindo...</span>
                    </button>

                    <button 
                        wire:click="back"
                        type="button"
                        class="w-full py-2 px-4 text-gray-600 hover:text-gray-800 font-medium transition-all"
                    >
                        ← Voltar e usar outro email
                    </button>
                </form>
            @endif

            {{-- Link para Login --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Lembrou sua senha? 
                    <a href="{{ route('login') }}" class="text-[var(--sh-muted-gold)] font-semibold hover:underline">
                        Entrar
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

