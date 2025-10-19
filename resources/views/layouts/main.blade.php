<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shava Haux')</title>

    {{-- Bootstrap CSS e Ícones via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Seus Estilos Personalizados --}}
    <style>
        @font-face { font-family: 'Psychoart'; src: url("{{ asset('fonts/psychoart.ttf') }}") format('truetype'); }
        body { font-family: 'Psychoart', serif; background-color: #FDF8F0; color: #403A30; }
        :root {
            --sh-muted-gold: #B3AF8F; --sh-dark-text: #403A30; --sh-white: #FFFFFF; --sh-medium-bg: #EAE3D4;
        }
        /* Header */
        .navbar { background-color: var(--sh-white); border-bottom: 1px solid var(--sh-medium-bg); }
        .sh-logo { height: 60px; }
        .navbar-nav .nav-link { color: var(--sh-dark-text); font-weight: 700; font-size: 1.1rem; padding: 0.8rem 1.2rem; }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active { color: var(--sh-muted-gold); }
        .dropdown-menu { background-color: var(--sh-white); border: 1px solid var(--sh-medium-bg); }
        .dropdown-item { font-family: 'Psychoart', serif; font-weight: bold; color: var(--sh-dark-text); }
        .dropdown-item:hover { background-color: var(--sh-medium-bg); }
        @media (min-width: 992px) { .navbar-nav .dropdown:hover .dropdown-menu { display: block; margin-top: 0; } }
        .navbar .btn-link { color: var(--sh-dark-text); text-decoration: none; }
        .navbar .btn-link:hover { color: var(--sh-muted-gold); }
        /* Footer */
        .footer-shava { background-color: #fefcf9; color: var(--sh-dark-text); padding-top: 4rem; padding-bottom: 2rem; position: relative; overflow: hidden; } /* Adicionado overflow: hidden */
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; } /* Removido overflow: hidden daqui */
        .footer-shava .footer-content { position: relative; z-index: 1; /* Removido background e blur daqui, pode ser adicionado se precisar */ }
        .footer-shava h6 { font-weight: bold; text-transform: uppercase; margin-bottom: 1rem; }
        .footer-shava a { color: var(--sh-dark-text); text-decoration: none; }
        .footer-shava a:hover { color: #C87941; }
        .footer-shava .bottom-bar { border-top: 1px solid var(--sh-medium-bg); color: var(--sh-muted-gold); }
        .footer-shava .footer-logo { max-height: 70px; margin-bottom: 1rem; }
        /* Carrossel */
         .hero-slide-image { width: 100%; height: 100%; background-size: cover; background-position: center; }
         .carousel-caption { background: rgba(0, 0, 0, 0.4); inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white;}
         /* Correção visibilidade controles carrossel */
         .carousel-control-prev-icon, .carousel-control-next-icon { filter: invert(1) grayscale(100); }
         .carousel-indicators button { background-color: rgba(255, 255, 255, 0.5); border: 0; }
         .carousel-indicators .active { background-color: white; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    <main>
        @yield('content')
    </main>
    @include('layouts.partials.footer')

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    {{-- Script p5.js e Lógica do Footer --}}
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.9.0/p5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvasContainer = document.getElementById('canvas-container');

            // Só executa o script p5 se o container existir
            if (canvasContainer) {
                const sketch = (p) => {
                    const palette = ['#2B2D42', '#C87941', '#A88C6B', '#8D99AE'];
                    const tileSize = 80;

                    p.setup = () => {
                        let canvas = p.createCanvas(canvasContainer.offsetWidth, canvasContainer.offsetHeight);
                        canvas.parent(canvasContainer);
                        p.noLoop();
                    };

                    p.draw = () => {
                        p.background('#fefcf9');
                        for (let x = -tileSize; x < p.width + tileSize; x += tileSize) {
                            for (let y = -tileSize; y < p.height + tileSize; y += tileSize) {
                                drawMotif(p, x, y, tileSize, palette);
                            }
                        }
                    };

                    function drawMotif(p, x, y, size, colors) {
                        p.push();
                        p.translate(x + size / 2, y + size / 2);
                        p.rotate(p.random([0, p.HALF_PI, p.PI, p.PI + p.HALF_PI]));
                        p.strokeWeight(1.5);
                        p.noFill();
                        p.stroke(p.random(colors));
                        p.quad(size / 2, 0, size, size / 2, size / 2, size, 0, size / 2); // Diamante
                        if(p.random(1) < 0.4) { // Detalhe interno
                           p.stroke(p.random(colors));
                           p.rect(-size*0.15, -size*0.15, size*0.3, size*0.3);
                        }
                        p.pop();
                    }

                    p.windowResized = () => {
                       p.resizeCanvas(canvasContainer.offsetWidth, canvasContainer.offsetHeight);
                       p.redraw(); // Força o redesenho após redimensionar
                    };
                };
                new p5(sketch);
            } else {
                console.warn('Container do canvas do footer (#canvas-container) não encontrado.');
            }
        });
    </script>
    @endpush

    {{-- Espaço para outros scripts --}}
    @stack('scripts')
</body>
</html>