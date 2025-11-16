<div>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold mb-8">Meu Perfil</h1>

        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded">
                <p class="font-medium">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button 
                    wire:click="$set('activeTab', 'profile')"
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition-colors {{ $activeTab === 'profile' ? 'border-[var(--sh-muted-gold)] text-[var(--sh-muted-gold)]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Informações Pessoais
                </button>
                <button 
                    wire:click="$set('activeTab', 'password')"
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition-colors {{ $activeTab === 'password' ? 'border-[var(--sh-muted-gold)] text-[var(--sh-muted-gold)]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Alterar Senha
                </button>
                <button 
                    wire:click="$set('activeTab', 'delete')"
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition-colors {{ $activeTab === 'delete' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Excluir Conta
                </button>
            </nav>
        </div>

        {{-- Tab: Informações Pessoais --}}
        @if($activeTab === 'profile')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold mb-2 text-gray-900">Informações Pessoais</h2>
                <p class="text-sm text-gray-600 mb-6">Atualize suas informações de perfil e endereço de e-mail.</p>

                <form wire:submit.prevent="updateProfile" class="space-y-6">
                    {{-- Nome --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            wire:model="name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="Seu nome completo"
                        >
                        @error('name') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            E-mail *
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

                    {{-- Celular e CPF --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="celular" class="block text-sm font-semibold text-gray-700 mb-2">
                                Celular
                            </label>
                            <input 
                                type="text" 
                                id="celular" 
                                wire:model="celular"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="(00) 00000-0000"
                            >
                            @error('celular') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <div>
                            <label for="cpf" class="block text-sm font-semibold text-gray-700 mb-2">
                                CPF
                            </label>
                            <input 
                                type="text" 
                                id="cpf" 
                                wire:model="cpf"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                                placeholder="000.000.000-00"
                            >
                            @error('cpf') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    {{-- Botão Salvar --}}
                    <div class="flex items-center gap-4">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updateProfile"
                            class="bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                        >
                            <span wire:loading.remove wire:target="updateProfile">Salvar Alterações</span>
                            <span wire:loading wire:target="updateProfile">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Tab: Alterar Senha --}}
        @if($activeTab === 'password')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold mb-2 text-gray-900">Alterar Senha</h2>
                <p class="text-sm text-gray-600 mb-6">Certifique-se de que sua conta está usando uma senha longa e aleatória para manter-se segura.</p>

                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    {{-- Senha Atual --}}
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Senha Atual *
                        </label>
                        <input 
                            type="password" 
                            id="current_password" 
                            wire:model="current_password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="Digite sua senha atual"
                        >
                        @error('current_password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Nova Senha --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nova Senha *
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            wire:model="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="Digite sua nova senha"
                        >
                        @error('password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Confirmar Nova Senha --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirmar Nova Senha *
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            wire:model="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--sh-muted-gold)] focus:border-[var(--sh-muted-gold)] transition-all"
                            placeholder="Confirme sua nova senha"
                        >
                    </div>

                    {{-- Botão Salvar --}}
                    <div class="flex items-center gap-4">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            class="bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                        >
                            <span wire:loading.remove wire:target="updatePassword">Salvar Nova Senha</span>
                            <span wire:loading wire:target="updatePassword">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Tab: Excluir Conta --}}
        @if($activeTab === 'delete')
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h2 class="text-xl font-bold mb-2 text-red-600">Excluir Conta</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Uma vez que sua conta seja excluída, todos os seus recursos e dados serão permanentemente excluídos. 
                    Antes de excluir sua conta, baixe todos os dados ou informações que deseja manter.
                </p>

                <form wire:submit.prevent="deleteAccount" class="space-y-6">
                    {{-- Senha para Confirmar --}}
                    <div>
                        <label for="delete_current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Digite sua senha para confirmar *
                        </label>
                        <input 
                            type="password" 
                            id="delete_current_password" 
                            wire:model="current_password"
                            class="w-full px-4 py-3 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                            placeholder="Digite sua senha para confirmar"
                        >
                        @error('current_password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Botão Excluir --}}
                    <div class="flex items-center gap-4">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="deleteAccount"
                            onclick="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita!')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                        >
                            <span wire:loading.remove wire:target="deleteAccount">Excluir Conta Permanentemente</span>
                            <span wire:loading wire:target="deleteAccount">Excluindo...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
