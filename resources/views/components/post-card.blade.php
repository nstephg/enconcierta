@props(['post'])

@php
    $genreColors = [
        'Urbano' => '#FF3D57',
        'Pop alternativo' => '#7C5CFF',
        'Reguetón' => '#FFB020',
        'Electro cumbia' => '#2FE6D0',
        'Jazz fusión' => '#2FE6D0',
    ];
    $color = $genreColors[$post['show']['genre']] ?? '#7C5CFF';
    $isParche = $post['type'] === 'parche';
@endphp

<article class="rounded-2xl overflow-hidden bg-[#0C0C13] border border-white/5">
    <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
        <img src="{{ $post['user']['avatar'] }}" alt="{{ $post['user']['name'] }}" class="w-8 h-8 rounded-full object-cover border border-white/10">
        <div class="flex items-center gap-1.5 flex-1 min-w-0">
            <span class="text-[#F5F5F7] text-sm font-semibold truncate">{{ $post['user']['name'] }}</span>
            @if($post['user']['verified'])
                <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs"></i>
            @endif
            <span class="text-[#9A9AA5] text-xs">· {{ $post['time'] }}</span>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase border"
              style="background-color: {{ $isParche ? 'rgba(47,230,208,0.12)' : 'rgba(255,176,32,0.12)' }}; color: {{ $isParche ? '#2FE6D0' : '#FFB020' }}; border-color: {{ $isParche ? 'rgba(47,230,208,0.25)' : 'rgba(255,176,32,0.25)' }}">
            {{ $isParche ? 'ARMANDO PARCHE' : 'ESTUVO AHÍ' }}
        </span>
    </div>

    <div class="mx-4 mb-4 rounded-xl overflow-hidden border" style="border-color: {{ $color }}30">
        <div class="flex items-stretch bg-gradient-to-r from-white/[0.03] to-transparent">
            <div class="w-1 flex-shrink-0" style="background-color: {{ $color }}"></div>
            <div class="flex-1 px-3 py-2.5 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <span class="font-headline text-[#F5F5F7] text-lg leading-none truncate">{{ strtoupper($post['show']['artist']) }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold" style="background-color: {{ $color }}20; color: {{ $color }}">{{ $post['show']['genre'] }}</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1.5 text-[#9A9AA5] text-xs">
                    <i class="fa-solid fa-location-dot text-[10px]"></i>
                    <span class="truncate">{{ $post['show']['venue'] }}</span>
                    <span>·</span>
                    <span>{{ $post['show']['date'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 pb-3">
        <p class="text-[#C8C8D0] text-sm leading-relaxed">{{ $post['content'] }}</p>
    </div>

    @if(!empty($post['img']))
        <div class="mx-4 mb-3 rounded-xl overflow-hidden h-48">
            <img src="{{ $post['img'] }}" alt="Concierto" class="w-full h-full object-cover">
        </div>
    @endif

    <div class="flex items-center gap-1 px-3 py-2.5 border-t border-white/5 text-xs text-[#9A9AA5]">
        <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-white/5 hover:text-[#FF3D57] transition-all">
            <i class="fa-solid fa-fire text-[#FF3D57]"></i>
            <span>{{ $post['likes'] }}</span>
            <span class="hidden sm:inline">Vibró</span>
        </button>

        <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-white/5 hover:text-white transition-all">
            <i class="fa-regular fa-comment"></i>
            <span>{{ $post['comments'] }}</span>
            <span class="hidden sm:inline">Comentar</span>
        </button>
    </div>
</article>