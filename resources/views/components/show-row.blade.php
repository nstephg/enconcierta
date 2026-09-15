@props([
    'title',
    'venue',
    'genre',
    'date',
    'attendees',
    'image',
    'onClick' => ''
])

<div onclick="{{ $onClick }}" class="flex items-center gap-4 p-4 rounded-xl cursor-pointer transition-all duration-200 group border border-white/6 hover:border-[#7C5CFF]/35 hover:bg-[#0C0C13]/90 bg-[#17171F]/40">
    <div class="relative shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-[#0C0C13]">
        <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div>
                <h4 class="text-white font-semibold text-sm leading-tight truncate group-hover:text-[#2FE6D0] transition-colors">{{ $title }}</h4>
                <p class="text-[#9A9AA5] text-xs mt-0.5 truncate">{{ $venue }}</p>
            </div>
            <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium bg-[#7C5CFF]/15 text-[#9B80FF]">
                {{ $genre }}
            </span>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="text-[#FFB020] text-xs font-medium">{{ $date }}</span>
            <span class="text-[#9A9AA5] text-xs">{{ $attendees }} en el parche</span>
        </div>
    </div>
    <button class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 bg-[#FF3D57]/12 text-[#FF3D57] group-hover:bg-[#FF3D57] group-hover:text-white group-hover:shadow-[0_0_14px_rgba(255,61,87,0.4)]" aria-label="Sincronizar">
        <i class="fa-solid fa-arrow-right text-xs"></i>
    </button>
</div>