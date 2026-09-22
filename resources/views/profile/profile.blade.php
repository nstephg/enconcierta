@extends('layouts.dashboard')

@section('title', 'ENCONCIERTA — Mi Perfil')
@section('page-title', 'Mi perfil')

@section('content')
@php
    $bio = $user->bio ?? '';
    $spotify = $user->spotify ?? '';
    $spotifyDisplayName = $user->spotify_name ?? $spotify;
    $instagram = $user->instagram ?? '';
    $generos = $user->generos ?? [];
    $artistas = $user->artistas ?? [];

    $avatarUrl = !empty($user->avatar) 
        ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar))
        : asset('images/default-avatar.svg');
        
    $coverUrl = !empty($user->portada) 
        ? (str_starts_with($user->portada, 'http') ? $user->portada : asset('storage/' . $user->portada))
        : 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1000&h=350&fit=crop&auto=format';

    $isInitialEdit = session('success') ? true : false;

    $followingList = $followingList ?? [];
    $followersList = $followersList ?? [];
    $followingCount = count($followingList);
    $followersCount = count($followersList);

    // Validación de pertenencia del perfil
    $isOwnProfile = auth()->check() && (auth()->id() == ($user->id_usuario ?? $user->id ?? null));
    $isFollowingAuthor = $isFollowingAuthor ?? false;
@endphp

