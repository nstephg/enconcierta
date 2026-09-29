@extends('layouts.dashboard')

@section('title', 'Momento y Comentarios — ENCONCIERTA')
@section('page-title', 'Momento')

@section('content')
@php
    $prevUrl = url()->previous();
    $currUrl = url()->current();
    $backUrl = ($prevUrl && $prevUrl !== $currUrl) ? $prevUrl : route('dashboard');

    $showTitle = !empty($post->show_nombre) ? $post->show_nombre : ($post->evento ? $post->evento->artista : null);
    $showPlace = $post->evento ? ($post->evento->lugar . ' · ' . $post->evento->ciudad) : $post->ubicacion;
    $showDate = $post->evento ? \Carbon\Carbon::parse($post->evento->fecha)->format('D d M') : ($post->created_at ? $post->created_at->format('d M') : null);
    $hasLiked = $post->isLikedBy(auth()->id());
    $likesCount = $post->likes->count();
    $mediaList = $post->media_list;
    $commentsCount = $post->comentarios->count();

    $authorId = $post->user->id_usuario ?? $post->id_usuario ?? null;
    $authorAvatar = $post->user ? $post->user->avatar_url : asset('images/default-avatar.svg');
    $currentUserAvatar = auth()->user()->avatar_url;

    $encuesta = $post->encuesta;
    $totalVotos = 0;
    $userVotedIdx = null;
    if (!empty($encuesta) && is_array($encuesta)) {
        foreach ($encuesta['opciones'] ?? [] as $opt) {
            $totalVotos += ($opt['votos'] ?? 0);
        }
        $userVotedIdx = $encuesta['votos_usuarios'][auth()->id()] ?? null;
    }
@endphp

<script>
    if (typeof window.highlightMentions === 'undefined') {
        window.highlightMentions = function(text) {
            if (!text) return '';
            var escaped = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            // Se elimina font-semibold para evitar desfasar el ancho de los caracteres y deslinear el cursor
            var highlighted = escaped.replace(/@[a-zA-Z0-9_]+/g, '<span class="text-[#7C5CFF]">$&</span>');
            if (text.endsWith('\n')) {
                highlighted += '<br>&nbsp;';
            }
            return highlighted;
        };
    }
</script>

