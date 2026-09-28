<div x-data="{ 
        openNotifs: false,
        items: @js($notificacionesSistema ?? []),
        get unreadCount() {
            return this.items.filter(i => !i.leido || i.leido == 0).length;
        },
        get allIds() {
            return this.items.map(i => i.id_notificacion);
        },
        selectedNotifs: [],
        async marcarLeidas() {
            this.items.forEach(i => i.leido = 1);
            try {
                await fetch('{{ route('notificaciones.marcarLeidas') }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                });
            } catch (e) {
                console.error(e);
            }
        },
        async eliminar(all = false, singleId = null) {
            let payload = { _token: '{{ csrf_token() }}' };
            if (all) {
                payload.all = true;
                this.items = [];
                this.selectedNotifs = [];
            } else if (singleId) {
                payload.ids = [singleId];
                this.items = this.items.filter(i => i.id_notificacion !== singleId);
                this.selectedNotifs = this.selectedNotifs.filter(id => id !== singleId);
            } else if (this.selectedNotifs.length > 0) {
                payload.ids = this.selectedNotifs;
                this.items = this.items.filter(i => !this.selectedNotifs.includes(i.id_notificacion));
                this.selectedNotifs = [];
            } else {
                return;
            }

            try {
                await fetch('{{ route('notificaciones.eliminar') }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                console.error(e);
            }
        },
        toggleSelectAll(e) {
            if (e.target.checked) {
                this.selectedNotifs = [...this.allIds];
            } else {
                this.selectedNotifs = [];
            }
        }
     }" 
     class="sticky top-0 z-40 w-full bg-[#0A0A0F]/85 backdrop-blur-md border-b border-white/5">

    <!-- HEADER DESKTOP -->
    <header class="hidden md:flex items-center justify-between px-6 h-[68px]">
        <h1 class="text-[#F5F5F7] font-semibold text-base">@yield('page-title', 'Feed')</h1>
        
        <div class="flex items-center gap-4">
            <!-- BOTÓN NOTIFICACIONES DESKTOP -->
            <button @click="openNotifs = !openNotifs" type="button" class="relative text-[#9A9AA5] hover:text-[#F5F5F7] p-2 rounded-xl transition-colors cursor-pointer focus:outline-none">
                <i class="fa-regular fa-bell text-lg"></i>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-1 right-1 text-white text-[9px] font-bold rounded-full bg-[#FF3D57] min-w-[16px] h-[16px] flex items-center justify-center px-1 shadow-[0_0_8px_#FF3D57]"
                          x-text="unreadCount > 99 ? '+99' : unreadCount">
                    </span>
                </template>
            </button>

            <a href="{{ route('profile.show') }}" class="flex items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" 
                     alt="{{ auth()->user()->nombre }}" 
                     class="w-8 h-8 rounded-full object-cover border-2 border-[#FF3D57]/40">
            </a>
        </div>
    </header>

    <!-- HEADER MOBILE -->
    <header class="flex md:hidden items-center justify-between px-4 h-[56px]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-[#FF3D57] flex items-center justify-center shadow-[0_0_10px_#FF3D57]">
                <i class="fa-solid fa-compact-disc text-white text-xs"></i>
            </div>
            <span class="font-headline text-base text-[#F5F5F7] tracking-wider">ENCONCIERTA</span>
        </a>

        <div class="flex items-center gap-3">
            <!-- BOTÓN NOTIFICACIONES MOBILE -->
            <button @click="openNotifs = !openNotifs" type="button" class="relative text-[#9A9AA5] hover:text-[#F5F5F7] p-1.5 focus:outline-none cursor-pointer">
                <i class="fa-regular fa-bell text-lg"></i>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-0.5 right-0.5 text-white text-[9px] font-bold rounded-full bg-[#FF3D57] min-w-[15px] h-[15px] flex items-center justify-center px-1 shadow-[0_0_8px_#FF3D57]"
                          x-text="unreadCount > 99 ? '+99' : unreadCount">
                    </span>
                </template>
            </button>

            <a href="{{ route('profile.show') }}">
                <img src="{{ auth()->user()->avatar_url }}" 
                     alt="{{ auth()->user()->nombre ?? 'Usuario' }}" 
                     class="w-7 h-7 rounded-full object-cover border-2 border-[#FF3D57]/40">
            </a>
        </div>
    </header>

    <!-- PANEL FLOTANTE DE NOTIFICACIONES -->
    @include('layouts.partials.notifications-panel')
</div>