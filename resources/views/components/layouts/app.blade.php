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
    
    {{-- Google Fonts - Fonte elegante e moderna --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Preconnect para Stripe (melhora performance) --}}
    <link rel="preconnect" href="https://js.stripe.com">
    <link rel="dns-prefetch" href="https://js.stripe.com">
    
    {{-- Estilos Personalizados --}}
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            background-color: #FAF8F5; 
            color: #5A5A5A; 
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            color: #4A4A4A;
            font-weight: 500;
        }
        :root {
            --sh-muted-gold: #C9A87A; 
            --sh-dark-text: #4A4A4A; 
            --sh-white: #FFFFFF; 
            --sh-light-bg: #FAF8F5;
            --sh-cream: #F5F1EB;
            --sh-lavender: #E8E3F0;
            --sh-soft-purple: #D4C5E8;
            --sh-text-light: #8B8B8B;
            --sh-border: #E5E0D8;
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
