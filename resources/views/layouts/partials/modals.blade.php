<!-- MODAL: MATCHING WIZARD -->
<div id="wizardModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-[#0A0A0F]/85 backdrop-blur-md">
    <div class="bg-[#17171F] border border-white/15 rounded-3xl max-w-lg w-full p-6 sm:p-8 relative shadow-2xl overflow-hidden">
        
        <button onclick="closeWizardModal()" class="absolute top-6 right-6 text-[#9A9AA5] hover:text-white text-lg p-2 cursor-pointer" aria-label="Cerrar modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="mb-6">
            <span class="text-xs font-mono-code text-[#2FE6D0] uppercase tracking-wider">Paso <span id="wizardStepNum">1</span> de 3</span>
            <h3 class="font-headline text-2xl sm:text-3xl text-white mt-1" id="wizardTitle">¿A cuál show vas a asistir?</h3>
        </div>

        <div id="wizardStep1" class="space-y-4">
            <label class="text-xs text-[#9A9AA5] block">Selecciona o busca la fecha:</label>
            <select id="wizardShowSelect" class="w-full bg-[#0A0A0F] border border-white/20 rounded-xl p-3 text-sm text-white focus:border-[#FF3D57]">
                <option value="Morat - Movistar Arena">Morat - Movistar Arena (Bogotá)</option>
                <option value="Bomba Estéreo - Simón Bolívar">Bomba Estéreo - Simón Bolívar (Bogotá)</option>
                <option value="Feid - La Macarena">Feid - La Macarena (Medellín)</option>
                <option value="Monsieur Periné - Teatro Metropolitano">Monsieur Periné (Medellín)</option>
                <option value="Karol G - Pascual Guerrero">Karol G - Pascual Guerrero (Cali)</option>
            </select>

            <div class="pt-4">
                <button onclick="nextWizardStep(2)" class="w-full bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold py-3.5 rounded-xl glow-primary transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>Siguiente: Define tu vibra</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        <div id="wizardStep2" class="hidden space-y-4">
            <label class="text-xs text-[#9A9AA5] block">¿Cómo prefieres vivir la previa?</label>
            <div class="grid grid-cols-1 gap-2.5">
                <button onclick="selectVibeOption(this, 'Fila Temprano')" class="vibe-opt w-full p-3 rounded-xl border border-white/10 bg-[#0A0A0F] text-left text-sm text-white hover:border-[#7C5CFF] flex items-center justify-between cursor-pointer">
                    <span>🔥 Fila desde temprano (Primera fila)</span>
                    <i class="fa-solid fa-circle-check opacity-0 text-[#7C5CFF]"></i>
                </button>
                <button onclick="selectVibeOption(this, 'Previa Cerveza')" class="vibe-opt w-full p-3 rounded-xl border border-white/10 bg-[#0A0A0F] text-left text-sm text-white hover:border-[#7C5CFF] flex items-center justify-between cursor-pointer">
                    <span>🍻 Previa en bar/parque cercano</span>
                    <i class="fa-solid fa-circle-check opacity-0 text-[#7C5CFF]"></i>
                </button>
                <button onclick="selectVibeOption(this, 'Pogo Intenso')" class="vibe-opt w-full p-3 rounded-xl border border-white/10 bg-[#0A0A0F] text-left text-sm text-white hover:border-[#7C5CFF] flex items-center justify-between cursor-pointer">
                    <span>⚡ Pogo y energía al 100%</span>
                    <i class="fa-solid fa-circle-check opacity-0 text-[#7C5CFF]"></i>
                </button>
            </div>

            <div class="flex gap-3 pt-4">
                <button onclick="nextWizardStep(1)" class="w-1/3 bg-[#232330] text-white font-medium py-3 rounded-xl cursor-pointer">
                    Atrás
                </button>
                <button onclick="nextWizardStep(3)" class="w-2/3 bg-[#FF3D57] text-white font-bold py-3.5 rounded-xl glow-primary cursor-pointer">
                    Siguiente
                </button>
            </div>
        </div>

        <div id="wizardStep3" class="hidden space-y-4 text-center py-4">
            <div class="w-16 h-16 rounded-full bg-[#2FE6D0]/20 text-[#2FE6D0] text-3xl flex items-center justify-center mx-auto mb-2 animate-bounce">
                <i class="fa-solid fa-tower-cell"></i>
            </div>
            <h4 class="text-xl font-bold text-white">¡Sintonización Encontrada!</h4>
            <p class="text-sm text-[#9A9AA5]">
                Hay <strong class="text-white">12 melómanos</strong> armando la previa para este show con tu misma vibra.
            </p>
            <div class="bg-[#0A0A0F] p-4 rounded-xl border border-white/10 text-left text-xs text-[#9A9AA5] space-y-1">
                <div>📍 Punto de encuentro sugerido: <span class="text-white">Entrada Norte / Bar La Previa</span></div>
                <div>🕒 Hora recomendada: <span class="text-white">4:30 PM</span></div>
            </div>

            <button onclick="confirmJoinParche()" class="w-full bg-[#7C5CFF] hover:bg-[#7C5CFF]/90 text-white font-bold py-3.5 rounded-xl glow-secondary transition-all cursor-pointer">
                Sincronizarme con este parche
            </button>
        </div>

    </div>
