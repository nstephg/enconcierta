@props(['comment', 'level' => 0])

@php
    $cAuthorId = $comment->user->id_usuario ?? $comment->id_usuario ?? null;
    $cAvatar = $comment->user ? $comment->user->avatar_url : asset('images/default-avatar.svg');
    $cHasLiked = $comment->isLikedBy(auth()->id());
    $cLikes = $comment->likes->count();
    $cMedia = $comment->media_list;

    // Obtener handle o nombre del usuario al que responde
    $replyToHandle = null;
    if ($level > 0 && $comment->parent && $comment->parent->user) {
        $replyToHandle = !empty($comment->parent->user->handle) 
            ? $comment->parent->user->handle 
            : $comment->parent->user->nombre;
    }
@endphp

<div class="flex items-start gap-2.5 group" id="comment-{{ $comment->id_comentario }}">
    <a href="{{ route('profile.show', $comment->user->id_usuario ?? $comment->id_usuario) }}">
        <img src="{{ $comment->user->avatar_url ?? asset('images/default-avatar.svg') }}" class="w-7 h-7 rounded-full object-cover">
    </a>
    
    <div class="flex-1 min-w-0 space-y-1">
        <div class="flex items-center gap-1.5">
            <a href="{{ route('profile.show', $cAuthorId) }}" class="text-xs font-bold text-white hover:text-[#FF3D57] transition-colors truncate">
                {{ $comment->user->nombre ?? 'Usuario' }}
            </a>
            @if($comment->user?->es_verificado)
                <i class="fa-solid fa-circle-check text-[#7C5CFF] text-[10px]" title="Melómano verificado"></i>
            @endif
            <span class="text-[10px] text-[#9A9AA5] flex-shrink-0">· {{ $comment->created_at->diffForHumans() }}</span>
        </div>

        <p class="text-xs sm:text-sm text-[#F5F5F7] leading-relaxed break-words">
            @if($replyToHandle)
                <span class="text-[#2FE6D0] font-semibold mr-1">@ {{ $replyToHandle }}</span>
            @endif
            {{ $comment->contenido }}
        </p>

        <!-- Galería de Fotos y Videos -->
        @if(!empty($cMedia))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-w-md">
                @foreach($cMedia as $m)
                    <div class="rounded-xl overflow-hidden border border-white/10 bg-[#0A0A0F] flex items-center justify-center max-h-64">
                        @if(($m['type'] ?? 'image') === 'video')
                            <video src="{{ $m['url'] }}" autoplay muted playsinline loop controls class="w-full h-full max-h-64 object-cover rounded-xl"></video>
                        @else
                            <img src="{{ $m['url'] }}" class="w-full h-full max-h-64 object-contain">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center gap-4 pt-0.5 text-xs">
            <button onclick="toggleCommentLike({{ $comment->id_comentario }}, this)" class="{{ $cHasLiked ? 'text-[#FF3D57]' : 'text-[#9A9AA5] hover:text-[#FF3D57]' }} transition-colors flex items-center gap-1.5 font-semibold cursor-pointer">
                <i class="fa-solid fa-fire text-xs"></i>
                <span class="c-like-count">{{ $cLikes }}</span>
            </button>

            <button type="button" onclick="setupReply({{ $comment->id_comentario }}, '{{ e($comment->user->nombre ?? 'Melómano') }}')" class="text-[#9A9AA5] hover:text-[#2FE6D0] font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-reply text-xs"></i> Responder
            </button>
        </div>

        <!-- ESTRUCTURA VERTICAL PLANA ESTILO TIKTOK -->
        @if($level === 0)
            @php 
                $allReplies = $comment->getAllDescendants(); 
                $totalReplies = $allReplies->count();
            @endphp

            @if($totalReplies > 0)
                <div class="mt-2">
                    <button type="button" onclick="toggleCommentReplies({{ $comment->id_comentario }})" class="text-[11px] font-bold text-[#7C5CFF] hover:text-[#2FE6D0] transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" id="icon-reply-{{ $comment->id_comentario }}"></i>
                        <span id="text-reply-{{ $comment->id_comentario }}" data-total="{{ $totalReplies }}">Ver {{ $totalReplies }} {{ $totalReplies === 1 ? 'respuesta' : 'respuestas' }}</span>
                    </button>

                    <!-- Un solo contenedor vertical para TODAS las respuestas -->
                    <div id="replies-wrapper-{{ $comment->id_comentario }}" class="hidden mt-3 pl-3 border-l-2 border-white/10 space-y-4">
                        @foreach($allReplies as $reply)
                            <x-comment-item :comment="$reply" :level="1" />
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>