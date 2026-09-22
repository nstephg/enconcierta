<!-- Carga de Alpine.js e Interact JS -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<div 
    x-data="{
        title: '',
        subtitle: '',
        coverImg: null,
        showArtist: '',
        showVenue: '',
        published: false,
        
        // Estilos del Lienzo
        globalFont: 'Inter, sans-serif',
        canvasWidth: '780px',
        canvasPadding: 'py-10 px-6 sm:px-16',
        bgType: 'solid',
        bgColor: '#0A0A0F',
        bgGrad: 'linear-gradient(135deg,#0A0A0F 0%,#1a0a2e 100%)',
        bgImg: null,
        bgOverlayOpacity: 40,
        bgBlur: 0,
        textColor: '#F5F5F7',
        titleColor: '#F5F5F7',
        
        // Sombra de Título (Desactivada por defecto)
        enableTitleShadow: false,
        shadowX: 0,
        shadowY: 10,
        shadowBlur: 20,
        shadowColor: 'rgba(0,0,0,0.6)',

        canvasAccentColor: '#FF3D57',
        lineHeight: '1.6',
        letterSpacing: '0px',

        // Estado e Interactividad
        showPanel: true,
        panelTab: 'fondo',
        activeId: null,
        pendingMediaId: null,
        editingTextId: null,

        // Bloques Dinámicos Libres
        blocks: [
            { id: 'b1', type: 'text', content: '', color: null, bg: null, fontSize: 16, align: 'left', bold: false, italic: false, font: '', x: 0, y: 0, w: '100%', h: '100px' }
        ],

        fonts: [
            { id: 'inter', label: 'Inter (Sans)', css: 'Inter, sans-serif' },
            { id: 'anton', label: 'Anton (Display)', css: 'Anton, sans-serif' },
            { id: 'serif', label: 'Georgia (Serif)', css: 'Georgia, serif' },
            { id: 'mono', label: 'JetBrains Mono', css: 'monospace' },
            { id: 'playfair', label: 'Playfair Display', css: 'Playfair Display, serif' }
        ],
        stickersList: ['🔥', '⚡', '🎸', '🎟️', '🎧', '👑', '🖤', '✨', '🤘', '🎤', '🔊', '🌟', '💥', '🎶', '🥳', '🍸'],
        bgPresets: ['#0A0A0F','#0D0A1E','#05141A','#130C05','#F2F2F7','#1a0a2e','#001a12','#1a0000','#0a1a2e','#1a1a00'],
        gradPresets: [
            'linear-gradient(135deg,#0A0A0F 0%,#1a0a2e 100%)',
            'linear-gradient(135deg,#0D0A1E 0%,#2d1b69 100%)',
            'linear-gradient(135deg,#05141A 0%,#003322 100%)',
            'linear-gradient(160deg,#1a0010 0%,#0a0a2e 50%,#001a12 100%)',
            'linear-gradient(135deg,#130C05 0%,#2e1800 100%)',
            'linear-gradient(135deg,#F2F2F7 0%,#E0E0F0 100%)',
            'linear-gradient(135deg,#1F1C2C 0%,#928DAB 100%)',
            'linear-gradient(135deg,#000000 0%,#434343 100%)'
        ],

        get canvasBg() {
            if (this.bgType === 'gradient') return this.bgGrad;
            if (this.bgType === 'image' && this.bgImg) return `url(${this.bgImg}) center/cover no-repeat fixed`;
            return this.bgColor;
        },

        get titleShadowStyle() {
            if (!this.title || this.title.trim() === '' || !this.enableTitleShadow) return 'none';
            return `${this.shadowX}px ${this.shadowY}px ${this.shadowBlur}px ${this.shadowColor}`;
        },

        get stylesJson() {
            return JSON.stringify({
                globalFont: this.globalFont,
                canvasWidth: this.canvasWidth,
                canvasPadding: this.canvasPadding,
                bgType: this.bgType,
                bgColor: this.bgColor,
                bgGrad: this.bgGrad,
                bgOverlayOpacity: this.bgOverlayOpacity,
                bgBlur: this.bgBlur,
                textColor: this.textColor,
                titleColor: this.titleColor,
                titleShadowStyle: this.titleShadowStyle,
                canvasAccentColor: this.canvasAccentColor,
                lineHeight: this.lineHeight,
                letterSpacing: this.letterSpacing
            });
        },

        get blocksJson() {
            return JSON.stringify(this.blocks);
        },

        get activeBlock() {
            return this.blocks.find(b => b.id === this.activeId) || null;
        },

        initTransformables() {
            this.$nextTick(() => {
                interact('.transform-block').draggable({
                    inertia: false,
                    ignoreFrom: 'textarea:read-write, input:read-write',
                    listeners: {
                        move: (event) => {
                            const target = event.target;
                            const id = target.getAttribute('data-id');
                            if (this.editingTextId === id) return;
                            const block = this.blocks.find(b => b.id === id);
                            if (block) {
                                block.x = (block.x || 0) + event.dx;
                                block.y = (block.y || 0) + event.dy;
                            }
                        }
                    }
                }).resizable({
                    edges: { left: true, right: true, bottom: true, top: true },
                    margin: 8,
                    listeners: {
                        move: (event) => {
                            const target = event.target;
                            const id = target.getAttribute('data-id');
                            const block = this.blocks.find(b => b.id === id);
                            if (block) {
                                block.w = Math.max(100, event.rect.width) + 'px';
                                block.h = Math.max(40, event.rect.height) + 'px';
                                block.x = (block.x || 0) + event.deltaRect.left;
                                block.y = (block.y || 0) + event.deltaRect.top;
                            }
                        }
                    }
                });
            });
        },

        addBlock(type, customContent = null) {
            const id = 'b_' + Date.now();
            const newBlock = { 
                id: id, 
                type: type, 
                content: customContent !== null ? customContent : (type === 'sticker' ? '🔥' : ''), 
                color: null, 
                bg: null, 
                fontSize: type === 'heading' ? 28 : (type === 'quote' ? 18 : (type === 'sticker' ? 64 : 16)), 
                align: 'left', 
                bold: false, 
                italic: false, 
                font: '',
                x: 0,
                y: this.blocks.length * 30,
                w: type === 'media' ? '320px' : '100%',
                h: type === 'media' ? '220px' : 'auto'
            };
            this.blocks.push(newBlock);
            this.activeId = id;
            this.initTransformables();
        },

        removeBlock(id) {
            this.blocks = this.blocks.filter(b => b.id !== id);
            if (this.blocks.length === 0) {
                this.addBlock('text');
            }
            this.activeId = null;
            this.editingTextId = null;
        },

        enableTextEdit(id, event) {
            this.editingTextId = id;
            this.activeId = id;
            this.$nextTick(() => {
                if (event && event.target) {
                    event.target.focus();
                }
            });
        },

        deselectAll() {
            this.activeId = null;
            this.editingTextId = null;
        },

        submitToLaravel() {
            if (!this.title.trim()) {
                alert('Ingresa al menos un título para tu blog.');
                return;
            }
            this.published = true;
            document.getElementById('laravel_title').value = this.title;
            document.getElementById('laravel_subtitle').value = this.subtitle;
            document.getElementById('laravel_artist').value = this.showArtist;
            document.getElementById('laravel_venue').value = this.showVenue;
            document.getElementById('laravel_content').value = this.blocksJson;
            document.getElementById('laravel_styles').value = this.stylesJson;

            setTimeout(() => {
                document.getElementById('realBlogForm').submit();
            }, 600);
        },

        handleCoverUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.coverImg = URL.createObjectURL(file);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('laravel_cover').files = dataTransfer.files;
            }
        },

        handleBgImgUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.bgImg = URL.createObjectURL(file);
                this.bgType = 'image';
            }
        },

        handleMediaUpload(e) {
            const file = e.target.files[0];
            if (file && this.pendingMediaId) {
                const b = this.blocks.find(x => x.id === this.pendingMediaId);
                if (b) {
                    b.content = URL.createObjectURL(file);
                    b.mediaType = file.type.startsWith('video') ? 'video' : 'image';
                }
                this.pendingMediaId = null;
            }
        }
    }"
    x-init="initTransformables()"
    class="fixed inset-0 z-50 flex flex-col bg-[#0A0A0F] text-[#F5F5F7] font-sans select-none"
