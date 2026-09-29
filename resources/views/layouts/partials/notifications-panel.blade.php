<div x-show="openNotifs" 
     @click.outside="openNotifs = false" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
     x-cloak
     style="display: none;"
     class="absolute top-full right-3 sm:right-6 md:right-8 mt-2 z-50 w-[calc(100vw-24px)] sm:w-[410px] rounded-2xl overflow-hidden bg-[#0C0C13]/95 backdrop-blur-xl border border-white/10 shadow-[0_24px_60px_rgba(0,0,0,0.95)]">

    <!-- HEADER PANEL -->
    <div class="flex items-center justify-between px-4 py-3.5 border-b border-white/5 bg-[#0A0A0F]">
        <div class="flex items-center gap-2">
            <span class="text-[#F5F5F7] font-bold text-sm tracking-wide">Notificaciones</span>
            <template x-if="unreadCount > 0">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white bg-[#FF3D57] shadow-[0_0_8px_#FF3D57]"
                      x-text="unreadCount > 99 ? '+99' : unreadCount">
                </span>
            </template>
        </div>
        
        <div class="flex items-center gap-3 text-xs">
            <template x-if="items.length > 0">
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="marcarLeidas()"
                            class="text-[#9A9AA5] hover:text-[#2FE6D0] transition-colors font-medium cursor-pointer">
                        Leídas
                    </button>
                    <span class="text-white/10">|</span>
                    <button type="button" 
                            @click="selectedNotifs.length > 0 ? eliminar(false) : eliminar(true)"
                            class="text-[#9A9AA5] hover:text-[#FF3D57] transition-colors font-medium cursor-pointer"
                            x-text="selectedNotifs.length > 0 ? 'Borrar (' + selectedNotifs.length + ')' : 'Borrar todas'">
                    </button>
                </div>
            </template>
            <button @click="openNotifs = false" type="button" class="text-[#9A9AA5] hover:text-[#F5F5F7] p-1 transition-colors cursor-pointer ml-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- BARRA DE SELECCIÓN RÁPIDA -->
    <template x-if="items.length > 0">
        <div class="px-4 py-2.5 bg-white/[0.02] border-b border-white/5 flex items-center justify-between text-[11px] text-[#6A6A75]">
            <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                <div class="relative flex items-center">
                    <input type="checkbox" 
                           @change="toggleSelectAll($event)"
                           :checked="selectedNotifs.length === items.length && items.length > 0"
                           class="sr-only peer">
                    <div class="w-4 h-4 rounded border border-white/20 bg-white/5 peer-checked:bg-[#FF3D57] peer-checked:border-[#FF3D57] transition-all flex items-center justify-center">
                        <i class="fa-solid fa-check text-[9px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                    </div>
                </div>
                <span class="group-hover:text-[#D0D0D8] transition-colors">Seleccionar todas</span>
            </label>
            <span x-text="selectedNotifs.length + ' seleccionadas'" class="text-[#2FE6D0]/80 font-medium"></span>
        </div>
    </template>

    <!-- LISTA DE NOTIFICACIONES REALES -->
    <div class="max-h-[380px] overflow-y-auto divide-y divide-white/5 scrollbar-thin scrollbar-thumb-white/10">
        <template x-for="notif in items" :key="notif.id_notificacion">
            <div class="group flex items-start gap-3 px-4 py-3.5 transition-all hover:bg-white/[0.04]"
                 :class="{ 'bg-[#FF3D57]/[0.03]': !notif.leido }">
                
                <!-- CASILLA SELECCIÓN ESTILIZADA -->
                <div class="pt-1 flex-shrink-0">
                    <label class="relative flex items-center cursor-pointer">
                        <input type="checkbox" 
                               :value="notif.id_notificacion" 
                               x-model.number="selectedNotifs"
                               class="sr-only peer">
                        <div class="w-4 h-4 rounded border border-white/20 bg-white/5 peer-checked:bg-[#FF3D57] peer-checked:border-[#FF3D57] transition-all flex items-center justify-center">
                            <i class="fa-solid fa-check text-[9px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                    </label>
                </div>

                <!-- AVATAR E ICONO DE ACCIÓN (CON ENLACE AL PERFIL) -->
                <a :href="notif.profile_url" class="relative flex-shrink-0 block group/avatar">
                    <img :src="notif.actor && notif.actor.avatar_url_formatted ? notif.actor.avatar_url_formatted : '{{ asset('images/default-avatar.svg') }}'" 
                         :alt="notif.actor ? notif.actor.nombre : 'Usuario'" 
                         class="w-9 h-9 rounded-full object-cover border border-white/10 group-hover/avatar:border-[#FF3D57]/60 transition-colors">
                    
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[#0C0C13] border border-white/10 flex items-center justify-center shadow-md">
                        <template x-if="notif.tipo === 'vibro'">
                            <i class="fa-solid fa-fire text-[8px] text-[#FF3D57]"></i>
                        </template>
                        <template x-if="notif.tipo === 'comment'">
                            <i class="fa-solid fa-comment text-[8px] text-[#FFB020]"></i>
                        </template>
                        <template x-if="notif.tipo === 'reply'">
                            <i class="fa-solid fa-reply text-[8px] text-[#FFB020]"></i>
                        </template>
                        <template x-if="notif.tipo === 'tag'">
                            <i class="fa-solid fa-at text-[8px] text-[#7C5CFF]"></i>
                        </template>
                        <template x-if="notif.tipo === 'repost'">
                            <i class="fa-solid fa-retweet text-[8px] text-[#2FE6D0]"></i>
                        </template>
                        <template x-if="notif.tipo === 'follow'">
                            <i class="fa-solid fa-user-plus text-[8px] text-[#7C5CFF]"></i>
                        </template>
                        <template x-if="notif.tipo === 'parche'">
                            <i class="fa-solid fa-ticket text-[8px] text-[#2FE6D0]"></i>
                        </template>
                        <template x-if="!['vibro','comment','reply','tag','repost','follow','parche'].includes(notif.tipo)">
                            <i class="fa-solid fa-bell text-[8px] text-[#2FE6D0]"></i>
                        </template>
                    </span>
                </a>

                <!-- CONTENIDO DETALLADO (NOMBRE CREADOR + ASUNTO CON ENLACES) -->
                <div class="flex-1 min-w-0">
                    <p class="text-[#D0D0D8] text-xs leading-relaxed">
                        <a :href="notif.profile_url" 
                           class="text-[#F5F5F7] font-semibold hover:underline hover:text-[#2FE6D0] transition-colors" 
                           x-text="notif.actor ? notif.actor.nombre : 'Usuario'">
                        </a> 
                        <span x-text="' ' + notif.accion + ' '"></span>
                        <template x-if="notif.subject">
                            <span>
                                <template x-if="notif.post_url">
                                    <a :href="notif.post_url" 
                                       class="text-[#2FE6D0] font-medium hover:underline" 
                                       x-text="notif.subject">
                                    </a>
                                </template>
                                <template x-if="!notif.post_url">
                                    <span class="text-[#2FE6D0] font-medium" x-text="notif.subject"></span>
                                </template>
                            </span>
                        </template>
                    </p>
                    <span class="text-[#6A6A75] text-[10px] mt-0.5 block font-normal" x-text="notif.time_ago"></span>
                </div>

                <!-- BOTÓN BORRAR INDIVIDUAL + INDICADOR NO LEÍDO -->
                <div class="flex items-center gap-2 flex-shrink-0 pt-1">
                    <template x-if="!notif.leido">
                        <span class="w-2 h-2 rounded-full bg-[#FF3D57] shadow-[0_0_6px_#FF3D57]"></span>
                    </template>
                    <button type="button" 
                            @click="eliminar(false, notif.id_notificacion)"
                            title="Eliminar notificación"
                            class="opacity-0 group-hover:opacity-100 text-[#6A6A75] hover:text-[#FF3D57] transition-all p-1 cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        </template>

        <!-- ESTADO VACÍO -->
        <template x-if="items.length === 0">
            <div class="py-12 px-4 text-center">
                <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-3 text-[#6A6A75]">
                    <i class="fa-regular fa-bell-slash text-base"></i>
                </div>
                <p class="text-xs text-[#9A9AA5]">No tienes notificaciones por el momento</p>
            </div>
        </template>
    </div>
</div>