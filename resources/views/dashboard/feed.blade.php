@extends('layouts.dashboard')

@section('title', 'ENCONCIERTA — Feed Principal')
@section('page-title', 'Feed Principal')

@section('content')
<div class="flex gap-6 max-w-[1100px] mx-auto w-full px-3 sm:px-4 py-4 sm:py-6 pb-20 md:pb-6">
    <div class="flex-1 min-w-0 flex flex-col gap-4">
        <x-compose-post />

        @foreach($posts as $post)
            <x-post-card :post="$post" />
        @endforeach
    </div>

    <div class="w-[272px] flex-shrink-0 hidden xl:flex flex-col gap-4 sticky top-6 self-start max-h-[calc(100vh-100px)] overflow-y-auto no-scrollbar">
        <div class="rounded-2xl p-4 bg-[#0C0C13] border border-white/5">
            <h3 class="text-[#F5F5F7] text-sm font-semibold mb-4">Melómanos sugeridos</h3>
            <div class="flex flex-col gap-3">
                @foreach($suggestedUsers as $user)
                    <div class="flex items-center gap-3">
                        <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" class="w-9 h-9 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="text-[#F5F5F7] text-xs font-semibold truncate">{{ $user['name'] }}</div>
                            <div class="text-[#9A9AA5] text-[11px]">{{ $user['genre'] }}</div>
                        </div>
                        <button type="button" class="text-xs font-semibold px-3 py-1 rounded-full bg-[#FF3D57] text-white hover:brightness-110 transition-all">
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
                            <div class="text-[#9A9AA5] text-[11px]">{{ $event->ciudad }}</div>
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
    function switchComposeType(type) {
        document.getElementById('compose-type').value = type;
        const isMoment = type === 'review';
        document.getElementById('tab-moment').className = `flex-1 py-3 text-xs font-bold tracking-wider uppercase transition-all relative ${isMoment ? 'text-[#FFB020]' : 'text-[#6A6A75]'}`;
        document.getElementById('tab-parche').className = `flex-1 py-3 text-xs font-bold tracking-wider uppercase transition-all relative ${!isMoment ? 'text-[#2FE6D0]' : 'text-[#6A6A75]'}`;
        document.getElementById('indicator-moment').classList.toggle('hidden', !isMoment);
        document.getElementById('indicator-parche').classList.toggle('hidden', isMoment);
    }
</script>
@endpush