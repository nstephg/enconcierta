<!-- HEADER DESKTOP -->
<header class="hidden md:flex items-center justify-between px-6 h-[68px] flex-shrink-0 border-b border-white/5 bg-[#0A0A0F]/85 backdrop-blur-md">
    <h1 class="text-[#F5F5F7] font-semibold text-base">@yield('page-title', 'Feed')</h1>
    
    <div class="flex items-center gap-4">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}" 
                 alt="{{ auth()->user()->nombre }}" 
                 class="w-8 h-8 rounded-full object-cover border-2 border-[#FF3D57]/40">
        </a>
    </div>
</header>

<!-- HEADER MOBILE (LIMPIO SIN HAMBURGUESA) -->
<header class="flex md:hidden items-center justify-between px-4 h-[56px] flex-shrink-0 border-b border-white/5 bg-[#0A0A0F]/90 backdrop-blur-md sticky top-0 z-40">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg bg-[#FF3D57] flex items-center justify-center shadow-[0_0_10px_#FF3D57]">
            <i class="fa-solid fa-compact-disc text-white text-xs"></i>
        </div>
        <span class="font-headline text-base text-[#F5F5F7] tracking-wider">ENCONCIERTA</span>
    </a>

    <div class="flex items-center gap-3">
        <button type="button" class="relative text-[#9A9AA5] hover:text-[#F5F5F7] p-1">
            <i class="fa-regular fa-bell text-lg"></i>
            <span class="absolute top-0 right-0 text-white text-[9px] font-bold rounded-full bg-[#FF3D57] min-w-[14px] h-[14px] flex items-center justify-center px-0.5">3</span>
        </button>

        <a href="{{ route('profile.show') }}">
            <img src="{{ auth()->user()->avatar_url }}" 
                 alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
                 class="w-7 h-7 rounded-full object-cover border-2 border-[#FF3D57]/40">
        </a>
    </div>
</header>