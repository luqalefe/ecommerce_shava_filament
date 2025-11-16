<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shava Haux')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Vite para TailwindCSS e Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
    {{-- Bootstrap CSS (mantido para compatibilidade com páginas antigas) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Estilos Personalizados --}}
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            background-color: #FDF8F0; 
            color: #403A30; 
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        :root {
            --sh-muted-gold: #B3AF8F; 
            --sh-dark-text: #403A30; 
            --sh-white: #FFFFFF; 
            --sh-medium-bg: #EAE3D4;
        }
        /* Carrossel (mantido para compatibilidade) */
        .hero-slide-image { width: 100%; height: 100%; background-size: cover; background-position: center; }
        .carousel-caption { background: rgba(0, 0, 0, 0.4); inset: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white;}
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

    {{-- Bootstrap JS Bundle (mantido para compatibilidade) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    {{-- Livewire Scripts --}}
    @livewireScripts

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