<div class="max-w-[780px] mx-auto w-full px-3 sm:px-4 py-4 sm:py-6 pb-20 md:pb-6">

    <!-- Modal Lightbox -->
    <div id="profileLightbox" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6 bg-black/90 backdrop-blur-md" onclick="closeLightbox()">
        <img id="profileLightboxImg" src="" alt="Vista previa" class="max-w-full max-h-full rounded-2xl object-contain shadow-[0_0_60px_rgba(0,0,0,0.8)]">
        <button type="button" class="absolute top-5 right-5 text-white/60 hover:text-white transition-colors cursor-pointer" onclick="closeLightbox()">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Modal Social (Siguiendo / Seguidores) -->
    <div id="socialModal" data-is-own="{{ $isOwnProfile ? '1' : '0' }}" data-profile-id="{{ $user->id_usuario }}" class="fixed inset-0 z-[100] hidden flex items-end sm:items-center justify-center bg-black/80 backdrop-blur-md p-0 sm:p-4" onclick="closeSocialModal()">
        <div class="w-full sm:max-w-[440px] rounded-t-3xl sm:rounded-2xl flex flex-col bg-[#0C0C13] border border-white/10 shadow-2xl h-[75vh] sm:h-auto sm:max-h-[85vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="flex justify-center pt-3 sm:hidden flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-white/15"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 flex-shrink-0 border-b border-white/[0.06]">
                <div>
                    <h2 id="socialModalTitle" class="text-[#F5F5F7] font-bold text-base">Siguiendo</h2>
                    <p id="socialModalSubtitle" class="text-[#9A9AA5] text-xs mt-0.5">
                        {{ $followingCount }} {{ $isOwnProfile ? 'melómanos que sigues' : 'melómanos que sigue' }}
                    </p>
                </div>
                <button type="button" onclick="closeSocialModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-[#9A9AA5] hover:text-white bg-white/5 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="px-5 py-3 flex-shrink-0 border-b border-white/[0.05]">
                <div class="flex items-center gap-2 rounded-xl px-3 py-2 bg-white/[0.04] border border-white/[0.07]">
                    <i class="fa-solid fa-magnifying-glass text-[#9A9AA5] text-xs"></i>
                    <input type="text" id="socialSearchInput" oninput="filterSocialList(this.value)" placeholder="Buscar…" class="flex-1 bg-transparent text-[#F5F5F7] text-xs placeholder-[#9A9AA5] outline-none">
                </div>
            </div>
            <div class="flex-1 overflow-y-auto min-h-0">
                <!-- Lista de Siguiendo -->
                <div id="social-list-following" class="flex flex-col">
                    @forelse($followingList as $person)
                        @php
                            $pAvatar = !empty($person->avatar) 
                                ? (str_starts_with($person->avatar, 'http') ? $person->avatar : asset('storage/' . $person->avatar))
                                : asset('images/default-avatar.svg');
                            $isMe = auth()->id() == $person->id_usuario;
                            $pVerified = ($person->id_rol ?? 1) == 2 || !empty($person->es_verificado);
                        @endphp
                        <div class="social-item flex items-center justify-between gap-3 px-5 py-3 border-b border-white/[0.04]" 
                             data-name="{{ strtolower($person->nombre ?? '') }}" 
                             data-username="{{ strtolower($person->handle ?? '') }}">
                            <!-- Enlace al Perfil del Usuario -->
                            <a href="{{ route('profile.show', $person->id_usuario) }}" class="flex items-center gap-3 flex-1 min-w-0 group cursor-pointer">
                                <img src="{{ $pAvatar }}" class="w-11 h-11 rounded-full object-cover border-2 border-white/[0.08] group-hover:border-[#FF3D57] transition-colors flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[#F5F5F7] text-sm font-semibold truncate group-hover:text-[#FF3D57] transition-colors">{{ $person->nombre }}</span>
                                        @if($pVerified)
                                            <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs flex-shrink-0" title="Melómano verificado"></i>
                                        @endif
                                    </div>
                                    <div class="text-[#9A9AA5] text-xs truncate">{{ '@' . $person->handle }}</div>
                                </div>
                            </a>
                            @if(!$isMe)
                                <button type="button" 
                                        onclick="toggleFollowUser({{ $person->id_usuario }}, this)" 
                                        class="w-24 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all text-center flex-shrink-0 {{ $person->is_following ? 'border border-white/15 text-[#9A9AA5]' : 'bg-[#FF3D57] text-white shadow-[0_0_12px_rgba(255,61,87,0.3)]' }}">
                                    {{ $person->is_following ? 'Siguiendo' : 'Seguir' }}
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10 text-[#9A9AA5] text-xs">
                            <i class="fa-solid fa-user-plus text-sm mb-2 text-[#7C5CFF]/50 block"></i>
                            {{ $isOwnProfile ? 'Aún no sigues a ningún melómano.' : 'Aún no sigue a ningún melómano.' }}
                        </div>
                    @endforelse
                </div>

                <!-- Lista de Seguidores -->
                <div id="social-list-followers" class="flex flex-col hidden">
                    @forelse($followersList as $person)
                        @php
                            $pAvatar = !empty($person->avatar) 
                                ? (str_starts_with($person->avatar, 'http') ? $person->avatar : asset('storage/' . $person->avatar))
                                : asset('images/default-avatar.svg');
                            $isMe = auth()->id() == $person->id_usuario;
                            $pVerified = ($person->id_rol ?? 1) == 2 || !empty($person->es_verificado);
                        @endphp
                        <div class="social-item flex items-center justify-between gap-3 px-5 py-3 border-b border-white/[0.04]" 
                             data-name="{{ strtolower($person->nombre ?? '') }}" 
                             data-username="{{ strtolower($person->handle ?? '') }}">
                            <!-- Enlace al Perfil del Usuario -->
                            <a href="{{ route('profile.show', $person->id_usuario) }}" class="flex items-center gap-3 flex-1 min-w-0 group cursor-pointer">
                                <img src="{{ $pAvatar }}" class="w-11 h-11 rounded-full object-cover border-2 border-white/[0.08] group-hover:border-[#FF3D57] transition-colors flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[#F5F5F7] text-sm font-semibold truncate group-hover:text-[#FF3D57] transition-colors">{{ $person->nombre }}</span>
                                        @if($pVerified)
                                            <i class="fa-solid fa-circle-check text-[#7C5CFF] text-xs flex-shrink-0" title="Melómano verificado"></i>
                                        @endif
                                    </div>
                                    <div class="text-[#9A9AA5] text-xs truncate">{{ '@' . $person->handle }}</div>
                                </div>
                            </a>
                            @if(!$isMe)
                                <button type="button" 
                                        onclick="toggleFollowUser({{ $person->id_usuario }}, this)" 
                                        class="w-24 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all text-center flex-shrink-0 {{ $person->is_following ? 'border border-white/15 text-[#9A9AA5]' : 'bg-[#FF3D57] text-white shadow-[0_0_12px_rgba(255,61,87,0.3)]' }}">
                                    {{ $person->is_following ? 'Siguiendo' : 'Seguir' }}
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10 text-[#9A9AA5] text-xs">
                            <i class="fa-solid fa-users text-sm mb-2 text-[#FF3D57]/50 block"></i>
                            {{ $isOwnProfile ? 'Aún no tienes seguidores.' : 'Aún no tiene seguidores.' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Header Vista 'Mis momentos' (Visible para todos) -->
    <div id="header-posts-view" class="{{ ($isOwnProfile && $isInitialEdit) ? 'hidden' : '' }}">
        <div class="relative rounded-2xl overflow-hidden mb-4 h-44 group cursor-pointer bg-[#0C0C13]" onclick="openLightbox('{{ $coverUrl }}')">
            <img id="coverImgPostView" src="{{ $coverUrl }}" alt="Portada" class="w-full h-full object-cover opacity-70">
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/45">
                <span class="flex items-center gap-2 text-white text-xs font-medium"><i class="fa-solid fa-magnifying-glass-plus"></i> Ver portada</span>
            </div>
        </div>

        <div class="flex items-end gap-3 mb-3 -mt-[52px] pl-4">
            <div class="relative group cursor-pointer flex-shrink-0" onclick="openLightbox('{{ $avatarUrl }}')">
                <img id="avatarImgPostView" src="{{ $avatarUrl }}" alt="{{ $user->nombre }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-[3px] border-[#0A0A0F]">
            </div>
            <div class="flex-1 min-w-0 pb-1 flex items-center justify-between pr-2 pt-14 sm:pt-16">
                <div>
                    <div class="flex items-center gap-1.5">
                        <h2 class="text-[#F5F5F7] font-bold text-base sm:text-lg truncate">{{ $user->nombre }}</h2>
                        @if(($user->id_rol ?? 1) == 2 || !empty($user->es_verificado))
                            <i class="fa-solid fa-circle-check text-[#7C5CFF] text-sm" title="Melómano verificado"></i>
                        @endif
                    </div>
                    <div class="text-[#9A9AA5] text-xs sm:text-sm">{{ '@' . $user->handle }}</div>
                </div>

                <!-- Botón de Acción Principal (Seguir o Configuración) -->
                @if(!$isOwnProfile)
                    <button type="button" 
                            id="btn-follow-main"
                            onclick="toggleFollowUser({{ $user->id_usuario }}, this)" 
                            class="px-5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $isFollowingAuthor ? 'border border-white/15 text-[#9A9AA5]' : 'bg-[#FF3D57] text-white shadow-[0_0_12px_rgba(255,61,87,0.3)]' }}">
                        {{ $isFollowingAuthor ? 'Siguiendo' : 'Seguir' }}
                    </button>
                @else
                    <button type="button" 
                            onclick="toggleProfileSettings()" 
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center text-[#9A9AA5] hover:text-white bg-[#17171F] border border-white/10 hover:border-white/25 transition-all cursor-pointer shadow-sm"
                            title="Configuración y Editar Perfil">
                        <i class="fa-solid fa-gear text-xs sm:text-sm"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="mb-5 flex flex-col gap-2.5 px-1">
            @if($bio)
                <p class="text-[#E0E0E6] text-sm leading-relaxed">{{ $bio }}</p>
            @endif

            <!-- ENLACES SOCIALES (UBICACIÓN, INSTAGRAM, SPOTIFY) -->
            <div class="flex flex-wrap gap-x-4 gap-y-1">
                @if($user->ciudad)
                    <span class="flex items-center gap-1.5 text-[#9A9AA5] text-xs">
                        <i class="fa-solid fa-location-dot text-[#9A9AA5]"></i> {{ $user->ciudad }}
                    </span>
                @endif
                @if($instagram)
                    <a href="https://instagram.com/{{ str_replace('@', '', $instagram) }}" target="_blank" class="flex items-center gap-1.5 text-xs font-medium text-[#FF3D57] hover:opacity-80 transition-opacity">
                        <i class="fa-brands fa-instagram"></i> {{ str_starts_with($instagram, '@') ? $instagram : '@' . $instagram }}
                    </a>
                @endif
                @if($spotify)
                    <a href="https://open.spotify.com/user/{{ $spotify }}" target="_blank" class="flex items-center gap-1.5 text-xs font-medium text-[#1DB954] hover:opacity-80 transition-opacity" title="ID de Spotify: {{ $spotify }}">
                        <i class="fa-brands fa-spotify"></i> {{ $spotifyDisplayName ?: $spotify }}
                    </a>
                @endif
            </div>

            <!-- Géneros Favoritos -->
            @if(!empty($generos) && count($generos) > 0)
                <div class="flex flex-wrap gap-2 pt-1">
                    @foreach($generos as $g)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#7C5CFF]/15 text-[#9B80FF] border border-[#7C5CFF]/30">
                            {{ $g }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Artistas Favoritos -->
            @if(!empty($artistas) && count($artistas) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($artistas as $a)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#FF3D57]/15 text-[#FF6070] border border-[#FF3D57]/30">
                            {{ $a }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($isOwnProfile)
        <!-- Header Vista 'Editar Perfil' (Solo visible para el propietario) -->
        <div id="header-edit-view" class="{{ $isInitialEdit ? '' : 'hidden' }} mb-6">
            <div class="relative rounded-2xl overflow-hidden mb-4 h-44 group cursor-pointer bg-[#0C0C13] border border-white/10" onclick="document.getElementById('coverFileInput').click()">
                <img id="coverImgEditView" src="{{ $coverUrl }}" alt="Portada" class="w-full h-full object-cover opacity-70">
                <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-medium gap-2">
                    <i class="fa-solid fa-camera text-sm text-[#2FE6D0]"></i>
                    <span>Cambiar foto de portada</span>
                </div>
            </div>

            <div class="flex items-end gap-3 -mt-[52px] pl-4">
                <div class="relative group cursor-pointer flex-shrink-0" onclick="document.getElementById('avatarFileInput').click()">
                    <img id="avatarImgEditView" src="{{ $avatarUrl }}" alt="{{ $user->nombre }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-[3px] border-[#0A0A0F]">
                    <div class="absolute inset-0 rounded-full flex flex-col items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-medium gap-0.5">
                        <i class="fa-solid fa-camera text-xs text-[#FF3D57]"></i>
                        <span>Cambiar</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0 pb-1 flex items-center justify-between pr-2 pt-14 sm:pt-16">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h2 class="text-[#F5F5F7] font-bold text-base sm:text-lg truncate">{{ $user->nombre }}</h2>
                            @if($user->es_verificado)
                                <i class="fa-solid fa-circle-check text-[#7C5CFF] text-sm" title="Melómano verificado"></i>
                            @endif
                        </div>
                        <div class="text-[#9A9AA5] text-xs sm:text-sm">{{ '@' . $user->handle }}</div>
                    </div>

                    <!-- Botón para Salir/Cerrar Configuración -->
                    <button type="button" 
                            onclick="toggleProfileSettings()" 
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center text-[#FF3D57] bg-[#FF3D57]/10 border border-[#FF3D57]/30 hover:bg-[#FF3D57]/20 transition-all cursor-pointer shadow-sm"
                            title="Volver al perfil">
                        <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Contador de Estadísticas -->
    <div class="flex gap-0 mb-6 rounded-2xl overflow-hidden bg-[#0C0C13] border border-white/[0.06]">
        <div class="flex-1 text-center py-3 border-r border-white/[0.06]">
            <div class="text-[#F5F5F7] font-bold text-base">{{ $blogsCount ?? $showsCount ?? 0 }}</div>
            <div class="text-xs text-[#9A9AA5]">Blogs</div>
        </div>
        <button type="button" onclick="openSocialModal('siguiendo')" class="flex-1 text-center py-3 border-r border-white/[0.06] hover:bg-white/[0.03] transition-all cursor-pointer">
            <div class="text-[#F5F5F7] font-bold text-base" id="stat-following-count">{{ $followingCount }}</div>
            <div class="text-xs text-[#7C5CFF]">Siguiendo</div>
        </button>
        <button type="button" onclick="openSocialModal('seguidores')" class="flex-1 text-center py-3 hover:bg-white/[0.03] transition-all cursor-pointer">
            <div class="text-[#F5F5F7] font-bold text-base" id="stat-followers-count">{{ $followersCount }}</div>
            <div class="text-xs text-[#7C5CFF]">Seguidores</div>
        </button>
    </div>

    <!-- Switcher de Pestañas (Momentos / Blogs) -->
    <div class="flex gap-1 mb-6 rounded-2xl p-1.5 bg-[#0C0C13] border border-white/[0.06]">
        <button type="button" 
                id="btn-tab-posts"
                onclick="switchProfileTab('posts')" 
                class="flex-1 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer {{ $isInitialEdit ? 'text-[#9A9AA5] hover:text-white' : 'bg-[#FF3D57] text-white shadow-[0_0_15px_rgba(255,61,87,0.35)]' }}">
            Posts
        </button>
        <button type="button" 
                id="btn-tab-blogs"
                onclick="switchProfileTab('blogs')" 
                class="flex-1 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer text-[#9A9AA5] hover:text-white">
            Blogs
        </button>
    </div>

    <!-- Pestaña 1: Momentos (Publicaciones) -->
    <div id="tab-content-posts" class="{{ ($isOwnProfile && $isInitialEdit) ? 'hidden' : '' }} flex flex-col gap-4">
        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="text-center py-12 bg-[#0C0C13] rounded-2xl border border-white/[0.06]">
                <i class="fa-solid fa-compact-disc text-3xl text-[#9A9AA5] mb-2"></i>
                <p class="text-[#9A9AA5] text-sm">Aún no se ha publicado ningún momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Pestaña 2: Blogs -->
    <div id="tab-content-blogs" class="hidden flex flex-col gap-4">
        <div class="text-center py-12 bg-[#0C0C13] rounded-2xl border border-white/[0.06]">
            <i class="fa-solid fa-pen-nib text-3xl text-[#9A9AA5] mb-2"></i>
            <p class="text-[#9A9AA5] text-sm">Aún no se han redactado blogs en esta cuenta.</p>
        </div>
    </div>

    @if($isOwnProfile)
    <!-- Pestaña 3: Editar Perfil (Acceso exclusivo por botón de tuerca) -->
    <div id="tab-content-editar" class="{{ $isInitialEdit ? '' : 'hidden' }} flex flex-col gap-5">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <input type="file" id="avatarFileInput" name="avatar" class="hidden" accept="image/*" onchange="previewProfileImage(this, 'avatarImgEditView'); previewProfileImage(this, 'avatarImgPostView');">
            <input type="file" id="coverFileInput" name="portada" class="hidden" accept="image/*" onchange="previewProfileImage(this, 'coverImgEditView'); previewProfileImage(this, 'coverImgPostView');">

            <div id="genresHiddenInputs">
                @foreach($generos as $g)
                    <input type="hidden" name="generos[]" value="{{ $g }}">
                @endforeach
            </div>
            <div id="artistsHiddenInputs">
                @foreach($artistas as $a)
                    <input type="hidden" name="artistas[]" value="{{ $a }}">
                @endforeach
            </div>

            <!-- Sección: IDENTIDAD -->
            <div class="rounded-2xl p-5 flex flex-col gap-4 bg-[#0C0C13] border border-white/[0.06]">
                <h3 class="text-[#9A9AA5] text-[11px] font-bold tracking-widest uppercase">IDENTIDAD</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[#9A9AA5] text-xs font-semibold block mb-2">Nombre completo</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" required 
                               class="w-full text-[#F5F5F7] text-sm rounded-xl px-4 py-3 bg-[#17171F] border border-white/10 outline-none focus:border-[#FF3D57]">
                    </div>
                    <div>
                        <label class="text-[#9A9AA5] text-xs font-semibold block mb-2">Usuario</label>
                        <div class="flex items-center rounded-xl overflow-hidden bg-[#17171F] border border-white/10 focus-within:border-[#FF3D57]">
                            <span class="px-3 text-[#9A9AA5] text-sm select-none">@</span>
                            <input type="text" name="handle" value="{{ old('handle', $user->handle) }}" required 
                                   class="flex-1 bg-transparent text-[#F5F5F7] text-sm py-3 pr-4 outline-none">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="text-[#9A9AA5] text-xs font-semibold block mb-2">Biografía</label>
                    <textarea name="bio" rows="3" maxlength="160" oninput="updateBioCount(this)"
                              class="w-full text-[#F5F5F7] text-sm rounded-xl px-4 py-3 bg-[#17171F] border border-white/10 outline-none focus:border-[#FF3D57] resize-none">{{ old('bio', $bio) }}</textarea>
                    <span id="bioCharCounter" class="text-[#9A9AA5] text-[11px] self-end mt-1 font-mono-code">{{ strlen($bio) }}/160</span>
                </div>
                <div>
                    <label class="text-[#9A9AA5] text-xs font-semibold block mb-2">Ciudad</label>
                    <div class="flex items-center rounded-xl overflow-hidden bg-[#17171F] border border-white/10 focus-within:border-[#FF3D57]">
                        <span class="px-3 text-[#9A9AA5]"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $user->ciudad ?? 'Bogotá, Colombia') }}" placeholder="Ej: Bogotá, Colombia" 
                               class="flex-1 bg-transparent text-[#F5F5F7] text-sm py-3 pr-4 outline-none">
                    </div>
                </div>
            </div>

            <!-- Sección: GUSTOS MUSICALES -->
            <div class="rounded-2xl p-5 flex flex-col gap-4 bg-[#0C0C13] border border-white/[0.06]">
                <h3 class="text-[#9A9AA5] text-[11px] font-bold tracking-widest uppercase">GUSTOS MUSICALES</h3>
                <div>
                    <label class="text-[#9A9AA5] text-xs font-semibold block mb-3">Géneros favoritos</label>
                    <div id="genresContainer" class="flex gap-2 flex-wrap items-center">
                        @foreach($generos as $g)
                            <span class="genre-badge flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#7C5CFF]/15 text-[#9B80FF] border border-[#7C5CFF]/30">
                                <span>{{ $g }}</span>
                                <button type="button" onclick="removeGenreTag(this, '{{ addslashes($g) }}')" class="opacity-60 hover:opacity-100 cursor-pointer">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </span>
                        @endforeach
                        <div class="flex items-center border border-dashed border-white/20 rounded-full px-3 py-1.5 text-xs text-[#9A9AA5] focus-within:border-[#7C5CFF]">
                            <span class="mr-1">+</span>
                            <input type="text" id="inputAddGenre" onkeydown="if(event.key==='Enter'){ event.preventDefault(); addGenreTag(); }" placeholder="Agregar" 
                                   class="bg-transparent outline-none w-16 text-xs text-white placeholder-[#9A9AA5]">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-[#9A9AA5] text-xs font-semibold block mb-3">Artistas favoritos</label>
                    <div id="artistsContainer" class="flex gap-2 flex-wrap mb-3">
                        @foreach($artistas as $a)
                            <span class="artist-badge flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#FF3D57]/15 text-[#FF6070] border border-[#FF3D57]/30">
                                <span>{{ $a }}</span>
                                <button type="button" onclick="removeArtistTag(this, '{{ addslashes($a) }}')" class="opacity-60 hover:opacity-100 cursor-pointer">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="inputAddArtist" onkeydown="if(event.key==='Enter'){ event.preventDefault(); addArtistTag(); }" placeholder="Añadir artista…" 
                               class="flex-1 text-[#F5F5F7] text-sm rounded-xl px-4 py-2.5 bg-[#17171F] border border-white/10 outline-none focus:border-[#FF3D57]">
                        <button type="button" onclick="addArtistTag()" class="px-5 py-2.5 rounded-xl bg-[#FF3D57] text-white text-xs font-bold hover:brightness-110 cursor-pointer">
                            Añadir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sección: REDES SOCIALES -->
            <div class="rounded-2xl p-5 flex flex-col gap-4 bg-[#0C0C13] border border-white/[0.06]">
                <h3 class="text-[#9A9AA5] text-[11px] font-bold tracking-widest uppercase">REDES SOCIALES</h3>

                <!-- Conexión Spotify -->
                <div class="flex flex-col gap-2">
                    <label class="text-[#9A9AA5] text-xs font-semibold flex items-center gap-2">
                        <i class="fa-brands fa-spotify text-[#1DB954]"></i> Spotify
                    </label>
                    
                    @if($spotify)
                        <!-- Inputs ocultos para mantener los datos al enviar el formulario de editar perfil -->
                        <input type="hidden" name="spotify" value="{{ $spotify }}">
                        <input type="hidden" name="spotify_name" value="{{ $spotifyDisplayName }}">

                        <div class="p-3 bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl font-medium text-xs flex items-center justify-between flex-wrap gap-2">
                            <span class="flex items-center gap-2 min-w-0 truncate">
                                <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                                <span class="truncate">Conectado como <strong class="text-white">{{ $spotifyDisplayName ?: $spotify }}</strong></span>
                            </span>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <a href="{{ route('spotify.login') }}" class="text-[#9A9AA5] hover:text-white text-[11px] underline transition-colors">
                                    Reconectar
                                </a>
                                <button type="button" 
                                        onclick="event.preventDefault(); document.getElementById('disconnect-spotify-form').submit();" 
                                        class="text-[#FF3D57] hover:underline text-[11px] font-medium transition-colors cursor-pointer">
                                    Desconectar
                                </button>
                            </div>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('spotify.login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1DB954] hover:bg-[#1ed760] text-black font-bold rounded-xl transition text-xs shadow-lg shadow-[#1DB954]/10">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 0C5.376 0 0 5.376 0 12s5.376 12 12 12 12-5.376 12-12S18.624 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.12-.779-.18-.899-.54-.12-.42.18-.78.54-.899 4.62-1.08 8.52-.66 11.7 1.26.36.18.48.66.24 1.08zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-.96-.18-1.08-.66-.12-.48.18-.96.66-1.08 4.38-1.32 9.84-.66 13.56 1.62.36.18.54.78.24 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.18-1.2-.18-1.38-.72-.18-.6.18-1.2.72-1.38 4.26-1.26 11.28-1.02 15.72 1.62.54.3.72 1.02.42 1.56-.3.42-1.02.6-1.56.3z"/>
                                </svg>
                                Conectar a Spotify
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Instagram -->
                <div>
                    <label class="text-[#9A9AA5] text-xs font-semibold flex items-center gap-2 mb-2">
                        <i class="fa-brands fa-instagram text-[#E1306C]"></i> Instagram
                    </label>
                    <div class="flex items-center rounded-xl overflow-hidden bg-[#17171F] border border-white/10 focus-within:border-[#FF3D57]">
                        <span class="px-3 text-[#9A9AA5] text-sm select-none">@</span>
                        <input type="text" name="instagram" value="{{ old('instagram', str_replace('@', '', $instagram)) }}" placeholder="usuario" 
                               class="flex-1 bg-transparent text-[#F5F5F7] text-sm py-3 pr-4 outline-none">
                    </div>
                </div>
            </div>

            <!-- Botón de Guardar Cambios -->
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 py-4 rounded-2xl bg-[#FF3D57] text-white font-bold text-xs sm:text-sm tracking-widest uppercase shadow-[0_0_20px_rgba(255,61,87,0.35)] hover:brightness-110 transition-all cursor-pointer">
                <span>GUARDAR CAMBIOS</span>
            </button>
        </form>

        <!-- Botón de Cerrar Sesión en Móvil (Pestaña Editar) -->
        <form method="POST" action="{{ route('logout') }}" class="md:hidden pt-2">
            @csrf
            <button type="submit" class="w-full py-3.5 rounded-2xl border border-[#FF3D57]/40 text-[#FF3D57] font-bold text-xs tracking-widest uppercase hover:bg-[#FF3D57]/10 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                <span>CERRAR SESIÓN</span>
            </button>
        </form>

        <!-- Formulario independiente para la desvinculación (Ubicado fuera del formulario principal) -->
        <form id="disconnect-spotify-form" action="{{ route('spotify.disconnect') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
@endif
</div>
@endsection