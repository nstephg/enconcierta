<nav class="md:hidden fixed bottom-0 inset-x-0 z-[9999] bg-[#0C0C13] border-t border-white/10 px-3 pt-2 pb-2 flex items-center justify-around shadow-[0_-10px_30px_rgba(0,0,0,0.95)]">
    <!-- Feed -->
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 p-1 min-w-[52px] {{ request()->routeIs('dashboard') ? 'text-[#FF3D57]' : 'text-[#9A9AA5] hover:text-white' }}">
        <i class="fa-solid fa-house text-lg"></i>
        <span class="text-[10px] font-medium">Feed</span>
    </a>

    <!-- Explorar -->
    <a href="#" class="flex flex-col items-center gap-1 p-1 min-w-[52px] text-[#9A9AA5] hover:text-white">
        <i class="fa-solid fa-compass text-lg"></i>
        <span class="text-[10px] font-medium">Explorar</span>
    </a>

    <!-- Botón Crear Blog / Post Flotante -->
    <button 
        type="button" 
        onclick="if (typeof openCreatePostModal === 'function') { openCreatePostModal(); } else { window.location.href = '{{ route('blogs.create') }}'; }" 
        class="flex items-center justify-center w-11 h-11 rounded-full bg-[#FF3D57] text-white shadow-[0_0_16px_rgba(255,61,87,0.6)] active:scale-95 transition-transform -mt-5 border-2 border-[#0A0A0F] cursor-pointer"
    >
        <i class="fa-solid fa-plus text-base"></i>
    </button>

    <!-- Chats -->
    <a href="#" class="flex flex-col items-center gap-1 p-1 min-w-[52px] text-[#9A9AA5] hover:text-white relative">
        <i class="fa-solid fa-comments text-lg"></i>
        <span class="text-[10px] font-medium">Chats</span>
        <span class="absolute top-0 right-1 text-white text-[8px] font-bold rounded-full bg-[#FF3D57] min-w-[13px] h-[13px] flex items-center justify-center px-0.5">10</span>
    </a>

    <!-- Mi Perfil -->
    <a href="{{ route('profile.show') }}" class="flex flex-col items-center gap-1 p-1 min-w-[52px] {{ request()->routeIs('profile.show') ? 'text-[#FF3D57]' : 'text-[#9A9AA5] hover:text-white' }}">
        <i class="fa-solid fa-user text-lg"></i>
        <span class="text-[10px] font-medium">Perfil</span>
    </a>
</nav>