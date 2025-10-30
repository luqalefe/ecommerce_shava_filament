@extends('layouts.main')

@section('title', 'Pague com PIX')

@section('content')
<div class="container py-5 my-4 text-center">
    
    @if(isset($pixData) && !empty($pixData))
        <h1 class="h2 fw-bold mb-4">Seu pedido foi realizado!</h1>
        <p class="lead text-muted mb-4">Para concluir, pague usando o QR Code ou o código Pix Copia e Cola abaixo.</p>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Pagamento via PIX</h5>
                        
                        {{-- O brCodeBase64 é a imagem do QR Code --}}
                        <img src="{{ $pixData['brCodeBase64'] }}" alt="QR Code Pix" class="img-fluid rounded mb-3">

                        <p class="small text-muted">Pix Copia e Cola:</p>
                        <div class="input-group">
                            {{-- O brCode é o texto do copia e cola --}}
                            <input type="text" id="pixCode" class="form-control form-control-sm" value="{{ $pixData['brCode'] }}" readonly>
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyPixCode()">Copiar</button>
                        </div>
                        <p id="copyMessage" class="small text-success mt-2 d-none">Código copiado!</p>

                        <hr class="my-4">
                        <p class="small text-muted">
                            Este código expira em <strong>{{ \Carbon\Carbon::parse($pixData['expiresAt'])->format('d/m/Y H:i') }}</strong>.
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-outline-warning mt-3">Voltar para a Loja</a>
                    </div>
                </div>
            </div>
        </div>
    
    @else
        <h1 class="h2 fw-bold mb-4">Ooops!</h1>
        <p class="lead text-muted mb-4">Não encontramos os dados do seu pedido. Por favor, tente novamente.</p>
        <a href="{{ route('cart.index') }}" class="btn btn-warning text-white fw-bold">Voltar ao Carrinho</a>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function copyPixCode() {
        // Pega o campo de texto
        var copyText = document.getElementById("pixCode");

        // Seleciona o texto
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Para mobile

        // Copia o texto
        try {
            document.execCommand('copy');
            document.getElementById("copyMessage").classList.remove('d-none');
            setTimeout(function() {
                document.getElementById("copyMessage").classList.add('d-none');
            }, 2000);
        } catch (err) {
            console.error('Falha ao copiar o código PIX: ', err);
        }
    }
</script>
@endpush