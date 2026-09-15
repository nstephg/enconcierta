<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ENCONCIERTA — Sincroniza tu parche, vive el show')</title>

    <!-- Tailwind v4 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased selection:bg-[#FF3D57] selection:text-white bg-[#0A0A0F] text-[#F5F5F7] font-sans overflow-x-hidden">

    <!-- Interactive Frequency Wave Canvas Background -->
    <div class="fixed inset-0 pointer-events-none z-0 opacity-40">
        <canvas id="frequencyCanvas" class="w-full h-full"></canvas>
    </div>

    <!-- Ambient Stage Background Spotlights -->
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-[#FF3D57]/20 rounded-full blur-[120px] pointer-events-none z-0 animate-pulse-glow"></div>
    <div class="fixed top-1/3 -right-40 w-125 h-125 bg-[#7C5CFF]/20 rounded-full blur-[150px] pointer-events-none z-0 animate-pulse-glow" style="animation-delay: 3s;"></div>

    <!-- MAIN CONTAINER -->
    <div class="relative z-10 flex flex-col min-h-screen">
        @include('layouts.partials.header')

        <main class="grow">
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>

    @include('layouts.partials.modals')

    @stack('scripts')
</body>
</html>