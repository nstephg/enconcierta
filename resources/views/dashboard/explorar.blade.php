@extends('layouts.dashboard')

@section('content')
<div class="max-w-[1120px] mx-auto w-full px-3 sm:px-5 py-4 sm:py-6 pb-20 md:pb-8" x-data="{ filtersOpen: false }">
    <!-- Barra superior de Búsqueda y Filtros Flotantes -->
    <div class="flex justify-end mb-5">
        <form action="{{ route('explorar') }}" method="GET" class="relative flex gap-2 w-full sm:w-auto">
            <div class="flex-1 sm:w-[300px] flex items-center gap-2 rounded-xl px-3.5 py-2.5 bg-[#0C0C13] border border-white/10">
                <span class="text-stage-400 flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input name="search" class="flex-1 bg-transparent text-stage-50 text-sm placeholder-stage-400 min-w-0 outline-none" placeholder="Buscar show…" value="{{ request('search') }}" />
                @if(request('search'))
                    <a href="{{ route('explorar') }}" class="text-stage-400 hover:text-stage-50">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </a>
                @endif
            </div>

            <button type="button" @click="filtersOpen = !filtersOpen" class="relative flex items-center gap-2 px-3.5 rounded-xl text-sm font-semibold transition-all"
                :class="filtersOpen || '{{ request('ciudad') || request('genero') }}' ? 'bg-[#7C5CFF]/20 border border-[#7C5CFF]/45 text-[#B8A7FF]' : 'bg-[#0C0C13] border border-white/10 text-[#C8C8D0]'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><circle cx="9" cy="6" r="2"/><line x1="4" y1="18" x2="20" y2="18"/><circle cx="15" cy="18" r="2"/></svg>
                <span class="hidden sm:inline">Filtros</span>
                @if(request('ciudad') || request('genero'))
                    <span class="w-5 h-5 rounded-full text-[10px] flex items-center justify-center text-white bg-[#7C5CFF]">!</span>
                @endif
            </button>

            <!-- Modal Flotante de Filtros -->
            <div x-show="filtersOpen" x-cloak @click.outside="filtersOpen = false" class="absolute z-30 right-0 top-[48px] w-[min(360px,calc(100vw-24px))] rounded-2xl p-5 bg-[#12121B] border border-white/10 shadow-[0_24px_70px_rgba(0,0,0,0.55)]">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-stage-50 text-sm font-bold">Ajusta tu radar</p>
                        <p class="text-stage-400 text-xs mt-1">Solo verás parches que coincidan.</p>
                    </div>
                    <a href="{{ route('explorar') }}" class="text-primary-400 text-xs font-semibold">Limpiar</a>
                </div>

                <p class="text-stage-400 text-[11px] font-bold uppercase tracking-wider mb-2">Ciudad</p>
                <div class="grid grid-cols-2 gap-2 mb-5">
                    @foreach(['Todas', 'Bogotá', 'Medellín', 'Cali'] as $c)
                        <button type="submit" name="ciudad" value="{{ $c == 'Todas' ? '' : $c }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-left transition-all {{ (request('ciudad') == $c || (!request('ciudad') && $c == 'Todas')) ? 'bg-primary-500/15 border border-primary-500/35 text-[#FF6070]' : 'bg-white/5 border border-white/5 text-[#C8C8D0]' }}">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>

                <p class="text-stage-400 text-[11px] font-bold uppercase tracking-wider mb-2">Sonido</p>
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach(['Todos', 'Urbano', 'Pop alternativo', 'Electro cumbia', 'Jazz fusión', 'Reguetón'] as $g)
                        <button type="submit" name="genero" value="{{ $g == 'Todos' ? '' : $g }}" class="px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ (request('genero') == $g || (!request('genero') && $g == 'Todos')) ? 'bg-[#7C5CFF]/15 border border-[#7C5CFF]/40 text-[#B8A7FF]' : 'bg-white/5 border border-white/5 text-[#C8C8D0]' }}">
                            {{ $g }}
                        </button>
                    @endforeach
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#7C5CFF]">
                    Ver shows
                </button>
            </div>
        </form>
    </div>

    <!-- Título de Sección -->
    <div class="flex items-end justify-between gap-4 mb-4">
        <div>
            <h2 class="text-stage-50 uppercase font-['Anton',sans-serif] text-[24px] tracking-[0.02em]">
                {{ request('search') || request('ciudad') || request('genero') ? 'Resultados' : 'Próximos conciertos' }}
            </h2>
            <p class="text-stage-400 text-xs mt-1">{{ $eventos->count() }} {{ Str::plural('show', $eventos->count()) }} disponibles</p>
        </div>
        <span class="hidden sm:flex items-center gap-2 text-stage-400 text-xs">
            <span class="w-2 h-2 rounded-full bg-[#2FE6D0]"></span>Actualizado ahora
        </span>
    </div>

    <!-- Grilla de Shows Reales -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($eventos as $evento)
            <article onclick="window.location.href='{{ route('explorar.show', $evento->id_evento) }}'" class="rounded-2xl overflow-hidden group cursor-pointer transition-all duration-300 hover:-translate-y-1 bg-[#0C0C13] border border-white/[0.07]">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ Str::startsWith($evento->imagen_portada, 'http') ? $evento->imagen_portada : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=600&h=400&fit=crop&auto=format') }}" alt="{{ $evento->artista }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 opacity-80" />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0C0C13] via-transparent to-transparent"></div>
                    
                    <!-- Badge de Género -->
                    <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white bg-[#7C5CFF]/85 backdrop-blur-sm">
                        {{ $evento->genero ?? 'Música' }}
                    </div>

                    <!-- Artista y Fecha Overlay -->
                    <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                        <h3 class="text-stage-50 text-3xl uppercase leading-none font-['Anton',sans-serif]">{{ $evento->artista }}</h3>
                        <span class="text-[#FFB020] text-[11px] font-bold">{{ \Carbon\Carbon::parse($evento->fecha)->translatedFormat('D d M') }}</span>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Ubicación Real -->
                    <div class="flex items-center gap-2 text-stage-400 text-xs mb-4">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $evento->lugar }} · {{ $evento->ciudad }}</span>
                    </div>

                    <!-- Asistentes y Conteo de Parches Reales -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex -space-x-2">
                                @forelse($evento->participantes->take(3) as $participante)
                                    <img src="{{ $participante->avatar ? asset('storage/' . $participante->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($participante->nombre) }}" 
                                         alt="{{ $participante->nombre }}" 
                                         class="w-8 h-8 rounded-full object-cover border-2 border-[#0C0C13]" />
                                @empty
                                    <div class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[10px] text-stage-400">
                                        🎵
                                    </div>
                                @endforelse
                            </div>
                            <span class="text-stage-400 text-[11px] ml-2">
                                {{ $evento->participantes->count() > 0 ? 'Comunidad activa' : 'Sin asistentes aún' }}
                            </span>
                        </div>
                        <span class="text-[#2FE6D0] text-xs font-semibold">
                            {{ $evento->parches_count }} {{ Str::plural('parche', $evento->parches_count) }} {{ Str::plural('abierto', $evento->parches_count) }}
                        </span>
                    </div>

                    <!-- Footer Dinámico -->
                    <div class="flex items-center justify-between pt-3 border-t border-white/[0.06]">
                        <span class="text-stage-400 text-xs">
                            {{ $evento->participantes->count() }} {{ Str::plural('melómano', $evento->participantes->count()) }} {{ Str::plural('va', $evento->participantes->count()) }}
                        </span>
                        <a href="{{ route('explorar.show', $evento->id_evento) }}" class="flex items-center gap-1.5 text-stage-50 text-xs font-bold group-hover:text-[#FF3D57] transition-colors">
                            Ver parches
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl py-16 text-center bg-[#0C0C13] border border-white/[0.07]">
                <p class="text-stage-50 font-semibold text-base">No encontramos parches con esos filtros</p>
                <p class="text-stage-400 text-sm mt-2">Prueba otra ciudad o un sonido diferente.</p>
                @if(request('search') || request('ciudad') || request('genero'))
                    <a href="{{ route('explorar') }}" class="inline-block mt-5 px-5 py-2.5 rounded-xl text-sm font-semibold text-primary-400 bg-primary-500/12 border border-primary-500/25">
                        Restablecer búsqueda
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</div>
@endsection