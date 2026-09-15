<!DOCTYPE html>
<html lang="es" class="h-full bg-[#0A0A0F]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ENCONCIERTA — Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind v4 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-body text-[#F5F5F7] antialiased grain-overlay overflow-hidden">
    <div class="flex h-screen bg-[#0A0A0F] overflow-hidden">
        
        @include('layouts.partials.dashboard-sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('layouts.partials.dashboard-header')

            <main class="flex-1 overflow-y-auto no-scrollbar" id="main-content">
                @yield('content')
            </main>

            <!-- Bottom Nav para Mobile -->
            <nav class="flex md:hidden items-center justify-around px-2 py-2 flex-shrink-0 border-t border-white/5 bg-[#0A0A0F]/95 backdrop-blur-md">
                <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center py-2 text-[#FF3D57]">
                    <i class="fa-solid fa-house text-lg"></i>
                </a>
                <a href="#" class="flex-1 flex flex-col items-center py-2 text-[#9A9AA5] hover:text-white">
                    <i class="fa-solid fa-compass text-lg"></i>
                </a>
                <button type="button" onclick="openCreatePostModal()" class="w-10 h-10 rounded-xl bg-[#FF3D57] text-white flex items-center justify-center shadow-[0_0_12px_rgba(255,61,87,0.4)]">
                    <i class="fa-solid fa-plus text-base"></i>
                </button>
                <a href="#" class="flex-1 flex flex-col items-center py-2 text-[#9A9AA5] hover:text-white relative">
                    <i class="fa-solid fa-comments text-lg"></i>
                    <span class="absolute top-1 right-5 w-2 h-2 rounded-full bg-[#FF3D57]"></span>
                </a>
                <a href="#" class="flex-1 flex flex-col items-center py-2 text-[#9A9AA5] hover:text-white">
                    <i class="fa-solid fa-user text-lg"></i>
                </a>
            </nav>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>