</div>

<!-- MODAL: AUTH / REGISTRO -->
<div id="authModal" class="hidden fixed inset-0 z-50 items-center justify-center p-3 sm:p-4 bg-[#0A0A0F]/85 backdrop-blur-xl transition-all duration-300">
    
    <!-- Modal Card Container -->
    <div class="bg-[#17171F] border border-white/15 rounded-3xl max-w-md w-full p-6 sm:p-8 relative shadow-2xl overflow-hidden my-auto max-h-[92vh] flex flex-col justify-between" style="box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.9), 0 0 30px rgba(124, 92, 255, 0.15);">
        
        <!-- Modal Close Button -->
        <button onclick="closeAuthModal()" class="absolute top-5 right-5 text-[#9A9AA5] hover:text-white text-lg p-2 rounded-full transition-colors cursor-pointer z-20" aria-label="Cerrar modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Modal Header -->
        <div class="text-center pt-2 pb-4">
            <div class="px-3.5 py-1.5 rounded-xl border border-dashed border-white/30 bg-[#0A0A0F] text-white/80 font-mono-code text-xs inline-block mb-3">
                [ ENCONCIERTA ]
            </div>
            
            <h2 class="font-headline text-2xl sm:text-3xl uppercase text-white tracking-wide leading-tight">
                ENTRA A <span class="text-[#FF3D57]">ENCONCIERTA</span>
            </h2>
            <p class="text-xs text-[#9A9AA5] mt-1">Sincroniza tu parche, vive el show.</p>

            <!-- TAB SWITCHER: Iniciar Sesión vs Registrarse -->
            <div class="mt-5 p-1 bg-[#0A0A0F] rounded-2xl border border-white/10 grid grid-cols-2 gap-1 text-xs font-semibold">
                <button id="tabLogin" onclick="switchAuthTab('login')" class="py-2.5 rounded-xl transition-all duration-200 text-white bg-[#232330] shadow-md cursor-pointer">
                    Iniciar sesión
                </button>
                <button id="tabRegister" onclick="switchAuthTab('register')" class="py-2.5 rounded-xl transition-all duration-200 text-[#9A9AA5] hover:text-white cursor-pointer">
                    Registrarse
                </button>
            </div>
        </div>

        <!-- Scrollable Modal Body -->
        <div class="overflow-y-auto pr-1 -mr-1 space-y-4 my-2">

            <!-- SOCIAL AUTH BUTTONS -->
            <div class="space-y-2.5">
                <!-- Spotify Connect Button -->
                <button onclick="handleSocialAuth('Spotify')" class="w-full bg-[#1DB954]/15 hover:bg-[#1DB954]/25 text-[#1DB954] border border-[#1DB954]/40 font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer group hover:border-[#1DB954] glow-spotify">
                    <i class="fa-brands fa-spotify text-lg group-hover:scale-110 transition-transform"></i>
                    <span>Conectar con Spotify</span>
                </button>

                <!-- Google Auth Button -->
                <button onclick="handleSocialAuth('Google')" class="w-full bg-[#232330] hover:bg-[#323242] text-white border border-white/10 font-medium text-xs sm:text-sm py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer group">
                    <i class="fa-brands fa-google text-[#FF3D57] group-hover:scale-110 transition-transform"></i>
                    <span>Continuar con Google</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-4">
                <div class="h-px flex-1 bg-white/10"></div>
                <span class="text-[10px] font-mono-code text-[#9A9AA5] uppercase tracking-wider">o con tu correo</span>
                <div class="h-px flex-1 bg-white/10"></div>
            </div>

            <!-- FORM 1: INICIAR SESIÓN -->
            <form id="formLogin" onsubmit="submitLogin(event)" class="space-y-3.5">
                @csrf
                <div>
                    <label for="loginEmail" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Correo electrónico</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="email" id="loginEmail" name="email" required placeholder="tu.nombre@correo.com" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all duration-200">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="loginPass" class="text-xs text-[#9A9AA5] font-medium">Contraseña</label>
                        <a href="#" onclick="forgotPassword(event)" class="text-[11px] text-[#7C5CFF] hover:text-[#9B80FF] transition-colors">¿Olvidaste tu clave?</a>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="password" id="loginPass" name="password" required placeholder="••••••••" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-10 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all duration-200">
                        <button type="button" onclick="togglePassword('loginPass', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5] hover:text-white cursor-pointer" aria-label="Mostrar u ocultar contraseña">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-1">
                    <label for="rememberMe" class="inline-flex items-center gap-2.5 cursor-pointer group select-none">
                        <input type="checkbox" id="rememberMe" name="remember" class="sr-only peer">
                        <div class="w-5 h-5 rounded-md border border-white/20 bg-[#0A0A0F] flex items-center justify-center transition-all duration-200 group-hover:border-[#7C5CFF]/70 peer-checked:bg-[#7C5CFF] peer-checked:border-[#7C5CFF] peer-checked:shadow-[0_0_12px_rgba(124,92,255,0.45)] peer-checked:[&_i]:opacity-100 peer-checked:[&_i]:scale-100">
                            <i class="fa-solid fa-check text-[10px] text-white opacity-0 scale-75 transition-all duration-200"></i>
                        </div>
                        <span class="text-xs text-[#9A9AA5] group-hover:text-white transition-colors font-medium">Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="w-full mt-2 bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold text-sm py-3.5 rounded-xl glow-red transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                    <span>Iniciar sesión</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- FORM 2: REGISTRARSE -->
            <form id="formRegister" onsubmit="submitRegister(event)" class="hidden space-y-3.5">
                @csrf
                <div>
                    <label for="regName" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Nombre o apodo</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="text" id="regName" name="nombre" required placeholder="Ej: Valeria" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label for="regEmail" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Correo electrónico</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="email" id="regEmail" name="email" required placeholder="tu.correo@correo.com" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label for="regPass" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Contraseña</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="password" id="regPass" name="password" minlength="8" required placeholder="Mínimo 8 caracteres" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-10 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all duration-200">
                        <button type="button" onclick="togglePassword('regPass', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5] hover:text-white cursor-pointer" aria-label="Mostrar u ocultar contraseña">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-1">
                    <label for="regTerms" class="inline-flex items-center gap-2.5 cursor-pointer group select-none">
                        <input type="checkbox" id="regTerms" required class="sr-only peer">
                        <div class="w-5 h-5 rounded-md border border-white/20 bg-[#0A0A0F] shrink-0 flex items-center justify-center transition-all duration-200 group-hover:border-[#7C5CFF]/70 peer-checked:bg-[#7C5CFF] peer-checked:border-[#7C5CFF] peer-checked:shadow-[0_0_12px_rgba(124,92,255,0.45)] peer-checked:[&_i]:opacity-100 peer-checked:[&_i]:scale-100">
                            <i class="fa-solid fa-check text-[10px] text-white opacity-0 scale-75 transition-all duration-200"></i>
                        </div>
                        <span class="text-xs text-[#9A9AA5] group-hover:text-white transition-colors font-medium">
                            Acepto los <a href="#" onclick="showTermsNotice(event)" class="text-[#2FE6D0] hover:underline">términos y condiciones</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full mt-2 bg-[#7C5CFF] hover:bg-[#7C5CFF]/90 text-white font-bold text-sm py-3.5 rounded-xl glow-violet transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                    <span>Crear cuenta</span>
                    <i class="fa-solid fa-user-plus text-xs"></i>
                </button>
            </form>

        </div>

        <!-- Modal Footer Pledge -->
        <div class="pt-4 mt-2 border-t border-white/10 text-center">
            <p class="text-[11px] text-[#9A9AA5] flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-[#2FE6D0]"></i>
                <span>Comunidad 100% verificada para la cultura en vivo.</span>
            </p>
        </div>

    </div>
</div>

<!-- TOAST NOTIFICATION CONTAINER -->
<div id="toast" class="fixed bottom-6 right-6 z-50 bg-[#17171F] border border-[#2FE6D0]/50 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 transform translate-y-4 opacity-0 pointer-events-none transition-all duration-300">
    <i class="fa-solid fa-circle-check text-[#2FE6D0] text-lg"></i>
    <span id="toastMessage" class="text-sm font-medium">Acción completada</span>
</div>