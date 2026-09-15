@props([
    'image',
    'avatar',
    'username',
    'details',
    'text',
    'likes',
    'borderColor' => 'border-[#FF3D57]/40'
])

<article class="bg-[#17171F]/50 rounded-2xl border border-white/10 overflow-hidden group hover:border-white/20 transition-all duration-300 flex flex-col justify-between">
    <div>
        <div class="relative h-44 overflow-hidden bg-[#0C0C13]">
            <img src="{{ $image }}" alt="{{ $username }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-linear-to-t from-[#0A0A0F]/80 to-transparent"></div>
        </div>
        <div class="p-5 flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ $avatar }}" alt="{{ $username }}" class="w-9 h-9 rounded-full object-cover border-2 {{ $borderColor }}">
                <div>
                    <div class="text-sm font-bold text-white">{{ $username }}</div>
                    <div class="text-xs text-[#9A9AA5]">{{ $details }}</div>
                </div>
            </div>
            <p class="text-[#F5F5F7] text-sm leading-relaxed font-light line-clamp-3">
                {{ $text }}
            </p>
        </div>
    </div>

    <div class="px-5 pb-5 pt-1 flex items-center gap-2 text-[#FF3D57] text-xs font-semibold">
        <button onclick="reactPost(this)" class="flex items-center gap-2 text-[#FF3D57] hover:scale-105 transition-transform cursor-pointer">
            <i class="fa-solid fa-heart"></i>
            <span><strong class="like-count">{{ $likes }}</strong> se identifican</span>
        </button>
    </div>
</article>