import './compose.js';
import './post-detail.js';
import './profile.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

let selectedShowForSync = null;

window.toggleProfileSettings = function() {
    const editSection = document.getElementById('tab-content-editar');
    const isEditing = editSection && !editSection.classList.contains('hidden');

    if (isEditing) {
        window.switchProfileTab('posts');
    } else {
        window.switchProfileTab('editar');
    }
};

// 1. MANEJO DE TRANSPARENCIA Y NAVEGACIÓN DEL HEADER
window.addEventListener("scroll", function() {
    const header = document.getElementById("mainHeader");
    if (!header) return;
    if (window.scrollY > 20) {
        header.classList.remove("bg-transparent", "border-transparent");
        header.classList.add("bg-[#0A0A0F]/95", "border-white/10", "backdrop-blur-md", "shadow-xl");
    } else {
        header.classList.add("bg-transparent", "border-transparent");
        header.classList.remove("bg-[#0A0A0F]/95", "border-white/10", "backdrop-blur-md", "shadow-xl");
    }
});

// 2. FUNCIÓN UNIFICADA DE MENÚ MÓVIL
window.toggleMobileMenu = function() {
    const drawer = document.getElementById("mobileDrawer");
    const icon = document.getElementById("mobileMenuIcon");
    const header = document.getElementById("mainHeader");
    if (!drawer) return;

    drawer.style.removeProperty('display');
    const isHidden = drawer.classList.toggle("hidden");

    // Bloquear scroll del cuerpo cuando el menú está abierto
    document.body.classList.toggle("overflow-hidden", !isHidden);

    if (header) {
        // Desactivar temporalmente la transición CSS para que el cambio de color sea instantáneo
        header.style.transition = 'none';

        if (!isHidden) {
            header.classList.add("bg-[#0A0A0F]", "border-white/10");
            header.classList.remove("bg-transparent", "border-transparent");
        } else if (window.scrollY <= 20) {
            header.classList.remove("bg-[#0A0A0F]", "border-white/10");
            header.classList.add("bg-transparent", "border-transparent");
        }

        // Restablecer la transición fluida para el scroll normal en el siguiente frame
        setTimeout(() => {
            header.style.transition = '';
        }, 50);
    }

    if (icon) {
        if (isHidden) {
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");
        } else {
            icon.classList.remove("fa-bars");
            icon.classList.add("fa-xmark");
        }
    }
};

// 3. WIZARD MODAL PARCHE
window.openWizardModal = function() {
    const modal = document.getElementById("wizardModal");
    if (modal) {
        modal.classList.remove("hidden");
        window.nextWizardStep(1);
    }
};

window.closeWizardModal = function() {
    const modal = document.getElementById("wizardModal");
    if (modal) modal.classList.add("hidden");
};

window.nextWizardStep = function(step) {
    const num = document.getElementById("wizardStepNum");
    const title = document.getElementById("wizardTitle");
    const s1 = document.getElementById("wizardStep1");
    const s2 = document.getElementById("wizardStep2");
    const s3 = document.getElementById("wizardStep3");

    if (num) num.innerText = step;
    if (s1) s1.classList.add("hidden");
    if (s2) s2.classList.add("hidden");
    if (s3) s3.classList.add("hidden");

    if (step === 1) {
        if (title) title.innerText = "¿A cuál show vas a asistir?";
        if (s1) s1.classList.remove("hidden");
    } else if (step === 2) {
        if (title) title.innerText = "¿Cómo prefieres vivir la previa?";
        if (s2) s2.classList.remove("hidden");
    } else if (step === 3) {
        if (title) title.innerText = "Sintonización Completa";
        if (s3) s3.classList.remove("hidden");
    }
};

window.selectVibeOption = function(btn, vibeName) {
    document.querySelectorAll(".vibe-opt").forEach(b => {
        b.classList.remove("border-[#7C5CFF]", "bg-[#7C5CFF]/10");
        const icon = b.querySelector(".fa-circle-check");
        if (icon) icon.classList.add("opacity-0");
    });
    btn.classList.add("border-[#7C5CFF]", "bg-[#7C5CFF]/10");
    const icon = btn.querySelector(".fa-circle-check");
    if (icon) icon.classList.remove("opacity-0");
};

window.confirmJoinParche = function() {
    window.closeWizardModal();
    window.showToast("¡Te has sincronizado con éxito al parche!");
};

// 4. SYNC MODAL
window.openSyncModal = function(showTitle) {
    selectedShowForSync = showTitle;
    const title = document.getElementById("syncModalShowTitle");
    const modal = document.getElementById("syncModal");
    if (title) title.innerText = showTitle;
    if (modal) modal.classList.remove("hidden");
};

window.closeSyncModal = function() {
    const modal = document.getElementById("syncModal");
    if (modal) modal.classList.add("hidden");
};

