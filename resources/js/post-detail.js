let cImageFiles = [];
let cGifUrls = [];
let cGiphyDebounce = null;

document.addEventListener("DOMContentLoaded", () => {
    const commentInput = document.getElementById('commentInput');

    if (commentInput) {
        commentInput.addEventListener('paste', (e) => {
            const items = (e.clipboardData || window.clipboardData)?.items;
            if (!items) return;

            let hasPastedImages = false;
            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    const blob = item.getAsFile();
                    if (blob) {
                        if (cImageFiles.length >= 3) {
                            if (window.showToast) window.showToast('Máximo 3 imágenes por comentario');
                            break;
                        }
                        const pastedFile = new File([blob], `pasted-comment-${Date.now()}.png`, { type: blob.type });
                        cImageFiles.push(pastedFile);
                        hasPastedImages = true;
                    }
                }
            }

            if (hasPastedImages) {
                const dt = new DataTransfer();
                cImageFiles.forEach(file => dt.items.add(file));
                const input = document.getElementById('commentImageInput');
                if (input) input.files = dt.files;
                renderCommentMediaPreviews();
            }
        });
    }

    const commentPicker = document.querySelector('#commentEmojiPicker emoji-picker');
    if (commentPicker) {
        commentPicker.addEventListener('emoji-click', event => {
            const input = document.getElementById('commentInput');
            if (input) {
                input.value += event.detail.unicode;
                document.getElementById('commentEmojiPicker')?.classList.add('hidden');
                input.focus();
            }
        });
    }

    document.addEventListener('click', (event) => {
        const emojiPicker = document.getElementById('commentEmojiPicker');
        const giphyPicker = document.getElementById('commentGiphyPicker');
        const btnEmoji = document.getElementById('btnCommentEmojiToggle');
        const btnGif = document.getElementById('btnCommentGifToggle');

        if (emojiPicker && !emojiPicker.classList.contains('hidden')) {
            if (!emojiPicker.contains(event.target) && btnEmoji && !btnEmoji.contains(event.target)) {
                emojiPicker.classList.add('hidden');
            }
        }
        if (giphyPicker && !giphyPicker.classList.contains('hidden')) {
            if (!giphyPicker.contains(event.target) && btnGif && !btnGif.contains(event.target)) {
                giphyPicker.classList.add('hidden');
            }
        }
    });
});

window.submitPollVote = async function(postId, optionIdx) {
    try {
        const response = await fetch(`/posts/${postId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ opcion_index: optionIdx })
        });

        const data = await response.json();
        if (response.ok && data.success) {
            location.reload();
        }
    } catch (err) {
        console.error('Error al votar:', err);
    }
};

window.setupReply = function(commentId, userName) {
    const parentInput = document.getElementById('parentCommentId');
    const replyName = document.getElementById('replyUserName');
    const banner = document.getElementById('replyingBanner');
    const input = document.getElementById('commentInput');

    if (parentInput) parentInput.value = commentId;
    if (replyName) replyName.innerText = userName;
    if (banner) banner.classList.remove('hidden');
    if (input) {
        input.placeholder = `Respondiendo a @${userName}...`;
        input.focus();
    }
};

window.cancelReply = function() {
    const parentInput = document.getElementById('parentCommentId');
    const banner = document.getElementById('replyingBanner');
    const input = document.getElementById('commentInput');

    if (parentInput) parentInput.value = '';
    if (banner) banner.classList.add('hidden');
    if (input) input.placeholder = 'Tu memoria del show...';
};

window.toggleCommentEmojiPicker = function(e) {
    if (e) e.stopPropagation();
    const giphyPicker = document.getElementById('commentGiphyPicker');
    const emojiPicker = document.getElementById('commentEmojiPicker');
    if (giphyPicker) giphyPicker.classList.add('hidden');
    if (emojiPicker) emojiPicker.classList.toggle('hidden');
};

window.toggleCommentGifPicker = function(e) {
    if (e) e.stopPropagation();
    const emojiPicker = document.getElementById('commentEmojiPicker');
    const picker = document.getElementById('commentGiphyPicker');
    if (emojiPicker) emojiPicker.classList.add('hidden');
    
    if (picker) {
        const isHidden = picker.classList.contains('hidden');
        picker.classList.toggle('hidden');
        if (isHidden) {
            const input = document.getElementById('commentGiphySearchInput');
            if (input) input.focus();
            window.searchCommentGiphy('concert');
        }
    }
};

window.searchCommentGiphy = function(query) {
    clearTimeout(cGiphyDebounce);
    const searchQuery = query && query.trim().length > 0 ? query : 'concert';

    cGiphyDebounce = setTimeout(async () => {
        try {
            const apiKey = '6lteB8C7KwPD3KSNLB5YYX8Z2ZEnAqEw';
            const endpoint = query && query.trim().length > 0 
                ? `https://api.giphy.com/v1/gifs/search?api_key=${apiKey}&q=${encodeURIComponent(searchQuery)}&limit=15`
                : `https://api.giphy.com/v1/gifs/trending?api_key=${apiKey}&limit=15`;

            const response = await fetch(endpoint);
            const data = await response.json();
            
            const container = document.getElementById('commentGiphyResults');
            if (!container) return;
            container.innerHTML = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(gif => {
                    const gifUrl = gif.images.fixed_height_small.url;
                    const originalUrl = gif.images.original.url;
                    const img = document.createElement('img');
                    img.src = gifUrl;
                    img.className = 'w-full h-20 object-cover rounded-lg cursor-pointer hover:scale-105 border border-white/5 transition-all';
                    img.onclick = () => window.selectCommentGif(originalUrl);
                    container.appendChild(img);
                });
            } else {
                container.innerHTML = '<div class="text-center text-[#9A9AA5] text-[11px] col-span-3 py-6">Sin resultados</div>';
            }
        } catch (err) {
            console.error(err);
        }
    }, 250);
};

