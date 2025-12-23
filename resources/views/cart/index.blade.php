@extends('layouts.main')

@section('title', 'Meu Carrinho | Shava Haux')

@section('content')
<div class="cart-page">
    
    {{-- Breadcrumb --}}
    <nav class="cart-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="separator">›</span>
        <span class="current">Carrinho de Compras</span>
    </nav>

    {{-- Header --}}
    <div class="cart-header">
        <h1>Carrinho de Compras</h1>
        @if($cartItems->isNotEmpty())
            <span class="cart-count">{{ $cartItems->count() }} {{ $cartItems->count() === 1 ? 'item' : 'itens' }}</span>
        @endif
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" role="alert">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if($cartItems->isNotEmpty())
        <div class="cart-layout">
            
            {{-- COLUNA DE ITENS --}}
            <div class="cart-items-section">
                {{-- Frete Grátis Banner --}}
                @php
                    $freeShippingThreshold = 100;
                    $remaining = max(0, $freeShippingThreshold - $subTotal);
                    $progress = min(100, ($subTotal / $freeShippingThreshold) * 100);
                @endphp
                
                @if($remaining > 0)
                    <div class="free-shipping-banner">
                        <div class="shipping-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <div class="shipping-info">
                            <span>Falta <strong>R$ {{ number_format($remaining, 2, ',', '.') }}</strong> para frete grátis em Rio Branco!</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="free-shipping-banner success">
                        <div class="shipping-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        </div>
                        <div class="shipping-info">
                            <span><strong>Parabéns!</strong> Você ganhou frete grátis para Rio Branco!</span>
                        </div>
                    </div>
                @endif

                {{-- Lista de Itens --}}
                <div class="cart-items-card">
                    <div class="card-header">
                        <span class="header-product">Produto</span>
                        <span class="header-price">Preço</span>
                        <span class="header-qty">Qtd</span>
                        <span class="header-subtotal">Subtotal</span>
                    </div>
                    
                    <div class="cart-items-list">
                        @foreach($cartItems as $item)
                            <div class="cart-item">
                                {{-- Imagem --}}
                                <div class="item-image">
                                    @php $imageUrl = $item->attributes->has('image') ? asset('storage/' . $item->attributes->image) : 'https://via.placeholder.com/100'; @endphp
                                    <a href="{{ $item->attributes->has('slug') ? route('product.show', $item->attributes->slug) : '#' }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->name }}">
                                    </a>
                                </div>
                                
                                {{-- Info --}}
                                <div class="item-info">
                                    <h3 class="item-name">
                                        <a href="{{ $item->attributes->has('slug') ? route('product.show', $item->attributes->slug) : '#' }}">
                                            {{ $item->name }}
                                        </a>
                                    </h3>
                                    <p class="item-stock">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        Em estoque
                                    </p>
                                    {{-- Mobile Actions --}}
                                    <div class="item-mobile-actions">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="qty-form-mobile">
                                            @csrf @method('PATCH')
                                            <div class="qty-selector">
                                                <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.submit()">−</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()">
                                                <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.submit()">+</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="remove-btn">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                {{-- Preço Unitário (Desktop) --}}
                                <div class="item-price">
                                    R$ {{ number_format($item->price, 2, ',', '.') }}
                                </div>
                                
                                {{-- Quantidade (Desktop) --}}
                                <div class="item-qty">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="qty-form">
                                        @csrf @method('PATCH')
                                        <div class="qty-selector">
                                            <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.submit()">−</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()">
                                            <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.submit()">+</button>
                                        </div>
                                    </form>
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="remove-link">Remover</button>
                                    </form>
                                </div>
                                
                                {{-- Subtotal --}}
                                <div class="item-subtotal">
                                    R$ {{ number_format($item->getPriceSum(), 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Continuar Comprando --}}
                <div class="continue-shopping">
                    <a href="{{ route('products.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Continuar comprando
                    </a>
                </div>
            </div>

            {{-- SIDEBAR DE RESUMO --}}
            <div class="cart-summary-section">
                <div class="summary-card">
                    <h2>Resumo do Pedido</h2>
                    
                    {{-- Valores --}}
                    <div class="summary-row">
                        <span>Subtotal ({{ $cartItems->count() }} {{ $cartItems->count() === 1 ? 'item' : 'itens' }})</span>
                        <span>R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                    </div>
                    
                    <div class="summary-row shipping">
                        <span>Frete</span>
                        @if($subTotal >= 100)
                            <span class="free">Grátis</span>
                        @else
                            <span class="calculate">Calcular no checkout</span>
                        @endif
                    </div>
                    
                    <hr>
                    
                    {{-- Total --}}
                    <div class="summary-total">
                        <span>Total</span>
                        <div class="total-value">
                            <span class="price">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
                            @php $pixTotal = $subTotal * 0.95; @endphp
                            <span class="pix-price">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                    <path d="M13.365 16.673l-3.192-3.192a1.5 1.5 0 010-2.121l3.192-3.192a2.001 2.001 0 012.828 0l3.192 3.192a1.5 1.5 0 010 2.121l-3.192 3.192a2.001 2.001 0 01-2.828 0z"/>
                                </svg>
                                R$ {{ number_format($pixTotal, 2, ',', '.') }} no PIX
                            </span>
                        </div>
                    </div>
                    
                    {{-- Parcelamento --}}
                    <div class="installment-info">
                        ou em até <strong>3x de R$ {{ number_format($subTotal / 3, 2, ',', '.') }}</strong> sem juros
                    </div>
                    
                    {{-- Botão Checkout --}}
                    <a href="{{ route('checkout.index') }}" class="checkout-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Finalizar Compra
                    </a>
                    
                    {{-- Trust Badges --}}
                    <div class="summary-trust">
                        <div class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Compra Segura</span>
                        </div>
                        <div class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span>Pague com Cartão</span>
                        </div>
                        <div class="trust-item">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.365 16.673l-3.192-3.192a1.5 1.5 0 010-2.121l3.192-3.192a2.001 2.001 0 012.828 0l3.192 3.192a1.5 1.5 0 010 2.121l-3.192 3.192a2.001 2.001 0 01-2.828 0z"/>
                            </svg>
                            <span>5% OFF no PIX</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- CARRINHO VAZIO --}}
        <div class="empty-cart">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                </svg>
            </div>
            <h2>Seu carrinho está vazio</h2>
            <p>Parece que você ainda não adicionou nenhum produto ao carrinho.</p>
            <a href="{{ route('products.index') }}" class="shop-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Continuar Comprando
            </a>
        </div>
    @endif

