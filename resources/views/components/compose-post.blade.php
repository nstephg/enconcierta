@props(['eventos' => []])

<div class="rounded-2xl bg-[#0C0C13] border border-white/5 relative z-20">
    <!-- Header Estático Visual (Registrar Momento) -->
    <div class="px-4 py-3 border-b border-white/5 bg-[#0C0C13] rounded-t-2xl flex items-center justify-center">
        <span class="text-xs font-bold tracking-wider uppercase text-[#FFB020]">
            Registrar Momento
        </span>
    </div>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="p-4 flex flex-col gap-3">
        @csrf
        <input type="hidden" name="tipo" value="review">
        <div id="gifInputsContainer"></div>

        <!-- Input Show -->
        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/[0.03] border border-white/10">
            <i class="fa-solid fa-music text-[#2FE6D0] text-xs"></i>
            <input type="text" name="show_nombre" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="flex-1 bg-transparent text-[#F5F5F7] text-xs outline-none placeholder-[#9A9AA5]" placeholder="¿De qué show es este momento? (Opcional)">
        </div>

        <!-- Input Contenido -->
        <div class="flex items-start gap-3">
            <a href="{{ route('profile.show') }}" class="flex-shrink-0">
                <img src="{{ auth()->user()->avatar_url }}" alt="Usuario" class="w-9 h-9 rounded-full object-cover border border-white/10 hover:border-[#FF3D57] transition-all">
            </a>
            <textarea id="composeTextarea" name="contenido" rows="3" class="flex-1 bg-transparent text-[#F5F5F7] text-sm resize-none outline-none focus:outline-none focus:ring-0 placeholder-[#9A9AA5] leading-relaxed" placeholder="¿Mmm?"></textarea>
        </div>

        <!-- Carrusel de Previsualización de Medios Adjuntos -->
        <div id="mediaPreviewContainer" class="hidden w-full overflow-x-auto flex gap-2.5 pb-2 pt-1 no-scrollbar snap-x"></div>

        <!-- Extra: Input Ubicación -->
        <div id="locationInputBox" class="hidden flex items-center gap-2 px-3 py-2 rounded-xl bg-[#17171F] border border-[#2FE6D0]/40 text-xs">
            <i class="fa-solid fa-location-dot text-[#2FE6D0]"></i>
            <input type="text" name="ubicacion" id="inputUbicacion" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="flex-1 bg-transparent text-white outline-none placeholder-[#9A9AA5]" placeholder="Escribe el lugar o ciudad del show...">
            <button type="button" onclick="toggleLocationInput()" class="text-[#9A9AA5] hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Extra: Creador de Encuestas Dinámicas -->
        <div id="pollInputBox" class="hidden flex flex-col gap-2 p-3.5 rounded-xl bg-[#17171F] border border-[#7C5CFF]/40 text-xs">
            <div class="flex items-center justify-between text-[#7C5CFF] font-semibold">
                <span><i class="fa-solid fa-chart-simple mr-1"></i> Crear Encuesta</span>
                <button type="button" onclick="togglePollInput()" class="text-[#9A9AA5] hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <input type="text" name="encuesta_pregunta" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="bg-[#0A0A0F] border border-white/10 rounded-lg p-2.5 text-white outline-none focus:border-[#7C5CFF]" placeholder="Pregunta de la encuesta...">
            
            <div id="pollOptionsContainer" class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <input type="text" name="encuesta_opciones[]" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="flex-1 bg-[#0A0A0F] border border-white/10 rounded-lg p-2 text-white outline-none" placeholder="Opción 1">
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="encuesta_opciones[]" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="flex-1 bg-[#0A0A0F] border border-white/10 rounded-lg p-2 text-white outline-none" placeholder="Opción 2">
                </div>
            </div>

            <button type="button" id="btnAddPollOption" onclick="addPollOption()" class="mt-1 self-start text-[#2FE6D0] hover:underline font-semibold text-[11px] flex items-center gap-1">
                <i class="fa-solid fa-plus text-[10px]"></i> Agregar otra opción
            </button>
        </div>

        <!-- Toolbar Multimedia -->
        <div class="flex items-center justify-between border-t border-white/5 pt-3 relative">
            <div class="flex items-center gap-1.5 relative">
                <!-- Adjuntar Fotos -->
                <label for="postImageInput" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#2FE6D0] hover:bg-white/5 transition-all cursor-pointer flex items-center justify-center" title="Adjuntar fotos o videos (máx. 6)">
                    <i class="fa-regular fa-image text-base"></i>
                </label>
                <input type="file" id="postImageInput" name="imagenes[]" accept="image/*,video/*" multiple class="hidden" onchange="handleImagesSelected(this)">

                <!-- GIF Toggle -->
                <button type="button" id="btnGifToggle" onclick="toggleGifPicker(event)" class="px-2 py-0.5 text-[10px] font-extrabold rounded-md border border-white/20 text-white/80 hover:text-white hover:border-[#2FE6D0] transition-all cursor-pointer" title="Insertar GIF">
                    GIF
                </button>

                <!-- Emoji Toggle -->
                <button type="button" id="btnEmojiToggle" onclick="toggleEmojiPicker(event)" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#FFB020] hover:bg-white/5 transition-all cursor-pointer" title="Agregar emoji">
                    <i class="fa-regular fa-face-smile text-base"></i>
                </button>

                <!-- Ubicación Toggle -->
                <button type="button" onclick="toggleLocationInput()" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#2FE6D0] hover:bg-white/5 transition-all cursor-pointer" title="Agregar ubicación">
                    <i class="fa-solid fa-location-dot text-base"></i>
                </button>

                <!-- Encuesta Toggle -->
                <button type="button" onclick="togglePollInput()" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#7C5CFF] hover:bg-white/5 transition-all cursor-pointer" title="Crear encuesta">
                    <i class="fa-solid fa-chart-simple text-base"></i>
                </button>

                <!-- EMOJI MART PICKER POPOVER -->
                <div id="emojiPicker" class="hidden absolute top-full mt-2 left-0 z-[100] rounded-2xl shadow-2xl overflow-hidden border border-white/10 bg-[#17171F]">
                    <div class="flex items-center justify-between px-3 py-2 border-b border-white/5 bg-[#121218]">
                        <span class="text-[11px] font-semibold text-[#9A9AA5]">Emojis</span>
                        <button type="button" onclick="toggleEmojiPicker(event)" class="text-[#9A9AA5] hover:text-white text-xs"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <emoji-picker class="dark text-xs" style="--num-columns: 8; --emoji-size: 1.25rem; height: 330px; width: 320px;"></emoji-picker>
                </div>

                <!-- GIPHY POP-OVER -->
                <div id="giphyPicker" class="hidden absolute top-full mt-2 left-0 z-[100] bg-[#17171F] border border-white/10 rounded-2xl p-3.5 shadow-2xl w-80 sm:w-[440px] flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-[#FFB020]"></i> GIPHY
                        </span>
                        <button type="button" onclick="toggleGifPicker(event)" class="text-[#9A9AA5] hover:text-white text-xs"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <div class="flex items-center gap-2 bg-[#0A0A0F] border border-white/10 rounded-xl px-3 py-2">
                        <i class="fa-solid fa-magnifying-glass text-xs text-[#9A9AA5]"></i>
                        <input type="text" id="giphySearchInput" onkeydown="handleGiphySearchKeydown(event)" onkeyup="searchGiphy(this.value)" class="flex-1 bg-transparent text-xs text-white outline-none placeholder-[#9A9AA5]" placeholder="Buscar GIFs de conciertos, artistas...">
                    </div>

                    <div id="giphyResults" class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-72 overflow-y-auto pr-1 no-scrollbar">
                        <div class="text-center text-[#9A9AA5] text-[11px] col-span-3 py-8">Cargando GIFs...</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold tracking-wide uppercase transition-all bg-[#FF3D57] text-white hover:brightness-110 shadow-[0_0_12px_rgba(255,61,87,0.3)] cursor-pointer">
                Publicar
            </button>
        </div>
    </form>
</div>