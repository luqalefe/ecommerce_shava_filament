<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shava Haux - Headshop e Tabacaria em Rio Branco')</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'Shava Haux - Headshop e tabacaria em Rio Branco, Acre. Sedas, pipes, dichavadores, roupas de cânhamo e artigos exclusivos. Frete grátis em Rio Branco. Entrega rápida!')">
    <meta name="keywords" content="@yield('meta_keywords', 'headshop rio branco, tabacaria acre, sedas, pipes, dichavador, artigos de tabacaria, headshop acre, loja de seda, shava haux, hempwear, roupas de cânhamo')">
    <meta name="author" content="Shava Haux">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Shava Haux - Headshop e Tabacaria em Rio Branco')">
    <meta property="og:description" content="@yield('og_description', 'Headshop e tabacaria em Rio Branco. Sedas, pipes, dichavadores e artigos exclusivos. Frete grátis em Rio Branco!')">
    <meta property="og:image" content="@yield('og_image', asset('images/shava_banner.png'))">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Shava Haux">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Shava Haux - Headshop e Tabacaria em Rio Branco')">
    <meta name="twitter:description" content="@yield('og_description', 'Headshop e tabacaria em Rio Branco. Sedas, pipes, dichavadores e artigos exclusivos.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/shava_banner.png'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo_shava.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Vite para TailwindCSS e Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
    {{-- Bootstrap Icons (apenas ícones, sem CSS/JS do Bootstrap) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts - DM Serif Display --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    
    {{-- Estilos Personalizados --}}
    <style>
        body { 
            font-family: 'DM Serif Display', serif; 
            background-color: #FAFAF9; 
            color: #44403C;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'DM Serif Display', serif;
            text-transform: uppercase;
        }
        /* Reset para inputs e botões */
        input, textarea, select, button {
            font-family: 'DM Serif Display', serif;
        }
        /* Exceções - manter lowercase onde necessário */
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea {
            text-transform: none;
        }
        :root {
            --sh-muted-gold: #A8947A; 
            --sh-dark-text: #44403C; 
            --sh-primary: #1C4532;
            --sh-primary-hover: #15372A;
            --sh-terracotta: #9A4F32;
            --sh-white: #FFFFFF; 
            --sh-cream: #FAFAF9;
            --sh-stone-100: #F5F5F4;
            --sh-stone-200: #E7E5E4;
            --sh-border: #E7E5E4;
        }
        /* Utilitários CSS customizados */
        .hero-slide-image { width: 100%; height: 100%; background-size: cover; background-position: center; }
        
        /* Alpine.js x-cloak - hide elements until Alpine loads */
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    <main style="position: relative; z-index: 1;">
        @yield('content')
    </main>
    @include('layouts.partials.bottom-nav')
    @include('layouts.partials.footer')

    {{-- Bootstrap JS removido - usando Alpine.js via Vite --}}
    
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