<div class="p-2 sm:p-6 flex items-start sm:items-center justify-center min-h-[calc(100vh-80px)] w-full pb-20 md:pb-6">
    <main class="relative z-10 w-full max-w-5xl bg-[#08080C] border border-white/10 rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col md:flex-row md:h-[85vh] md:max-h-[850px] overflow-hidden">
        
        <!-- PANEL IZQUIERDO: POST COMPLETO -->
        <section class="w-full md:w-[480px] lg:w-[520px] md:flex-shrink-0 border-b md:border-b-0 md:border-r border-white/10 flex flex-col md:overflow-y-auto bg-[#08080C] no-scrollbar">
            
            <div class="px-5 pt-4 pb-1 flex items-center justify-between flex-shrink-0">
                <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#9A9AA5] hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="p-5 pb-3 flex items-center justify-between flex-shrink-0">
                <a href="{{ route('profile.show', $authorId) }}" class="flex items-center gap-3 group">
                    <img src="{{ $authorAvatar }}" alt="{{ $post->user->nombre ?? 'Melómano' }}" class="w-10 h-10 rounded-full object-cover border border-white/20 group-hover:border-[#FF3D57] transition-all">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h2 class="text-sm font-bold text-white leading-tight group-hover:text-[#FF3D57] transition-colors">{{ $post->user->nombre ?? 'Melómano' }}</h2>
                            @if($post->user?->es_verificado)
                                <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs" title="Melómano Verificado"></i>
                            @endif
                        </div>
                        <p class="text-xs text-[#9A9AA5]">{{ $post->user->handle ? '@'.$post->user->handle : 'melomano' }} · {{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            </div>

            <!-- TICKET DE SHOW -->
            @if(!empty($showTitle))
                <div class="mx-5 mb-4 rounded-2xl overflow-hidden border border-[#FF3D57]/30 bg-[#120B13] flex items-stretch flex-shrink-0 relative">
                    <div class="w-1.5 bg-[#FF3D57]"></div>
                    <div class="p-3.5 flex-1 min-w-0 pr-4">
                        <h3 class="font-headline text-2xl text-white uppercase tracking-wide leading-none mb-1.5">{{ $showTitle }}</h3>
                        @if(!empty($showPlace))
                            <div class="flex items-center gap-2 text-xs text-[#9A9AA5] truncate">
                                <i class="fa-solid fa-location-dot text-[#9A9AA5]"></i>
                                <span class="truncate">{{ $showPlace }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="border-r border-dashed border-white/20 my-2"></div>
                    <div class="w-20 flex flex-col justify-center items-center p-2 text-center flex-shrink-0">
                        <span class="text-[10px] font-bold uppercase text-[#FF3D57] tracking-wider">SHOW</span>
                        <span class="text-xs font-bold text-white">{{ $showDate }}</span>
                    </div>
                </div>
            @endif

            <div class="px-5 mb-4 text-xs sm:text-sm text-[#9A9AA5] leading-relaxed flex-shrink-0 space-y-2">
                <p class="text-[#F5F5F7] whitespace-pre-line break-words">{!! format_mentions($post->contenido) !!}</p>

                @if(!empty($post->ubicacion))
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.03] text-[#2FE6D0] text-xs font-medium border border-[#2FE6D0]/20">
                        <i class="fa-solid fa-location-dot text-[11px]"></i>
                        <span>{{ $post->ubicacion }}</span>
                    </div>
                @endif
            </div>

            <!-- Encuesta Funcional -->
            @if(!empty($encuesta) && is_array($encuesta))
                <div class="mx-5 mb-4 p-3.5 rounded-xl bg-[#17171F] border border-[#7C5CFF]/30 space-y-2 flex-shrink-0" id="poll-box-detail-{{ $post->id_publicacion }}">
                    <div class="text-xs font-semibold text-white flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-chart-simple text-[#7C5CFF]"></i> {{ $encuesta['pregunta'] ?? 'Encuesta' }}</span>
                        <span class="text-[10px] text-[#9A9AA5] font-mono-code total-votes">{{ $totalVotos }} votos</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($encuesta['opciones'] ?? [] as $idx => $opcion)
                            @php
                                $votosOp = $opcion['votos'] ?? 0;
                                $pct = $totalVotos > 0 ? round(($votosOp / $totalVotos) * 100) : 0;
                                $isSelected = ($userVotedIdx !== null && (int)$userVotedIdx === $idx);
                            @endphp
                            <button type="button" onclick="submitPollVote({{ $post->id_publicacion }}, {{ $idx }})" class="w-full text-left relative overflow-hidden rounded-xl border p-2.5 transition-all text-xs flex items-center justify-between cursor-pointer {{ $isSelected ? 'border-[#7C5CFF] bg-[#7C5CFF]/10' : 'border-white/10 bg-[#0A0A0F] hover:border-white/20' }}">
                                <div class="absolute top-0 bottom-0 left-0 bg-[#7C5CFF]/20 transition-all duration-500 rounded-r-lg" style="width: {{ $pct }}%"></div>
                                <span class="relative z-10 font-medium text-white flex items-center gap-2">
                                    {{ $opcion['texto'] }}
                                    @if($isSelected)
                                        <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs"></i>
                                    @endif
                                </span>
                                <span class="relative z-10 font-mono-code text-[11px] text-[#9A9AA5]">{{ $pct }}%</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Galería de Medios -->
            @if(count($mediaList) === 1)
                <div class="mx-5 mb-4 rounded-2xl overflow-hidden border border-white/10 flex-shrink-0 md:flex-1 max-h-72 sm:max-h-80 md:max-h-[520px] bg-[#0A0A0F] flex items-center justify-center min-h-[220px]">
                    @if(($mediaList[0]['type'] ?? 'image') === 'video')
                        <video src="{{ $mediaList[0]['url'] }}" autoplay muted playsinline loop controls class="w-full h-full max-h-72 sm:max-h-80 md:max-h-[520px] object-cover rounded-2xl"></video>
                    @else
                        <img src="{{ $mediaList[0]['url'] }}" alt="Momento" class="w-full h-full object-contain">
                    @endif
                </div>
            @elseif(count($mediaList) > 1)
                <div class="mx-5 mb-4 flex gap-2 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-1 md:flex-1 items-center flex-shrink-0">
                    @foreach($mediaList as $media)
                        <div class="snap-start flex-shrink-0 w-64 h-52 sm:h-56 md:h-80 rounded-2xl overflow-hidden border border-white/10 bg-[#0A0A0F] flex items-center justify-center">
                            @if(($media['type'] ?? 'image') === 'video')
                                <video src="{{ $media['url'] }}" autoplay muted playsinline loop controls class="w-full h-full object-cover rounded-2xl"></video>
                            @else
                                <img src="{{ $media['url'] }}" alt="Momento" class="w-full h-full object-contain">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-auto px-5 py-4 border-t border-white/10 flex items-center justify-between text-xs font-medium text-[#9A9AA5] flex-shrink-0 bg-[#08080C]">
                <button id="vibroBtn" onclick="togglePostLike({{ $post->id_publicacion }}, this)" class="flex items-center gap-2 {{ $hasLiked ? 'text-[#FF3D57] liked' : 'text-[#9A9AA5] hover:text-[#FF3D57]' }} transition-all cursor-pointer">
                    <i class="fa-solid fa-fire text-sm"></i>
                    <span class="like-count font-bold text-white">{{ $likesCount }}</span>
                    <span>Vibró</span>
                </button>

                <div class="flex items-center gap-2 text-[#9A9AA5]">
                    <i class="fa-regular fa-comment text-sm"></i>
                    <span class="font-bold text-white">{{ $commentsCount }}</span>
                    <span>Comentarios</span>
                </div>
            </div>
        </section>

        <!-- PANEL DERECHO: SECCIÓN DE COMENTARIOS -->
        <section class="w-full md:flex-1 flex flex-col min-w-0 bg-[#08080C] relative h-full">
            
            <div class="hidden md:flex p-4 sm:p-5 pb-3 border-b border-white/10 items-center justify-between flex-shrink-0 bg-[#08080C]">
                <h3 class="text-base sm:text-lg font-bold text-white">{{ $commentsCount }} comentarios</h3>
            </div>

            @php
                $myFollowingUsers = auth()->check() 
                    ? auth()->user()->following()->select('users.id_usuario', 'users.nombre', 'users.handle', 'users.avatar')->get()->map(fn($u) => [
                        'id' => $u->id_usuario,
                        'nombre' => $u->nombre,
                        'handle' => $u->handle,
                        'avatar' => $u->avatar_url
                    ]) 
                    : collect();
            @endphp

            <div class="p-3 sm:p-4 border-b border-white/10 flex-shrink-0 bg-[#08080C] z-20"
                x-data="{ 
                    openCommentMentions: false, 
                    query: '', 
                    commentText: '',
                    following: @js($myFollowingUsers),
                    get filteredFollowing() {
                        if (!this.query) return this.following;
                        const q = this.query.toLowerCase();
                        return this.following.filter(u => u.nombre.toLowerCase().includes(q) || u.handle.toLowerCase().includes(q));
                    },
                    handleInput(e) {
                        this.commentText = e.target.value;
                        const pos = e.target.selectionStart;
                        const textBefore = this.commentText.substring(0, pos);
                        const match = textBefore.match(/@([a-zA-Z0-9_]*)$/);
                        if (match) {
                            this.openCommentMentions = true;
                            this.query = match[1];
                        } else {
                            this.openCommentMentions = false;
                        }
                    },
                    handleBackspace(e) {
                        const el = e.target;
                        const val = el.value;
                        const pos = el.selectionStart;
                        if (pos !== el.selectionEnd) return;
                        const textBefore = val.substring(0, pos);
                        const match = textBefore.match(/@([a-zA-Z0-9_]+)$/);
                        if (match) {
                            e.preventDefault();
                            const newTextBefore = textBefore.substring(0, match.index);
                            const textAfter = val.substring(pos);
                            el.value = newTextBefore + textAfter;
                            this.commentText = el.value;
                            el.selectionStart = el.selectionEnd = newTextBefore.length;
                            this.openCommentMentions = false;
                        }
                    },
                    insertMention(handle) {
                        const input = document.getElementById('commentInput');
                        if (!input) return;
                        
                        const cursorPos = input.selectionStart;
                        const textBefore = input.value.substring(0, cursorPos);
                        const textAfter = input.value.substring(cursorPos);
                        
                        const lastAt = textBefore.lastIndexOf('@');
                        if (lastAt !== -1) {
                            input.value = textBefore.substring(0, lastAt) + '@' + handle + ' ' + textAfter;
                        } else {
                            input.value = textBefore + '@' + handle + ' ' + textAfter;
                        }
                        
                        this.commentText = input.value;
                        this.openCommentMentions = false;
                        input.focus();
                    }
                }">
                <div id="replyingBanner" class="hidden flex items-center justify-between px-3 py-1.5 mb-2 rounded-xl bg-[#7C5CFF]/15 border border-[#7C5CFF]/30 text-xs text-white">
                    <span class="flex items-center gap-1.5 text-[#2FE6D0]">
                        <i class="fa-solid fa-reply"></i> Respondiendo a <strong id="replyUserName"></strong>
                    </span>
                    <button type="button" onclick="cancelReply()" class="text-[#9A9AA5] hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form action="{{ route('posts.comments.store', $post->id_publicacion) }}" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    onsubmit="return handleCommentSubmit(this)" 
                    class="flex flex-col gap-2 relative">
                    @csrf
                    <input type="hidden" name="parent_id" id="parentCommentId" value="">
                    <div id="commentGifInputsContainer"></div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('profile.show') }}" class="flex-shrink-0">
                            <img src="{{ $currentUserAvatar }}" alt="Tu perfil" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border border-white/20 hover:border-[#FF3D57] transition-all">
                        </a>
                        
                        <div class="flex-1 flex items-center gap-1.5 sm:gap-2 bg-[#12121A] border border-white/10 rounded-full px-3.5 sm:px-4 py-2 transition-all relative">
                            <div class="relative flex-1 min-w-0 flex items-center">
                                <!-- Capa espejo con tipografía e interlineado idénticos al input -->
                                <div x-ref="commentBackdrop" 
                                     class="absolute inset-0 pointer-events-none whitespace-nowrap overflow-hidden text-xs sm:text-sm font-sans font-normal leading-normal tracking-normal text-[#F5F5F7] flex items-center select-none p-0 m-0 border-0"
                                     x-html="highlightMentions(commentText)"></div>

                                <input type="text" 
                                       id="commentInput" 
                                       name="contenido" 
                                       maxlength="1000"
                                       x-model="commentText"
                                       @input="handleInput($event)"
                                       @scroll="$refs.commentBackdrop.scrollLeft = $el.scrollLeft"
                                       @keydown.backspace="handleBackspace($event)"
                                       placeholder="Respuesta..." 
                                       class="w-full bg-transparent text-transparent caret-white text-xs sm:text-sm font-sans font-normal leading-normal tracking-normal border-0 outline-none focus:outline-none focus:ring-0 placeholder-[#9A9AA5] relative z-10 p-0 m-0">
                            </div>
                            
                            <div class="flex items-center gap-0.5 sm:gap-1">
                                <button type="button" 
                                        @click="openCommentMentions = !openCommentMentions; query = '';" 
                                        class="p-1 text-[#9A9AA5] hover:text-[#7C5CFF] cursor-pointer" 
                                        title="Mencionar melómano (@)">
                                    <i class="fa-solid fa-at text-xs"></i>
                                </button>

                                <label for="commentImageInput" class="p-1 text-[#9A9AA5] hover:text-[#2FE6D0] cursor-pointer" title="Adjuntar foto o video">
                                    <i class="fa-regular fa-image text-sm"></i>
                                </label>
                                <input type="file" id="commentImageInput" name="imagenes[]" accept="image/*,video/*" multiple class="hidden" onchange="handleCommentImagesSelected(this)">

                                <button type="button" id="btnCommentGifToggle" onclick="toggleCommentGifPicker(event)" class="px-1.5 py-0.5 text-[9px] font-extrabold rounded border border-white/20 text-white/80 hover:border-[#2FE6D0] cursor-pointer" title="Insertar GIF">
                                    GIF
                                </button>

                                <button type="button" id="btnCommentEmojiToggle" onclick="toggleCommentEmojiPicker(event)" class="p-1 text-[#9A9AA5] hover:text-[#FFB020] cursor-pointer" title="Agregar emoji">
                                    <i class="fa-regular fa-face-smile text-sm"></i>
                                </button>
                            </div>

                            <button type="submit" class="text-[#FF3D57] hover:scale-110 transition-transform cursor-pointer flex-shrink-0 p-1 ml-0.5">
                                <i class="fa-regular fa-paper-plane text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- POP-OVER DE MENCIÓN COMENTARIOS (@) -->
                    <div x-show="openCommentMentions" 
                        @click.outside="openCommentMentions = false" 
                        x-cloak 
                        class="absolute top-full mt-2 right-0 sm:right-2 z-[100] w-64 bg-[#17171F] border border-white/10 rounded-2xl p-2.5 shadow-2xl flex flex-col gap-2">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-semibold text-[#7C5CFF] flex items-center gap-1.5">
                                <i class="fa-solid fa-at text-xs"></i> Etiquetar melómano
                            </span>
                            <button type="button" @click="openCommentMentions = false" class="text-[#9A9AA5] hover:text-white text-xs"><i class="fa-solid fa-xmark"></i></button>
                        </div>

                        <div class="flex items-center gap-2 bg-[#0A0A0F] border border-white/10 rounded-xl px-2.5 py-1.5">
                            <i class="fa-solid fa-magnifying-glass text-xs text-[#9A9AA5]"></i>
                            <input type="text" x-model="query" class="flex-1 bg-transparent text-xs text-white outline-none placeholder-[#9A9AA5]" placeholder="Buscar seguidos...">
                        </div>

                        <div class="max-h-44 overflow-y-auto divide-y divide-white/5 scrollbar-thin scrollbar-thumb-white/10">
                            <template x-for="user in filteredFollowing" :key="user.id">
                                <button type="button" 
                                        @click="insertMention(user.handle)" 
                                        class="w-full flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 transition-colors text-left cursor-pointer">
                                    <img :src="user.avatar" class="w-7 h-7 rounded-full object-cover border border-white/10">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold text-white truncate" x-text="user.nombre"></div>
                                        <div class="text-[10px] text-[#9A9AA5] truncate" x-text="'@' + user.handle"></div>
                                    </div>
                                </button>
                            </template>
                            <template x-if="filteredFollowing.length === 0">
                                <div class="text-center text-[#9A9AA5] text-[11px] py-4">No se encontraron melómanos seguidos</div>
                            </template>
                        </div>
                    </div>

                    <!-- EMOJI PICKER -->
                    <div id="commentEmojiPicker" class="hidden absolute top-full mt-2 right-0 sm:right-2 z-[100] rounded-2xl shadow-2xl overflow-hidden border border-white/10 bg-[#17171F] max-h-[310px]">
                        <div class="flex items-center justify-between px-3 py-1.5 border-b border-white/5 bg-[#121218]">
                            <span class="text-[11px] font-semibold text-[#9A9AA5]">Emojis</span>
                            <button type="button" onclick="toggleCommentEmojiPicker(event)" class="text-[#9A9AA5] hover:text-white text-xs"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <emoji-picker class="dark text-xs" style="--num-columns: 8; --emoji-size: 1.15rem; height: 260px; width: 290px;"></emoji-picker>
                    </div>

                    <!-- GIPHY PICKER -->
                    <div id="commentGiphyPicker" class="hidden absolute top-full mt-2 right-0 sm:right-2 z-[100] bg-[#17171F] border border-white/10 rounded-2xl p-3 shadow-2xl w-72 sm:w-[350px] flex flex-col gap-2 max-h-[310px]">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-white flex items-center gap-1">
                                <i class="fa-solid fa-bolt text-[#FFB020]"></i> GIPHY
                            </span>
                            <button type="button" onclick="toggleCommentGifPicker(event)" class="text-[#9A9AA5] hover:text-white text-xs"><i class="fa-solid fa-xmark"></i></button>
                        </div>

                        <div class="flex items-center gap-2 bg-[#0A0A0F] border border-white/10 rounded-xl px-2.5 py-1.5">
                            <i class="fa-solid fa-magnifying-glass text-xs text-[#9A9AA5]"></i>
                            <input type="text" id="commentGiphySearchInput" onkeydown="if(event.key==='Enter'){event.preventDefault();searchCommentGiphy(this.value);}" onkeyup="searchCommentGiphy(this.value)" class="flex-1 bg-transparent text-xs text-white outline-none" placeholder="Buscar GIF...">
                        </div>

                        <div id="commentGiphyResults" class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-44 overflow-y-auto no-scrollbar">
                            <div class="text-center text-[#9A9AA5] text-[11px] col-span-3 py-6">Cargando GIFs...</div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Previsualizador de archivos adjuntos -->
            <div id="commentMediaPreviewContainer" class="hidden px-4 pt-2 flex gap-2 overflow-x-auto no-scrollbar bg-[#08080C] flex-shrink-0"></div>

            <!-- LISTA DE COMENTARIOS -->
            <div id="commentsList" class="flex-1 md:overflow-y-auto p-4 sm:p-5 space-y-5 no-scrollbar min-h-0">
                @forelse($post->comentarios as $comment)
                    <x-comment-item :comment="$comment" :level="0" />
                @empty
                    <div class="text-center py-8 text-[#9A9AA5] text-xs">
                        <i class="fa-regular fa-comments text-2xl mb-1.5 text-[#7C5CFF]/60 block"></i>
                        Sé el primero en comentar esta experiencia.
                    </div>
                @endforelse
            </div>

        </section>
    </main>
</div>
@endsection