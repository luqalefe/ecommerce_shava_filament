{{-- resources/views/layouts/partials/navbar.blade.php --}}
{{-- REFATORADO para TailwindCSS, Alpine.js e Livewire --}}

{{-- 
    Usa Alpine.js para controlar:
    - openMobileMenu: Controla o menu hamburger em telas pequenas
--}}
<header x-data="{ openMobileMenu: false }" class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-[var(--sh-border)]" style="color: var(--sh-dark-text);">
    
    <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8" aria-label="Global">
        
        {{-- 1. Logo --}}
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                <span class="sr-only">Shava Haux</span>
                <img class="h-10 w-auto" src="{{ asset('images/logo_shava.png') }}" alt="Logo Shava Haux">
            </a>
        </div>

        {{-- 2. Botão do Menu Mobile (Hamburger) --}}
        <div class="flex lg:hidden">
            <button @click="openMobileMenu = !openMobileMenu" type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-[var(--sh-dark-text)] hover:bg-[var(--sh-cream)] transition-colors">
                <span class="sr-only">Abrir menu principal</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        {{-- 3. Links do Menu Principal (Desktop - Centralizado) --}}
        <div class="hidden lg:flex lg:flex-1 lg:justify-center lg:gap-x-6">

            {{-- Loop pelas Categorias Globais --}}
            @if(isset($globalCategories) && $globalCategories->isNotEmpty())
                @foreach ($globalCategories as $category)
                    
                    {{-- Dropdown para Categorias com Filhos --}}
                    @if ($category->children->isNotEmpty())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" 
                                    class="flex items-center gap-x-1 px-3 py-2 rounded-md text-sm font-medium hover:text-[var(--sh-muted-gold)]
                                           {{ request()->is('categoria/'.$category->slug.'*') ? 'text-[var(--sh-muted-gold)] border-b-2 border-[var(--sh-muted-gold)]' : '' }}">
                                {{ strtoupper($category->name) }}
                                <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Painel do Dropdown (Alpine.js) --}}
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white/95 backdrop-blur-sm p-2 shadow-lg ring-1 ring-[var(--sh-border)]">
                                
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold leading-6 text-[var(--sh-dark-text)] hover:bg-[var(--sh-cream)] transition-colors {{ request()->is('categoria/'.$category->slug) ? 'bg-[var(--sh-cream)]' : '' }}">
                                    Ver Tudo em {{ $category->name }}
                                </a>
                                <div class="my-2 h-px bg-[var(--sh-border)]"></div>
                                
                                @foreach ($category->children as $child)
                                    <a href="{{ route('category.show', $child->slug) }}" 
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold leading-6 text-[var(--sh-dark-text)] hover:bg-[var(--sh-cream)] transition-colors {{ request()->is('categoria/'.$child->slug) ? 'bg-[var(--sh-cream)]' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    
                    {{-- Link Simples para Categorias sem Filhos --}}
                    @else
                        <a href="{{ route('category.show', $category->slug) }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium
                                  {{ request()->is('categoria/'.$category->slug) 
                                     ? 'text-[var(--sh-muted-gold)] border-b-2 border-[var(--sh-muted-gold)]' 
                                     : 'hover:text-[var(--sh-muted-gold)]' }}">
                            {{ strtoupper($category->name) }}
                        </a>
                    @endif
                @endforeach
            @endif

            {{-- Link Fixo para Sobre Nós --}}
            <a href="{{ route('about') }}" 
               class="px-3 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('about') 
                         ? 'text-[var(--sh-muted-gold)] border-b-2 border-[var(--sh-muted-gold)]' 
                         : 'hover:text-[var(--sh-muted-gold)] transition-colors' }}">
                SOBRE NÓS
            </a>
        </div>

        {{-- 4. Ícones da Direita (Desktop) --}}
        <div class="hidden lg:flex lg:flex-shrink-0 lg:items-center lg:justify-end lg:gap-x-4">
            @auth
                {{-- Dropdown do Usuário (quando logado) --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="p-1 hover:text-[var(--sh-muted-gold)] transition-colors" 
                            title="Minha Conta">
                        <span class="sr-only">Minha Conta</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </button>

                    {{-- Painel do Dropdown --}}
                    <div x-show="open" 
                         @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 top-full z-10 mt-3 w-56 rounded-xl bg-white/95 backdrop-blur-sm p-2 shadow-lg ring-1 ring-[var(--sh-border)]">
                        
                        <a href="{{ route('orders.index') }}" 
                           class="block rounded-lg px-3 py-2 text-sm font-semibold leading-6 text-[var(--sh-dark-text)] hover:bg-[var(--sh-cream)] transition-colors {{ request()->routeIs('orders.*') ? 'bg-[var(--sh-cream)]' : '' }} relative">
                            Meus Pedidos
                            @php
                                $unreadCount = auth()->user()->unreadNotifications->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="ml-2 inline-flex items-center justify-center rounded-full bg-[var(--sh-muted-gold)] text-xs font-bold text-white h-5 w-5">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="block rounded-lg px-3 py-2 text-sm font-semibold leading-6 text-[var(--sh-dark-text)] hover:bg-[var(--sh-cream)] transition-colors {{ request()->routeIs('profile.*') ? 'bg-[var(--sh-cream)]' : '' }}">
                            Meu Perfil
                        </a>
                        <div class="my-2 h-px bg-[var(--sh-border)]"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left rounded-lg px-3 py-2 text-sm font-semibold leading-6 text-red-400 hover:bg-red-50/50 transition-colors">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Link Minha Conta (quando não logado) --}}
            <a href="{{ route('login') }}" class="p-1 hover:text-[var(--sh-muted-gold)]" title="Minha Conta">
                <span class="sr-only">Minha Conta</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </a>
            @endauth
            
            {{-- SEPARADOR --}}
            <span class="h-6 w-px bg-[var(--sh-border)]" aria-hidden="true"></span>

            {{-- 
              MODIFICADO: MiniCart Livewire 
              Esta é a mudança principal. Removemos o ícone antigo
              e colocamos o componente Livewire no lugar.
            --}}
            <livewire:mini-cart />

        </div>
    </nav>

    {{-- 5. Menu Mobile (Colapsável) --}}
    <div x-show="openMobileMenu" @click.away="openMobileMenu = false" class="lg:hidden" x-transition>
        <div class="space-y-1 px-2 pb-3 pt-2">

            {{-- Loop pelas Categorias Globais (Mobile) --}}
            @if(isset($globalCategories) && $globalCategories->isNotEmpty())
                @foreach ($globalCategories as $category)
                    
                    {{-- Dropdown Mobile (Alpine.js) --}}
                    @if ($category->children->isNotEmpty())
                        <div x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex w-full items-center justify-between rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-medium-bg)] 
                                           {{ request()->is('categoria/'.$category->slug.'*') ? 'bg-[var(--sh-medium-bg)]' : '' }}">
                                <span>{{ strtoupper($category->name) }}</span>
                                <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" class="mt-1 space-y-1 pl-4">
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-medium-bg)] {{ request()->is('categoria/'.$category->slug) ? 'bg-[var(--sh-medium-bg)]' : '' }}">
                                    Ver Tudo
                                </a>
                                @foreach ($category->children as $child)
                                    <a href="{{ route('category.show', $child->slug) }}" 
                                       class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-medium-bg)] {{ request()->is('categoria/'.$child->slug) ? 'bg-[var(--sh-medium-bg)]' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    
                    {{-- Link Simples (Mobile) --}}
                    @else
                        <a href="{{ route('category.show', $category->slug) }}" 
                           class="block rounded-md px-3 py-2 text-base font-medium 
                                  {{ request()->is('categoria/'.$category->slug) 
                                     ? 'bg-[var(--sh-medium-bg)] text-[var(--sh-dark-text)]' 
                                     : 'hover:bg-[var(--sh-medium-bg)]' }}">
                            {{ strtoupper($category->name) }}
                        </a>
                    @endif
                @endforeach
            @endif
            
            {{-- Link Fixo para Sobre Nós (Mobile) --}}
            <a href="{{ route('about') }}" 
               class="block rounded-md px-3 py-2 text-base font-medium 
                      {{ request()->routeIs('about') 
                         ? 'bg-[var(--sh-cream)] text-[var(--sh-dark-text)]' 
                         : 'hover:bg-[var(--sh-cream)] transition-colors' }}">
                SOBRE NÓS
            </a>
            
            {{-- Ícones (Mobile) --}}
            <div class="border-t border-[var(--sh-border)] pt-3 mt-3 space-y-2">
                @auth
                    <a href="{{ route('orders.index') }}" class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-cream)] transition-colors {{ request()->routeIs('orders.*') ? 'bg-[var(--sh-cream)]' : '' }}">
                        Meus Pedidos
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-cream)] transition-colors {{ request()->routeIs('profile.*') ? 'bg-[var(--sh-cream)]' : '' }}">
                        Meu Perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-red-400 hover:bg-red-50/50 transition-colors">
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-cream)] transition-colors">
                    Minha Conta
                </a>
                @endauth
                <a href="{{ route('cart.index') }}" class="block rounded-md px-3 py-2 text-base font-medium hover:bg-[var(--sh-cream)] transition-colors">
                    Carrinho
                </a>
            </div>

        </div>
    </div>
</header>