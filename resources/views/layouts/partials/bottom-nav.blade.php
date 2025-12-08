{{-- Bottom Navigation Mobile - Fixo no rodapé --}}
{{-- IMPORTANTE: Só aparece em telas < 768px usando CSS puro para evitar conflito Bootstrap/Tailwind --}}

<nav id="mobile-bottom-nav">
    <div class="bottom-nav-inner">
        
        {{-- Home --}}
        <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('home') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Início</span>
        </a>

        {{-- Loja --}}
        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('products.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Loja</span>
        </a>

        {{-- Carrinho --}}
        <a href="{{ route('cart.index') }}" class="nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <div class="icon-wrapper">
                <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('cart.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php $cartCount = \Cart::getTotalQuantity(); @endphp
                @if($cartCount > 0)
                    <span class="badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </div>
            <span>Carrinho</span>
        </a>

        {{-- Conta --}}
        @auth
            <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') || request()->routeIs('profile.*') ? 'active' : '' }}">
                <div class="icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="{{ request()->routeIs('orders.*') || request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="badge red">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </div>
                <span>Conta</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Entrar</span>
            </a>
        @endauth

    </div>
</nav>

{{-- Espaçador para não cobrir conteúdo (só mobile) --}}
<div id="bottom-nav-spacer"></div>

<style>
    /* BOTTOM NAVIGATION - CSS Puro (evita conflito Bootstrap/Tailwind) */
    #mobile-bottom-nav {
        display: none; /* Escondido por padrão */
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: white;
        border-top: 1px solid #e5e5e5;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        padding-bottom: env(safe-area-inset-bottom, 0);
    }
    
    #bottom-nav-spacer {
        display: none;
        height: 64px;
    }
    
    /* MOSTRAR APENAS EM MOBILE (< 768px) */
    @media (max-width: 767px) {
        #mobile-bottom-nav { display: block; }
        #bottom-nav-spacer { display: block; }
    }
    
    .bottom-nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 8px 0;
    }
    
    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 12px;
        min-width: 60px;
        text-decoration: none;
        color: #888;
        transition: color 0.2s;
    }
    
    .nav-item:hover,
    .nav-item.active {
        color: var(--sh-muted-gold, #B3AF8F);
    }
    
    .nav-item svg {
        width: 24px;
        height: 24px;
    }
    
    .nav-item span {
        font-size: 10px;
        margin-top: 4px;
        font-weight: 500;
    }
    
    .icon-wrapper {
        position: relative;
    }
    
    .icon-wrapper .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--sh-muted-gold, #B3AF8F);
        color: white;
        font-size: 10px;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }
    
    .icon-wrapper .badge.red {
        background: #ef4444;
    }
</style>
