@extends('layouts.dashboard')

@section('content')
@php
    $activosCount = $parche->miembros->where('estado', 'activo')->count();
    $expulsadosCount = $parche->miembros->where('estado', 'expulsado')->count();
    $cuposLibres = max(0, $parche->cupos - $activosCount);
    $isOwner = Auth::id() === $parche->id_usuario_creador || Auth::user()->id_rol === 2;
    $userMiembro = $parche->miembros->where('id_usuario', Auth::id())->first();
    $isJoined = $userMiembro && $userMiembro->estado === 'activo';
    $isKicked = $userMiembro && $userMiembro->estado === 'expulsado';
@endphp

<div class="max-w-[1120px] mx-auto w-full px-3 sm:px-5 py-4 sm:py-6 pb-24 md:pb-8" 
     x-data="{ tab: '{{ request('tab', 'parche') }}', editOpen: false, editingPlan: false, showExpulsados: false }">
    <!-- Botón Volver -->
    <a href="{{ route('explorar.show', $evento->id_evento) }}" class="flex items-center gap-2 text-stage-400 hover:text-stage-50 text-sm font-medium mb-4 transition-colors">
        <span class="w-8 h-8 rounded-full flex items-center justify-center bg-white/[0.06] border border-white/[0.08]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
        Volver a los parches
    </a>

    <!-- Banner Principal -->
    <div class="relative h-[170px] sm:h-[200px] rounded-2xl overflow-hidden mb-5 border border-white/[0.08]">
        <img src="{{ Str::startsWith($evento->imagen_portada, 'http') ? $evento->imagen_portada : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1200&h=400&fit=crop&auto=format') }}" alt="{{ $evento->artista }}" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#0A0A0F]/96 via-[#0A0A0F]/70 to-transparent"></div>
        
        <div class="absolute inset-0 p-5 sm:p-6 flex flex-col justify-end">
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#2FE6D0] text-[#0A0A0F]">Parche {{ $parche->tipo_plan }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold text-stage-200 bg-black/50 border border-white/10">{{ $cuposLibres }} cupos libres</span>
            </div>
            <h1 class="text-stage-50 uppercase leading-none text-3xl sm:text-4xl font-['Anton',sans-serif]">{{ $parche->nombre }}</h1>
            <div class="flex flex-wrap items-center gap-x-3 mt-2 text-stage-300 text-xs">
                <span>{{ $evento->artista }}</span>
                <span>·</span>
                <span>{{ \Carbon\Carbon::parse($evento->fecha)->format('D d M') }}</span>
                <span>·</span>
                <span>{{ $evento->lugar }}</span>
            </div>
        </div>
    </div>

    @if($isKicked)
        <div class="mb-5 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-semibold flex items-center gap-3">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Has sido expulsado de este parche por el anfitrión y no tienes acceso al chat ni reingreso.
        </div>
    @endif

    <!-- Layout Grid (Columna Principal + Sidebar a la Derecha) -->
    <div class="grid lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
        <!-- Columna Principal -->
        <div class="min-w-0">
            <!-- Pestañas -->
            <div class="flex items-center gap-1 p-1 rounded-xl mb-5 w-full sm:w-fit bg-[#0C0C13] border border-white/[0.07]">
                <button @click="tab = 'parche'" class="flex-1 sm:flex-none px-5 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                    :class="tab === 'parche' ? 'bg-[#7C5CFF]/20 text-[#B8A7FF] border border-[#7C5CFF]/30' : 'text-[#9A9AA5] hover:text-white'">
                    El parche
                </button>
                <button @click="tab = 'chat'; $nextTick(() => $dispatch('scroll-chat'))" class="flex-1 sm:flex-none px-5 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                    :class="tab === 'chat' ? 'bg-[#7C5CFF]/20 text-[#B8A7FF] border border-[#7C5CFF]/30' : 'text-[#9A9AA5] hover:text-white'">
                    Chat del parche ({{ $parche->mensajes->count() }})
                </button>
            </div>

            <!-- Tab: El Parche -->
            <div x-show="tab === 'parche'" class="flex flex-col gap-4">
                <div class="grid sm:grid-cols-3 gap-3">
                    <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/[0.07]">
                        <p class="text-[#9A9AA5] text-[10px] uppercase tracking-wider font-semibold">Punto de encuentro</p>
                        <p class="text-stage-50 text-sm font-semibold mt-1">{{ $evento->lugar }} · {{ $parche->hora_encuentro }}</p>
                    </div>
                    <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/[0.07]">
                        <p class="text-[#9A9AA5] text-[10px] uppercase tracking-wider font-semibold">Sector del parche</p>
                        <p class="text-stage-50 text-sm font-semibold mt-1">{{ $parche->sector }}</p>
                    </div>
                    <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/[0.07]">
                        <p class="text-[#9A9AA5] text-[10px] uppercase tracking-wider font-semibold">Cupos disponibles</p>
                        <p class="text-stage-50 text-sm font-semibold mt-1">{{ $cuposLibres }} lugares libres</p>
                    </div>
                </div>

                @php
                    $lines = array_filter(array_map('trim', explode("\n", $parche->plan_noche ?? '')));
                    $parsedItems = [];
                    if (count($lines) > 0) {
                        foreach ($lines as $l) {
                            $parts = explode('|', $l, 2);
                            $parsedItems[] = [
                                'title' => trim($parts[0]),
                                'detail' => isset($parts[1]) ? trim($parts[1]) : ''
                            ];
                        }
                    } else {
                        $parsedItems = [
                            [
                                'title' => ($parche->hora_encuentro ?? '05:30 p. m.') . ' — Nos conocemos para ' . ($parche->tipo_plan ?? 'previa'),
                                'detail' => 'Punto de encuentro: ' . ($parche->sector ?? 'General')
                            ],
                            [
                                'title' => 'Ingresamos juntos al show',
                                'detail' => 'Ubicación grupal en ' . ($parche->sector ?? 'General')
                            ]
                        ];
                    }
                @endphp

                <!-- El Plan de la Noche -->
                <div class="rounded-2xl p-5 sm:p-6 bg-[#0C0C13] border border-white/[0.07]" 
                    x-data="{
                        editingPlan: false,
                        items: {{ json_encode($parsedItems) }},
                        addItem(index) {
                            this.items.splice(index + 1, 0, { title: '', detail: '' });
                            this.$nextTick(() => {
                                const inputs = $refs.planContainer.querySelectorAll('.plan-title-input');
                                if (inputs[index + 1]) inputs[index + 1].focus();
                            });
                        },
                        removeItem(index) {
                            if (this.items.length > 1) {
                                this.items.splice(index, 1);
                            }
                        },
                        get serializedPlan() {
                            return this.items
                                .map(i => (i.detail && i.detail.trim() !== '') ? `${i.title} | ${i.detail}` : i.title)
                                .filter(t => t.trim() !== '')
                                .join('\n');
                        }
                    }">
                    
                    <form action="{{ route('parche.update', $parche->id_parche) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="show_nombre" value="{{ $parche->nombre }}" />
                        <input type="hidden" name="sector" value="{{ $parche->sector }}" />
                        <input type="hidden" name="hora_encuentro" value="{{ $parche->hora_encuentro }}" />
                        <input type="hidden" name="cupos" value="{{ $parche->cupos }}" />
                        <input type="hidden" name="plan_noche" :value="serializedPlan" />

                        <!-- Header del Plan -->
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div>
                                <h2 class="text-stage-50 font-bold text-base">El plan</h2>
                                <p class="text-[#9A9AA5] text-xs mt-0.5">Definido por el anfitrión para coordinar el evento</p>
                            </div>
                            @if($isOwner)
                                <div>
                                    <template x-if="editingPlan">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="editingPlan = false" class="text-stage-400 hover:text-white text-xs font-medium transition-colors cursor-pointer">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="px-3 py-1 rounded-lg text-xs font-bold text-[#0A0A0F] bg-[#2FE6D0] hover:bg-[#25c4b1] transition-all cursor-pointer">
                                                Guardar
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!editingPlan">
                                        <button type="button" @click="editingPlan = true" class="text-[#2FE6D0] text-xs font-semibold hover:underline flex items-center gap-1 cursor-pointer">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar plan
                                        </button>
                                    </template>
                                </div>
                            @endif
                        </div>

                        <!-- Línea de Tiempo -->
                        <div class="flex flex-col gap-3" x-ref="planContainer">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="grid grid-cols-[16px_1fr] gap-3 items-start relative group">
                                    <div class="flex flex-col items-center h-full">
                                        <span class="w-2.5 h-2.5 rounded-full mt-1 shrink-0"
                                            :style="'background-color: ' + ['#FF3D57', '#7C5CFF', '#2FE6D0', '#FFB020'][index % 4] + '; box-shadow: 0 0 10px ' + ['#FF3D57', '#7C5CFF', '#2FE6D0', '#FFB020'][index % 4] + '80;'"></span>
                                        <span x-show="index < items.length - 1" class="w-px flex-1 min-h-[20px] bg-white/10 my-1"></span>
                                    </div>

                                    <div class="pb-1 w-full flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <template x-if="!editingPlan">
                                                <div>
                                                    <p class="text-stage-50 text-xs font-semibold leading-snug" x-text="item.title"></p>
                                                    <p x-show="item.detail" class="text-[#9A9AA5] text-[11px] mt-0.5" x-text="item.detail"></p>
                                                </div>
                                            </template>

                                            <template x-if="editingPlan">
                                                <div class="flex flex-col gap-0.5">
                                                    <input type="text"
                                                        x-model="item.title"
                                                        @keydown.enter.prevent="addItem(index)"
                                                        @keydown.backspace="if(!item.title && !item.detail) removeItem(index)"
                                                        placeholder="Escribe el título o la hora..."
                                                        class="plan-title-input w-full bg-transparent border-0 p-0 text-stage-50 text-xs font-semibold leading-snug focus:ring-0 focus:outline-none placeholder-stage-400/40" />
                                                    
                                                    <input type="text"
                                                        x-model="item.detail"
                                                        @keydown.enter.prevent="addItem(index)"
                                                        placeholder="Lugar o detalle (opcional)..."
                                                        class="w-full bg-transparent border-0 p-0 text-[#9A9AA5] text-[11px] focus:ring-0 focus:outline-none placeholder-stage-400/30" />
                                                </div>
                                            </template>
                                        </div>

                                        <button type="button" 
                                                x-show="editingPlan && items.length > 1" 
                                                @click="removeItem(index)" 
                                                class="text-stage-400 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity p-0.5"
                                                title="Eliminar este punto">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </form>
                </div>

                <!-- Integrantes del Parche -->
                <div class="rounded-2xl p-5 sm:p-6 bg-[#0C0C13] border border-white/[0.07]">
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-stage-50 font-bold text-base">Integrantes del parche</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-[#0A0A0F] bg-[#2FE6D0]">
                                {{ $activosCount }}/{{ $parche->cupos }}
                            </span>
                        </div>
                        
                        @if($isOwner && $expulsadosCount > 0)
                            <button @click="showExpulsados = !showExpulsados" class="text-[#9A9AA5] hover:text-white text-xs font-medium transition-colors cursor-pointer">
                                <span x-text="showExpulsados ? 'Ocultar vetados' : 'Ver vetados ({{ $expulsadosCount }})'"></span>
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($parche->miembros->where('estado', 'activo') as $m)
                            @php $userId = $m->user->id_usuario ?? $m->user->id; @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.03] border border-white/5 hover:border-white/10 transition-all">
                                <a href="{{ route('profile.show', $userId) }}" class="flex items-center gap-3 min-w-0 group/user">
                                    <img src="{{ $m->user->avatar ? asset('storage/' . $m->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($m->user->nombre ?? 'U') }}" class="w-9 h-9 rounded-full object-cover border border-white/10 shrink-0 group-hover/user:border-[#2FE6D0] transition-colors" />
                                    <div class="min-w-0">
                                        <p class="text-stage-50 text-xs font-semibold truncate group-hover/user:text-[#2FE6D0] transition-colors">{{ $m->user->nombre }}</p>
                                        @if($m->id_usuario === $parche->id_usuario_creador)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#2FE6D0] mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#2FE6D0]"></span> Anfitrión
                                            </span>
                                        @else
                                            <span class="text-[10px] text-stage-400">Integrante</span>
                                        @endif
                                    </div>
                                </a>

                                @if($isOwner && $m->id_usuario !== $parche->id_usuario_creador)
                                    <form action="{{ route('parche.kick', [$parche->id_parche, $m->id_usuario]) }}" method="POST" onsubmit="return confirm('¿Expulsar a {{ $m->user->nombre }} de este parche?');" class="shrink-0">
                                        @csrf
                                        <button type="submit" title="Expulsar integrante" class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/25 transition-all flex items-center gap-1 cursor-pointer">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>
                                            Expulsar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($isOwner && $expulsadosCount > 0)
                        <div x-show="showExpulsados" x-cloak class="mt-4 pt-3 border-t border-white/5">
                            <p class="text-[#9A9AA5] text-xs font-semibold mb-2">Integrantes expulsados / vetados</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($parche->miembros->where('estado', 'expulsado') as $m)
                                    @php $vetadoId = $m->user->id_usuario ?? $m->user->id; @endphp
                                    <a href="{{ route('profile.show', $vetadoId) }}" class="flex items-center gap-2.5 p-2.5 rounded-xl bg-red-500/5 border border-red-500/10 hover:border-red-500/30 transition-all group/vetado">
                                        <img src="{{ $m->user->avatar ? asset('storage/' . $m->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($m->user->nombre ?? 'U') }}" class="w-7 h-7 rounded-full object-cover opacity-60 group-hover/vetado:opacity-100 transition-opacity" />
                                        <span class="text-stage-300 text-xs truncate group-hover/vetado:text-white transition-colors">{{ $m->user->nombre }}</span>
                                        <span class="ml-auto text-[9px] font-semibold text-red-400 bg-red-500/10 px-2 py-0.5 rounded">Vetado</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div> {{-- CIERRA tab === 'parche' --}}

            <!-- Carga de selector de emojis -->
            <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

            <!-- Tab: Chat del Parche -->
            <div x-show="tab === 'chat'" 
                @scroll-chat.window="scrollToBottom()"
                class="rounded-2xl overflow-hidden min-h-[480px] flex flex-col bg-[#0C0C13] border border-white/[0.08] shadow-2xl relative"
                x-data="parcheChat({
                    parcheId: {{ $parche->id_parche }},
                    currentUserId: {{ Auth::id() }},
                    currentUserName: @js(Auth::user()->nombre ?? 'Usuario'),
                    currentUserHandle: @js(Auth::user()->handle ?? ('user'.Auth::id())),
                    miembros: @js($parche->miembros->where('estado', 'activo')->map(fn($m) => [
                        'id' => $m->user->id_usuario ?? $m->user->id,
                        'nombre' => $m->user->nombre ?? 'Usuario',
                        'handle' => $m->user->handle ?? ('user'.$m->id_usuario),
                        'avatar' => $m->user->avatar ? asset('storage/' . $m->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($m->user->nombre ?? 'U')
                    ])->values()),
                    mensajes: @js($parche->mensajes->map(fn($msg) => [
                        'id' => $msg->id_mensaje ?? $msg->id,
                        'id_usuario' => $msg->user->id_usuario ?? $msg->user->id,
                        'is_me' => ($msg->user->id_usuario ?? $msg->user->id) === Auth::id(),
                        'nombre' => $msg->user->nombre ?? 'Usuario',
                        'avatar' => $msg->user->avatar ? asset('storage/' . $msg->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->nombre ?? 'U'),
                        'mensaje' => $msg->mensaje ?? '',
                        'gif_url' => $msg->gif_url ?? null,
                        'imagenes' => $msg->imagenes ?? null,
                        'has_parent' => !empty($msg->parent_id) && $msg->parent,
                        'parent_id' => $msg->parent_id ?? null,
                        'parent_user' => $msg->parent?->user?->nombre ?? 'Usuario',
                        'parent_mensaje' => $msg->parent?->mensaje ?? '',
                        'created_at' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    ])->values())
                })">

                <!-- Header del Chat con Menciones -->
                <div class="px-5 py-3.5 bg-[#101018] border-b border-white/[0.06] flex items-center justify-between relative" x-data="{ openMentionsModal: false }">
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#2FE6D0] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#2FE6D0]"></span>
                        </span>
                        <p class="text-stage-50 text-xs font-bold tracking-wide">Chat exclusivo del parche</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="openMentionsModal = !openMentionsModal"
                                class="px-3 py-1 rounded-full text-[10px] font-semibold transition-all flex items-center gap-1.5 cursor-pointer border shadow-sm"
                                :class="myMentionsCount > 0 ? 'bg-[#7C5CFF]/20 border-[#7C5CFF]/50 text-[#B8A7FF] hover:bg-[#7C5CFF]/30' : 'bg-white/[0.04] border-white/[0.08] text-[#9A9AA5] hover:text-white'">
                            <svg class="w-3.5 h-3.5 text-[#2FE6D0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
                            <span>Menciones y Respuestas</span>
                            <span x-show="myMentionsCount > 0" 
                                class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-[#7C5CFF] text-white" 
                                x-text="myMentionsCount"></span>
                        </button>
                    </div>

                    <!-- Popover de Menciones -->
                    <div x-show="openMentionsModal" 
                        @click.outside="openMentionsModal = false" 
                        x-cloak 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="absolute top-full right-4 mt-2 z-50 w-72 sm:w-80 rounded-2xl bg-[#141420] border border-white/10 p-3 shadow-2xl flex flex-col gap-2">
                        
                        <div class="flex items-center justify-between pb-2 border-b border-white/5">
                            <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                <span class="text-[#2FE6D0] font-bold">@</span> Te han etiquetado o respondido
                            </span>
                            <button type="button" @click="openMentionsModal = false" class="text-[#9A9AA5] hover:text-white text-xs cursor-pointer">✕</button>
                        </div>

                        <div class="max-h-56 overflow-y-auto divide-y divide-white/5 custom-scrollbar">
                            <template x-for="m in myMentions" :key="m.id">
                                <button type="button" 
                                        @click="scrollToMessage(m.id); openMentionsModal = false;" 
                                        class="w-full text-left p-2.5 rounded-xl hover:bg-white/5 transition-all flex flex-col gap-1 cursor-pointer group/mitem">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-[#2FE6D0] group-hover/mitem:underline" x-text="m.nombre"></span>
                                        <span class="text-[9px] text-[#8E8E9A]" x-text="m.created_at"></span>
                                    </div>
                                    <p class="text-xs text-stage-200 line-clamp-2 leading-tight" x-text="m.mensaje"></p>
                                </button>
                            </template>

                            <template x-if="myMentions.length === 0">
                                <div class="py-6 text-center text-[11px] text-[#9A9AA5]">
                                    No tienes menciones ni respuestas pendientes en este parche.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Stream de Mensajes -->
                <div class="flex-1 py-4 sm:py-5 flex flex-col gap-1.5 overflow-y-auto max-h-[420px] custom-scrollbar" 
                    x-ref="chatContainer">
                    
                    <template x-for="msg in mensajes" :key="msg.id">
                    <!-- Contenedor Fila con transición acelerada (duration-300) -->
                    <div :id="'msg-' + msg.id"
                        class="w-full px-4 sm:px-5 py-1.5 transition-colors duration-300 flex items-center"
                        :class="msg.is_me ? 'justify-end' : 'justify-start'">
                            
                            <!-- Bloque del Mensaje y Acciones -->
                            <div class="flex items-end gap-2.5 max-w-[85%] sm:max-w-[75%] group">
                                
                                <template x-if="msg.is_me && {{ $isJoined ? 'true' : 'false' }}">
                                    <button type="button" 
                                            @click="setReply(msg.id, 'Tú', msg.mensaje)" 
                                            class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity p-1.5 text-[#9A9AA5] hover:text-[#2FE6D0] shrink-0 mb-1 cursor-pointer bg-white/[0.04] sm:bg-transparent rounded-lg" 
                                            title="Responder este mensaje">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                    </button>
                                </template>

                                <template x-if="!msg.is_me">
                                    <a :href="'/perfil/' + msg.id_usuario" class="shrink-0 mb-0.5 hover:opacity-80 transition-opacity" :title="msg.nombre">
                                        <img :src="msg.avatar" class="w-7 h-7 rounded-full object-cover border border-white/10" />
                                    </a>
                                </template>

                                <div class="flex flex-col min-w-0" :class="msg.is_me ? 'items-end' : 'items-start'">
                                    <template x-if="!msg.is_me">
                                        <span class="text-[10px] font-semibold text-[#2FE6D0] mb-1 ml-1 truncate max-w-[180px]" x-text="msg.nombre"></span>
                                    </template>

                                    <template x-if="msg.has_parent">
                                        <div @click="if(msg.parent_id) scrollToMessage(msg.parent_id)" 
                                            title="Ir al mensaje original"
                                            class="mb-1.5 px-3 py-1.5 rounded-xl border-l-2 flex items-center gap-1.5 max-w-full text-[11px] backdrop-blur-sm cursor-pointer hover:opacity-85 transition-all shadow-inner" 
                                            :class="msg.is_me ? 'bg-white/10 text-white border-[#2FE6D0]' : 'bg-white/[0.04] text-stage-200 border-[#7C5CFF]'">
                                            <svg class="w-3 h-3 shrink-0 opacity-70" :class="msg.is_me ? 'text-[#2FE6D0]' : 'text-[#7C5CFF]'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                            <span class="font-bold shrink-0" :class="msg.is_me ? 'text-[#2FE6D0]' : 'text-[#B8A7FF]'" x-text="msg.parent_user"></span>
                                            <span class="text-white/20 shrink-0">•</span>
                                            <span class="truncate opacity-80" x-text="msg.parent_mensaje"></span>
                                        </div>
                                    </template>

                                    <div class="relative px-3.5 py-2.5 rounded-2xl text-xs leading-relaxed break-words shadow-md transition-all" 
                                        :class="msg.is_me ? 'bg-gradient-to-r from-[#7C5CFF] to-[#6541FF] text-white rounded-br-xs border border-[#8F72FF]/30 shadow-[#7C5CFF]/15' : 'bg-[#151522] text-stage-100 rounded-bl-xs border border-white/[0.08]'">
                                        
                                        <template x-if="msg.gif_url">
                                            <div class="mb-2 rounded-xl overflow-hidden border border-white/10 max-w-xs">
                                                <img :src="msg.gif_url" alt="GIF" class="w-full max-h-48 object-cover" />
                                            </div>
                                        </template>

                                        <template x-if="msg.imagenes && msg.imagenes.length > 0">
                                            <div class="mb-2 grid gap-1.5 max-w-xs" :class="msg.imagenes.length > 1 ? 'grid-cols-2' : 'grid-cols-1'">
                                                <template x-for="img in msg.imagenes" :key="img">
                                                    <img :src="'/storage/' + img" class="w-full h-32 object-cover rounded-xl border border-white/10" />
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="msg.mensaje">
                                            <p class="whitespace-pre-wrap" x-html="formatMentions(msg.mensaje, msg.is_me)"></p>
                                        </template>

                                        <div class="mt-1 flex items-center justify-end gap-1 text-[9px]" :class="msg.is_me ? 'text-white/70' : 'text-[#8E8E9A]'">
                                            <span x-text="msg.created_at"></span>
                                        </div>
                                    </div>
                                </div>

                                <template x-if="!msg.is_me && {{ $isJoined ? 'true' : 'false' }}">
                                    <button type="button" 
                                            @click="setReply(msg.id, msg.nombre, msg.mensaje)" 
                                            class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity p-1.5 text-[#9A9AA5] hover:text-[#2FE6D0] shrink-0 mb-1 cursor-pointer bg-white/[0.04] sm:bg-transparent rounded-lg" 
                                            title="Responder este mensaje">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                                    </button>
                                </template>

                                <template x-if="msg.is_me">
                                    <a :href="'/perfil/' + msg.id_usuario" class="shrink-0 mb-0.5 hover:opacity-80 transition-opacity" title="Tú">
                                        <img :src="msg.avatar" class="w-7 h-7 rounded-full object-cover border border-[#7C5CFF]/60 shadow-sm" />
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="replyingTo" x-cloak class="px-4 py-2 bg-[#141420] border-t border-white/5 flex items-center justify-between text-xs text-stage-200">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-[#2FE6D0] font-bold text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                            Respondiendo a <span class="text-white" x-text="replyingTo?.name"></span>:
                        </span>
                        <span class="text-[#9A9AA5] truncate text-xs" x-text="replyingTo?.text"></span>
                    </div>
                    <button type="button" @click="cancelReply()" class="text-[#9A9AA5] hover:text-white p-1 cursor-pointer">✕</button>
                </div>

                <div x-show="previewMedia.length > 0 || selectedGifUrl" x-cloak class="px-4 py-2.5 bg-[#101018] border-t border-white/5 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <template x-if="selectedGifUrl">
                            <div class="flex items-center gap-3 bg-white/[0.04] p-1.5 pr-4 rounded-xl border border-white/10">
                                <img :src="selectedGifUrl" class="w-14 h-10 object-cover rounded-lg" />
                                <span class="text-[#2FE6D0] font-bold text-xs">GIF adjunto</span>
                            </div>
                        </template>
                        <template x-for="(med, idx) in previewMedia" :key="idx">
                            <div class="flex items-center gap-3 bg-white/[0.04] p-1.5 pr-4 rounded-xl border border-white/10">
                                <img :src="med.url" class="w-14 h-10 object-cover rounded-lg" />
                                <span class="text-[#2FE6D0] font-bold text-xs">Imagen adjunta</span>
                                <button type="button" @click="removeMedia(idx)" class="text-[#9A9AA5] hover:text-white ml-2 cursor-pointer">✕</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="selectedGifUrl = ''; previewMedia = []" class="text-[#9A9AA5] hover:text-white p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Input del Chat -->
                @if($isJoined)
                    <div class="p-3 sm:p-4 bg-[#101018] border-t border-white/[0.06] relative">
                        <form @submit.prevent="sendMessage($event)" action="{{ route('parche.comment', $parche->id_parche) }}" method="POST" class="flex items-center gap-2.5">
                            @csrf

                            <div class="flex-1 relative flex items-center bg-[#141420] border border-white/10 rounded-2xl sm:rounded-full px-4 py-2.5 transition-all focus-within:border-[#7C5CFF]/60 focus-within:ring-1 focus-within:ring-[#7C5CFF]/60">
                                <input type="text" 
                                    x-ref="chatInput" 
                                    x-model="commentText" 
                                    @input="handleInput($event)" 
                                    @paste="handlePaste($event)" 
                                    placeholder="Escribe un mensaje o usa @ para mencionar..." 
                                    class="w-full bg-transparent border-0 text-xs text-white placeholder-[#8E8E9A] focus:outline-none focus:ring-0 p-0 pr-3" />

                                <div class="flex items-center gap-2.5 shrink-0 text-[#9A9AA5]">
                                    <button type="button" @click="toggleMentions()" class="hover:text-[#2FE6D0] transition-colors p-1 font-semibold text-xs cursor-pointer" title="Mencionar usuario">
                                        @
                                    </button>

                                    <label class="hover:text-[#2FE6D0] transition-colors p-1 cursor-pointer" title="Adjuntar imagen o vídeo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <input type="file" @change="handleFilesSelected($event)" accept="image/*,video/*" multiple class="hidden" />
                                    </label>

                                    <button type="button" @click="toggleGifPicker()" class="hover:text-[#2FE6D0] transition-colors p-1 font-bold text-[11px] cursor-pointer" title="Añadir GIF">
                                        GIF
                                    </button>

                                    <button type="button" @click="toggleEmojiPicker()" class="hover:text-[#2FE6D0] transition-colors p-1 flex items-center justify-center cursor-pointer" title="Añadir emoji">
                                        <i class="fa-regular fa-face-smile text-sm"></i>
                                    </button>
                                </div>

                                <div x-show="openMentions && filteredMentions.length > 0" 
                                    @click.outside="openMentions = false" 
                                    x-cloak 
                                    class="absolute bottom-full mb-3 left-0 w-64 max-h-48 overflow-y-auto bg-[#141420] border border-white/10 rounded-2xl shadow-2xl p-1 z-50 divide-y divide-white/5 custom-scrollbar">
                                    <template x-for="user in filteredMentions" :key="user.id">
                                        <button type="button" 
                                                @click="insertMention(user.handle)" 
                                                class="w-full flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 text-left transition-all cursor-pointer">
                                            <img :src="user.avatar" class="w-6 h-6 rounded-full object-cover border border-white/10 shrink-0" />
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-white truncate" x-text="user.nombre"></p>
                                                <p class="text-[10px] text-[#2FE6D0] truncate" x-text="'@' + user.handle"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button type="submit" 
                                :disabled="sending || !canSubmit()" 
                                class="w-10 h-10 rounded-2xl bg-[#7C5CFF] hover:bg-[#6b47ff] text-white flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed shrink-0 cursor-pointer shadow-lg shadow-[#7C5CFF]/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </form>

                        <div x-show="openGif" 
                            @click.outside="openGif = false" 
                            x-cloak 
                            class="absolute bottom-full mb-3 right-12 z-50 w-80 bg-[#141420] border border-white/10 rounded-2xl shadow-2xl p-3 flex flex-col gap-2">
                            <input type="text" 
                                @input.debounce.300ms="searchGiphy($event.target.value)" 
                                placeholder="Buscar GIF en Giphy..." 
                                class="w-full bg-[#181824] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#2FE6D0]" />
                            
                            <div class="grid grid-cols-3 gap-1.5 max-h-52 overflow-y-auto custom-scrollbar">
                                <template x-for="g in gifResults" :key="g.id">
                                    <img :src="g.images.fixed_height_small.url" 
                                        @click="selectGif(g.images.fixed_height.url)" 
                                        class="w-full h-16 object-cover rounded-lg cursor-pointer hover:scale-105 transition-transform" />
                                </template>
                            </div>
                        </div>

                        <div x-show="openEmoji" 
                            @click.outside="openEmoji = false" 
                            x-cloak 
                            class="absolute bottom-full mb-3 right-4 z-50 bg-[#141420] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                            <emoji-picker @emoji-click="addEmoji($event.detail.unicode)" class="dark"></emoji-picker>
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-[#101018] border-t border-white/[0.06] text-center">
                        <p class="text-xs text-[#9A9AA5]">Únete al parche para chatear con los demás integrantes.</p>
                    </div>
                @endif
            </div> {{-- CIERRA tab === 'chat' --}}
        </div> {{-- CIERRA Columna Principal min-w-0 --}}

        <!-- Sidebar Lateral (Derecha) -->
        <aside class="rounded-2xl p-5 bg-[#0C0C13] border border-white/[0.08] h-fit flex flex-col gap-4 lg:sticky lg:top-6">
            @php $creadorId = $parche->creador->id_usuario ?? $parche->creador->id ?? null; @endphp
            <a href="{{ $creadorId ? route('profile.show', $creadorId) : '#' }}" class="flex items-center justify-between group/host">
                <div>
                    <p class="text-stage-50 text-sm font-bold group-hover/host:text-[#2FE6D0] transition-colors">{{ $parche->creador->nombre ?? 'Organizador' }}</p>
                    <p class="text-[#9A9AA5] text-xs mt-0.5">Anfitrión del parche</p>
                </div>
                <img src="{{ $parche->creador->avatar ? asset('storage/' . $parche->creador->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($parche->creador->nombre ?? 'A') }}" class="w-10 h-10 rounded-full object-cover border-2 border-[#7C5CFF] group-hover/host:border-[#2FE6D0] transition-colors" />
            </a>

            <p class="text-[#9A9AA5] text-xs leading-relaxed">
                {{ $parche->contenido }}
            </p>

            @if($isOwner)
                <button @click="editOpen = true" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-[#7C5CFF] hover:bg-[#6b47ff] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar información
                </button>

                <form action="{{ route('parche.destroy', $parche->id_parche) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar permanentemente este parche?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Eliminar parche
                    </button>
                </form>
            @elseif($isJoined)
                <form action="{{ route('parche.leave', $parche->id_parche) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold text-red-400 bg-red-500/15 border border-red-500/30 hover:bg-red-500/25 transition-all">
                        Salir de este parche
                    </button>
                </form>
            @elseif(!$isKicked)
                <form action="{{ route('parche.join', $parche->id_parche) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-[#FF3D57] hover:bg-[#ff2442] transition-all">
                        Unirme a este parche
                    </button>
                </form>
            @endif
        </aside>
    </div>

    <!-- Modal Editar Parche -->
    @if($isOwner)
    <div x-show="editOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div @click.outside="editOpen = false" class="w-full max-w-[500px] rounded-3xl overflow-hidden bg-[#101018] border border-white/10 shadow-2xl">
            @php
                $modalEventoImg = Str::startsWith($evento->imagen_portada ?? '', 'http') 
                    ? $evento->imagen_portada 
                    : ($evento->imagen_portada ? asset('storage/' . $evento->imagen_portada) : ($evento->imagen_url ?? $evento->imagen ?? 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=400&fit=crop&auto=format'));
            @endphp

            <div class="relative h-28 overflow-hidden">
                <img src="{{ $modalEventoImg }}" 
                     alt="{{ $evento->artista ?? $evento->nombre ?? 'Evento' }}" 
                     class="absolute inset-0 w-full h-full object-cover opacity-50" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#101018] via-[#101018]/60 to-transparent"></div>
                
                <div class="absolute inset-x-0 bottom-0 p-5 flex flex-col justify-end">
                    <span class="text-[#2FE6D0] text-[10px] font-bold uppercase tracking-[0.18em] mb-0.5">{{ $evento->artista ?? $evento->nombre ?? 'Evento' }}</span>
                    <h2 class="text-white text-2xl font-bold font-['Anton',sans-serif] uppercase tracking-wide">Editar tu parche</h2>
                </div>

                <button @click="editOpen = false" type="button" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/60 hover:bg-black/80 border border-white/10 flex items-center justify-center text-stage-300 hover:text-white transition-all cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form action="{{ route('parche.update', $parche->id_parche) }}" method="POST" class="p-6 flex flex-col gap-4 text-left">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Nombre del parche</label>
                    <input type="text" 
                           name="show_nombre" 
                           value="{{ old('show_nombre', $parche->nombre) }}" 
                           required 
                           placeholder="Ej. {{ $evento->artista ?? 'Concierto' }} desde el norte" 
                           class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                </div>

                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Tipo de plan</label>
                    <input type="text" 
                           name="tipo_plan" 
                           value="{{ old('tipo_plan', $parche->tipo_plan) }}" 
                           required 
                           placeholder="Ej. Previa, Ir en grupo, etc." 
                           class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-stage-300 text-xs font-semibold block mb-1.5">Sector</label>
                        <input type="text" 
                               name="sector" 
                               value="{{ old('sector', $parche->sector) }}" 
                               required 
                               placeholder="Ej. Platea 102" 
                               class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                    </div>

                    <div>
                        <label class="text-stage-300 text-xs font-semibold block mb-1.5">Cupos totales</label>
                        <input type="number" 
                               name="cupos" 
                               value="{{ old('cupos', $parche->cupos) }}" 
                               min="1" 
                               max="100" 
                               required 
                               class="w-full rounded-xl px-4 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                    </div>
                </div>

                <div>
                    <label class="text-stage-300 text-xs font-semibold block mb-1.5">Hora de encuentro</label>
                    <div class="relative">
                        <input type="text" 
                               name="hora_encuentro" 
                               value="{{ old('hora_encuentro', $parche->hora_encuentro) }}" 
                               required 
                               placeholder="05:30 p. m." 
                               class="w-full rounded-xl pl-4 pr-10 py-3 bg-[#181824] border border-white/10 text-white text-sm outline-none placeholder:text-stage-400/60 focus:border-[#7C5CFF]/60 focus:bg-[#1f1f2e] transition-all" />
                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stage-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>

                <div class="rounded-2xl p-4 bg-[#2FE6D0]/[0.06] border border-[#2FE6D0]/20 flex items-start gap-3 my-1">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-[#2FE6D0] mt-0.5">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <p class="text-stage-300 text-xs leading-relaxed">Los cambios se actualizarán de forma inmediata para todos.</p>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl text-sm font-semibold text-white bg-[#7C5CFF] hover:bg-[#6a4ae8] border border-white/10 transition-all cursor-pointer mt-1 shadow-lg shadow-[#7C5CFF]/25">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection