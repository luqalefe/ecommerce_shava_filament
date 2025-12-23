<div class="max-w-md mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Trocar Senha</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Atualize sua senha de forma segura
            </p>
        </div>

        @if($success)
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $success }}
            </div>
        @endif

        @if($error)
            <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $error }}
            </div>
        @endif

        <form wire:submit="updatePassword" class="space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Senha Atual
                </label>
                <input 
                    wire:model="current_password" 
                    type="password" 
                    id="current_password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                >
                @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nova Senha
                </label>
                <input 
                    wire:model="password" 
                    type="password" 
                    id="password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                >
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Confirmar Nova Senha
                </label>
                <input 
                    wire:model="password_confirmation" 
                    type="password" 
                    id="password_confirmation"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                >
            </div>

            <button 
                type="submit"
                class="w-full py-3 px-4 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition duration-200"
            >
                Atualizar Senha
            </button>
        </form>
    </div>
</div>
