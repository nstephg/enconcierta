let imageFiles = [];
let gifUrls = [];
let giphyDebounce = null;

document.addEventListener("DOMContentLoaded", () => {
    const composeTextarea = document.getElementById('composeTextarea');

    if (composeTextarea) {
        composeTextarea.addEventListener('paste', (e) => {
            const items = (e.clipboardData || window.clipboardData)?.items;
            if (!items) return;

            let hasPastedImages = false;
            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    const blob = item.getAsFile();
                    if (blob) {
                        if (imageFiles.length >= 6) {
                            if (window.showToast) window.showToast('Máximo 6 imágenes por publicación');
                            break;
                        }
                        const pastedFile = new File([blob], `pasted-${Date.now()}.png`, { type: blob.type });
                        imageFiles.push(pastedFile);
                        hasPastedImages = true;
                    }
                }
            }

            if (hasPastedImages) {
                syncImageFileInput();
                renderMediaPreviews();
            }
        });
    }

    const picker = document.querySelector('#emojiPicker emoji-picker');
    if (picker) {
        picker.addEventListener('emoji-click', event => {
            const textarea = document.getElementById('composeTextarea');
            if (textarea) {
                textarea.value += event.detail.unicode;
                document.getElementById('emojiPicker')?.classList.add('hidden');
                textarea.focus();
            }
        });
    }

    document.addEventListener('click', (event) => {
        const emojiPicker = document.getElementById('emojiPicker');
        const giphyPicker = document.getElementById('giphyPicker');
        const btnEmoji = document.getElementById('btnEmojiToggle');
        const btnGif = document.getElementById('btnGifToggle');

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

window.handleGiphySearchKeydown = function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        window.searchGiphy(e.target.value);
    }
};

window.toggleEmojiPicker = function(e) {
    if (e) e.stopPropagation();
    const emojiPicker = document.getElementById('emojiPicker');
    const giphyPicker = document.getElementById('giphyPicker');
    if (giphyPicker) giphyPicker.classList.add('hidden');
    if (emojiPicker) emojiPicker.classList.toggle('hidden');
};

window.toggleGifPicker = function(e) {
    if (e) e.stopPropagation();
    const giphyPicker = document.getElementById('giphyPicker');
    const emojiPicker = document.getElementById('emojiPicker');
    if (emojiPicker) emojiPicker.classList.add('hidden');
    
    if (giphyPicker) {
        const isHidden = giphyPicker.classList.contains('hidden');
        giphyPicker.classList.toggle('hidden');
        
        if (isHidden) {
            const input = document.getElementById('giphySearchInput');
            if (input) input.focus();
            window.searchGiphy('concert live');
        }
    }
};

window.handleImagesSelected = function(input) {
    const files = Array.from(input.files);
    if (imageFiles.length + files.length > 6) {
        if (window.showToast) window.showToast('Máximo 6 imágenes por publicación');
    }
    files.forEach(file => {
        if (imageFiles.length < 6) {
            imageFiles.push(file);
        }
    });
    syncImageFileInput();
    renderMediaPreviews();
};

function syncImageFileInput() {
    const dt = new DataTransfer();
    imageFiles.forEach(file => dt.items.add(file));
    const input = document.getElementById('postImageInput');
    if (input) input.files = dt.files;
}

window.selectGif = function(url) {
    if (gifUrls.length >= 4) {
        if (window.showToast) window.showToast('Máximo 4 GIFs por publicación');
        return;
    }
    gifUrls.push(url);
    syncGifInputs();
    renderMediaPreviews();
    const giphyPicker = document.getElementById('giphyPicker');
    if (giphyPicker) giphyPicker.classList.add('hidden');
};

function syncGifInputs() {
    const container = document.getElementById('gifInputsContainer');
    if (!container) return;
    container.innerHTML = '';
    gifUrls.forEach(url => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'gifs[]';
        input.value = url;
        container.appendChild(input);
    });
}

window.removeImage = function(index) {
    imageFiles.splice(index, 1);
    syncImageFileInput();
    renderMediaPreviews();
};

window.removeGif = function(index) {
    gifUrls.splice(index, 1);
    syncGifInputs();
    renderMediaPreviews();
};

function renderMediaPreviews() {
    const container = document.getElementById('mediaPreviewContainer');
    if (!container) return;
    container.innerHTML = '';

    if (imageFiles.length === 0 && gifUrls.length === 0) {
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');

    imageFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'snap-start flex-shrink-0 relative rounded-xl overflow-hidden border border-white/10 h-28 w-36 bg-[#17171F]';
            
            const isVideo = file.type.startsWith('video/');
            const mediaTag = isVideo 
                ? `<video src="${e.target.result}" class="w-full h-full object-cover" muted></video>` 
                : `<img src="${e.target.result}" class="w-full h-full object-cover">`;

            div.innerHTML = `
                ${mediaTag}
                <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white flex items-center justify-center text-xs hover:bg-[#FF3D57] transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    gifUrls.forEach((url, index) => {
        const div = document.createElement('div');
        div.className = 'snap-start flex-shrink-0 relative rounded-xl overflow-hidden border border-[#2FE6D0]/40 h-28 w-40 bg-[#17171F]';
        div.innerHTML = `
            <img src="${url}" class="w-full h-full object-cover">
            <button type="button" onclick="removeGif(${index})" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white flex items-center justify-center text-xs hover:bg-[#FF3D57] transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        container.appendChild(div);
    });
}

window.searchGiphy = function(query) {
    clearTimeout(giphyDebounce);
    const searchQuery = query && query.trim().length > 0 ? query : 'concert live';

    giphyDebounce = setTimeout(async () => {
        try {
            const apiKey = '6lteB8C7KwPD3KSNLB5YYX8Z2ZEnAqEw';
            const endpoint = query && query.trim().length > 0 
                ? `https://api.giphy.com/v1/gifs/search?api_key=${apiKey}&q=${encodeURIComponent(searchQuery)}&limit=30`
                : `https://api.giphy.com/v1/gifs/trending?api_key=${apiKey}&limit=30`;

            const response = await fetch(endpoint);
            const data = await response.json();
            
            const resultsContainer = document.getElementById('giphyResults');
            if (!resultsContainer) return;
            resultsContainer.innerHTML = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(gif => {
                    const gifUrl = gif.images.fixed_height_small.url;
                    const originalUrl = gif.images.original.url;
                    const img = document.createElement('img');
                    img.src = gifUrl;
                    img.className = 'w-full h-24 object-cover rounded-xl cursor-pointer hover:scale-105 hover:brightness-110 border border-white/5 transition-all';
                    img.onclick = () => window.selectGif(originalUrl);
                    resultsContainer.appendChild(img);
                });
            } else {
                resultsContainer.innerHTML = '<div class="text-center text-[#9A9AA5] text-[11px] col-span-3 py-8">No se encontraron GIFs</div>';
            }
        } catch (err) {
            console.error(err);
        }
    }, 250);
};

window.toggleLocationInput = function() {
    const box = document.getElementById('locationInputBox');
    if (box) box.classList.toggle('hidden');
};

window.togglePollInput = function() {
    const box = document.getElementById('pollInputBox');
    if (box) box.classList.toggle('hidden');
};

window.addPollOption = function() {
    const container = document.getElementById('pollOptionsContainer');
    if (!container) return;
    const count = container.children.length;
    if (count >= 4) {
        if (window.showToast) window.showToast('Máximo 4 opciones por encuesta');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="encuesta_opciones[]" onkeydown="if(event.key === 'Enter') event.preventDefault()" class="flex-1 bg-[#0A0A0F] border border-white/10 rounded-lg p-2 text-white outline-none" placeholder="Opción ${count + 1}">
        <button type="button" onclick="removePollOption(this)" class="text-[#9A9AA5] hover:text-[#FF3D57] p-1"><i class="fa-solid fa-trash text-xs"></i></button>
    `;
    container.appendChild(div);
};

window.removePollOption = function(btn) {
    const container = document.getElementById('pollOptionsContainer');
    if (!container) return;
    if (container.children.length <= 2) {
        if (window.showToast) window.showToast('Mínimo 2 opciones requeridas');
        return;
    }
    btn.parentElement.remove();
};