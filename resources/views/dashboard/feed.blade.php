@extends('layouts.dashboard')

@section('title', 'ENCONCIERTA — Feed Principal')
@section('page-title', 'Feed')

@section('content')
<div class="flex gap-6 max-w-[1100px] mx-auto w-full px-3 sm:px-4 py-4 sm:py-6 pb-20 md:pb-6">
    <div class="flex-1 min-w-0 flex flex-col gap-4">
        <x-compose-post />

        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="rounded-2xl p-8 bg-[#0C0C13] border border-white/5 text-center">
                <i class="fa-solid fa-compact-disc text-3xl text-[#7C5CFF] mb-3"></i>
                <h3 class="text-[#F5F5F7] text-base font-semibold">Aún no hay publicaciones en la tribu</h3>
                <p class="text-[#9A9AA5] text-xs mt-1">Sé el primero en registrar un momento para los próximos shows.</p>
            </div>
        @endforelse
    </div>

    <div class="w-[272px] flex-shrink-0 hidden xl:flex flex-col gap-4 sticky top-6 self-start max-h-[calc(100vh-100px)] overflow-y-auto no-scrollbar">
        <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/5">
            <h3 class="text-[#F5F5F7] text-sm font-semibold mb-4">Melómanos en ENCONCIERTA</h3>
            <div class="flex flex-col gap-3">
                @foreach($suggestedUsers as $user)
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&auto=format' }}" alt="{{ $user->nombre }}" class="w-9 h-9 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="text-[#F5F5F7] text-xs font-semibold truncate">{{ $user->nombre }}</div>
                            <div class="text-[#9A9AA5] text-[11px] truncate">{{ $user->handle ? '@'.$user->handle : $user->ciudad }}</div>
                        </div>
                        <button type="button" class="text-xs font-semibold px-3 py-1 rounded-full bg-[#FF3D57] text-white hover:brightness-110 transition-all cursor-pointer">
                            Seguir
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/5">
            <h3 class="text-[#F5F5F7] text-sm font-semibold mb-4">Próximos shows</h3>
            <div class="flex flex-col gap-3">
                @foreach($upcomingEvents as $event)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 bg-white/5">
                            <img src="{{ $event->imagen_portada ?? 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=200&fit=crop' }}" alt="{{ $event->artista }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[#F5F5F7] text-xs font-semibold truncate">{{ $event->artista }}</div>
                            <div class="text-[#9A9AA5] text-[11px]">{{ $event->ciudad }} · {{ \Carbon\Carbon::parse($event->fecha)->format('d M') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function togglePostLike(postId, btn) {
        try {
            const response = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const countSpan = btn.querySelector('.like-count');
                countSpan.innerText = data.likes_count;

                if (data.liked) {
                    btn.classList.add('text-[#FF3D57]', 'bg-[#FF3D57]/10');
                    btn.classList.remove('hover:bg-white/5', 'hover:text-[#FF3D57]');
                } else {
                    btn.classList.remove('text-[#FF3D57]', 'bg-[#FF3D57]/10');
                    btn.classList.add('hover:bg-white/5', 'hover:text-[#FF3D57]');
                }
            }
        } catch (err) {
            console.error('Error al procesar reacción:', err);
        }
    }
</script>
@endpush