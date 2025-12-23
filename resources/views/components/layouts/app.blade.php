<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Shava Haux' }}</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Vite para TailwindCSS e Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
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
            --sh-white: #FFFFFF; 
            --sh-cream: #FAFAF9;
            --sh-stone-100: #F5F5F4;
            --sh-stone-200: #E7E5E4;
            --sh-border: #E7E5E4;
            --sh-text-light: #78716C;
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    
    <main>
        {{ $slot }}
    </main>
    
    @include('layouts.partials.footer')
    
    {{-- Livewire Scripts --}}
    @livewireScripts
    
    
    @stack('scripts')
</body>
</html>
