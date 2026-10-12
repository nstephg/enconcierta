<div x-show="openNotifs" 
     @@click.outside="openNotifs = false; selectingMode = false; selectedNotifs = []" 
     x-data="{ selectingMode: false }"
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
                            @@click="marcarLeidas()"
                            class="text-[#9A9AA5] hover:text-[#2FE6D0] transition-colors font-medium cursor-pointer">
                        Leídas
                    </button>
                    <span class="text-white/10">|</span>
                    
                    <template x-if="!selectingMode">
                        <button type="button" 
                                @@click="selectingMode = true"
                                class="text-[#9A9AA5] hover:text-[#FF3D57] transition-colors font-medium cursor-pointer flex items-center gap-1">
                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                            <span>Borrar</span>
                        </button>
                    </template>

                    <template x-if="selectingMode">
                        <button type="button" 
                                @@click="selectingMode = false; selectedNotifs = []"
                                class="text-[#2FE6D0] hover:underline transition-colors font-medium cursor-pointer">
                            Cancelar
                        </button>
                    </template>
                </div>
            </template>
            <button @@click="openNotifs = false; selectingMode = false; selectedNotifs = []" type="button" class="text-[#9A9AA5] hover:text-[#F5F5F7] p-1 transition-colors cursor-pointer ml-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- BARRA DE SELECCIÓN MODO MÚLTIPLE -->
    <template x-if="items.length > 0 && selectingMode">
        <div x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="px-4 py-2.5 bg-white/[0.03] border-b border-white/5 flex items-center justify-between text-[11px]">
            
            <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                <div class="relative flex items-center">
                    <input type="checkbox" 
                           @@change="toggleSelectAll($event)"
                           :checked="selectedNotifs.length === items.length && items.length > 0"
                           class="sr-only peer">
                    <div class="w-4 h-4 rounded border border-white/20 bg-white/5 peer-checked:bg-[#FF3D57] peer-checked:border-[#FF3D57] transition-all flex items-center justify-center">
                        <i class="fa-solid fa-check text-[9px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                    </div>
                </div>
                <span class="text-[#9A9AA5] group-hover:text-white transition-colors">Seleccionar todas</span>
            </label>

            <button type="button" 
                    @@click="if (selectedNotifs.length > 0) { eliminar(false); selectingMode = false; } else { eliminar(true); selectingMode = false; }"
                    class="px-2.5 py-1 rounded-lg bg-[#FF3D57]/15 border border-[#FF3D57]/30 text-[#FF3D57] hover:bg-[#FF3D57]/25 font-semibold text-[11px] transition-all cursor-pointer">
                <span x-text="selectedNotifs.length > 0 ? 'Eliminar (' + selectedNotifs.length + ')' : 'Borrar todas'"></span>
            </button>
        </div>
    </template>

    <!-- LISTA DE NOTIFICACIONES -->
    <div class="max-h-[380px] overflow-y-auto divide-y divide-white/5 scrollbar-thin scrollbar-thumb-white/10">
        <template x-for="notif in items" :key="notif.id_notificacion">
            <div class="group flex items-start gap-3 px-4 py-3.5 transition-all hover:bg-white/[0.04]"
                 :class="{ 'bg-[#FF3D57]/[0.03]': !notif.leido }">
                
                <!-- CASILLA SELECCIÓN -->
                <template x-if="selectingMode">
                    <div class="pt-1 flex-shrink-0"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-x-2"
                         x-transition:enter-end="opacity-100 translate-x-0">
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
                </template>

                <!-- AVATAR O IMAGEN DEL EVENTO PARA PARCHES -->
                <a :href="'/notificaciones/' + notif.id_notificacion + '/go'" class="relative flex-shrink-0 block group/avatar">
                    <template x-if="notif.tipo === 'parche'">
                        <img :src="notif.event_image ? notif.event_image : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(notif.subject || 'Parche') + '&background=2FE6D0&color=000'" 
                             :alt="notif.subject" 
                             class="w-9 h-9 rounded-xl object-cover border border-[#2FE6D0]/40 group-hover/avatar:border-[#2FE6D0] transition-colors">
                    </template>
                    <template x-if="notif.tipo !== 'parche'">
                        <img :src="notif.actor && notif.actor.avatar_url_formatted ? notif.actor.avatar_url_formatted : 'https://ui-avatars.com/api/?name=Usuario'" 
                             :alt="notif.actor ? notif.actor.nombre : 'Usuario'" 
                             class="w-9 h-9 rounded-full object-cover border border-white/10 group-hover/avatar:border-[#FF3D57]/60 transition-colors">
                    </template>
                    
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
                        <template x-if="notif.tipo === 'tag' || notif.tipo === 'mention'">
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
                        <template x-if="!['vibro','comment','reply','tag','mention','repost','follow','parche'].includes(notif.tipo)">
                            <i class="fa-solid fa-bell text-[8px] text-[#2FE6D0]"></i>
                        </template>
                    </span>
                </a>

                <!-- CONTENIDO DETALLADO CON REDIRECCIÓN AUTO-LEÍDA -->
                <div class="flex-1 min-w-0">
                    <p class="text-[#D0D0D8] text-xs leading-relaxed">
                        <a :href="'/notificaciones/' + notif.id_notificacion + '/go'" 
                           class="text-[#F5F5F7] font-semibold hover:underline hover:text-[#2FE6D0] transition-colors cursor-pointer" 
                           x-text="notif.tipo === 'parche' ? notif.subject : (notif.actor ? notif.actor.nombre : 'Usuario')">
                        </a> 
                        <span x-text="' ' + notif.accion + ' '"></span>
                        <template x-if="notif.subject && notif.tipo !== 'parche'">
                            <span>
                                <a :href="'/notificaciones/' + notif.id_notificacion + '/go'" 
                                   class="text-[#2FE6D0] font-medium hover:underline cursor-pointer" 
                                   x-text="notif.subject">
                                </a>
                            </span>
                        </template>
                    </p>
                    <span class="text-[#6A6A75] text-[10px] mt-0.5 block font-normal" x-text="notif.time_ago"></span>
                </div>

                <!-- INDICADOR DE NO LEÍDO -->
                <div class="flex items-center gap-2 flex-shrink-0 pt-1">
                    <template x-if="!notif.leido">
                        <span class="w-2 h-2 rounded-full bg-[#FF3D57] shadow-[0_0_6px_#FF3D57]"></span>
                    </template>
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