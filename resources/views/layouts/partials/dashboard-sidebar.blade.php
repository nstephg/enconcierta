<aside id="sidebar-menu" class="hidden md:flex flex-col h-full w-[240px] flex-shrink-0 bg-[#0C0C13] border-r border-white/5 transition-all duration-300 relative">
    <div class="flex items-center gap-3 px-5 py-5 min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 bg-[#FF3D57] flex items-center justify-center shadow-[0_0_14px_rgba(255,61,87,0.45)]">
            <i class="fa-solid fa-compact-disc text-white text-base"></i>
        </div>
        <span class="font-headline text-lg text-[#F5F5F7] tracking-widest whitespace-nowrap">ENCONCIERTA</span>
    </div>

    <nav class="flex flex-col gap-1 px-3 flex-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all relative {{ request()->routeIs('dashboard') ? 'bg-[#FF3D57]/12 text-[#FF3D57]' : 'text-[#9A9AA5] hover:bg-white/5 hover:text-white' }}">
            <i class="fa-solid fa-house text-lg w-5 text-center"></i>
            <span class="text-sm font-medium">Feed</span>
            @if(request()->routeIs('dashboard'))
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 rounded-full bg-[#FF3D57]"></div>
            @endif
        </a>

        <a href="#" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-[#9A9AA5] hover:bg-white/5 hover:text-white transition-all">
            <i class="fa-solid fa-compass text-lg w-5 text-center"></i>
            <span class="text-sm font-medium">Explorar</span>
        </a>

        <a href="#" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-[#9A9AA5] hover:bg-white/5 hover:text-white transition-all">
            <i class="fa-solid fa-comments text-lg w-5 text-center"></i>
            <span class="text-sm font-medium">Chats</span>
            <span class="ml-auto text-white text-[10px] font-bold rounded-full bg-[#FF3D57] min-w-[18px] h-[18px] flex items-center justify-center px-1">10</span>
        </a>

        <a href="#" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-[#9A9AA5] hover:bg-white/5 hover:text-white transition-all">
            <i class="fa-solid fa-user text-lg w-5 text-center"></i>
            <span class="text-sm font-medium">Mi perfil</span>
        </a>

        <div class="mt-4">
            <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#FF3D57] text-white font-semibold text-sm shadow-[0_0_16px_rgba(255,61,87,0.3)] hover:brightness-110 transition-all">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Nuevo post</span>
            </button>
        </div>
    </nav>

    <div class="border-t border-white/5 p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-white/[0.03]">
            <img src="{{ auth()->user()->avatar ?? 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=120&h=120&fit=crop&auto=format' }}" 
                 alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
                 class="w-9 h-9 rounded-full object-cover flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="text-[#F5F5F7] text-xs font-semibold truncate">{{ auth()->user()->nombre ?? 'Camila Restrepo' }}</div>
                <div class="text-[#9A9AA5] text-[11px] truncate">{{ auth()->user()->email ?? '@camila_beats' }}</div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[#9A9AA5] hover:text-[#FF3D57] transition-colors p-1" title="Cerrar sesión">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</aside>