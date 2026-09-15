<!-- HEADER DESKTOP -->
<header class="hidden md:flex items-center justify-between px-6 h-[68px] flex-shrink-0 border-b border-white/5 bg-[#0A0A0F]/85 backdrop-blur-md">
    <h1 class="text-[#F5F5F7] font-semibold text-base">@yield('page-title', 'Feed')</h1>
    
    <div class="flex items-center gap-4">
        <button type="button" class="relative text-[#9A9AA5] hover:text-[#F5F5F7] transition-colors">
            <i class="fa-regular fa-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 text-white text-[9px] font-bold rounded-full bg-[#FF3D57] min-w-[15px] h-[15px] flex items-center justify-center px-0.5">3</span>
        </button>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ auth()->user()->avatar ?? 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=120&h=120&fit=crop&auto=format' }}" 
                 alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
                 class="w-8 h-8 rounded-full object-cover border-2 border-[#FF3D57]/40">
        </a>
    </div>
</header>

<!-- HEADER MOBILE -->
<header class="flex md:hidden items-center justify-between px-4 h-[56px] flex-shrink-0 border-b border-white/5 bg-[#0A0A0F]/90 backdrop-blur-md">
    <button type="button" onclick="toggleMobileMenu()" class="w-9 h-9 flex items-center justify-center rounded-xl text-[#9A9AA5] hover:text-[#F5F5F7] hover:bg-white/5">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <div class="flex items-center gap-2 absolute left-1/2 -translate-x-1/2">
        <div class="w-6 h-6 rounded-lg bg-[#FF3D57] flex items-center justify-center shadow-[0_0_10px_#FF3D57]">
            <i class="fa-solid fa-compact-disc text-white text-xs"></i>
        </div>
        <span class="font-headline text-[15px] text-[#F5F5F7] tracking-wider">ENCONCIERTA</span>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" class="relative text-[#9A9AA5] hover:text-[#F5F5F7]">
            <i class="fa-regular fa-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 text-white text-[9px] font-bold rounded-full bg-[#FF3D57] min-w-[15px] h-[15px] flex items-center justify-center px-0.5">3</span>
        </button>

        <img src="{{ auth()->user()->avatar ?? 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=120&h=120&fit=crop&auto=format' }}" 
             alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
             class="w-7 h-7 rounded-full object-cover border-2 border-[#FF3D57]/40">
    </div>
</header>