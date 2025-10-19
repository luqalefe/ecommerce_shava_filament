{{-- resources/views/layouts/partials/navbar.blade.php --}}

{{-- Importa a facade do Carrinho para poder usar Cart::getContent() --}}
@php use Darryldecode\Cart\Facades\CartFacade as Cart; @endphp

<header class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm"> {{-- Adicionado shadow-sm --}}
    <div class="container-fluid">
        {{-- Logo com link para a Home --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo_shava.png') }}" alt="Logo Shava Haux" class="sh-logo">
        </a>
        {{-- Botão Toggler para Mobile --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        {{-- Conteúdo Colapsável do Menu --}}
        <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">

            {{-- Links do Menu Principal --}}
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                 {{-- Link Fixo para a Loja --}}
                 <li class="nav-item">
                    {{-- Verifica se a rota atual é 'products.index' para aplicar a classe 'active' --}}
                    <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">LOJA</a>
                 </li>

                 {{-- Loop pelas Categorias Globais --}}
                @if(isset($globalCategories) && $globalCategories->isNotEmpty())
                    @foreach ($globalCategories as $category)
                        {{-- Dropdown para Categorias com Filhos --}}
                        @if ($category->children->isNotEmpty())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('categoria/'.$category->slug.'*') ? 'active' : '' }}" href="{{ route('category.show', $category->slug) }}" id="navbarDropdown-{{ $category->id }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ strtoupper($category->name) }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown-{{ $category->id }}">
                                    <li><a class="dropdown-item {{ request()->is('categoria/'.$category->slug) ? 'active' : '' }}" href="{{ route('category.show', $category->slug) }}">Ver Tudo em {{ $category->name }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    {{-- Loop pelas subcategorias --}}
                                    @foreach ($category->children as $child)
                                        <li><a class="dropdown-item {{ request()->is('categoria/'.$child->slug) ? 'active' : '' }}" href="{{ route('category.show', $child->slug) }}">{{ $child->name }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        {{-- Link Simples para Categorias sem Filhos --}}
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('categoria/'.$category->slug) ? 'active' : '' }}" href="{{ route('category.show', $category->slug) }}">{{ strtoupper($category->name) }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
                {{-- Link Fixo para Sobre Nós --}}
                 <li class="nav-item"> <a class="nav-link" href="#">SOBRE NÓS</a> </li>
            </ul>

            {{-- Ícones da Direita --}}
            <div class="d-flex align-items-center">
                {{-- Link Minha Conta (Ajustar href quando tiver as rotas de auth) --}}
                <a href="{{ route('login') }}" class="btn btn-link me-2" title="Minha Conta"><i class="bi bi-person fs-5"></i></a>
                {{-- Link Carrinho com Contagem Dinâmica --}}
                <a href="{{ route('cart.index') }}" class="btn btn-link position-relative" title="Carrinho">
                    <i class="bi bi-cart fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background-color: var(--sh-muted-gold); color: white;">
                        {{ Cart::getContent()->count() }} {{-- Mostra a contagem de tipos de itens --}}
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>