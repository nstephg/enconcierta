@props(['post'])

@php
    $showTitle = !empty($post->show_nombre) ? $post->show_nombre : ($post->evento ? $post->evento->artista : null);
    $showDetail = $post->evento ? ($post->evento->nombre . ' · ' . $post->evento->lugar) : null;
    $hasLiked = $post->isLikedBy(auth()->id());
    $likesCount = $post->likes->count();
    $commentsCount = $post->comentarios->count();

    $authorId = $post->user->id_usuario ?? $post->id_usuario ?? null;
    $avatarUrl = $post->user ? $post->user->avatar_url : asset('images/default-avatar.svg');

    $mediaList = $post->media_list;

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

<article class="rounded-2xl overflow-hidden bg-[#0C0C13] border border-white/5 hover:border-white/10 transition-all">
    <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
        <a href="{{ route('profile.show', $authorId) }}" class="flex-shrink-0">
            <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover">
        </a>
        <div class="flex items-center gap-1.5 flex-1 min-w-0">
            <a href="{{ route('profile.show', $authorId) }}" class="text-[#F5F5F7] text-sm font-semibold truncate hover:text-[#FF3D57]">
                {{ $post->user->nombre ?? 'Melómano' }}
            </a>
            @if($post->user?->es_verificado)
                <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs" title="Melómano verificado"></i>
            @endif
            @if(!empty($post->user->handle))
                <span class="text-[#9A9AA5] text-xs">({{ '@' . $post->user->handle }})</span>
            @endif
            <span class="text-[#9A9AA5] text-xs">· {{ $post->created_at ? $post->created_at->diffForHumans() : 'Reciente' }}</span>
        </div>
    </div>

    @if(!empty($showTitle))
        <a href="{{ route('posts.show', $post->id_publicacion) }}" class="block mx-4 mb-4 rounded-xl overflow-hidden border border-[#7C5CFF]/30 hover:border-[#7C5CFF]/60 transition-all">
            <div class="flex items-stretch bg-gradient-to-r from-white/[0.03] to-transparent">
                <div class="w-1 flex-shrink-0 bg-[#7C5CFF]"></div>
                <div class="flex-1 px-3 py-2.5 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-headline text-[#F5F5F7] text-lg leading-none truncate">{{ strtoupper($showTitle) }}</span>
                    </div>
                    @if($showDetail)
                        <div class="flex items-center gap-1.5 mt-1.5 text-[#9A9AA5] text-xs">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            <span class="truncate">{{ $showDetail }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </a>
    @endif

    <a href="{{ route('posts.show', $post->id_publicacion) }}" class="block px-4 pb-3 space-y-2">
        <p class="text-[#C8C8D0] text-sm leading-relaxed">{{ $post->contenido }}</p>

        @if(!empty($post->ubicacion))
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.03] text-[#2FE6D0] text-xs font-medium border border-[#2FE6D0]/20">
                <i class="fa-solid fa-location-dot text-[11px]"></i>
                <span>{{ $post->ubicacion }}</span>
            </div>
        @endif
    </a>

    <!-- Galería Multimedia con soporte para Video -->
    @if(count($mediaList) === 1)
        <div class="mx-4 mb-3 rounded-2xl overflow-hidden border border-white/10 bg-[#0A0A0F] flex items-center justify-center max-h-[480px]">
            @if(($mediaList[0]['type'] ?? 'image') === 'video')
                <video src="{{ $mediaList[0]['url'] }}" autoplay muted playsinline loop controls class="max-h-[480px] w-full object-cover bg-black"></video>
            @else
                <a href="{{ route('posts.show', $post->id_publicacion) }}" class="w-full h-full block">
                    <img src="{{ $mediaList[0]['url'] }}" alt="Contenido multimedia" class="max-h-[480px] w-full object-contain">
                </a>
            @endif
        </div>
    @elseif(count($mediaList) > 1)
        <div class="mx-4 mb-3 flex gap-2.5 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-1">
            @foreach($mediaList as $media)
                <div class="snap-start flex-shrink-0 max-w-[85%] sm:max-w-[75%] h-64 sm:h-72 rounded-2xl overflow-hidden border border-white/10 bg-[#0A0A0F] flex items-center justify-center">
                    @if(($media['type'] ?? 'image') === 'video')
                        <video src="{{ $media['url'] }}" autoplay muted playsinline loop controls class="w-full h-full object-cover bg-black"></video>
                    @else
                        <a href="{{ route('posts.show', $post->id_publicacion) }}" class="w-full h-full block">
                            <img src="{{ $media['url'] }}" alt="Contenido multimedia" class="w-full h-full object-contain">
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Encuesta si existe -->
    @if(!empty($encuesta) && is_array($encuesta))
        <div class="mx-4 mb-3 p-3.5 rounded-xl bg-[#17171F] border border-[#7C5CFF]/30 space-y-2" id="poll-box-{{ $post->id_publicacion }}">
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

    <div class="flex items-center gap-1 px-3 py-2.5 border-t border-white/5 text-xs text-[#9A9AA5]">
        <button type="button" onclick="togglePostLike({{ $post->id_publicacion }}, this)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all cursor-pointer {{ $hasLiked ? 'text-[#FF3D57] bg-[#FF3D57]/10' : 'hover:bg-white/5 hover:text-[#FF3D57]' }}">
            <i class="fa-solid fa-fire text-sm"></i>
            <span class="like-count font-semibold">{{ $likesCount }}</span>
            <span class="hidden sm:inline">Vibró</span>
        </button>

        <a href="{{ route('posts.show', $post->id_publicacion) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-white/5 hover:text-white transition-all cursor-pointer">
            <i class="fa-regular fa-comment text-sm"></i>
            <span>{{ $commentsCount }}</span>
            <span class="hidden sm:inline">Comentarios</span>
        </a>
    </div>
</article>