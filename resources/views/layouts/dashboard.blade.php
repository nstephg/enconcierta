<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#0A0A0F]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard — ENCONCIERTA')</title>

    <!-- Tailwind v4 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Emoji Mart Picker -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen min-h-[100dvh] antialiased bg-[#0A0A0F] text-[#F5F5F7] font-sans selection:bg-[#FF3D57] selection:text-white flex flex-col md:flex-row relative">

    <!-- SIDEBAR DESKTOP -->
    @include('layouts.partials.dashboard-sidebar')

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen md:min-h-0 md:h-full overflow-y-auto pb-24 md:pb-0 relative">
        @include('layouts.partials.dashboard-header')

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    <!-- BARRA NAVEGACIÓN INFERIOR MÓVIL FIJA -->
    @include('layouts.partials.dashboard-bottom-nav')

    @stack('scripts')
</body>
</html>