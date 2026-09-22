<header id="mainHeader" class="fixed top-0 left-0 right-0 z-50 border-b border-transparent bg-transparent transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between relative z-50">
        
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
                <button type="button" onclick="openAuthModal()" class="text-sm text-[#9A9AA5] hover:text-white px-3 py-2 font-medium transition-colors cursor-pointer">
                    Iniciar sesión
                </button>
            @endauth

            <button type="button" onclick="openWizardModal()" class="bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-semibold text-sm px-5 py-2.5 rounded-full transition-all duration-300 glow-primary hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                <span>Enconcierta tu parche</span>
            </button>
        </div>

        <!-- MOBILE HAMBURGER BUTTON -->
        <button type="button" 
                id="mobileMenuBtn" 
                onclick="toggleMobileMenu()" 
                class="md:hidden text-white p-2 focus:outline-none cursor-pointer select-none relative z-50" 
                aria-label="Abrir menú de navegación">
            <i class="fa-solid fa-bars text-xl" id="mobileMenuIcon"></i>
        </button>
    </div>

    <!-- MOBILE MENU FULL-SCREEN DRAWER -->
    <div id="mobileDrawer" 
         class="hidden md:hidden fixed inset-x-0 top-20 bottom-0 bg-[#0A0A0F] px-6 py-8 z-40 flex flex-col justify-between overflow-y-auto border-t border-white/10">
        
        <!-- NAVEGACIÓN CENTRADA -->
        <div class="flex flex-col items-center text-center gap-3">
            <a href="#como-funciona" onclick="toggleMobileMenu()" class="w-full text-center text-lg font-bold text-[#F5F5F7] hover:text-[#FF3D57] py-3.5 border-b border-white/5 transition-colors">
                Cómo funciona
            </a>
            
            <a href="#shows-section" onclick="toggleMobileMenu()" class="w-full text-center text-lg font-bold text-[#F5F5F7] hover:text-[#FF3D57] py-3.5 border-b border-white/5 flex items-center justify-center gap-2.5 transition-colors">
                <i class="fa-solid fa-ticket text-sm text-[#FF3D57]"></i>
                <span>Explorar shows</span>
            </a>
            
            <a href="#comunidad" onclick="toggleMobileMenu()" class="w-full text-center text-lg font-bold text-[#F5F5F7] hover:text-[#FF3D57] py-3.5 border-b border-white/5 transition-colors">
                Memorias
            </a>
        </div>

        <!-- BOTONES DE ACCIÓN CENTRADOS -->
        <div class="flex flex-col gap-3 pt-6 mt-auto w-full max-w-sm mx-auto">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full py-3.5 rounded-2xl border border-white/15 text-white font-semibold text-center text-sm hover:bg-white/5 transition-all">
                    Mi Feed
                </a>
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-3.5 rounded-2xl border border-[#FF3D57]/30 text-[#FF3D57] font-semibold text-center text-sm hover:bg-[#FF3D57]/10 transition-all cursor-pointer">
                        Cerrar sesión ({{ Auth::user()->nombre }})
                    </button>
                </form>
            @else
                <button type="button" onclick="toggleMobileMenu(); openAuthModal();" class="w-full py-3.5 rounded-2xl border border-white/15 text-white font-semibold text-center text-sm cursor-pointer hover:bg-white/5 transition-all">
                    Iniciar sesión
                </button>
            @endauth

            <button type="button" onclick="toggleMobileMenu(); openWizardModal();" class="w-full py-3.5 rounded-2xl bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold text-center text-sm glow-primary cursor-pointer transition-all active:scale-[0.98]">
                Enconcierta tu parche
            </button>
        </div>
    </div>
</header>