</div>

{{-- Mobile Sticky Footer --}}
@if($cartItems->isNotEmpty())
<div class="cart-sticky-footer">
    <div class="sticky-total">
        <span class="sticky-label">Total</span>
        <div class="sticky-prices">
            <span class="sticky-price">R$ {{ number_format($subTotal, 2, ',', '.') }}</span>
            <span class="sticky-pix">R$ {{ number_format($subTotal * 0.95, 2, ',', '.') }} PIX</span>
        </div>
    </div>
    <a href="{{ route('checkout.index') }}" class="sticky-checkout">
        Finalizar
    </a>
</div>
@endif
@endsection

@push('styles')
<style>
/* ============================================
   CART PAGE - AMAZON STYLE
   ============================================ */

.cart-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    padding-bottom: 100px;
}

@media (min-width: 768px) {
    .cart-page {
        padding: 2rem;
        padding-bottom: 2rem;
    }
}

/* Breadcrumb */
.cart-breadcrumb {
    font-size: 0.85rem;
    color: #78716C;
    margin-bottom: 1rem;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.cart-breadcrumb a {
    color: var(--sh-muted-gold, #A69067);
    text-decoration: none;
}

.cart-breadcrumb a:hover {
    text-decoration: underline;
}

.cart-breadcrumb .separator {
    color: #D6D3D1;
}

/* Header */
.cart-header {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #E7E5E4;
}

.cart-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1C1917;
    margin: 0;
}

@media (min-width: 768px) {
    .cart-header h1 {
        font-size: 2rem;
    }
}

.cart-count {
    font-size: 1rem;
    color: #78716C;
}

/* Alerts */
.alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.alert span {
    flex: 1;
}

.alert button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.7;
}

