{{-- resources/views/layouts/partials/footer.blade.php --}}

<footer class="footer-shava"> {{-- Classe CSS definida no main.blade.php --}}
    {{-- Container para o canvas do fundo animado --}}
    <div id="canvas-container"></div>

    <div class="container footer-content"> {{-- Classe CSS definida no main.blade.php --}}
        <div class="row">
            {{-- Coluna 1: Logo e Descrição --}}
            <div class="col-lg-4 col-md-12 mb-4 text-center text-lg-start">
                <img src="{{ asset('images/logo_shava.png') }}" alt="Logo Shava Haux" class="footer-logo">
                <p class="small mt-2">Expandindo a consciência através de aromas e sensações.</p>
            </div>

            {{-- Coluna 2: Navegação --}}
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <h6>Navegue</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}">Início</a></li>
                    <li class="mb-2"><a href="{{ route('products.index') }}">Produtos</a></li>
                    <li class="mb-2"><a href="#">Sobre Nós</a></li>
                </ul>
            </div>

            {{-- Coluna 3: Suporte --}}
            <div class="col-lg-3 col-md-4 col-6 mb-4">
                <h6>Suporte</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">Dúvidas Frequentes</a></li>
                    <li class="mb-2"><a href="#">Política de Privacidade</a></li>
                    <li class="mb-2"><a href="#">Termos de Uso</a></li>
                </ul>
            </div>

            {{-- Coluna 4: Contato --}}
            <div class="col-lg-3 col-md-4 col-12 mb-4">
                <h6>Contato</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" target="_blank"><i class="bi bi-whatsapp me-2"></i> WhatsApp</a></li>
                    <li class="mb-2"><a href="#" target="_blank"><i class="bi bi-instagram me-2"></i> Instagram</a></li>
                    <li class="mb-2"><a href="mailto:shavahaux@gmail.com"><i class="bi bi-envelope-fill me-2"></i> shavahaux@gmail.com</a></li>
                </ul>
            </div>
        </div>

        {{-- Barra Inferior --}}
        <div class="bottom-bar mt-4 pt-4 text-center">
            <p class="small mb-0">&copy; {{ date('Y') }} Shava Haux. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>