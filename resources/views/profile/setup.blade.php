<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arma tu Perfil — ENCONCIERTA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-headline { font-family: 'Anton', sans-serif; letter-spacing: 0.03em; }
        .font-mono-code { font-family: 'Space Grotesk', monospace; }
        .glow-red { box-shadow: 0 0 30px -5px rgba(255, 61, 87, 0.35); }
    </style>
</head>
<body class="min-h-screen bg-[#0A0A0F] text-[#F5F5F7] antialiased selection:bg-[#FF3D57] selection:text-white flex flex-col justify-between relative overflow-x-hidden">

    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gradient-to-b from-[#FF3D57]/10 via-[#7C5CFF]/10 to-transparent rounded-full blur-[120px] pointer-events-none z-0"></div>

    <header class="relative z-10 px-6 py-5 flex items-center justify-between border-b border-white/5">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="px-3 py-1.5 rounded-xl border border-dashed border-white/30 bg-[#17171F] text-white/80 font-mono-code text-xs">
                [ ENCONCIERTA ]
            </div>
        </a>
        <div class="text-xs text-[#9A9AA5] font-mono-code">Paso 1 de 1</div>
    </header>

    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="bg-[#17171F] border border-white/10 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <h1 class="font-headline text-2xl sm:text-3xl uppercase tracking-wide text-white">
                    ARMA TU <span class="text-[#FF3D57]">PERFIL</span>
                </h1>
                <p class="text-xs text-[#9A9AA5]">
                    Define tu identidad para coordinar la previa con tu parche.
                </p>
            </div>

            <form id="profileSetupForm" onsubmit="handleProfileSave(event)" class="space-y-5" enctype="multipart/form-data">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-xs text-[#9A9AA5] font-medium">Foto de portada y perfil <span class="text-[#9A9AA5]/60 font-normal">(Opcional)</span></label>
                    
                    <div class="relative rounded-2xl overflow-hidden border border-white/10 bg-[#0A0A0F]">
                        
                        <label for="bannerInput" class="block relative h-32 w-full bg-cover bg-center cursor-pointer group/banner transition-all bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#7C5CFF]/30 via-[#17171F] to-[#0A0A0F]" id="bannerPreview">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover/banner:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2 text-white text-xs font-medium backdrop-blur-[2px]">
                                <i class="fa-solid fa-camera text-sm text-[#2FE6D0]"></i>
                                <span>Cambiar foto de portada</span>
                            </div>
                            <input type="file" id="bannerInput" name="portada" accept="image/*" class="hidden" onchange="handleBannerChange(event)">
                        </label>

                        <div class="p-4 pt-0 -mt-10 flex items-end justify-start relative z-10 pointer-events-none">
                            <label for="avatarInput" class="relative w-20 h-20 rounded-full overflow-hidden border-2 border-[#FF3D57] bg-[#0A0A0F] shadow-xl group/avatar cursor-pointer pointer-events-auto">
                                <img id="avatarPreview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23FFFFFF'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 4c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm0 14c-2.03 0-4.43-.82-6.14-2.88 0-2.01 4.09-3.12 6.14-3.12s6.14 1.11 6.14 3.12C16.43 19.18 14.03 20 12 20z'/%3E%3C/svg%3E" alt="Avatar" class="w-full h-full object-cover bg-[#232330] p-2">
                                <div class="absolute inset-0 bg-black/65 opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center text-white text-[10px] font-medium gap-0.5">
                                    <i class="fa-solid fa-camera text-xs text-[#FF3D57]"></i>
                                    <span>Cambiar</span>
                                </div>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="handleAvatarChange(event)">
                            </label>
                        </div>

                    </div>
                </div>

                <div>
                    <label for="inputName" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Nombre o apodo <span class="text-[#FF3D57]">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <input type="text" id="inputName" name="nombre" value="{{ old('nombre', $user->nombre) }}" required placeholder="Ej: Valeria" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all">
                    </div>
                </div>

                <div>
                    <label for="inputHandle" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Nombre de usuario <span class="text-[#FF3D57]">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#FF3D57] font-bold">@</span>
                        <input type="text" id="inputHandle" name="handle" value="{{ old('handle', $user->handle) }}" required placeholder="tu_usuario" oninput="sanitizeHandle(this)" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-8 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all font-mono-code">
                    </div>
                </div>

                <div>
                    <label for="inputCity" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Ciudad <span class="text-[#9A9AA5]/60 font-normal">(Opcional)</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5]"></i>
                        <select id="inputCity" name="ciudad" onchange="handleCityChange()" class="w-full bg-[#0A0A0F] border border-white/15 rounded-xl py-3 pl-10 pr-10 text-xs sm:text-sm text-white appearance-none cursor-pointer">
                            <option value="">Selecciona tu ciudad</option>
                            <option value="Bogotá" {{ $user->ciudad === 'Bogotá' ? 'selected' : '' }}>Bogotá</option>
                            <option value="Medellín" {{ $user->ciudad === 'Medellín' ? 'selected' : '' }}>Medellín</option>
                            <option value="Cali" {{ $user->ciudad === 'Cali' ? 'selected' : '' }}>Cali</option>
                            <option value="Eje Cafetero" {{ $user->ciudad === 'Eje Cafetero' ? 'selected' : '' }}>Eje Cafetero</option>
                            <option value="Barranquilla" {{ $user->ciudad === 'Barranquilla' ? 'selected' : '' }}>Barranquilla</option>
                            <option value="Bucaramanga" {{ $user->ciudad === 'Bucaramanga' ? 'selected' : '' }}>Bucaramanga</option>
                            <option value="Otra">Otra ciudad...</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-[#9A9AA5] pointer-events-none"></i>
                    </div>
                </div>

                <div id="customCityWrapper" class="hidden transition-all duration-300">
                    <label for="inputCustomCity" class="block text-xs text-[#9A9AA5] mb-1 font-medium">Escribe el nombre de tu ciudad <span class="text-[#FF3D57]">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-city absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#2FE6D0]"></i>
                        <input type="text" id="inputCustomCity" name="custom_ciudad" placeholder="Ej: Pasto, Tunja, Santa Marta..." class="w-full bg-[#0A0A0F] border border-[#2FE6D0]/40 rounded-xl py-3 pl-10 pr-4 text-xs sm:text-sm text-white placeholder-[#9A9AA5]/50 transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#FF3D57] hover:bg-[#FF3D57]/90 text-white font-bold text-xs sm:text-sm py-3.5 rounded-xl glow-red transition-all cursor-pointer flex items-center justify-center gap-2">
                        <span>Guardar y continuar</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </form>

        </div>
    </main>

    <footer class="relative z-10 py-4 text-center text-[11px] text-[#9A9AA5] border-t border-white/5">
        <p>© 2026 ENCONCIERTA — Sincroniza tu parche, vive el show.</p>
    </footer>

    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden bg-[#17171F] border border-[#2FE6D0]/60 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 transform translate-y-4 transition-all">
        <i class="fa-solid fa-circle-check text-[#2FE6D0] text-base"></i>
        <span id="toastMsg" class="text-xs font-medium">Perfil actualizado</span>
    </div>

    <script>
        function handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("avatarPreview").src = e.target.result;
                    showToast("Foto de perfil cargada");
                };
                reader.readAsDataURL(file);
            }
        }

        function handleBannerChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("bannerPreview").style.backgroundImage = `url('${e.target.result}')`;
                    showToast("Portada cargada");
                };
                reader.readAsDataURL(file);
            }
        }

        function sanitizeHandle(input) {
            input.value = input.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        }

        function handleCityChange() {
            const citySelect = document.getElementById("inputCity");
            const customCityWrapper = document.getElementById("customCityWrapper");
            const customCityInput = document.getElementById("inputCustomCity");

            if (citySelect.value === "Otra") {
                customCityWrapper.classList.remove("hidden");
                customCityInput.required = true;
                customCityInput.focus();
            } else {
                customCityWrapper.classList.add("hidden");
                customCityInput.required = false;
                customCityInput.value = "";
            }
        }

        async function handleProfileSave(e) {
            e.preventDefault();
            const form = document.getElementById("profileSetupForm");
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('profile.setup.save') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json"
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    let errorMsg = data.message || "Error al actualizar perfil";
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        errorMsg = data.errors[firstKey][0];
                    }
                    showToast(errorMsg);
                }
            } catch (err) {
                showToast("Error de conexión con el servidor.");
            }
        }

        function showToast(message) {
            const toast = document.getElementById("toast");
            const toastMsg = document.getElementById("toastMsg");
            toastMsg.innerText = message;
            
            toast.classList.remove("hidden", "translate-y-4");
            toast.classList.add("translate-y-0");

            setTimeout(() => {
                toast.classList.add("translate-y-4");
                setTimeout(() => toast.classList.add("hidden"), 300);
            }, 2200);
        }
    </script>
</body>
</html>