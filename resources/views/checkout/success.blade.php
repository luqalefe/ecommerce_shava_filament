@extends('layouts.main')

@section('title', 'Pague com PIX')

@section('content')
<div class="container mx-auto px-4 py-12 text-center">
    
    @if(isset($pixData) && !empty($pixData))
        <h1 class="text-2xl md:text-3xl font-bold mb-6">Seu pedido foi realizado!</h1>
        <p class="text-lg text-gray-600 mb-8">Para concluir, pague usando o QR Code ou o código Pix Copia e Cola abaixo.</p>

        <div class="flex justify-center">
            <div class="max-w-sm w-full">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h5 class="text-lg font-semibold mb-4">Pagamento via PIX</h5>
                        
                        {{-- O brCodeBase64 é a imagem do QR Code --}}
                        <img src="{{ $pixData['brCodeBase64'] }}" alt="QR Code Pix" class="w-full max-w-xs mx-auto rounded mb-4">

                        <p class="text-sm text-gray-500 mb-2">Pix Copia e Cola:</p>
                        <div class="flex">
                            {{-- O brCode é o texto do copia e cola --}}
                            <input type="text" id="pixCode" class="flex-1 px-3 py-2 border border-gray-300 rounded-l text-sm" value="{{ $pixData['brCode'] }}" readonly>
                            <button class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-r border border-l-0 border-gray-300 text-sm transition-colors" type="button" onclick="copyPixCode()">Copiar</button>
                        </div>
                        <p id="copyMessage" class="text-sm text-green-600 mt-2 hidden">Código copiado!</p>

                        <hr class="my-6 border-gray-200">
                        <p class="text-sm text-gray-500">
                            Este código expira em <strong>{{ \Carbon\Carbon::parse($pixData['expiresAt'])->format('d/m/Y H:i') }}</strong>.
                        </p>
                        <a href="{{ route('home') }}" class="inline-block mt-4 border border-amber-500 text-amber-600 hover:bg-amber-50 font-medium py-2 px-6 rounded-lg transition-colors">Voltar para a Loja</a>
                    </div>
                </div>
            </div>
        </div>
    
    @else
        <h1 class="text-2xl md:text-3xl font-bold mb-6">Ooops!</h1>
        <p class="text-lg text-gray-600 mb-8">Não encontramos os dados do seu pedido. Por favor, tente novamente.</p>
        <a href="{{ route('cart.index') }}" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">Voltar ao Carrinho</a>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function copyPixCode() {
        var copyText = document.getElementById("pixCode");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        try {
            document.execCommand('copy');
            document.getElementById("copyMessage").classList.remove('hidden');
            setTimeout(function() {
                document.getElementById("copyMessage").classList.add('hidden');
            }, 2000);
        } catch (err) {
            console.error('Falha ao copiar o código PIX: ', err);
        }
    }
</script>
@endpush