.alert-success {
    background: #ECFDF5;
    color: #059669;
    border: 1px solid #A7F3D0;
}

.alert-error {
    background: #FEF2F2;
    color: #DC2626;
    border: 1px solid #FECACA;
}

/* Layout */
.cart-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .cart-layout {
        grid-template-columns: 1fr 380px;
        gap: 2rem;
    }
}

/* Free Shipping Banner */
.free-shipping-banner {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #FEF3C7;
    border: 1px solid #FCD34D;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.free-shipping-banner.success {
    background: #ECFDF5;
    border-color: #A7F3D0;
}

.shipping-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
}

.shipping-icon svg {
    width: 24px;
    height: 24px;
    color: #D97706;
}

.free-shipping-banner.success .shipping-icon svg {
    color: #059669;
}

.shipping-info {
    flex: 1;
}

.shipping-info span {
    font-size: 0.9rem;
    color: #92400E;
}

.free-shipping-banner.success .shipping-info span {
    color: #065F46;
}

.progress-bar {
    margin-top: 0.5rem;
    height: 6px;
    background: #FDE68A;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #F59E0B;
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Cart Items Card */
.cart-items-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    display: none;
    background: #F5F5F4;
    padding: 1rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #78716C;
    text-transform: uppercase;
}

@media (min-width: 768px) {
    .card-header {
        display: grid;
        grid-template-columns: 1fr 100px 120px 100px;
        gap: 1rem;
        align-items: center;
    }
    
    .header-price, .header-qty, .header-subtotal {
        text-align: center;
    }
}

/* Cart Item */
.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 1rem;
    padding: 1.5rem;
    border-bottom: 1px solid #E7E5E4;
}

.cart-item:last-child {
    border-bottom: none;
}

@media (min-width: 768px) {
    .cart-item {
        grid-template-columns: 100px 1fr 100px 120px 100px;
        align-items: center;
    }
}

.item-image {
    width: 80px;
    height: 80px;
    background: #F5F5F4;
    border-radius: 8px;
    overflow: hidden;
}

@media (min-width: 768px) {
    .item-image {
        width: 100px;
        height: 100px;
    }
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.item-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.item-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.4;
}

.item-name a {
    color: #1C1917;
    text-decoration: none;
}

.item-name a:hover {
    color: var(--sh-muted-gold, #A69067);
}

.item-stock {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    color: #059669;
    margin: 0;
}

.item-stock svg {
    width: 14px;
    height: 14px;
}

/* Mobile Actions */
.item-mobile-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.5rem;
}

@media (min-width: 768px) {
    .item-mobile-actions {
        display: none;
    }
}

/* Desktop columns */
.item-price, .item-qty, .item-subtotal {
    display: none;
}

