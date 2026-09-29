<aside id="sidebar-menu" class="hidden md:flex flex-col h-screen sticky top-0 w-[240px] flex-shrink-0 bg-[#0C0C13] border-r border-white/5 transition-all duration-300 z-30">
    <!-- BRAND HEADER -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-5 min-h-[68px] flex-shrink-0 group">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 bg-[#FF3D57] flex items-center justify-center shadow-[0_0_14px_rgba(255,61,87,0.45)] group-hover:scale-105 transition-transform">
            <i class="fa-solid fa-compact-disc text-white text-base"></i>
        </div>
        <span class="font-headline text-lg text-[#F5F5F7] tracking-widest whitespace-nowrap group-hover:text-white transition-colors">ENCONCIERTA</span>
    </a>

    <!-- NAVEGACIÓN PRINCIPAL -->
    <nav class="flex flex-col gap-1 px-3 flex-1 overflow-y-auto no-scrollbar">
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

        @php
            $isOwnProfile = request()->routeIs('profile.show') && (!request()->route('id') || request()->route('id') == auth()->id());
        @endphp
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all relative {{ $isOwnProfile ? 'bg-[#FF3D57]/12 text-[#FF3D57]' : 'text-[#9A9AA5] hover:bg-white/5 hover:text-white' }}">
            <i class="fa-solid fa-user text-lg w-5 text-center"></i>
            <span class="text-sm font-medium">Mi perfil</span>
            @if($isOwnProfile)
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 rounded-full bg-[#FF3D57]"></div>
            @endif
        </a>

        <div class="mt-4 pb-2">
            <a href="{{ route('blogs.create') }}" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#FF3D57] text-white font-semibold text-sm shadow-[0_0_16px_rgba(255,61,87,0.3)] hover:brightness-110 transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Nuevo Blog</span>
            </a>
        </div>
    </nav>

   <!-- FOOTER -->
    @php
        $savedAccountIds = session('saved_accounts', []);
        if (!in_array(auth()->id(), $savedAccountIds)) {
            $savedAccountIds[] = auth()->id();
            session(['saved_accounts' => array_unique($savedAccountIds)]);
        }
        $savedUsers = \App\Models\User::whereIn('id_usuario', $savedAccountIds)->get();
    @endphp

    <div class="border-t border-white/5 p-2.5 mt-auto flex-shrink-0 relative" x-data="{ openAccounts: false }">
        
        <!-- POP-OVER DE SELECCIÓN DE CUENTAS (DISEÑO ULTRA COMPACTO Y MINIMALISTA) -->
        <div x-show="openAccounts" 
             @click.outside="openAccounts = false" 
             x-cloak
             style="display: none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
             class="absolute bottom-full left-2 right-2 mb-2 z-50 bg-[#121218] border border-white/10 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.9)] p-1.5 flex flex-col gap-1">
            
            <div class="px-2 py-1 text-[10px] font-bold tracking-wider uppercase text-[#9A9AA5] border-b border-white/5 flex items-center justify-between">
                <span>Cuentas</span>
                <span class="text-[9px] font-mono text-[#7C5CFF]">{{ count($savedUsers) }}</span>
            </div>

            <!-- LISTA DE CUENTAS REGISTRADAS -->
            <div class="max-h-36 overflow-y-auto space-y-0.5 no-scrollbar py-0.5">
                @foreach($savedUsers as $sUser)
                    @php
                        $isActive = $sUser->id_usuario === auth()->id();
                        $sAvatar = !empty($sUser->avatar) 
                            ? (str_starts_with($sUser->avatar, 'http') ? $sUser->avatar : asset('storage/' . $sUser->avatar))
                            : asset('images/default-avatar.svg');
                    @endphp

                    <div class="flex items-center justify-between p-1 rounded-lg transition-colors {{ $isActive ? 'bg-white/[0.06]' : 'hover:bg-white/[0.03]' }}">
                        @if(!$isActive)
                            <form action="{{ route('accounts.switch', $sUser->id_usuario) }}" method="POST" class="flex-1 min-w-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 text-left cursor-pointer group">
                                    <img src="{{ $sAvatar }}" class="w-6 h-6 rounded-full object-cover border border-white/10 group-hover:border-[#FF3D57] transition-colors flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[11px] font-medium text-[#E0E0E6] truncate group-hover:text-white transition-colors leading-none">{{ $sUser->nombre }}</div>
                                        <div class="text-[9px] text-[#9A9AA5] truncate leading-none mt-0.5">{{ '@' . $sUser->handle }}</div>
                                    </div>
                                </button>
                            </form>
                        @else
                            <div class="flex items-center gap-2 flex-1 min-w-0 px-1 py-0.5">
                                <img src="{{ $sAvatar }}" class="w-6 h-6 rounded-full object-cover border border-[#FF3D57] flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="text-[11px] font-semibold text-white truncate leading-none flex items-center gap-1">
                                        <span class="truncate">{{ $sUser->nombre }}</span>
                                        <i class="fa-solid fa-circle-check text-[#2FE6D0] text-[8px]" title="Activa"></i>
                                    </div>
                                    <div class="text-[9px] text-[#9A9AA5] truncate leading-none mt-0.5">{{ '@' . $sUser->handle }}</div>
                                </div>
                            </div>
                        @endif

                        @if(count($savedUsers) > 1)
                            <form action="{{ route('accounts.remove', $sUser->id_usuario) }}" method="POST" class="flex-shrink-0 ml-1">
                                @csrf
                                <button type="submit" class="p-1 text-[#6A6A75] hover:text-[#FF3D57] transition-colors cursor-pointer" title="Quitar cuenta">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- ACCIONES DEL MENÚ -->
            <div class="border-t border-white/5 pt-1 mt-0.5 space-y-0.5">
                <button type="button" 
                        onclick="triggerAuthModalLogin()" 
                        @click="openAccounts = false"
                        class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-[11px] font-medium text-[#D0D0D8] hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px] text-[#2FE6D0]"></i>
                    <span>Añadir otra cuenta</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-[11px] font-medium text-[#D0D0D8] hover:bg-[#FF3D57]/10 hover:text-[#FF3D57] transition-colors cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-[10px] text-[#FF3D57]"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- BOTÓN PRINCIPAL DEL FOOTER -->
        <button type="button" 
                @click="openAccounts = !openAccounts" 
                class="w-full flex items-center gap-2.5 p-1.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.07] border border-transparent hover:border-white/10 transition-all text-left cursor-pointer group">
            <img src="{{ auth()->user()->avatar_url }}" 
                 alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
                 class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-white/10 group-hover:border-[#FF3D57] transition-all">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1 min-w-0">
                    <span class="text-[#F5F5F7] text-xs font-semibold truncate group-hover:text-[#FF3D57] transition-colors">{{ auth()->user()->nombre ?? 'Melómano' }}</span>
                    @if(auth()->user()?->es_verificado)
                        <i class="fa-solid fa-circle-check text-[#7C5CFF] text-[9px] flex-shrink-0" title="Melómano verificado"></i>
                    @endif
                </div>
                <div class="text-[#9A9AA5] text-[10px] truncate">{{ auth()->user()->email ?? '@melomano' }}</div>
            </div>
            
            <i class="fa-solid fa-ellipsis-vertical text-[#9A9AA5] group-hover:text-white transition-colors p-1 text-xs"></i>
        </button>
    </div>

    <script>
        if (typeof window.triggerAuthModalLogin === 'undefined') {
            window.triggerAuthModalLogin = function() {
                const modal = document.getElementById('authModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    if (typeof window.switchAuthTab === 'function') {
                        window.switchAuthTab('login');
                    }
                }
            };
        }
    </script>
</aside>