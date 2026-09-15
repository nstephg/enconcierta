let selectedShowForSync = null;

window.addEventListener("scroll", function() {
    const header = document.getElementById("mainHeader");
    if (!header) return;
    if (window.scrollY > 40) {
        header.classList.remove("bg-transparent", "border-transparent");
        header.classList.add("bg-[#0A0A0F]/85", "border-white/5", "backdrop-blur-md", "shadow-xl");
    } else {
        header.classList.add("bg-transparent", "border-transparent");
        header.classList.remove("bg-[#0A0A0F]/85", "border-white/5", "backdrop-blur-md", "shadow-xl");
    }
});

window.toggleMobileMenu = function() {
    document.getElementById("mobileDrawer").classList.toggle("hidden");
};

window.openWizardModal = function() {
    document.getElementById("wizardModal").classList.remove("hidden");
    window.nextWizardStep(1);
};

window.closeWizardModal = function() {
    document.getElementById("wizardModal").classList.add("hidden");
};

window.nextWizardStep = function(step) {
    document.getElementById("wizardStepNum").innerText = step;
    document.getElementById("wizardStep1").classList.add("hidden");
    document.getElementById("wizardStep2").classList.add("hidden");
    document.getElementById("wizardStep3").classList.add("hidden");

    if (step === 1) {
        document.getElementById("wizardTitle").innerText = "¿A cuál show vas a asistir?";
        document.getElementById("wizardStep1").classList.remove("hidden");
    } else if (step === 2) {
        document.getElementById("wizardTitle").innerText = "¿Cómo prefieres vivir la previa?";
        document.getElementById("wizardStep2").classList.remove("hidden");
    } else if (step === 3) {
        document.getElementById("wizardTitle").innerText = "Sintonización Completa";
        document.getElementById("wizardStep3").classList.remove("hidden");
    }
};

window.selectVibeOption = function(btn, vibeName) {
    document.querySelectorAll(".vibe-opt").forEach(b => {
        b.classList.remove("border-[#7C5CFF]", "bg-[#7C5CFF]/10");
        b.querySelector(".fa-circle-check").classList.add("opacity-0");
    });
    btn.classList.add("border-[#7C5CFF]", "bg-[#7C5CFF]/10");
    btn.querySelector(".fa-circle-check").classList.remove("opacity-0");
};

window.confirmJoinParche = function() {
    window.closeWizardModal();
    window.showToast("¡Te has sincronizado con éxito al parche!");
};

window.openSyncModal = function(showTitle) {
    selectedShowForSync = showTitle;
    document.getElementById("syncModalShowTitle").innerText = showTitle;
    document.getElementById("syncModal").classList.remove("hidden");
};

window.closeSyncModal = function() {
    document.getElementById("syncModal").classList.add("hidden");
};

window.submitSyncForm = function() {
    const name = document.getElementById("syncNameInput").value;
    if(!name) {
        window.showToast("Por favor ingresa tu apodo melómano");
        return;
    }
    window.closeSyncModal();
    window.showToast(`¡Genial ${name}! Te uniste al parche de ${selectedShowForSync}`);
};

window.openAuthModal = function() {
    document.getElementById("authModal").classList.remove("hidden");
};

window.closeAuthModal = function() {
    document.getElementById("authModal").classList.add("hidden");
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
    const icon = btn.querySelector("i");
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
            window.showToast(data.message);
            setTimeout(() => {
                window.location.href = data.redirect_url || '/dashboard';
            }, 800);
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
            window.showToast(data.message);
            setTimeout(() => {
                window.location.href = data.redirect_url || '/dashboard';
            }, 800);
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

window.handleSocialAuth = function(provider) {
    window.showToast(`Sincronizando con ${provider}...`);
    setTimeout(window.closeAuthModal, 1200);
};

window.forgotPassword = function(e) {
    e.preventDefault();
    window.showToast("Te hemos enviado un enlace para restablecer tu contraseña.");
};

window.showTermsNotice = function(e) {
    e.preventDefault();
    window.showToast("Términos y condiciones de ENCONCIERTA.");
};

window.reactPost = function(btn) {
    const countEl = btn.querySelector(".like-count");
    let count = parseInt(countEl.innerText);
    if(btn.classList.contains("liked")) {
        btn.classList.remove("liked");
        countEl.innerText = count - 1;
    } else {
        btn.classList.add("liked");
        countEl.innerText = count + 1;
        window.showToast("¡Te identificaste con este recuerdo!");
    }
};

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

document.addEventListener("DOMContentLoaded", () => {
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", window.toggleMobileMenu);
    }

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

window.toggleMobileDrawer = function() {
    const sidebar = document.getElementById('sidebar-menu');
    sidebar.classList.toggle('hidden');
    sidebar.classList.toggle('fixed');
    sidebar.classList.toggle('inset-0');
    sidebar.classList.toggle('z-50');
};

window.toggleNotifications = function() {
    console.log('Notificaciones toggle');
};

window.openCreatePostModal = function() {
    const textarea = document.querySelector('textarea[name="contenido"]');
    if (textarea) textarea.focus();
};