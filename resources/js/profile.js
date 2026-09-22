// Cambio de Pestañas (Mis momentos / Editar perfil)
// Cambio de Pestañas (Momentos / Blogs / Configuración)
window.switchProfileTab = function(tab) {
    const postsSection = document.getElementById('tab-content-posts');
    const blogsSection = document.getElementById('tab-content-blogs');
    const editSection = document.getElementById('tab-content-editar');

    const postsHeader = document.getElementById('header-posts-view');
    const editHeader = document.getElementById('header-edit-view');

    const btnPosts = document.getElementById('btn-tab-posts');
    const btnBlogs = document.getElementById('btn-tab-blogs');

    const activeClasses = ['bg-[#FF3D57]', 'text-white', 'shadow-[0_0_15px_rgba(255,61,87,0.35)]'];
    const inactiveClasses = ['text-[#9A9AA5]', 'hover:text-white'];

    // Ocultar todas las secciones de contenido
    if (postsSection) postsSection.classList.add('hidden');
    if (blogsSection) blogsSection.classList.add('hidden');
    if (editSection) editSection.classList.add('hidden');

    if (postsHeader) postsHeader.classList.remove('hidden');
    if (editHeader) editHeader.classList.add('hidden');

    // Resetear apariencia de los botones
    if (btnPosts) {
        btnPosts.classList.remove(...activeClasses);
        btnPosts.classList.add(...inactiveClasses);
    }
    if (btnBlogs) {
        btnBlogs.classList.remove(...activeClasses);
        btnBlogs.classList.add(...inactiveClasses);
    }

    if (tab === 'blogs') {
        if (blogsSection) blogsSection.classList.remove('hidden');
        if (btnBlogs) {
            btnBlogs.classList.remove(...inactiveClasses);
            btnBlogs.classList.add(...activeClasses);
        }
    } else if (tab === 'editar') {
        if (editSection) editSection.classList.remove('hidden');
        if (postsHeader) postsHeader.classList.add('hidden');
        if (editHeader) editHeader.classList.remove('hidden');
    } else {
        // 'posts' / 'momentos' por defecto
        if (postsSection) postsSection.classList.remove('hidden');
        if (btnPosts) {
            btnPosts.classList.remove(...inactiveClasses);
            btnPosts.classList.add(...activeClasses);
        }
    }
};

// Modal Social (Siguiendo / Seguidores)
window.openSocialModal = function(type) {
    const modal = document.getElementById('socialModal');
    const title = document.getElementById('socialModalTitle');
    const subtitle = document.getElementById('socialModalSubtitle');
    const listFollowing = document.getElementById('social-list-following');
    const listFollowers = document.getElementById('social-list-followers');
    const followingCount = document.getElementById('stat-following-count')?.innerText || '0';
    const followersCount = document.getElementById('stat-followers-count')?.innerText || '0';

    if (!modal) return;
    const isOwn = modal.getAttribute('data-is-own') === '1';

    modal.classList.remove('hidden');

    if (type === 'siguiendo') {
        if (title) title.innerText = 'Siguiendo';
        if (subtitle) subtitle.innerText = isOwn 
            ? `${followingCount} melómanos que sigues` 
            : `${followingCount} melómanos que sigue`;
        if (listFollowing) listFollowing.classList.remove('hidden');
        if (listFollowers) listFollowers.classList.add('hidden');
    } else {
        if (title) title.innerText = 'Seguidores';
        if (subtitle) subtitle.innerText = isOwn 
            ? `${followersCount} melómanos te siguen` 
            : `${followersCount} melómanos le siguen`;
        if (listFollowers) listFollowers.classList.remove('hidden');
        if (listFollowing) listFollowing.classList.add('hidden');
    }
};

window.closeSocialModal = function() {
    const modal = document.getElementById('socialModal');
    if (modal) modal.classList.add('hidden');
};

