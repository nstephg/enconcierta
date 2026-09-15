<footer class="bg-[#0A0A0F] border-t border-white/10 pt-16 pb-12 relative z-10 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mb-12">
            
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-4">
                    <div class="px-3.5 py-2 rounded-xl border border-dashed border-white/30 bg-[#17171F]/40 text-white/70 font-mono-code text-xs font-semibold tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#FF3D57]"></span>
                        <span>[ ENCONCIERTA ]</span>
                    </div>
                </a>
                <p class="text-[#9A9AA5] text-sm mb-6 max-w-sm">
                    "Sincroniza tu parche, vive el show." <br/>
                    La plataforma social que erradica la soledad en la cultura musical en Colombia.
                </p>

                <div class="flex items-center gap-4">
                    <a href="#" class="w-9 h-9 rounded-full bg-[#17171F] hover:bg-[#7C5CFF] text-white flex items-center justify-center border border-white/10 transition-colors" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-[#17171F] hover:bg-[#7C5CFF] text-white flex items-center justify-center border border-white/10 transition-colors" aria-label="TikTok">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-[#17171F] hover:bg-[#7C5CFF] text-white flex items-center justify-center border border-white/10 transition-colors" aria-label="X Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-[#17171F] hover:bg-[#7C5CFF] text-white flex items-center justify-center border border-white/10 transition-colors" aria-label="Spotify">
                        <i class="fa-brands fa-spotify"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-mono-code text-xs uppercase text-white font-bold tracking-wider mb-4">Navegación</h4>
                <ul class="space-y-2.5 text-[#9A9AA5]">
                    <li><a href="#como-funciona" class="hover:text-white transition-colors">Cómo funciona</a></li>
                    <li><a href="#shows-section" class="hover:text-white transition-colors">Explorar shows</a></li>
                    <li><a href="#comunidad" class="hover:text-white transition-colors">Memorias compartidas</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-mono-code text-xs uppercase text-white font-bold tracking-wider mb-4">Circuitos Local</h4>
                <ul class="space-y-2.5 text-[#9A9AA5]">
                    <li><a href="#shows-section" class="hover:text-white transition-colors">Bogotá Escenarios</a></li>
                    <li><a href="#shows-section" class="hover:text-white transition-colors">Medellín Tarimas</a></li>
                    <li><a href="#shows-section" class="hover:text-white transition-colors">Cali Circuito</a></li>
                    <li><a href="#shows-section" class="hover:text-white transition-colors">Eje Cafetero Fest</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-mono-code text-xs uppercase text-white font-bold tracking-wider mb-4">Manifiesto</h4>
                <ul class="space-y-2.5 text-[#9A9AA5]">
                    <li><a href="#" onclick="showToast('Código de convivencia en la tribu')" class="hover:text-white transition-colors">Código de convivencia</a></li>
                    <li><a href="#" onclick="showToast('Protocolo de seguridad en previas')" class="hover:text-white transition-colors">Seguridad en previa</a></li>
                    <li><a href="#" onclick="showToast('Términos y privacidad')" class="hover:text-white transition-colors">Términos de servicio</a></li>
                </ul>
            </div>

        </div>

        <div class="pt-8 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between text-xs text-[#9A9AA5] gap-4">
            <div>
                © {{ date('Y') }} ENCONCIERTA. Todos los derechos reservados.
            </div>
            <div class="font-mono-code flex items-center gap-1">
                Hecho con <i class="fa-solid fa-heart text-[#FF3D57]"></i> para la escena musical colombiana.
            </div>
        </div>
    </div>
</footer>