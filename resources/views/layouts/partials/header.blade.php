<header id="mainHeader" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 border-b border-transparent bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- BRAND LOGO -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none" aria-label="ENCONCIERTA - Inicio">
            <div class="px-3.5 py-2 rounded-xl border border-dashed border-white/30 bg-[#17171F]/40 backdrop-blur-sm text-white/70 font-mono-code text-xs font-semibold tracking-wider group-hover:border-[#FF3D57] group-hover:text-white transition-all duration-300 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FF3D57]"></span>
                <span>[ ENCONCIERTA ]</span>
            </div>
        </a>

        <!-- DESKTOP NAV LINKS -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="#como-funciona" class="text-[#9A9AA5] hover:text-white transition-colors">
                Cómo funciona
            </a>
            <a href="#shows-section" class="text-[#9A9AA5] hover:text-white transition-colors flex items-center gap-2">
                <i class="fa-solid fa-ticket text-xs text-[#FF3D57]"></i>
                Explorar shows
            </a>
            <a href="#comunidad" class="text-[#9A9AA5] hover:text-white transition-colors">
                Memorias
            </a>
        </nav>

        <!-- DESKTOP ACTIONS -->
        <div class="hidden md:flex items-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-[#9A9AA5] hover:text-white px-3 py-2 font-medium transition-colors cursor-pointer">
                    Mi Feed
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-[#FF3D57] hover:text-[#FF3D57]/80 px-3 py-2 font-medium transition-colors cursor-pointer">
                        Salir
                    </button>
                </form>
            @else
                <button onclick="openAuthModal()" class="text-sm text-[#9A9AA5] hover:text-white px-3 py-2 font-medium transition-colors cursor-pointer">
                    Iniciar sesión
                </button>
            @endauth

            <button onclick="openWizardModal()" class="bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-semibold text-sm px-5 py-2.5 rounded-full transition-all duration-300 glow-primary hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                <span>Enconcierta tu parche</span>
            </button>
        </div>

        <!-- MOBILE HAMBURGER BUTTON -->
        <button id="mobileMenuBtn" class="md:hidden text-white p-2 focus:outline-none" aria-label="Abrir menú de navegación">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <!-- MOBILE MENU DRAWER -->
    <div id="mobileDrawer" class="hidden md:hidden bg-[#17171F] border-b border-white/10 px-6 py-6 transition-all duration-300">
        <div class="flex flex-col gap-4">
            <a href="#como-funciona" onclick="toggleMobileMenu()" class="text-white py-2 flex items-center gap-3">
                <i class="fa-solid fa-list-check text-[#7C5CFF]"></i>
                Cómo funciona
            </a>
            <a href="#shows-section" onclick="toggleMobileMenu()" class="text-white py-2 flex items-center gap-3">
                <i class="fa-solid fa-ticket text-[#FF3D57]"></i>
                Explorar shows
            </a>
            <a href="#comunidad" onclick="toggleMobileMenu()" class="text-white py-2 flex items-center gap-3">
                <i class="fa-solid fa-camera-retro text-[#FFB020]"></i>
                Memorias del Show
            </a>

            <div class="pt-4 border-t border-white/10 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full py-3 rounded-xl border border-white/20 text-white font-medium text-center">
                        Mi Feed
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl border border-[#FF3D57] text-[#FF3D57] font-medium text-center">
                            Cerrar sesión ({{ Auth::user()->nombre }})
                        </button>
                    </form>
                @else
                    <button onclick="openAuthModal(); toggleMobileMenu();" class="w-full py-3 rounded-xl border border-white/20 text-white font-medium text-center">
                        Iniciar sesión
                    </button>
                @endauth
                <button onclick="openWizardModal(); toggleMobileMenu();" class="w-full py-3 rounded-xl bg-[#FF3D57] text-white font-semibold text-center glow-primary">
                    Enconcierta tu parche
                </button>
            </div>
        </div>
    </div>
</header>