window.selectCommentGif = function(url) {
    if (cGifUrls.length >= 2) return;
    cGifUrls.push(url);
    syncCommentGifInputs();
    renderCommentMediaPreviews();
    const picker = document.getElementById('commentGiphyPicker');
    if (picker) picker.classList.add('hidden');
};

function syncCommentGifInputs() {
    const container = document.getElementById('commentGifInputsContainer');
    if (!container) return;
    container.innerHTML = '';
    cGifUrls.forEach(url => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'gifs[]';
        input.value = url;
        container.appendChild(input);
    });
}

window.handleCommentImagesSelected = function(input) {
    const files = Array.from(input.files);
    files.forEach(file => {
        if (cImageFiles.length < 3) cImageFiles.push(file);
    });
    const dt = new DataTransfer();
    cImageFiles.forEach(file => dt.items.add(file));
    const commentImgInput = document.getElementById('commentImageInput');
    if (commentImgInput) commentImgInput.files = dt.files;
    renderCommentMediaPreviews();
};

function renderCommentMediaPreviews() {
    const container = document.getElementById('commentMediaPreviewContainer');
    if (!container) return;
    container.innerHTML = '';

    if (cImageFiles.length === 0 && cGifUrls.length === 0) {
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');

    cImageFiles.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative rounded-lg overflow-hidden border border-white/10 h-16 w-20 bg-[#17171F] flex-shrink-0';
            
            const isVideo = file.type.startsWith('video/');
            const mediaTag = isVideo 
                ? `<video src="${e.target.result}" controls muted playsinline loop class="w-full h-full object-cover"></video>` 
                : `<img src="${e.target.result}" class="w-full h-full object-cover">`;

            div.innerHTML = `${mediaTag}
                <button type="button" onclick="removeCommentImage(${idx})" class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-black/80 text-white flex items-center justify-center text-[10px]"><i class="fa-solid fa-xmark"></i></button>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    cGifUrls.forEach((url, idx) => {
        const div = document.createElement('div');
        div.className = 'relative rounded-lg overflow-hidden border border-[#2FE6D0]/40 h-16 w-24 bg-[#17171F] flex-shrink-0';
        div.innerHTML = `<img src="${url}" class="w-full h-full object-cover">
            <button type="button" onclick="removeCommentGif(${idx})" class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-black/80 text-white flex items-center justify-center text-[10px]"><i class="fa-solid fa-xmark"></i></button>`;
        container.appendChild(div);
    });
}

window.removeCommentImage = function(idx) {
    cImageFiles.splice(idx, 1);
    const dt = new DataTransfer();
    cImageFiles.forEach(file => dt.items.add(file));
    const input = document.getElementById('commentImageInput');
    if (input) input.files = dt.files;
    renderCommentMediaPreviews();
};

window.removeCommentGif = function(idx) {
    cGifUrls.splice(idx, 1);
    syncCommentGifInputs();
    renderCommentMediaPreviews();
};

window.togglePostLikeInDetail = async function(postId, btn) {
    try {
        const response = await fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            const countSpan = btn.querySelector('.like-count');
            if (countSpan) countSpan.innerText = data.likes_count;

            if (data.liked) {
                btn.classList.add('text-[#FF3D57]');
                btn.classList.remove('text-[#9A9AA5]');
            } else {
                btn.classList.remove('text-[#FF3D57]');
                btn.classList.add('text-[#9A9AA5]');
            }
        }
    } catch (err) {
        console.error(err);
    }
};

window.toggleCommentLike = async function(commentId, btn) {
    try {
        const response = await fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (response.ok && data.success) {
            const countSpan = btn.querySelector('.c-like-count');
            if (countSpan) countSpan.innerText = data.likes_count;
            if (data.liked) {
                btn.classList.add('text-[#FF3D57]');
                btn.classList.remove('text-[#9A9AA5]');
            } else {
                btn.classList.remove('text-[#FF3D57]');
                btn.classList.add('text-[#9A9AA5]');
            }
        }
    } catch (err) {
        console.error(err);
    }
};

window.toggleCommentReplies = function(commentId) {
    const wrapper = document.getElementById(`replies-wrapper-${commentId}`);
    const textSpan = document.getElementById(`text-reply-${commentId}`);
    const icon = document.getElementById(`icon-reply-${commentId}`);

    if (!wrapper) return;

    const isHidden = wrapper.classList.contains('hidden');
    if (isHidden) {
        wrapper.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
        if (textSpan) textSpan.innerText = 'Ocultar respuestas';
    } else {
        wrapper.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
        if (textSpan) {
            const total = textSpan.getAttribute('data-total') || '1';
            textSpan.innerText = `Ver ${total} ${total === '1' ? 'respuesta' : 'respuestas'}`;
        }
    }
};