>
    <!-- Inputs ocultos -->
    <input type="file" x-ref="coverRef" accept="image/*" class="hidden" @change="handleCoverUpload">
    <input type="file" x-ref="bgImgRef" accept="image/*" class="hidden" @change="handleBgImgUpload">
    <input type="file" x-ref="mediaBlockRef" accept="image/*,video/mp4,video/webm,image/gif" class="hidden" @change="handleMediaUpload">

    <!-- BARRA SUPERIOR FIJA -->
    <div class="flex items-center justify-between px-4 sm:px-6 py-3 flex-shrink-0 z-30 border-b border-white/10 bg-[#0A0A0F] text-[#F5F5F7]">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-sm font-medium hover:opacity-70 transition-opacity text-white/70">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Volver
            </a>
            <div class="w-px h-4 bg-white/15"></div>
            <span class="text-xs font-bold tracking-widest uppercase text-[#FF3D57]">Blog Studio · ENCONCIERTA</span>
        </div>
        
        <div class="flex items-center gap-2">
            <button 
                type="button"
                @click="showPanel = !showPanel"
                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all border cursor-pointer"
                :class="showPanel ? 'bg-[#FF3D57]/20 text-[#FF3D57] border-[#FF3D57]/40' : 'bg-white/5 text-[#9A9AA5] border-white/10 hover:bg-white/10'"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Personalizar</span>
            </button>

            <button 
                type="button"
                @click="submitToLaravel"
                :disabled="published"
                class="px-4 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all hover:brightness-110 text-white cursor-pointer bg-[#FF3D57] shadow-[0_0_20px_rgba(255,61,87,0.4)]"
            >
                <span x-show="published" class="flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Guardando...
                </span>
                <span x-show="!published" class="flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Publicar blog
                </span>
            </button>
        </div>
    </div>

    <!-- CANVAS Y PANEL DERECHO -->
    <div class="flex flex-1 min-h-0 overflow-hidden relative">
        
        <!-- COLUMNA VERTICAL IZQUIERDA DE ACCESOS RÁPIDOS -->
        <div class="w-16 flex-shrink-0 bg-[#0A0A0F] border-r border-white/10 z-20 flex flex-col items-center py-4 gap-3">
            <span class="text-[9px] font-bold text-white/40 uppercase tracking-tighter mb-1">Añadir</span>
            
            <button type="button" @click="addBlock('text')" title="Texto" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-[#FF3D57]/20 hover:text-[#FF3D57] flex items-center justify-center transition-colors text-white text-xs font-bold cursor-pointer border border-white/5">
                T
            </button>
            <button type="button" @click="addBlock('media')" title="Imagen, Video o GIF" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-[#FF3D57]/20 hover:text-[#FF3D57] flex items-center justify-center transition-colors text-white cursor-pointer border border-white/5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </button>
            <button type="button" @click="addBlock('quote')" title="Cita" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-[#FF3D57]/20 hover:text-[#FF3D57] flex items-center justify-center transition-colors text-white text-base font-bold cursor-pointer border border-white/5">
                “
            </button>
            <button type="button" @click="panelTab = 'stickers'; showPanel = true" title="Stickers" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-[#FF3D57]/20 hover:text-[#FF3D57] flex items-center justify-center transition-colors text-white text-lg cursor-pointer border border-white/5">
                🔥
            </button>
            <button type="button" @click="addBlock('divider')" title="Divisor" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-[#FF3D57]/20 hover:text-[#FF3D57] flex items-center justify-center transition-colors text-white cursor-pointer border border-white/5">
                ―
            </button>
        </div>

        <!-- LIENZO PRINCIPAL -->
        <div class="flex-1 overflow-y-auto relative transition-all" :style="{ background: canvasBg }" @click="deselectAll()">
            <div 
                class="absolute inset-0 pointer-events-none transition-all" 
                :style="{ 
                    backgroundColor: bgType === 'image' ? `rgba(0,0,0, ${bgOverlayOpacity / 100})` : 'transparent',
                    backdropFilter: bgBlur > 0 ? `blur(${bgBlur}px)` : 'none'
                }"
            ></div>

            <div 
                id="blogCanvasInner"
                class="mx-auto w-full transition-all relative z-10 pb-32 min-h-[850px]" 
                :class="canvasPadding"
                :style="{ maxWidth: canvasWidth, fontFamily: globalFont, letterSpacing: letterSpacing }"
            >
                <!-- CABECERA ESTÁTICA -->
                <div @click.stop>
                    <div 
                        class="relative rounded-2xl overflow-hidden mb-8 group cursor-pointer transition-all duration-300 shadow-2xl"
                        :style="{ 
                            height: coverImg ? '360px' : '160px', 
                            background: coverImg ? 'transparent' : canvasAccentColor + '12', 
                            border: '2px dashed ' + canvasAccentColor + '40' 
                        }"
                        @click.stop="$refs.coverRef.click()"
                    >
                        <img x-show="coverImg" :src="coverImg" alt="Cover" class="w-full h-full object-cover">
                        
                        <div 
                            class="absolute inset-0 flex flex-col items-center justify-center gap-2 transition-opacity"
                            :class="coverImg ? 'opacity-0 group-hover:opacity-100 bg-black/60 backdrop-blur-sm' : 'opacity-100 bg-transparent'"
                        >
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" :stroke="coverImg ? 'white' : canvasAccentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            <span class="text-sm font-semibold" :style="{ color: coverImg ? 'white' : canvasAccentColor }" x-text="coverImg ? 'Cambiar foto de portada' : 'Añadir foto de portada'"></span>
                        </div>

                        <button 
                            x-show="coverImg"
                            @click.stop="coverImg = null; document.getElementById('laravel_cover').value = '';" 
                            class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all bg-black/70 text-white hover:bg-red-500 cursor-pointer"
                        >
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <div x-show="showArtist || showVenue" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold mb-6 border shadow-lg backdrop-blur-md" :style="{ background: canvasAccentColor + '22', color: canvasAccentColor, borderColor: canvasAccentColor + '40' }">
                        <span>♪</span>
                        <span x-text="showArtist + (showVenue ? ' · ' + showVenue : '')"></span>
                    </div>

                    <!-- TÍTULO -->
                    <div class="w-full mb-3">
                        <input 
                            type="text"
                            x-model="title"
                            placeholder="Título" 
                            class="w-full bg-transparent font-extrabold leading-tight placeholder-current block outline-none transition-all"
                            :style="{ 
                                fontSize: 'clamp(32px,5vw,56px)', 
                                color: title ? titleColor : titleColor + '40', 
                                caretColor: canvasAccentColor,
                                textShadow: titleShadowStyle
                            }"
                        >
                    </div>

                    <!-- SUBTÍTULO -->
                    <div class="w-full mb-8 pb-4" :style="{ borderBottom: '1px solid ' + canvasAccentColor + '25' }">
                        <input 
                            type="text"
                            x-model="subtitle"
                            placeholder="Subtítulo" 
                            class="w-full bg-transparent text-xl placeholder-current block outline-none transition-all"
                            :style="{ color: subtitle ? textColor + 'DD' : textColor + '40', caretColor: canvasAccentColor }"
                        >
                    </div>
                </div>

                <!-- ZONA LIBRE -->
                <div class="relative w-full min-h-[600px]" @click.stop>
                    
                    <template x-for="(block, idx) in blocks" :key="block.id">
                        <div 
                            :data-id="block.id"
                            class="transform-block absolute rounded-xl transition-shadow group/block cursor-move"
                            :class="activeId === block.id ? 'ring-2 ring-[#FF3D57] shadow-2xl z-30' : 'z-10 hover:ring-1 hover:ring-white/20'"
                            :style="{ 
                                transform: `translate(${block.x || 0}px, ${block.y || 0}px)`,
                                width: block.w || '100%',
                                height: block.h || 'auto'
                            }"
                            @click.stop="activeId = block.id"
                        >
                            <!-- TOOLBAR CONTEXTUAL -->
                            <div 
                                x-show="activeId === block.id" 
                                class="absolute -top-11 left-0 z-50 flex items-center gap-1.5 p-1 rounded-xl bg-[#12121A] border border-white/15 shadow-2xl text-xs font-sans text-white backdrop-blur-md"
                            >
                                <button type="button" @click="block.bold = !block.bold" class="px-2 py-1 font-bold rounded cursor-pointer hover:bg-white/10" :class="block.bold ? 'text-[#FF3D57]' : ''">B</button>
                                <button type="button" @click="block.italic = !block.italic" class="px-2 py-1 font-bold rounded cursor-pointer hover:bg-white/10" :class="block.italic ? 'text-[#FF3D57]' : ''">I</button>
                                <div class="w-px h-3 bg-white/20"></div>
                                <button type="button" @click="block.fontSize = Math.max(10, (block.fontSize || 16) - 2)" class="px-1.5 py-0.5 rounded bg-white/10 hover:bg-white/20 cursor-pointer">−</button>
                                <span class="font-mono text-[10px]" x-text="(block.fontSize || 16) + 'px'"></span>
                                <button type="button" @click="block.fontSize = Math.min(72, (block.fontSize || 16) + 2)" class="px-1.5 py-0.5 rounded bg-white/10 hover:bg-white/20 cursor-pointer">+</button>
                                <div class="w-px h-3 bg-white/20"></div>
                                <button type="button" @click="removeBlock(block.id)" class="px-2 py-0.5 bg-[#FF3D57]/20 text-[#FF3D57] font-bold rounded hover:bg-[#FF3D57] hover:text-white cursor-pointer transition-colors text-[10px]">Eliminar</button>
                            </div>

                            <!-- PUNTOS DE ESCALA -->
                            <div x-show="activeId === block.id" class="absolute -top-1.5 -left-1.5 w-3 h-3 bg-[#FF3D57] border-2 border-white rounded-full z-40 cursor-nwse-resize shadow-md"></div>
                            <div x-show="activeId === block.id" class="absolute -top-1.5 -right-1.5 w-3 h-3 bg-[#FF3D57] border-2 border-white rounded-full z-40 cursor-nesw-resize shadow-md"></div>
                            <div x-show="activeId === block.id" class="absolute -bottom-1.5 -left-1.5 w-3 h-3 bg-[#FF3D57] border-2 border-white rounded-full z-40 cursor-nesw-resize shadow-md"></div>
                            <div x-show="activeId === block.id" class="absolute -bottom-1.5 -right-1.5 w-3 h-3 bg-[#FF3D57] border-2 border-white rounded-full z-40 cursor-nwse-resize shadow-md"></div>

                            <!-- BLOQUE TEXTO -->
                            <div x-show="block.type === 'text'" class="w-full h-full" @dblclick="enableTextEdit(block.id, $event)">
                                <textarea 
                                    x-model="block.content" 
                                    placeholder="Doble clic para escribir…"
                                    class="w-full h-full bg-transparent resize-none placeholder-current outline-none overflow-hidden block transition-all"
                                    :class="editingTextId === block.id ? 'cursor-text ring-1 ring-white/30 bg-black/20' : 'cursor-pointer'"
                                    :readonly="editingTextId !== block.id"
                                    :style="{ 
                                        color: block.color || textColor, 
                                        fontFamily: block.font || globalFont, 
                                        fontSize: (block.fontSize || 16) + 'px', 
                                        lineHeight: lineHeight,
                                        textAlign: block.align || 'left', 
                                        fontWeight: block.bold ? 700 : 400, 
                                        fontStyle: block.italic ? 'italic' : 'normal', 
                                        background: block.bg || 'transparent', 
                                        borderRadius: '12px', 
                                        padding: '8px'
                                    }"
                                ></textarea>
                            </div>

                            <!-- BLOQUE MEDIA (IMAGEN, GIF, VIDEO SILENCIADO POR DEFECTO) -->
                            <div x-show="block.type === 'media'" class="w-full h-full overflow-hidden rounded-2xl flex items-center justify-center bg-black/40">
                                <template x-if="block.content">
                                    <template x-if="block.mediaType === 'video'">
                                        <video :src="block.content" autoplay muted loop playsinline controls class="w-full h-full object-cover"></video>
                                    </template>
                                    <template x-if="block.mediaType !== 'video'">
                                        <img :src="block.content" class="w-full h-full object-cover pointer-events-none">
                                    </template>
                                </template>
                                <template x-if="!block.content">
                                    <button 
                                        type="button"
                                        @click="pendingMediaId = block.id; $refs.mediaBlockRef.click()"
                                        class="w-full h-full flex flex-col items-center justify-center gap-2 rounded-2xl bg-[#FF3D57]/10 border-2 border-dashed border-[#FF3D57]/40 text-[#FF3D57] hover:bg-[#FF3D57]/20 cursor-pointer"
                                    >
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <span class="text-xs font-bold">Imagen, Video o GIF</span>
                                    </button>
                                </template>
                            </div>

                            <!-- BLOQUE STICKER -->
                            <div x-show="block.type === 'sticker'" class="w-full h-full flex items-center justify-center">
                                <span class="select-none leading-none" :style="{ fontSize: (block.fontSize || 64) + 'px' }" x-text="block.content || '🔥'"></span>
                            </div>

                            <!-- BLOQUE CITA -->
                            <div x-show="block.type === 'quote'" class="w-full h-full border-l-4 pl-4" :style="{ borderColor: canvasAccentColor }" @dblclick="enableTextEdit(block.id, $event)">
                                <textarea 
                                    x-model="block.content" 
                                    placeholder="Doble clic para escribir cita…"
                                    class="w-full h-full bg-transparent resize-none italic placeholder-current outline-none overflow-hidden block"
                                    :readonly="editingTextId !== block.id"
                                    :style="{ color: block.color || textColor, fontSize: (block.fontSize || 18) + 'px', lineHeight: lineHeight }"
                                ></textarea>
                            </div>

                            <!-- BLOQUE DIVISOR -->
                            <div x-show="block.type === 'divider'" class="w-full h-full flex items-center gap-3">
                                <div class="flex-1 h-px" :style="{ background: canvasAccentColor + '30' }"></div>
                                <div class="w-2.5 h-2.5 rounded-full" :style="{ background: canvasAccentColor }"></div>
                                <div class="flex-1 h-px" :style="{ background: canvasAccentColor + '30' }"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- PANEL LATERAL DE PERSONALIZACIÓN -->
        <div x-show="showPanel" class="w-80 flex-shrink-0 flex flex-col overflow-hidden bg-[#0A0A0F] border-l border-white/10 z-20 backdrop-blur-xl font-sans text-[#F5F5F7]">
            <div class="flex flex-shrink-0 border-b border-white/10 overflow-x-auto no-scrollbar">
                <template x-for="t in ['fondo', 'texto', 'disposición', 'stickers']" :key="t">
                    <button 
                        type="button"
                        @click="panelTab = t"
                        class="flex-1 py-3 px-2 text-[10px] font-bold tracking-wider uppercase transition-all border-b-2 whitespace-nowrap cursor-pointer"
                        :class="panelTab === t ? 'text-[#FF3D57] border-[#FF3D57]' : 'text-[#6A6A75] border-transparent hover:text-white'"
                        x-text="t"
                    ></button>
                </template>
            </div>

            <div class="flex-1 overflow-y-auto p-5 flex flex-col gap-6">
                <!-- PESTAÑA FONDO -->
                <div x-show="panelTab === 'fondo'" class="flex flex-col gap-5">
                    <div>
                        <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Tipo de fondo</label>
                        <div class="flex rounded-xl overflow-hidden border border-white/10 p-0.5 bg-white/5">
                            <template x-for="t in ['solid', 'gradient', 'image']" :key="t">
                                <button 
                                    type="button"
                                    @click="bgType = t" 
                                    class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all capitalize cursor-pointer"
                                    :class="bgType === t ? 'bg-[#FF3D57]/30 text-[#FF3D57]' : 'text-[#6A6A75] hover:text-white'"
                                    x-text="t === 'solid' ? 'Sólido' : (t === 'gradient' ? 'Gradiente' : 'Imagen')"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <div x-show="bgType === 'solid'" class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-white/80 font-medium">Color de lienzo</span>
                            <label class="relative cursor-pointer">
                                <div class="w-8 h-8 rounded-xl border border-white/30" :style="{ background: bgColor }"></div>
                                <input type="color" x-model="bgColor" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            </label>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Paletas recomendadas</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="c in bgPresets" :key="c">
                                    <button type="button" @click="bgColor = c" class="w-7 h-7 rounded-lg border transition-transform hover:scale-110 cursor-pointer" :style="{ background: c, borderColor: bgColor === c ? '#FF3D57' : 'rgba(255,255,255,0.2)' }"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="bgType === 'gradient'">
                        <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Gradientes escénicos</label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="g in gradPresets" :key="g">
                                <button type="button" @click="bgGrad = g" class="h-12 rounded-xl border transition-transform hover:scale-105 cursor-pointer" :style="{ background: g, borderColor: bgGrad === g ? '#FF3D57' : 'transparent' }"></button>
                            </template>
                        </div>
                    </div>

                    <div x-show="bgType === 'image'" class="flex flex-col gap-4">
                        <button type="button" @click="$refs.bgImgRef.click()" class="w-full py-5 rounded-xl text-xs font-semibold flex flex-col items-center justify-center gap-2 border border-dashed border-[#FF3D57]/50 bg-[#FF3D57]/10 text-[#FF3D57] transition-all cursor-pointer hover:bg-[#FF3D57]/20">
                            <span class="bg-black/60 px-3 py-1.5 rounded-lg text-white font-bold" x-text="bgImg ? 'Cambiar imagen de fondo' : 'Subir imagen de fondo'"></span>
                        </button>
                        <div>
                            <div class="flex justify-between text-xs mb-1 text-white/80"><span>Oscurecer fondo</span><span x-text="bgOverlayOpacity + '%'"></span></div>
                            <input type="range" x-model="bgOverlayOpacity" min="0" max="90" class="w-full accent-[#FF3D57]">
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1 text-white/80"><span>Desenfoque (Blur)</span><span x-text="bgBlur + 'px'"></span></div>
                            <input type="range" x-model="bgBlur" min="0" max="20" class="w-full accent-[#FF3D57]">
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-white/10 pt-4">
                        <span class="text-xs text-white/80 font-medium">Color de acento del blog</span>
                        <label class="relative cursor-pointer">
                            <div class="w-8 h-8 rounded-xl border border-white/30" :style="{ background: canvasAccentColor }"></div>
                            <input type="color" x-model="canvasAccentColor" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        </label>
                    </div>
                </div>

                <!-- PESTAÑA TEXTO -->
                <div x-show="panelTab === 'texto'" class="flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-white/80">Color del título</span>
                        <label class="relative cursor-pointer">
                            <div class="w-7 h-7 rounded-lg border border-white/30" :style="{ background: titleColor }"></div>
                            <input type="color" x-model="titleColor" class="absolute inset-0 opacity-0 cursor-pointer">
                        </label>
                    </div>

                    <!-- CONTROL AVANZADO DE SOMBRA DE TÍTULO -->
                    <div class="flex flex-col gap-3 border-t border-white/10 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold tracking-widest uppercase text-[#6A6A75]">Sombra de Título</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="enableTitleShadow" class="sr-only peer">
                                <div class="w-9 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF3D57]"></div>
                            </label>
                        </div>
                        
                        <div x-show="enableTitleShadow" class="flex flex-col gap-3 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-white/80">Color de sombra</span>
                                <label class="relative cursor-pointer">
                                    <div class="w-7 h-7 rounded-lg border border-white/30" :style="{ background: shadowColor }"></div>
                                    <input type="color" x-model="shadowColor" class="absolute inset-0 opacity-0 cursor-pointer">
                                </label>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1 text-white/80"><span>Desplazamiento X</span><span x-text="shadowX + 'px'"></span></div>
                                <input type="range" x-model="shadowX" min="-30" max="30" class="w-full accent-[#FF3D57]">
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1 text-white/80"><span>Desplazamiento Y</span><span x-text="shadowY + 'px'"></span></div>
                                <input type="range" x-model="shadowY" min="-30" max="30" class="w-full accent-[#FF3D57]">
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1 text-white/80"><span>Difuminado (Blur)</span><span x-text="shadowBlur + 'px'"></span></div>
                                <input type="range" x-model="shadowBlur" min="0" max="50" class="w-full accent-[#FF3D57]">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-white/10 pt-4">
                        <span class="text-xs text-white/80">Color del cuerpo</span>
                        <label class="relative cursor-pointer">
                            <div class="w-7 h-7 rounded-lg border border-white/30" :style="{ background: textColor }"></div>
                            <input type="color" x-model="textColor" class="absolute inset-0 opacity-0 cursor-pointer">
                        </label>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 text-white/80"><span>Interlineado</span><span x-text="lineHeight"></span></div>
                        <input type="range" x-model="lineHeight" min="1.2" max="2.4" step="0.1" class="w-full accent-[#FF3D57]">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Tipografía base</label>
                        <div class="flex flex-col gap-1.5">
                            <template x-for="f in fonts" :key="f.id">
                                <button type="button" @click="globalFont = f.css" class="flex items-center justify-between px-3 py-2 rounded-xl border text-left cursor-pointer transition-all" :class="globalFont === f.css ? 'bg-[#FF3D57]/20 border-[#FF3D57]/50' : 'bg-white/5 border-transparent hover:bg-white/10'">
                                    <span :style="{ fontFamily: f.css }" class="text-xs text-white" x-text="f.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA DISPOSICIÓN -->
                <div x-show="panelTab === 'disposición'" class="flex flex-col gap-5">
                    <div>
                        <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Ancho del lienzo</label>
                        <div class="flex rounded-xl overflow-hidden border border-white/10 p-0.5 bg-white/5">
                            <button type="button" @click="canvasWidth = '640px'" class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="canvasWidth === '640px' ? 'bg-[#FF3D57]/30 text-[#FF3D57]' : 'text-[#6A6A75]'">Compacto</button>
                            <button type="button" @click="canvasWidth = '780px'" class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="canvasWidth === '780px' ? 'bg-[#FF3D57]/30 text-[#FF3D57]' : 'text-[#6A6A75]'">Estándar</button>
                            <button type="button" @click="canvasWidth = '900px'" class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all" :class="canvasWidth === '900px' ? 'bg-[#FF3D57]/30 text-[#FF3D57]' : 'text-[#6A6A75]'">Amplio</button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold tracking-widest uppercase mb-2 block text-[#6A6A75]">Ficha técnica del evento</label>
                        <div class="flex flex-col gap-2">
                            <input type="text" x-model="showArtist" placeholder="Artista / Banda principal" class="w-full text-xs rounded-xl px-3.5 py-2.5 bg-white/5 text-white border border-white/10 outline-none focus:border-[#FF3D57]">
                            <input type="text" x-model="showVenue" placeholder="Venue · Lugar · Ciudad" class="w-full text-xs rounded-xl px-3.5 py-2.5 bg-white/5 text-white border border-white/10 outline-none focus:border-[#FF3D57]">
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA STICKERS -->
                <div x-show="panelTab === 'stickers'">
                    <label class="text-[10px] font-extrabold tracking-widest uppercase mb-3 block text-[#6A6A75]">Añadir sticker libre al canvas</label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="st in stickersList" :key="st">
                            <button type="button" @click="addBlock('sticker', st)" class="text-2xl p-3 rounded-xl bg-white/5 hover:bg-white/15 transition-transform hover:scale-110 flex items-center justify-center border border-white/5 cursor-pointer" x-text="st"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>