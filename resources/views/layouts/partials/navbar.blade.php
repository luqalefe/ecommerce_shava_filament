{{-- resources/views/layouts/partials/navbar.blade.php --}}
{{-- LAYOUT: Mobile = Menu(esq) | Logo(centro) | Carrinho(dir) --}}

<header x-data="{ openMobileMenu: false }" class="navbar-header">
    
    <nav class="navbar-container">
        
        {{-- ========== MOBILE LAYOUT (< 1024px) ========== --}}
        <div class="mobile-header">
            {{-- Menu Hamburger (Esquerda) --}}
            <button @click="openMobileMenu = !openMobileMenu" type="button" class="mobile-menu-btn">
                <span class="sr-only">Abrir menu</span>
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            
            {{-- Logo (Centro) --}}
            <a href="{{ route('home') }}" class="mobile-logo">
                <img src="{{ asset('images/logo_shava.png') }}" alt="Shava Haux">
            </a>
            
            {{-- Carrinho (Direita) --}}
            <a href="{{ route('cart.index') }}" class="mobile-cart">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php $cartCount = \Cart::getTotalQuantity(); @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
        
        {{-- ========== DESKTOP LAYOUT (>= 1024px) ========== --}}
        <div class="desktop-header">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="desktop-logo">
                <img src="{{ asset('images/logo_shava.png') }}" alt="Shava Haux">
            </a>
            
            {{-- Links do Menu Central --}}
            <nav class="desktop-nav">
                @if(isset($globalCategories) && $globalCategories->isNotEmpty())
                    @foreach ($globalCategories as $category)
                        @if ($category->children->isNotEmpty())
                            <div class="nav-dropdown" data-dropdown>
                                <button data-dropdown-trigger class="nav-link {{ request()->is('categoria/'.$category->slug.'*') ? 'active' : '' }}">
                                    {{ strtoupper($category->name) }}
                                    <svg class="chevron" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div data-dropdown-menu class="dropdown-menu">
                                    <a href="{{ route('category.show', $category->slug) }}" class="dropdown-item">Ver Tudo em {{ $category->name }}</a>
                                    <div class="dropdown-divider"></div>
                                    @foreach ($category->children as $child)
                                        <a href="{{ route('category.show', $child->slug) }}" class="dropdown-item">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('category.show', $category->slug) }}" class="nav-link {{ request()->is('categoria/'.$category->slug) ? 'active' : '' }}">
                                {{ strtoupper($category->name) }}
                            </a>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">SOBRE NÓS</a>
            </nav>
            
            {{-- Ícones da Direita --}}
            <div class="desktop-actions">
                @auth
                    <div class="nav-dropdown" data-dropdown>
                        <button data-dropdown-trigger class="action-btn" title="Minha Conta">
                            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </button>
                        <div data-dropdown-menu class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('orders.index') }}" class="dropdown-item">
                                Meus Pedidos
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                @if($unreadCount > 0)
                                    <span class="notification-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">Meu Perfil</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item logout">Sair</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="action-btn" title="Entrar">
                        <svg class="icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @endauth
                
                <span class="divider"></span>
                
                <livewire:mini-cart />
            </div>
        </div>
    </nav>

    {{-- ========== MOBILE MENU (Slide-in) ========== --}}
    <div x-show="openMobileMenu" @click.away="openMobileMenu = false" x-transition class="mobile-menu">
        @if(isset($globalCategories) && $globalCategories->isNotEmpty())
            @foreach ($globalCategories as $category)
                @if ($category->children->isNotEmpty())
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="mobile-nav-item has-children">
                            <span>{{ strtoupper($category->name) }}</span>
                            <svg :class="{'rotate-180': open}" class="chevron" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" class="mobile-submenu">
                            <a href="{{ route('category.show', $category->slug) }}" class="mobile-nav-subitem">Ver Tudo</a>
                            @foreach ($category->children as $child)
                                <a href="{{ route('category.show', $child->slug) }}" class="mobile-nav-subitem">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route('category.show', $category->slug) }}" class="mobile-nav-item">{{ strtoupper($category->name) }}</a>
                @endif
            @endforeach
        @endif
        <a href="{{ route('about') }}" class="mobile-nav-item">SOBRE NÓS</a>
        
        <div class="mobile-menu-divider"></div>
        
        @auth
            <a href="{{ route('orders.index') }}" class="mobile-nav-item">Meus Pedidos</a>
            <a href="{{ route('profile.edit') }}" class="mobile-nav-item">Meu Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-nav-item logout">Sair</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-item">Entrar / Cadastrar</a>
        @endauth
    </div>
</header>

<style>
/* =============================================
   NAVBAR STYLES - CSS Puro (evita conflitos)
   ============================================= */

.navbar-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
    border-bottom: 1px solid #E7E5E4;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.navbar-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ===== MOBILE HEADER ===== */
.mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
}

.mobile-menu-btn,
.mobile-cart {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: none;
    border: none;
    color: #44403C;
    cursor: pointer;
    position: relative;
}

.mobile-logo {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.mobile-logo img {
    height: 40px;
    width: auto;
}

.icon {
    width: 24px;
    height: 24px;
}

.cart-badge {
    position: absolute;
    top: 2px;
    right: 2px;
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
}

/* ===== DESKTOP HEADER ===== */
.desktop-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
    position: relative;
    z-index: 50;
}



.desktop-logo img {
    height: 40px;
    width: auto;
}

.desktop-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 50;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #44403C;
    text-decoration: none;
    border: none;
    background: none;
    cursor: pointer;
    transition: color 0.2s;
}

.nav-link:hover,
.nav-link.active {
    color: #1C4532;
}

.chevron {
    width: 16px;
    height: 16px;
    transition: transform 0.2s;
}

.desktop-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: none;
    border: none;
    color: #44403C;
    cursor: pointer;
    transition: color 0.2s;
}

.action-btn:hover {
    color: #1C4532;
}

.divider {
    width: 1px;
    height: 24px;
    background: #E7E5E4;
}

/* Hide desktop header on mobile - applied via JS after Alpine init */
.desktop-header.hide-mobile {
    display: none !important;
}

/* ===== DROPDOWNS ===== */
.nav-dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 0.5rem;
    min-width: 200px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    padding: 0.5rem;
    z-index: 9999;
    border: 1px solid #E7E5E4;
    opacity: 0;
    transform: translateY(-10px);
    transition: opacity 0.2s ease, transform 0.2s ease;
    pointer-events: none;
}

.dropdown-menu.dropdown-open {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.dropdown-menu-right {
    left: auto;
    right: 0;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: #44403C;
    text-decoration: none;
    border-radius: 4px;
    text-align: left;
    background: none;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
}

.dropdown-item:hover {
    background: #F5F5F4;
}

.dropdown-item.logout {
    color: #ef4444;
}

.dropdown-divider {
    height: 1px;
    background: #E7E5E4;
    margin: 0.5rem 0;
}

.notification-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    background: var(--sh-muted-gold, #B3AF8F);
    color: white;
    font-size: 11px;
    font-weight: 700;
    border-radius: 50%;
    margin-left: 8px;
}

/* ===== MOBILE MENU ===== */
.mobile-menu {
    padding: 1rem;
    border-top: 1px solid #E7E5E4;
    background: white;
}

.mobile-nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 500;
    color: #44403C;
    text-decoration: none;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
}

.mobile-nav-item:hover {
    background: #F5F5F4;
}

.mobile-nav-item.logout {
    color: #ef4444;
}

.mobile-submenu {
    padding-left: 1rem;
}

.mobile-nav-subitem {
    display: block;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    color: #78716C;
    text-decoration: none;
}

.mobile-menu-divider {
    height: 1px;
    background: #E7E5E4;
    margin: 1rem 0;
}

/* ===== RESPONSIVE ===== */
@media (min-width: 1024px) {
    .mobile-header { display: none !important; }
    .mobile-menu { display: none !important; }
}

@media (max-width: 1023px) {
    .desktop-header { display: none !important; }
}
</style>

<script>
// Desktop Dropdown Implementation - Event Delegation (Bootstrap-compatible)
(function() {
    'use strict';
    
    // Use event delegation at document level to avoid Bootstrap conflicts
    // mousedown fires before click, giving us priority
    document.addEventListener('mousedown', function(e) {
        const trigger = e.target.closest('[data-dropdown-trigger]');
        
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = trigger.closest('[data-dropdown]');
            const menu = dropdown ? dropdown.querySelector('[data-dropdown-menu]') : null;
            
            if (!menu) return;
            
            const isOpen = menu.classList.contains('dropdown-open');
            
            // Close all other dropdowns first
            document.querySelectorAll('[data-dropdown-menu].dropdown-open').forEach(function(otherMenu) {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('dropdown-open');
                }
            });
            
            // Toggle this dropdown
            menu.classList.toggle('dropdown-open', !isOpen);
        }
    }, true); // Use capture phase for earlier execution
    
    // Close dropdowns when clicking outside (also use capture)
    document.addEventListener('mousedown', function(e) {
        if (e.target.closest('[data-dropdown]')) return;
        
        document.querySelectorAll('[data-dropdown-menu].dropdown-open').forEach(function(menu) {
            menu.classList.remove('dropdown-open');
        });
    }, false);
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-dropdown-menu].dropdown-open').forEach(function(menu) {
                menu.classList.remove('dropdown-open');
            });
        }
    });
    
    console.log('✓ Desktop dropdown event delegation attached');
})();
</script>