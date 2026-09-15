<div class="rounded-2xl overflow-hidden bg-[#0C0C13] border border-white/5">
    <div class="flex border-b border-white/5">
        <button type="button" id="tab-moment" onclick="switchComposeType('review')" class="flex-1 py-3 text-xs font-bold tracking-wider uppercase transition-all relative text-[#FFB020]">
            Registrar Momento
            <span id="indicator-moment" class="absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-[#FFB020]"></span>
        </button>
        <button type="button" id="tab-parche" onclick="switchComposeType('parche')" class="flex-1 py-3 text-xs font-bold tracking-wider uppercase transition-all relative text-[#6A6A75]">
            Armar Parche
            <span id="indicator-parche" class="hidden absolute bottom-0 left-4 right-4 h-0.5 rounded-full bg-[#2FE6D0]"></span>
        </button>
    </div>

    <form action="#" method="POST" class="p-4 flex flex-col gap-3">
        @csrf
        <input type="hidden" name="tipo" id="compose-type" value="review">

        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/[0.03] border border-white/10">
            <i class="fa-solid fa-music text-[#2FE6D0] text-xs"></i>
            <input type="text" name="show" class="flex-1 bg-transparent text-[#F5F5F7] text-xs outline-none placeholder-[#9A9AA5]" placeholder="¿De qué show es este momento?">
        </div>

        <div class="flex items-start gap-3">
            <img src="{{ auth()->user()->avatar ?? 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=120&h=120&fit=crop&auto=format' }}" alt="Usuario" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
            <textarea name="contenido" rows="3" class="flex-1 bg-transparent text-[#F5F5F7] text-sm resize-none outline-none placeholder-[#9A9AA5] leading-relaxed" placeholder="¿Cómo fue la vibra? Cuéntale a la tribu…"></textarea>
        </div>

        <div class="flex items-center justify-between border-t border-white/5 pt-3">
            <div class="flex items-center gap-1">
                <button type="button" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#2FE6D0] hover:bg-white/5 transition-all">
                    <i class="fa-solid fa-camera"></i>
                </button>
                <button type="button" class="p-2 rounded-xl text-[#9A9AA5] hover:text-[#FFB020] hover:bg-white/5 transition-all">
                    <i class="fa-regular fa-face-smile"></i>
                </button>
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold tracking-wide uppercase transition-all bg-[#FF3D57] text-white hover:brightness-110 shadow-[0_0_12px_rgba(255,61,87,0.3)]">
                Publicar
            </button>
        </div>
    </form>
</div>