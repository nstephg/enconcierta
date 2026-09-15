@extends('layouts.app')

@section('title')
    ENCONCIERTA — Sincroniza tu parche, vive el show
@endsection

@section('content')
<!-- HERO SECTION WITH BACKGROUND IMAGE -->
<section class="relative pt-28 pb-20 md:pt-36 md:pb-32 overflow-hidden min-h-[88vh] flex items-center justify-center">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1800&h=1200&fit=crop&auto=format" alt="Multitud en concierto" class="w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-linear-to-b from-[#0A0A0F]/80 via-[#0A0A0F]/65 to-[#0A0A0F]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_40%_50%_at_20%_60%,rgba(255,61,87,0.22)_0%,transparent_70%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_40%_50%_at_80%_40%,rgba(124,92,255,0.18)_0%,transparent_70%)]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-4xl mx-auto text-center flex flex-col items-center">
            <h1 class="font-headline text-4xl sm:text-6xl md:text-7xl lg:text-8xl uppercase tracking-tight text-white leading-[0.95] mb-6">
                NO DEJES QUE TU BANDA FAVORITA <br class="hidden sm:inline"/>
                <span class="text-transparent bg-clip-text bg-linear-to-r from-[#FF3D57] via-[#FFB020] to-[#7C5CFF]">
                    TOQUE SIN TI.
                </span>
            </h1>

            <p class="text-lg sm:text-xl md:text-2xl text-[#9A9AA5] max-w-2xl font-light mb-10 leading-relaxed">
                Encuentra tu parche y vive el show desde la previa.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto mb-12">
                <button onclick="openWizardModal()" class="w-full sm:w-auto bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold text-base px-8 py-4 rounded-full transition-all duration-300 glow-primary-lg hover:scale-105 active:scale-95 flex items-center justify-center gap-3 cursor-pointer group">
                    <span>Enconcierta tu parche</span>
                    <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>

                <a href="#shows-section" class="w-full sm:w-auto bg-[#17171F]/80 hover:bg-[#232330] text-[#F5F5F7] border border-white/15 font-semibold text-base px-8 py-4 rounded-full transition-all duration-300 hover:border-white/30 backdrop-blur-md flex items-center justify-center gap-2 cursor-pointer">
                    <span>Explorar shows</span>
                    <i class="fa-solid fa-ticket text-xs text-[#FF3D57]"></i>
                </a>
            </div>

            <div class="flex items-center gap-4 bg-[#17171F]/80 backdrop-blur-md px-6 py-3 rounded-full border border-white/10">
                <div class="flex -space-x-2">
                    <img class="w-8 h-8 rounded-full border-2 border-[#0A0A0F] object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Melómana Valentina">
                    <img class="w-8 h-8 rounded-full border-2 border-[#0A0A0F] object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" alt="Melómano Mateo">
                    <img class="w-8 h-8 rounded-full border-2 border-[#0A0A0F] object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80" alt="Melómana Camila">
                    <div class="w-8 h-8 rounded-full border-2 border-[#0A0A0F] bg-[#7C5CFF] text-white text-[10px] font-bold flex items-center justify-center">
                        +2.4k
                    </div>
                </div>
                <span class="text-xs sm:text-sm text-[#9A9AA5] font-medium">
                    <strong class="text-white">+2.400 melómanos</strong> ya sincronizados en Bogotá, Medellín y Cali
                </span>
            </div>
        </div>
    </div>
</section>

<!-- INFINITE MARQUEE TICKER STRIP -->
<section class="overflow-hidden py-0 relative z-20 border-y border-[#FF3D57]/20 bg-[#0C0C13]">
    <div class="flex marquee-track w-max">
        @for ($i = 0; $i < 4; $i++)
            <div class="flex items-center gap-0 shrink-0">
                <div class="flex items-baseline gap-3 px-10 py-5">
                    <span class="font-headline text-3xl sm:text-4xl text-[#FF3D57] tracking-wider leading-none">120+</span>
                    <span class="text-[#9A9AA5] text-xs font-semibold tracking-widest uppercase">SHOWS ACTIVOS</span>
                </div>
                <span class="text-[#FF3D57]/35 text-[8px] shrink-0">◆</span>
            </div>
            <div class="flex items-center gap-0 shrink-0">
                <div class="flex items-baseline gap-3 px-10 py-5">
                    <span class="font-headline text-3xl sm:text-4xl text-[#FF3D57] tracking-wider leading-none">2.400+</span>
                    <span class="text-[#9A9AA5] text-xs font-semibold tracking-widest uppercase">MELÓMANOS</span>
                </div>
                <span class="text-[#FF3D57]/35 text-[8px] shrink-0">◆</span>
            </div>
            <div class="flex items-center gap-0 shrink-0">
                <div class="flex items-baseline gap-3 px-10 py-5">
                    <span class="font-headline text-3xl sm:text-4xl text-[#FF3D57] tracking-wider leading-none">35</span>
                    <span class="text-[#9A9AA5] text-xs font-semibold tracking-widest uppercase">CIUDADES</span>
                </div>
                <span class="text-[#FF3D57]/35 text-[8px] shrink-0">◆</span>
            </div>
            <div class="flex items-center gap-0 shrink-0">
                <div class="flex items-baseline gap-3 px-10 py-5">
                    <span class="font-headline text-3xl sm:text-4xl text-[#FF3D57] tracking-wider leading-none">4.8★</span>
                    <span class="text-[#9A9AA5] text-xs font-semibold tracking-widest uppercase">CONFIANZA</span>
                </div>
                <span class="text-[#FF3D57]/35 text-[8px] shrink-0">◆</span>
            </div>
        @endfor
    </div>
</section>

<!-- CÓMO FUNCIONA -->
<section id="como-funciona" class="py-16 md:py-20 bg-[#0A0A0F] relative z-10">
    <div class="max-w-300 mx-auto px-5 lg:px-8">
        <div class="flex items-center gap-4 mb-12">
            <div class="h-px flex-1 bg-white/10"></div>
            <span class="text-[#9A9AA5] text-xs font-semibold tracking-[0.2em] uppercase font-mono-code">Cómo funciona</span>
            <div class="h-px flex-1 bg-white/10"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative p-6 rounded-2xl bg-[#17171F]/40 border border-white/10 overflow-hidden group hover:border-[#FF3D57]/40 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-headline text-4xl text-[#FF3D57] tracking-wide">01</span>
                    <span class="text-[10px] font-mono-code font-bold uppercase tracking-widest px-2.5 py-1 rounded-full text-[#FF3D57] bg-[#FF3D57]/10 border border-[#FF3D57]/30">BUSCA</span>
                </div>
                <h3 class="font-headline text-2xl text-white uppercase mb-2">Tu show, tu artista.</h3>
                <p class="text-sm text-[#9A9AA5] leading-relaxed">
                    Encuentra el concierto en el calendario y comprueba qué parches ya están armando la previa para esa fecha.
                </p>
            </div>

            <div class="relative p-6 rounded-2xl bg-[#17171F]/40 border border-white/10 overflow-hidden group hover:border-[#7C5CFF]/40 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-headline text-4xl text-[#7C5CFF] tracking-wide">02</span>
                    <span class="text-[10px] font-mono-code font-bold uppercase tracking-widest px-2.5 py-1 rounded-full text-[#9B80FF] bg-[#7C5CFF]/10 border border-[#7C5CFF]/30">CONECTA</span>
                </div>
                <h3 class="font-headline text-2xl text-white uppercase mb-2">Con tu parche.</h3>
                <p class="text-sm text-[#9A9AA5] leading-relaxed">
                    Melómanos verificados que comparten tu devoción. Sin algoritmos raros: solo gente que canta tus mismas canciones.
                </p>
            </div>

            <div class="relative p-6 rounded-2xl bg-[#17171F]/40 border border-white/10 overflow-hidden group hover:border-[#2FE6D0]/40 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-headline text-4xl text-[#2FE6D0] tracking-wide">03</span>
                    <span class="text-[10px] font-mono-code font-bold uppercase tracking-widest px-2.5 py-1 rounded-full text-[#2FE6D0] bg-[#2FE6D0]/10 border border-[#2FE6D0]/30">VIVE</span>
                </div>
                <h3 class="font-headline text-2xl text-white uppercase mb-2">El show completo.</h3>
                <p class="text-sm text-[#9A9AA5] leading-relaxed">
                    Previa, fila o primera fila — lo que importa es la energía compartida. El recuerdo queda guardado para siempre.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN SHOWS -->
<section id="shows-section" class="py-16 md:py-20 bg-[#0A0A0F] relative z-10 border-t border-white/5">
    <div class="max-w-300 mx-auto px-5 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="font-headline text-3xl sm:text-4xl lg:text-5xl uppercase text-white tracking-wide mb-2">
                    ESTOS PARCHES YA ESTÁN <span class="text-[#2FE6D0]">ARMANDO LA PREVIA</span>
                </h2>
                <p class="text-[#9A9AA5] text-sm">Shows con asistentes sincronizados ahora mismo</p>
            </div>

            <a onclick="openAuthModal(); toggleMobileMenu();" class="hidden md:flex items-center gap-1.5 text-[#2FE6D0] text-sm font-medium hover:text-[#5FEFDE] transition-colors cursor-pointer">
                <span>Ver todos los shows</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($eventoDestacado)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- SHOW DESTACADO (DINÁMICO DESDE ADMINER) -->
                <div class="relative rounded-2xl overflow-hidden cursor-pointer h-full min-h-120 flex flex-col justify-end group border border-white/10 hover:border-[#7C5CFF]/50 transition-all duration-300 bg-[#0C0C13]">
                    <img src="{{ $eventoDestacado->imagen_portada ?? 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=600&fit=crop&auto=format' }}" alt="{{ $eventoDestacado->artista }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.04]">
                    <div class="absolute inset-0 bg-linear-to-t from-[#0A0A0F] via-[#0A0A0F]/50 to-transparent"></div>

                    <div class="absolute top-5 left-5 z-10">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold text-white bg-[#7C5CFF]/90 backdrop-blur-md">
                            {{ $eventoDestacado->nombre }}
                        </span>
                    </div>

                    <div class="relative z-10 p-6 flex flex-col gap-4">
                        <div>
                            <h3 class="font-headline text-4xl text-[#F5F5F7] tracking-wide uppercase leading-none">
                                {{ $eventoDestacado->artista }}
                            </h3>
                            <p class="text-[#9A9AA5] text-sm mt-1">{{ $eventoDestacado->lugar }} · {{ $eventoDestacado->ciudad }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#FFB020] inline-block shadow-[0_0_6px_#FFB020]"></span>
                                <span class="text-[#FFB020] text-sm font-semibold">
                                    {{ \Carbon\Carbon::parse($eventoDestacado->fecha)->locale('es')->isoFormat('ddd D MMM') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[#9A9AA5] text-xs">{{ $eventoDestacado->participantes_count }} melómanos en este parche</span>
                            </div>
                        </div>

                        <button onclick="openSyncModal('{{ $eventoDestacado->artista }} — {{ $eventoDestacado->lugar }}')" class="w-full py-3 rounded-xl text-[#FF3D57] hover:text-white bg-[#FF3D57]/15 hover:bg-[#FF3D57] border border-[#FF3D57]/45 font-semibold text-sm transition-all duration-200 cursor-pointer shadow-lg hover:shadow-[#FF3D57]/35">
                            Sincronizarme al parche
                        </button>
                    </div>
                </div>

                <!-- LISTADO SECUNDARIO DE SHOWS (DINÁMICO DESDE ADMINER) -->
                <div class="flex flex-col gap-3">
                    @forelse($eventosSecundarios as $evento)
                        <x-show-row 
                            :title="$evento->artista"
                            :venue="$evento->lugar . ' · ' . $evento->ciudad"
                            :genre="$evento->nombre"
                            :date="\Carbon\Carbon::parse($evento->fecha)->locale('es')->isoFormat('ddd D MMM')"
                            :attendees="$evento->participantes_count"
                            :image="$evento->imagen_portada ?? 'https://images.unsplash.com/photo-1635199648587-5e6aa5b76cf5?w=600&h=400&fit=crop&auto=format'"
                            :onClick="'openSyncModal(\'' . $evento->artista . ' — ' . $evento->ciudad . '\')'"
                        />
                    @empty
                        <p class="text-xs text-[#9A9AA5] italic py-4">No hay más eventos programados por el momento.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="text-center py-12 bg-[#17171F]/40 rounded-2xl border border-white/10">
                <p class="text-[#9A9AA5]">No hay eventos activos registrados en la base de datos.</p>
            </div>
        @endif
    </div>
</section>

<!-- SECCIÓN COMUNIDAD -->
<section id="comunidad" class="pt-20 pb-12 bg-[#0A0A0F] border-t border-white/5 relative z-10">
    <div class="max-w-300 mx-auto px-5 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-headline text-3xl sm:text-4xl lg:text-5xl uppercase text-white tracking-wide mb-3">
                EL SHOW NO TERMINA CUANDO SE APAGAN <span class="text-[#FFB020]">LAS LUCES</span>
            </h2>
            <p class="text-[#9A9AA5] text-sm sm:text-base max-w-md mx-auto font-light">
                Comparte tus fotos, historias y vivencias de la fecha.
            </p>
        </div>

        <div id="memoryGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-memory-card 
                username="@valeria_m"
                details="Morat · Bogotá, Jul 2025"
                text="Nunca pensé que iría sola a un concierto de Morat, pero encontré mi parche en ENCONCIERTA la noche antes. Cantamos No ..."
                likes="214"
                image="https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=600&h=360&fit=crop&auto=format"
                avatar="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&auto=format"
                borderColor="border-[#FF3D57]/40"
            />

            <x-memory-card 
                username="@sebas_rock"
                details="Feid · Medellín, Ago 2025"
                text="La previa fue épica: conocimos gente de tres ciudades distintas y terminamos todos en primera fila. El show duró 3 horas. La amistad,..."
                likes="387"
                image="https://images.unsplash.com/photo-1559228461-4fa1e7eb677c?w=600&h=360&fit=crop&auto=format"
                avatar="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&auto=format"
                borderColor="border-[#7C5CFF]/40"
            />

            <x-memory-card 
                username="@camila_beats"
                details="Bomba Estéreo · Cali, Ago 2025"
                text="Me uní al parche solo porque compartíamos Spotify Wrapped. Terminé bailando en el escenario con gente que ahora llamo amigos."
                likes="162"
                image="https://images.unsplash.com/photo-1681150086113-fb84c74a43f2?w=600&h=360&fit=crop&auto=format"
                avatar="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&auto=format"
                borderColor="border-[#2FE6D0]/40"
            />
        </div>

        <div class="mt-8 text-center">
            <button onclick="openAuthModal(); toggleMobileMenu();" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-white text-sm font-semibold border border-white/20 hover:bg-white/10 transition-all duration-300 cursor-pointer">
                <span>Ver la comunidad</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>
</section>

<!-- CTA FINAL -->
<section class="py-12 sm:py-16 relative overflow-hidden bg-[#0A0A0F] z-10">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-150 h-87.5 bg-linear-to-r from-[#FF3D57]/15 via-[#7C5CFF]/20 to-[#2FE6D0]/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h2 class="font-headline text-4xl sm:text-6xl lg:text-7xl uppercase text-white leading-tight mb-6">
            TU BOLETA VALE EL DOBLE <br/>
            <span class="text-transparent bg-clip-text bg-linear-to-r from-[#FF3D57] via-[#FF6070] to-[#7C5CFF]">
                CUANDO LA COMPARTES.
            </span>
        </h2>

        <p class="text-lg sm:text-2xl text-[#9A9AA5] max-w-2xl mx-auto font-light mb-10">
            Sincronízate con la gente que canta las mismas canciones que tú.
        </p>

        <button onclick="openWizardModal()" class="bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold text-lg px-10 py-5 rounded-full transition-all duration-300 glow-primary-lg hover:scale-105 active:scale-95 inline-flex items-center gap-3 cursor-pointer">
            <span>Enconcierta tu parche ahora</span>
        </button>
    </div>
</section>
@endsection