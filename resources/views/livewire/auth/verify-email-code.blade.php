<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Verificar Email</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Enviamos um código de 6 dígitos para <strong>{{ Auth::user()->email }}</strong>
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

    <form wire:submit="verifyCode" class="space-y-4">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Código de Verificação
            </label>
            <input 
                wire:model="code" 
                type="text" 
                id="code"
                maxlength="6"
                pattern="[0-9]{6}"
                placeholder="000000"
                class="mt-1 block w-full px-4 py-3 text-center text-2xl tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required
                autofocus
            >
            @error('code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button 
            type="submit"
            class="w-full py-3 px-4 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition duration-200"
        >
            Verificar Código
        </button>
    </form>

    <div class="text-center space-y-2">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Não recebeu o código?
        </p>
        
        @if($resendCooldown > 0)
            <p class="text-sm text-gray-500">
                Reenvio disponível em {{ $resendCooldown }} segundos
            </p>
        @else
            <button 
                wire:click="resendCode"
                class="text-amber-600 hover:text-amber-700 font-medium text-sm"
            >
                Reenviar Código
            </button>
        @endif
    </div>

    @if($resendCooldown > 0)
        <script>
            setInterval(() => {
                @this.call('decrementCooldown');
            }, 1000);
        </script>
    @endif
</div>
