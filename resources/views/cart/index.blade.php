@extends('layouts.main')

@section('title', 'Meu Carrinho')

@section('content')
<div class="container py-5 my-4">
    <h1 class="text-center h1 fw-bold mb-5 text-uppercase">Meu Carrinho</h1>

    {{-- Exibe mensagens de sucesso/erro --}}
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

    @if($cartItems->isNotEmpty())
        <div class="row gx-lg-5">
            {{-- Coluna dos Itens --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Itens ({{ $cartItems->count() }})</h5></div>
                    <div class="card-body p-4">
                        @foreach($cartItems as $item)
                            <div class="row mb-4 border-bottom pb-4 align-items-center">
                                {{-- Imagem --}}
                                <div class="col-sm-2 text-center mb-3 mb-sm-0">
                                    @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/100'; @endphp
                                    <a href="{{ $item->attributes->has('slug') ? route('product.show', $item->attributes->slug) : '#' }}"><img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="img-fluid rounded" style="max-height: 80px; max-width: 80px; object-fit: contain;"></a>
                                </div>
                                {{-- Nome e Preço Unit. --}}
                                <div class="col-sm-4 mb-2 mb-sm-0">
                                    <h5 class="fw-bold mb-1 fs-6"><a href="{{ $item->attributes->has('slug') ? route('product.show', $item->attributes->slug) : '#' }}" class="text-dark text-decoration-none">{{ $item->name }}</a></h5>
                                    <p class="text-muted small mb-0">Preço: R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                                </div>
                                {{-- Quantidade --}}
                                <div class="col-sm-3 mb-2 mb-sm-0">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                                        @csrf @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm me-2" style="width: 70px;" required onchange="this.form.submit()">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Atualizar"><i class="bi bi-arrow-repeat"></i></button>
                                    </form>
                                </div>
                                {{-- Preço Total Item --}}
                                <div class="col-sm-2 text-sm-end mb-2 mb-sm-0"><p class="fw-bold mb-0">R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}</p></div>
                                {{-- Remover --}}
                                <div class="col-sm-1 text-sm-end">
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Remover"><i class="bi bi-trash-fill fs-5"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Coluna do Resumo --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Resumo do Pedido</h5></div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3"><span>Subtotal</span><span class="fw-bold">R$ {{ number_format($subTotal, 2, ',', '.') }}</span></div>
                        {{-- <div class="d-flex justify-content-between mb-3"><span>Frete</span><span class="fw-bold">A calcular</span></div> --}}
                        <hr class="my-3">
                        <div class="d-flex justify-content-between fw-bold h5 mt-3"><span>Total</span><span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span></div>
                        <div class="d-grid mt-4">
                            {{-- BOTÃO CORRIGIDO --}}
                            <a href="{{ route('checkout.index') }}" class="btn btn-warning btn-lg text-white fw-bold text-uppercase">
                                Finalizar Compra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Carrinho Vazio --}}
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size: 5rem; color: var(--sh-muted-gold);"></i>
            <h3 class="mt-4 fw-bold">Seu carrinho está vazio</h3>
            <p class="text-muted mb-4">Parece que você ainda não adicionou nenhum produto.</p>
            <a href="{{ route('products.index') }}" class="btn btn-dark">Continuar Comprando</a>
        </div>
    @endif
</div>
@endsection