window.filterSocialList = function(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.social-item').forEach(item => {
        const name = item.getAttribute('data-name') || '';
        const username = item.getAttribute('data-username') || '';
        if (name.includes(q) || username.includes(q)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
};

window.toggleFollowUser = async function(userId, btn) {
    if (!userId || btn.disabled) return;
    btn.disabled = true;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`/usuarios/${userId}/follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            const isCompactBtn = btn.closest('.social-item') !== null;
            const modal = document.getElementById('socialModal');
            const isOwnProfile = modal?.getAttribute('data-is-own') === '1';

            if (data.following) {
                btn.textContent = 'Siguiendo';
                if (isCompactBtn) {
                    btn.className = 'w-20 py-1 rounded-full text-xs font-semibold border border-white/15 text-[#9A9AA5] cursor-pointer transition-all text-center flex-shrink-0';
                } else {
                    btn.className = 'px-5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border border-white/15 text-[#9A9AA5]';
                }
            } else {
                btn.textContent = 'Seguir';
                if (isCompactBtn) {
                    btn.className = 'w-20 py-1 rounded-full text-xs font-semibold bg-[#FF3D57] text-white shadow-[0_0_10px_rgba(255,61,87,0.3)] hover:brightness-110 cursor-pointer transition-all text-center flex-shrink-0';
                } else {
                    btn.className = 'px-5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer bg-[#FF3D57] text-white shadow-[0_0_12px_rgba(255,61,87,0.3)]';
                }
            }

            // Actualización inteligente de contadores
            if (!isCompactBtn) {
                const followersStat = document.getElementById('stat-followers-count');
                if (followersStat && data.followers_count !== undefined) {
                    followersStat.textContent = data.followers_count;
                }
            } else if (isOwnProfile) {
                const followingStat = document.getElementById('stat-following-count');
                if (followingStat) {
                    let currentFollowing = parseInt(followingStat.textContent) || 0;
                    followingStat.textContent = data.following ? currentFollowing + 1 : Math.max(0, currentFollowing - 1);
                }
            }
        }
    } catch (error) {
        console.error('Error al cambiar el seguimiento:', error);
    } finally {
        btn.disabled = false;
    }
};

// Reacción de Likes en Publicaciones
window.togglePostLike = async function(postId, btn) {
    if (!btn) return;
    const countEl = btn.querySelector('.like-count') || btn.querySelector('span');
    let count = parseInt(countEl ? countEl.innerText : '0') || 0;
    const isLiked = btn.classList.contains('liked') || btn.classList.contains('text-[#FF3D57]');

    if (isLiked) {
        btn.classList.remove('liked', 'text-[#FF3D57]');
        btn.classList.add('text-[#9A9AA5]');
        if (countEl) countEl.innerText = Math.max(0, count - 1);
    } else {
        btn.classList.add('liked', 'text-[#FF3D57]');
        btn.classList.remove('text-[#9A9AA5]');
        if (countEl) countEl.innerText = count + 1;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
    } catch (e) {
        console.error('Error enviando reaccion:', e);
    }
};

// Previsualización de imágenes
window.previewProfileImage = function(input, imgTargetId) {
    const file = input.files[0];
    if (file) {
        const url = URL.createObjectURL(file);
        const target = document.getElementById(imgTargetId);
        if (target) {
            if (target.tagName.toLowerCase() === 'img') {
                target.src = url;
            } else {
                target.style.backgroundImage = `url('${url}')`;
            }
        }
    }
};

// Modales Lightbox de Imágenes
window.openLightbox = function(url) {
    const lightbox = document.getElementById('profileLightbox');
    const img = document.getElementById('profileLightboxImg');
    if (lightbox && img) {
        img.src = url;
        lightbox.classList.remove('hidden');
    }
};

window.closeLightbox = function() {
    const lightbox = document.getElementById('profileLightbox');
    if (lightbox) lightbox.classList.add('hidden');
};

// GESTIÓN SINCRONIZADA DE GÉNEROS MUSICALES
window.removeGenreTag = function(btnElement, genreValue) {
    const badge = btnElement.closest('.genre-badge');
    if (badge) badge.remove();

    const hiddenInputs = document.querySelectorAll('#genresHiddenInputs input[name="generos[]"]');
    hiddenInputs.forEach(input => {
        if (input.value === genreValue) {
            input.remove();
        }
    });
};

window.addGenreTag = function() {
    const input = document.getElementById('inputAddGenre');
    const container = document.getElementById('genresContainer');
    const hiddenInputs = document.getElementById('genresHiddenInputs');

    if (!input || !container) return;
    const val = input.value.trim();
    if (!val) return;

    const existingValues = Array.from(document.querySelectorAll('#genresHiddenInputs input[name="generos[]"]')).map(i => i.value);
    if (existingValues.includes(val)) {
        input.value = '';
        return;
    }

    const badge = document.createElement('span');
    badge.className = 'genre-badge flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#7C5CFF]/15 text-[#9B80FF] border border-[#7C5CFF]/30';
    badge.innerHTML = `<span>${val}</span><button type="button" onclick="removeGenreTag(this, '${val}')" class="opacity-60 hover:opacity-100 cursor-pointer"><i class="fa-solid fa-xmark text-xs"></i></button>`;
    
    container.insertBefore(badge, input.parentElement);

    if (hiddenInputs) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'generos[]';
        hidden.value = val;
        hiddenInputs.appendChild(hidden);
    }

    input.value = '';
};

// GESTIÓN SINCRONIZADA DE ARTISTAS FAVORITOS
window.removeArtistTag = function(btnElement, artistValue) {
    const badge = btnElement.closest('.artist-badge');
    if (badge) badge.remove();

    const hiddenInputs = document.querySelectorAll('#artistsHiddenInputs input[name="artistas[]"]');
    hiddenInputs.forEach(input => {
        if (input.value === artistValue) {
            input.remove();
        }
    });
};

window.addArtistTag = function() {
    const input = document.getElementById('inputAddArtist');
    const container = document.getElementById('artistsContainer');
    const hiddenInputs = document.getElementById('artistsHiddenInputs');

    if (!input || !container) return;
    const val = input.value.trim();
    if (!val) return;

    const existingValues = Array.from(document.querySelectorAll('#artistsHiddenInputs input[name="artistas[]"]')).map(i => i.value);
    if (existingValues.includes(val)) {
        input.value = '';
        return;
    }

    const badge = document.createElement('span');
    badge.className = 'artist-badge flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#FF3D57]/15 text-[#FF6070] border border-[#FF3D57]/30';
    badge.innerHTML = `<span>${val}</span><button type="button" onclick="removeArtistTag(this, '${val}')" class="opacity-60 hover:opacity-100 cursor-pointer"><i class="fa-solid fa-xmark text-xs"></i></button>`;

    container.appendChild(badge);

    if (hiddenInputs) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'artistas[]';
        hidden.value = val;
        hiddenInputs.appendChild(hidden);
    }

    input.value = '';
};

window.updateBioCount = function(textarea) {
    const counter = document.getElementById('bioCharCounter');
    if (counter) counter.innerText = `${textarea.value.length}/160`;
};