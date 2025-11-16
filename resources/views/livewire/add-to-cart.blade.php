<div>
    @if(session()->has('message'))
        <div class="mb-2 text-sm text-green-600">
            {{ session('message') }}
        </div>
    @endif
    
    <div class="flex gap-2">
        <input 
            type="number" 
            wire:model="quantity" 
            min="1" 
            class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
        >
        <button 
            wire:click="add"
            class="flex-1 bg-[var(--sh-muted-gold)] hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded transition-colors"
        >
            Adicionar
        </button>
    </div>
</div>