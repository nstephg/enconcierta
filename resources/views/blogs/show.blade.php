<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#0A0A0F]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $blog->titulo }} — ENCONCIERTA</title>

    <!-- Tailwind v4 CSS & Fonts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media (max-width: 639px) {
            .blog-block-item {
                position: relative !important;
                transform: none !important;
                width: 100% !important;
                height: auto !important;
                margin-bottom: 1.25rem !important;
                left: 0 !important;
                top: 0 !important;
            }
            .blog-canvas-container {
                height: auto !important;
            }
        }
        @media (min-width: 640px) {
            .blog-block-item {
                position: absolute;
                transform: translate(var(--b-x), var(--b-y));
                width: var(--b-w);
                height: var(--b-h);
            }
        }
    </style>
</head>
<body class="min-h-screen bg-[#0A0A0F] text-[#F5F5F7] font-sans antialiased pt-16 pb-8 px-2 sm:px-6 flex flex-col items-center" x-data="{ showDeleteModal: false }">

    @php
        $estilos = is_array($blog->estilos) 
            ? $blog->estilos 
            : json_decode($blog->estilos ?? '{}', true);

        $blocks = is_array($blog->contenido) 
            ? $blog->contenido 
            : json_decode($blog->contenido ?? '[]', true);

        if (!is_array($blocks) || empty($blocks)) {
            $blocks = [[
                'type' => 'text',
                'content' => is_string($blog->contenido) ? $blog->contenido : '',
                'x' => 0, 'y' => 0, 'w' => '100%', 'h' => 'auto'
            ]];
        }

        // Ordenar bloques por coordenada Y para secuencia lógica en lectura móvil
        usort($blocks, function($a, $b) {
            return intval($a['y'] ?? 0) <=> intval($b['y'] ?? 0);
        });

        $globalFont = $estilos['globalFont'] ?? 'Inter, sans-serif';
        $canvasWidth = $estilos['canvasWidth'] ?? '850px';
        $bgType = $estilos['bgType'] ?? 'solid';
        $bgColor = $estilos['bgColor'] ?? '#0A0A0F';
        $bgGrad = $estilos['bgGrad'] ?? 'linear-gradient(135deg,#0A0A0F 0%,#1a0a2e 100%)';
        $textColor = $estilos['textColor'] ?? '#F5F5F7';
        $titleColor = $estilos['titleColor'] ?? '#F5F5F7';
        $titleShadowStyle = $estilos['titleShadowStyle'] ?? 'none';
        $canvasAccentColor = $estilos['canvasAccentColor'] ?? '#FF3D57';
        $lineHeight = $estilos['lineHeight'] ?? '1.6';
        $letterSpacing = $estilos['letterSpacing'] ?? '0px';

        $canvasBg = $bgType === 'gradient' ? $bgGrad : $bgColor;

        $maxBlockBottom = 0;
        foreach ($blocks as $b) {
            $bY = intval($b['y'] ?? 0);
            $rawH = $b['h'] ?? '60';
            $bH = intval(preg_replace('/[^0-9]/', '', $rawH)) ?: 60;
            $bottom = $bY + $bH;
            if ($bottom > $maxBlockBottom) {
                $maxBlockBottom = $bottom;
            }
        }
        $calculatedHeight = max(20, $maxBlockBottom + 10);
        $isOwner = auth()->check() && (auth()->id() == ($blog->id_usuario ?? $blog->user_id ?? null));
    @endphp

    <!-- BARRA SUPERIOR FIJA EDGE-TO-EDGE -->
    <header class="fixed top-0 left-0 right-0 w-full z-50 bg-[#0C0C13]/90 backdrop-blur-md border-b border-white/10 px-4 sm:px-8 py-2.5 flex items-center justify-between shadow-lg">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#9A9AA5] hover:text-white transition-colors bg-white/5 px-3 py-1.5 rounded-xl border border-white/10">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Volver</span>
        </a>

        @if($isOwner)
            <div class="flex items-center gap-2">
                <a href="{{ route('blogs.edit', $blog->id_blog) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#7C5CFF]/20 text-[#7C5CFF] border border-[#7C5CFF]/40 hover:bg-[#7C5CFF]/30 transition-all shadow-[0_0_10px_rgba(124,92,255,0.2)]">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <span>Editar</span>
                </a>

                <button 
                    type="button" 
                    @click="showDeleteModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#FF3D57]/20 text-[#FF3D57] border border-[#FF3D57]/40 hover:bg-[#FF3D57] hover:text-white transition-all shadow-[0_0_10px_rgba(255,61,87,0.2)] cursor-pointer"
                >
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Eliminar</span>
                </button>
            </div>
        @endif
    </header>

    <!-- LIENZO PRINCIPAL DEL BLOG -->
    <div 
        class="w-full max-w-5xl rounded-2xl sm:rounded-3xl border border-white/10 overflow-hidden shadow-2xl relative transition-all my-2 sm:my-4"
        style="background: {{ $canvasBg }};"
    >
        <div 
            class="mx-auto w-full transition-all relative z-10 pt-6 sm:pt-8 pb-6 px-4 sm:px-12"
            style="max-width: {{ $canvasWidth }}; font-family: {{ $globalFont }}; letter-spacing: {{ $letterSpacing }}; color: {{ $textColor }};"
        >
            <!-- PORTADA -->
            @if(!empty($blog->portada))
                @php
                    $coverUrl = str_starts_with($blog->portada, 'http') 
                        ? $blog->portada 
                        : asset('storage/' . $blog->portada);
                @endphp
                <div class="relative rounded-2xl overflow-hidden mb-6 shadow-2xl h-48 sm:h-[360px] w-full">
                    <img src="{{ $coverUrl }}" alt="{{ $blog->titulo }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>
            @endif

            <!-- ARTISTA / VENUE -->
            @if(!empty($blog->artista) || !empty($blog->venue))
                <div 
                    class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] font-bold mb-4 border shadow-lg backdrop-blur-md"
                    style="background: {{ $canvasAccentColor }}22; color: {{ $canvasAccentColor }}; border-color: {{ $canvasAccentColor }}40;"
                >
                    <span>♪</span>
                    <span>{{ $blog->artista }}{{ !empty($blog->artista) && !empty($blog->venue) ? ' · ' : '' }}{{ $blog->venue }}</span>
                </div>
            @endif

            <!-- TÍTULO -->
            <h1 
                class="w-full font-extrabold leading-tight mb-2 break-words"
                style="font-size: clamp(26px, 6vw, 54px); color: {{ $titleColor }}; text-shadow: {{ $titleShadowStyle }};"
            >
                {{ $blog->titulo }}
            </h1>

            <!-- SUBTÍTULO -->
            @if(!empty($blog->subtitulo))
                <p 
                    class="w-full text-sm sm:text-lg mb-4 pb-3 break-words"
                    style="color: {{ $textColor }}DD; border-bottom: 1px solid {{ $canvasAccentColor }}25;"
                >
                    {{ $blog->subtitulo }}
                </p>
            @endif

            <!-- METADATOS -->
            <div class="flex items-center gap-4 py-2 text-[11px] mb-6 text-[#9A9AA5] border-y border-white/10">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ $blog->created_at ? $blog->created_at->format('d M, Y') : 'Reciente' }}</span>
                </div>
                <span>·</span>
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-eye"></i>
                    <span>{{ $blog->vistas ?? 0 }} lecturas</span>
                </div>
            </div>

            <!-- CONTENEDOR DE BLOQUES CON REFLUJO RESPONSIVE EN MÓVIL -->
            <div class="blog-canvas-container relative w-full" style="height: {{ $calculatedHeight }}px;">
                @foreach($blocks as $block)
                    @php
                        $bType = $block['type'] ?? 'text';
                        $bContent = $block['content'] ?? '';
                        $bX = $block['x'] ?? 0;
                        $bY = $block['y'] ?? 0;
                        $bW = $block['w'] ?? '100%';
                        $bH = $block['h'] ?? 'auto';
                        $bFontSize = $block['fontSize'] ?? ($bType === 'heading' ? 28 : ($bType === 'quote' ? 18 : ($bType === 'sticker' ? 64 : 16)));
                        $bColor = $block['color'] ?? $textColor;
                        $bBg = $block['bg'] ?? 'transparent';
                        $bAlign = $block['align'] ?? 'left';
                        $bBold = !empty($block['bold']);
                        $bItalic = !empty($block['italic']);
                        $bFont = !empty($block['font']) ? $block['font'] : $globalFont;

                        $mediaSrc = $bContent;
                        if (!empty($bContent) && !str_starts_with($bContent, 'data:') && !str_starts_with($bContent, 'http')) {
                            $mediaSrc = asset('storage/' . $bContent);
                        }
                    @endphp

                    <div 
                        class="blog-block-item rounded-xl transition-all"
                        style="--b-x: {{ $bX }}px; --b-y: {{ $bY }}px; --b-w: {{ $bW }}; --b-h: {{ $bH }};"
                    >
                        @if($bType === 'text' || $bType === 'heading')
                            <div 
                                class="w-full h-full whitespace-pre-line leading-relaxed break-words overflow-hidden"
                                style="color: {{ $bColor }}; font-family: {{ $bFont }}; font-size: {{ $bFontSize }}px; line-height: {{ $lineHeight }}; text-align: {{ $bAlign }}; font-weight: {{ $bBold ? 700 : 400 }}; font-style: {{ $bItalic ? 'italic' : 'normal' }}; background: {{ $bBg }}; padding: 6px;"
                            >
                                {{ $bContent }}
                            </div>
                        @elseif($bType === 'media' || $bType === 'image')
                            <div class="w-full h-full overflow-hidden rounded-2xl flex items-center justify-center bg-black/40 border border-white/10 shadow-lg max-sm:max-h-[380px]">
                                @if(!empty($block['mediaType']) && $block['mediaType'] === 'video')
                                    <video src="{{ $mediaSrc }}" autoplay muted loop playsinline controls class="w-full h-full object-cover max-sm:max-h-[380px]"></video>
                                @elseif(!empty($bContent))
                                    <img src="{{ $mediaSrc }}" alt="Media Block" class="w-full h-full object-cover block max-sm:max-h-[380px]">
                                @endif
                            </div>
                        @elseif($bType === 'sticker')
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="select-none leading-none" style="font-size: {{ $bFontSize }}px;">
                                    {{ $bContent ?: '🔥' }}
                                </span>
                            </div>
                        @elseif($bType === 'quote')
                            <div 
                                class="w-full h-full border-l-4 pl-3 italic whitespace-pre-line flex items-center break-words"
                                style="border-color: {{ $canvasAccentColor }}; color: {{ $bColor }}; font-size: {{ $bFontSize }}px; line-height: {{ $lineHeight }};"
                            >
                                {{ $bContent }}
                            </div>
                        @elseif($bType === 'divider')
                            <div class="w-full h-full flex items-center gap-3">
                                <div class="flex-1 h-px" style="background: {{ $canvasAccentColor }}30;"></div>
                                <div class="w-2 h-2 rounded-full" style="background: {{ $canvasAccentColor }};"></div>
                                <div class="flex-1 h-px" style="background: {{ $canvasAccentColor }}30;"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
    @if($isOwner)
        <div 
            x-show="showDeleteModal" 
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4"
            style="display: none;"
        >
            <div 
                @click.away="showDeleteModal = false"
                class="bg-[#12121A] border border-white/10 rounded-2xl p-6 max-w-sm w-full shadow-2xl flex flex-col gap-4 text-center"
            >
                <div class="w-12 h-12 rounded-full bg-[#FF3D57]/15 border border-[#FF3D57]/30 text-[#FF3D57] flex items-center justify-center mx-auto text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white mb-1">¿Eliminar este blog?</h3>
                    <p class="text-xs text-[#9A9AA5] leading-relaxed">
                        Esta acción borra el blog permanentemente de tu perfil y del feed.
                    </p>
                </div>

                <div class="flex items-center gap-2 mt-1">
                    <button 
                        type="button" 
                        @click="showDeleteModal = false"
                        class="flex-1 py-2 rounded-xl text-xs font-bold bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-colors cursor-pointer"
                    >
                        Cancelar
                    </button>

                    <form action="{{ route('blogs.destroy', $blog->id_blog) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="w-full py-2 rounded-xl text-xs font-bold bg-[#FF3D57] text-white hover:brightness-110 transition-all shadow-[0_0_12px_rgba(255,61,87,0.4)] cursor-pointer"
                        >
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</body>
</html>