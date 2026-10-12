@extends('layouts.dashboard')

@section('content')
<div class="max-w-[1120px] mx-auto w-full px-3 sm:px-5 py-4 sm:py-6 pb-20 md:pb-8" x-data="{ createModalOpen: false, filter: 'Todos' }">
    <!-- Botón Volver -->
    <a href="{{ route('explorar') }}" class="flex items-center gap-2 text-stage-400 hover:text-stage-50 text-sm font-medium mb-4 transition-colors">
        <span class="w-8 h-8 rounded-full flex items-center justify-center bg-white/[0.06] border border-white/[0.08]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
        Volver a la lista de conciertos
    </a>

    <!-- Banner Principal del Evento -->
    <div class="relative h-[160px] sm:h-[190px] rounded-2xl overflow-hidden mb-6 border border-white/[0.08]">
        <img src="{{ Str::startsWith($evento->imagen_portada, 'http') ? $evento->imagen_portada : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1200&h=400&fit=crop&auto=format') }}" alt="{{ $evento->artista }}" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#0A0A0F]/96 via-[#0A0A0F]/60 to-transparent"></div>
        <div class="absolute inset-0 p-5 sm:p-6 flex flex-col justify-end">
            <span class="text-[#2FE6D0] text-[10px] font-bold uppercase tracking-[0.18em] mb-1.5">{{ $evento->genero ?? 'FESTIVAL' }}</span>
            <h1 class="text-stage-50 uppercase leading-none text-3xl sm:text-5xl font-['Anton',sans-serif]">{{ $evento->artista }}</h1>
            <div class="flex flex-wrap items-center gap-x-3 mt-2 text-stage-200 text-xs">
                <span>{{ \Carbon\Carbon::parse($evento->fecha)->translatedFormat('l d \d\e F, Y') }}</span>
                <span>·</span>
                <span>{{ $evento->lugar }}</span>
                <span>·</span>
                <span>{{ $evento->ciudad }}</span>
            </div>
        </div>
    </div>

    <!-- Encabezado de Parches + Filtros -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-stage-50 uppercase text-2xl sm:text-3xl font-['Anton',sans-serif] tracking-wider">
                    PARCHES DISPONIBLES
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold text-[#0A0A0F] bg-[#2FE6D0]">
                    {{ $parches->count() }}
                </span>
            </div>
            <p class="text-stage-400 text-xs mt-1">Compara el plan antes de entrar.</p>
        </div>

        <!-- Filtros interactivos -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <template x-for="item in ['Todos', 'Con cupos', 'Previa', 'Mismo sector']" :key="item">
                <button @click="filter = item" 
                        class="px-3.5 py-2 rounded-xl text-[11px] font-semibold whitespace-nowrap transition-all cursor-pointer"
                        :class="filter === item 
                            ? 'bg-[#7C5CFF]/20 text-[#B8A7FF] border border-[#7C5CFF]/40' 
                            : 'bg-white/[0.035] text-[#9A9AA5] border border-white/[0.06] hover:text-white'"
                        x-text="item">
                </button>
            </template>
        </div>
    </div>

    <!-- Lista de Parches -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-start">
        @forelse($parches as $parche)
            @php
                $parcheId = $parche->id_parche ?? $parche->id_publicacion ?? $parche->id;
                $meta = is_string($parche->meta) ? json_decode($parche->meta, true) : ($parche->meta ?? []);
                $hora = $parche->hora_encuentro ?? $meta['hora_encuentro'] ?? '05:30 p. m.';
                $sector = $parche->sector ?? $meta['sector'] ?? ($parche->ubicacion ?? 'General');
                $cupos = $parche->cupos ?? $meta['cupos'] ?? 4;
                $plan = $parche->tipo_plan ?? $meta['tipo_plan'] ?? 'Parche';
                
                // Conteo preciso de miembros
                $miembrosActivos = $parche->miembros ? $parche->miembros->where('estado', 'activo')->count() : 1;
                $cuposDisponibles = max(0, $cupos - $miembrosActivos);

                $parcheAccents = ['#2FE6D0', '#FF3D57', '#9B80FF', '#FFB020'];
                $accent = $parcheAccents[$loop->index % 4];

                $eventoImg = Str::startsWith($evento->imagen_portada, 'http') 
                    ? $evento->imagen_portada 
                    : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=400&fit=crop&auto=format');
            @endphp

            <article onclick="window.location.href='{{ route('parche.show', $parcheId) }}'" 
                     x-show="filter === 'Todos' || 
                             (filter === 'Con cupos' && {{ $cuposDisponibles }} > 0) || 
                             (filter === 'Previa' && '{{ strtolower($plan) }}'.includes('previa')) || 
                             (filter === 'Mismo sector' && '{{ strtolower($sector) }}' !== 'general')"
                     class="group cursor-pointer rounded-2xl overflow-hidden transition-all duration-200 hover:border-white/20 bg-[#0C0C13] border border-white/[0.08] flex flex-col justify-between w-full max-w-sm mx-auto sm:max-w-none">
                
                <!-- Encabezado con imagen del show + Tipo de Plan + Título -->
                <div class="relative h-20 overflow-hidden">
                    <img src="{{ $eventoImg }}" alt="" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" style="object-position: {{ 45 + ($loop->index % 4) * 12 }}% {{ 45 + ($loop->index % 3) * 12 }}%;" />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0C0C13] via-[#0C0C13]/50 to-transparent"></div>
                    
                    <!-- Tag tipo plan con dot de color -->
                    <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-[#0A0A0F]/80 backdrop-blur-md px-2.5 py-1 rounded-full border border-white/10">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $accent }}; border-radius: 9999px;"></span>
                        <span class="text-white text-[9px] uppercase tracking-wider font-bold">{{ $plan }}</span>
                    </div>

                    <!-- Título del Parche -->
                    <h3 class="absolute bottom-2.5 left-3 right-3 text-stage-50 uppercase text-lg leading-tight truncate font-['Anton',sans-serif]">
                        {{ $parche->nombre ?? $parche->titulo }}
                    </h3>
                </div>

                <!-- Specs + Footer de Card -->
                <div class="p-3.5 flex flex-col justify-between flex-1">
                    <div class="grid grid-cols-3 gap-2 mb-3 bg-white/[0.02] p-2 rounded-xl border border-white/[0.04]">
                        <div>
                            <p class="text-stage-400 text-[8px] uppercase font-bold tracking-wider">Hora</p>
                            <p class="text-stage-200 text-[11px] font-semibold mt-0.5 truncate">{{ $hora }}</p>
                        </div>
                        <div>
                            <p class="text-stage-400 text-[8px] uppercase font-bold tracking-wider">Sector</p>
                            <p class="text-stage-200 text-[11px] font-semibold mt-0.5 truncate">{{ $sector }}</p>
                        </div>
                        <div>
                            <p class="text-stage-400 text-[8px] uppercase font-bold tracking-wider">Cupos</p>
                            <p class="text-stage-200 text-[11px] font-semibold mt-0.5 truncate">{{ $cuposDisponibles }} libres</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 border-t border-white/[0.06]">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                @php
                                    $creador = $parche->creador ?? $parche->user;
                                    $avatar = $creador && $creador->avatar ? asset('storage/' . $creador->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($creador->nombre ?? 'U');
                                @endphp
                                <img src="{{ $avatar }}" alt="{{ $creador->nombre ?? 'Usuario' }}" class="w-6 h-6 rounded-full object-cover border-2 border-[#0C0C13]" />
                            </div>
                            <span class="text-stage-400 text-[10px] font-medium">
                                {{ $miembrosActivos }} {{ $miembrosActivos === 1 ? 'miembro' : 'miembros' }}
                            </span>
                        </div>
                        <button class="w-7 h-7 rounded-full flex items-center justify-center text-[#0A0A0F] group-hover:scale-105 transition-transform" style="background-color: {{ $accent }};">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl py-12 px-4 text-center bg-[#0C0C13] border border-white/[0.07]">
                <h3 class="text-[#F2F2F7] font-bold text-base">Aún no hay parches creados para este show</h3>
                <p class="text-stage-400 text-xs mt-1 max-w-md mx-auto">Sé la primera persona en armar el grupo e invitar a otros fans.</p>
            </div>
        @endforelse
    </div>

    <!-- Botón Crear Mi Parche (Estilo plano, sin brillo/glow) -->
    <div class="mt-8 flex justify-center">
        <button @click="createModalOpen = true" class="px-5 py-2.5 rounded-xl text-xs font-bold text-[#B8A7FF] bg-[#7C5CFF]/15 border border-[#7C5CFF]/30 hover:bg-[#7C5CFF]/25 transition-all flex items-center gap-2 cursor-pointer">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Crear mi parche
        </button>
    </div>

    <!-- Modal Crear Parche -->
    <div x-show="createModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div @click.outside="createModalOpen = false" class="w-full max-w-[500px] rounded-3xl overflow-hidden bg-[#101018] border border-white/10 shadow-2xl">
            @php
                $modalEventoImg = Str::startsWith($evento->imagen_portada ?? '', 'http') 
                    ? $evento->imagen_portada 
                    : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : ($evento->imagen_url ?? $evento->imagen ?? 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=400&fit=crop&auto=format'));
            @endphp

            <!-- Banner Superior con Imagen del Evento -->
            <div class="relative h-28 overflow-hidden">
                <img src="{{ $modalEventoImg }}" 
                     alt="{{ $evento->artista ?? $evento->nombre ?? 'Evento' }}" 
                     class="absolute inset-0 w-full h-full object-cover opacity-50" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#101018] via-[#101018]/60 to-transparent"></div>
                
                <div class="absolute inset-x-0 bottom-0 p-5 flex flex-col justify-end">
                    <span class="text-[#2FE6D0] text-[10px] font-bold uppercase tracking-[0.18em] mb-0.5">{{ $evento->artista ?? $evento->nombre ?? 'Evento' }}</span>
                    <h2 class="text-white text-2xl font-bold font-['Anton',sans-serif] uppercase tracking-wide">Crea tu parche</h2>
                </div>

                <!-- Botón Cerrar -->
                <button @click="createModalOpen = false" type="button" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/60 hover:bg-black/80 border border-white/10 flex items-center justify-center text-stage-300 hover:text-white transition-all cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Formulario de Creación -->
            <form action="{{ route('parches.store') }}" method="POST" class="p-6 flex flex-col gap-4 text-left">
                @csrf
                <input type="hidden" name="id_evento" value="{{ $evento->id_evento ?? $evento->id }}" />

                <!-- Nombre del parche -->
                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Nombre del parche</label>
                    <input type="text" 
                           name="titulo" 
                           required 
                           placeholder="Ej. {{ $evento->artista ?? 'Concierto' }} desde el norte" 
                           class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                </div>

                <!-- Tipo de plan -->
                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Tipo de plan</label>
                    <input type="text" 
                           name="tipo_plan" 
                           required 
                           placeholder="Ej. Previa, Ir en grupo, etc." 
                           class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                </div>

                <!-- Grid: Sector y Cupos -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Sector -->
                    <div>
                        <label class="text-stage-300 text-xs font-semibold block mb-1.5">Sector</label>
                        <input type="text" 
                               name="sector" 
                               required 
                               placeholder="Ej. Platea 102" 
                               class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                    </div>

                    <!-- Cupos -->
                    <div>
                        <label class="text-stage-300 text-xs font-semibold block mb-1.5">Cupos totales</label>
                        <input type="number" 
                               name="cupos" 
                               value="4" 
                               min="1" 
                               max="100" 
                               required 
                               class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                    </div>
                </div>

                <!-- Hora de encuentro -->
                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Hora de encuentro</label>
                    <div class="relative">
                        <input type="text" 
                               name="hora_encuentro" 
                               required 
                               value="05:30 p. m."
                               placeholder="05:30 p. m." 
                               class="w-full rounded-xl pl-4 pr-10 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stage-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>

                <!-- Caja Informativa Verde -->
                <div class="rounded-2xl p-4 bg-[#2FE6D0]/[0.06] border border-[#2FE6D0]/20 flex items-start gap-3 my-1">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-[#2FE6D0] mt-0.5">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <p class="text-stage-300 text-xs leading-relaxed">Tu parche aparecerá como público. Podrás aprobar integrantes y editar el punto de encuentro después.</p>
                </div>

                <!-- Botón Publicar Parche -->
                <button type="submit" class="w-full py-3.5 rounded-xl text-sm font-semibold text-white bg-[#FF3D57] hover:brightness-110 border border-white/10 transition-all cursor-pointer mt-1 shadow-lg shadow-[#FF3D57]/25">
                    Publicar parche
                </button>
            </form>
        </div>
    </div>
</div>
@endsection