@media (min-width: 768px) {
    .item-price, .item-qty, .item-subtotal {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
}

.item-price {
    font-size: 0.95rem;
    color: #57534E;
}

.item-subtotal {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1C1917;
}

/* Quantity Selector */
.qty-selector {
    display: flex;
    align-items: center;
    border: 1px solid #E7E5E4;
    border-radius: 8px;
    overflow: hidden;
}

.qty-selector button {
    width: 36px;
    height: 36px;
    background: #F5F5F4;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background 0.2s;
}

.qty-selector button:hover {
    background: #E7E5E4;
}

.qty-selector input {
    width: 50px;
    height: 36px;
    border: none;
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
}

.qty-selector input::-webkit-inner-spin-button,
.qty-selector input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Remove buttons */
.remove-link {
    background: none;
    border: none;
    color: #DC2626;
    font-size: 0.85rem;
    cursor: pointer;
    padding: 0;
}

.remove-link:hover {
    text-decoration: underline;
}

.remove-btn {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    background: none;
    border: none;
    color: #DC2626;
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.5rem;
}

.remove-btn svg {
    width: 16px;
    height: 16px;
}

/* Continue Shopping */
.continue-shopping {
    margin-top: 1rem;
}

.continue-shopping a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--sh-muted-gold, #A69067);
    text-decoration: none;
    font-weight: 500;
}

.continue-shopping a:hover {
    text-decoration: underline;
}

.continue-shopping svg {
    width: 20px;
    height: 20px;
}

/* ============================================
   SUMMARY SIDEBAR
   ============================================ */

.summary-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem;
    position: sticky;
    top: 100px;
}

.summary-card h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1C1917;
    margin: 0 0 1.5rem 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
    color: #57534E;
}

.summary-row.shipping .free {
    color: #059669;
    font-weight: 600;
}

.summary-row.shipping .calculate {
    color: #78716C;
    font-size: 0.85rem;
}

.summary-card hr {
    border: none;
    border-top: 1px solid #E7E5E4;
    margin: 1rem 0;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.summary-total > span:first-child {
    font-size: 1rem;
    font-weight: 600;
    color: #1C1917;
}

.total-value {
    text-align: right;
}

.total-value .price {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1C1917;
}

.total-value .pix-price {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.25rem;
    color: #059669;
    font-size: 0.85rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

.total-value .pix-price svg {
    color: #059669;
}

.installment-info {
    font-size: 0.85rem;
    color: #78716C;
    text-align: right;
    margin-bottom: 1.5rem;
}

.checkout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    background: #F59E0B;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.checkout-btn:hover {
    background: #D97706;
    color: white;
    transform: translateY(-1px);
}

.checkout-btn svg {
    width: 20px;
    height: 20px;
}

/* Summary Trust Badges */
.summary-trust {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #E7E5E4;
}

.trust-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.trust-item svg {
    width: 24px;
    height: 24px;
    color: var(--sh-muted-gold, #A69067);
}

.trust-item span {
    font-size: 0.7rem;
    color: #78716C;
}

/* ============================================
   EMPTY CART
   ============================================ */

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 2rem;
    background: #F5F5F4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon svg {
    width: 50px;
    height: 50px;
    color: var(--sh-muted-gold, #A69067);
}

.empty-cart h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1C1917;
    margin: 0 0 0.5rem 0;
}

.empty-cart p {
    color: #78716C;
    margin: 0 0 2rem 0;
}

.shop-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #1C1917;
    color: white;
    font-weight: 600;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.shop-btn:hover {
    background: #44403C;
    color: white;
}

.shop-btn svg {
    width: 20px;
    height: 20px;
}

/* ============================================
   MOBILE STICKY FOOTER
   ============================================ */

.cart-sticky-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #E7E5E4;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    z-index: 100;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}

@media (min-width: 1024px) {
    .cart-sticky-footer {
        display: none;
    }
    
    .cart-page {
        padding-bottom: 2rem;
    }
}

.sticky-total {
    display: flex;
    flex-direction: column;
}

.sticky-label {
    font-size: 0.8rem;
    color: #78716C;
}

.sticky-prices {
    display: flex;
    flex-direction: column;
}

.sticky-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1C1917;
}

.sticky-pix {
    font-size: 0.8rem;
    color: #059669;
    font-weight: 500;
}

.sticky-checkout {
    background: #F59E0B;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.sticky-checkout:hover {
    background: #D97706;
    color: white;
}
</style>
@endpush