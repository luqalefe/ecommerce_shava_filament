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

            {{-- Formulário de Registro --}}
            <form wire:submit.prevent="register" class="space-y-6">
                {{-- Nome --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nome Completo
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                        placeholder="Seu nome"
                    >
                    @error('name') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

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
                        placeholder="seu@email.com"
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
                    <span wire:loading.remove wire:target="register">Criar Conta</span>
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