window.submitSyncForm = function() {
    const input = document.getElementById("syncNameInput");
    const name = input ? input.value : null;
    if (!name) {
        window.showToast("Por favor ingresa tu apodo melómano");
        return;
    }
    window.closeSyncModal();
    window.showToast(`¡Genial ${name}! Te uniste al parche de ${selectedShowForSync}`);
};

// 5. AUTH MODAL
window.openAuthModal = function() {
    const modal = document.getElementById("authModal");
    if (modal) modal.classList.remove("hidden");
};

window.closeAuthModal = function() {
    const modal = document.getElementById("authModal");
    if (modal) modal.classList.add("hidden");
};

window.switchAuthTab = function(tab) {
    const btnLogin = document.getElementById("tabLogin");
    const btnRegister = document.getElementById("tabRegister");
    const formLogin = document.getElementById("formLogin");
    const formRegister = document.getElementById("formRegister");

    if (!btnLogin || !btnRegister || !formLogin || !formRegister) return;

    if (tab === 'login') {
        btnLogin.classList.add("bg-[#232330]", "text-white", "shadow-md");
        btnLogin.classList.remove("text-[#9A9AA5]");
        btnRegister.classList.remove("bg-[#232330]", "text-white", "shadow-md");
        btnRegister.classList.add("text-[#9A9AA5]");

        formLogin.classList.remove("hidden");
        formRegister.classList.add("hidden");
    } else {
        btnRegister.classList.add("bg-[#232330]", "text-white", "shadow-md");
        btnRegister.classList.remove("text-[#9A9AA5]");
        btnLogin.classList.remove("bg-[#232330]", "text-white", "shadow-md");
        btnLogin.classList.add("text-[#9A9AA5]");

        formRegister.classList.remove("hidden");
        formLogin.classList.add("hidden");
    }
};

window.togglePassword = function(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn ? btn.querySelector("i") : null;
    if (!input || !icon) return;

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
};

window.submitLogin = async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('/login', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Redirección inmediata sin toast ni delay
            window.location.href = data.redirect_url || '/dashboard';
        } else {
            let errorMsg = data.message || 'Error al iniciar sesión';
            if (data.errors) {
                const firstErrorKey = Object.keys(data.errors)[0];
                errorMsg = data.errors[firstErrorKey][0];
            }
            window.showToast(errorMsg);
        }
    } catch (err) {
        window.showToast('Error de conexión con el servidor.');
    }
};

window.submitRegister = async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Redirección inmediata sin toast ni delay
            window.location.href = data.redirect_url || '/dashboard';
        } else {
            let errorMsg = data.message || 'Error en el registro';
            if (data.errors) {
                const firstErrorKey = Object.keys(data.errors)[0];
                errorMsg = data.errors[firstErrorKey][0];
            }
            window.showToast(errorMsg);
        }
    } catch (err) {
        window.showToast('Error de conexión con el servidor.');
    }
};

// 6. TOAST NOTIFICACIONES
let toastTimeout = null;

window.showToast = function(msg) {
    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");

    if (!toast || !toastMessage) return;

    toastMessage.innerText = msg;

    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }

    toast.classList.remove("translate-y-4", "opacity-0", "pointer-events-none");
    toast.classList.add("translate-y-0", "opacity-100", "pointer-events-auto");

    toastTimeout = setTimeout(() => {
        toast.classList.remove("translate-y-0", "opacity-100", "pointer-events-auto");
        toast.classList.add("translate-y-4", "opacity-0", "pointer-events-none");
    }, 3000);
};

// 7. INICIALIZACIÓN AL CARGAR DOM
document.addEventListener("DOMContentLoaded", () => {
    const authModal = document.getElementById("authModal");
    if (authModal) {
        authModal.addEventListener("click", function(e) {
            if (e.target === this) {
                window.closeAuthModal();
            }
        });
    }

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            window.closeAuthModal();
        }
    });

    const canvas = document.getElementById("frequencyCanvas");
    if (!canvas) return;
    const ctx = canvas.getContext("2d");

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);

    let step = 0;
    function animateFrequency() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.lineWidth = 1.5;

        ctx.beginPath();
        ctx.strokeStyle = "rgba(255, 61, 87, 0.15)";
        for (let x = 0; x < canvas.width; x += 10) {
            const y = Math.sin(x * 0.005 + step) * 40 + Math.cos(x * 0.002 + step * 0.5) * 20 + canvas.height / 3;
            if (x === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.stroke();

        ctx.beginPath();
        ctx.strokeStyle = "rgba(124, 92, 255, 0.15)";
        for (let x = 0; x < canvas.width; x += 10) {
            const y = Math.cos(x * 0.004 + step * 0.8) * 50 + Math.sin(x * 0.003 + step) * 25 + (canvas.height / 3) * 2;
            if (x === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.stroke();

        step += 0.015;
        requestAnimationFrame(animateFrequency);
    }
    animateFrequency();
});