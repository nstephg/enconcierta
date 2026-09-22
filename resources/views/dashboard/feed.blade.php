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
                    @php
                        $uAvatar = !empty($user->avatar) 
                            ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar))
                            : asset('images/default-avatar.svg');
                        $isVerified = ($user->id_rol ?? 1) == 2 || !empty($user->es_verificado);
                        $isFollowing = !empty($user->is_following);
                    @endphp
                    <div class="social-item flex items-center gap-3 justify-between">
                        <!-- Enlace al Perfil del Usuario -->
                        <a href="{{ route('profile.show', $user->id_usuario) }}" class="flex items-center gap-3 flex-1 min-w-0 group cursor-pointer">
                            <img src="{{ $uAvatar }}" alt="{{ $user->nombre }}" class="w-9 h-9 rounded-full object-cover border border-white/10 group-hover:border-[#FF3D57] transition-colors flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[#F5F5F7] text-xs font-semibold truncate group-hover:text-[#FF3D57] transition-colors">{{ $user->nombre }}</span>
                                    @if($isVerified)
                                        <i class="fa-solid fa-circle-check text-[#7C5CFF] text-[11px] flex-shrink-0" title="Melómano verificado"></i>
                                    @endif
                                </div>
                                <div class="text-[#9A9AA5] text-[11px] truncate">{{ $user->handle ? '@'.$user->handle : $user->ciudad }}</div>
                            </div>
                        </a>

                        <!-- Botón de Seguir Dinámico de Tamaño Fijo (w-20) -->
                        <button type="button" 
                                onclick="toggleFollowUser({{ $user->id_usuario }}, this)" 
                                class="w-20 py-1 rounded-full text-xs font-semibold transition-all cursor-pointer flex-shrink-0 text-center {{ $isFollowing ? 'border border-white/15 text-[#9A9AA5]' : 'bg-[#FF3D57] text-white shadow-[0_0_10px_rgba(255,61,87,0.3)] hover:brightness-110' }}">
                            {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
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