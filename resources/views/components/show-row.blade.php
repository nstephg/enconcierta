@props([
    'title',
    'venue',
    'genre' => null,
    'date',
    'attendees' => 0,
    'image',
    'onClick' => ''
])

<div onclick="{{ $onClick }}" class="group relative flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl bg-[#17171F]/40 border border-white/10 hover:border-[#FF3D57]/50 transition-all duration-300 cursor-pointer overflow-hidden w-full min-w-0">
    <!-- IMAGEN EVENTO -->
    <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden flex-shrink-0 bg-[#0C0C13]">
        <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
    </div>

    <!-- INFORMACIÓN PRINCIPAL -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap mb-1">
            <h4 class="font-headline text-base sm:text-lg text-white group-hover:text-[#FF3D57] transition-colors truncate">
                {{ $title }}
            </h4>
            @if($genre)
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#7C5CFF]/15 text-[#9B80FF] border border-[#7C5CFF]/30 truncate max-w-[120px] sm:max-w-none">
                    {{ $genre }}
                </span>
            @endif
        </div>

        <p class="text-xs text-[#9A9AA5] truncate mb-1.5">
            {{ $venue }}
        </p>

        <div class="flex items-center gap-3 text-xs flex-wrap">
            <span class="text-[#FFB020] font-semibold flex items-center gap-1">
                <i class="fa-solid fa-calendar-day text-[10px]"></i>
                {{ $date }}
            </span>
            <span class="text-[#9A9AA5] text-[11px] truncate">
                {{ $attendees }} en el parche
            </span>
        </div>
    </div>

    <!-- BOTÓN ACCIÓN -->
    <div class="flex-shrink-0 pl-1">
        <div class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[#9A9AA5] group-hover:bg-[#FF3D57] group-hover:text-white group-hover:border-[#FF3D57] transition-all">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </div>
    </div>
</div>