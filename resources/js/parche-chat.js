document.addEventListener('alpine:init', () => {
    Alpine.data('parcheChat', (config = {}) => ({
        commentText: '',
        replyingTo: null,
        openEmoji: false,
        openGif: false,
        openMentions: false,
        mentionQuery: '',
        selectedGifUrl: '',
        previewMedia: [],
        gifResults: [],
        gifSearching: false,
        following: config.miembros || [],
        mensajes: config.mensajes || [],
        currentUserId: config.currentUserId || null,
        currentUserName: config.currentUserName || '',
        currentUserHandle: config.currentUserHandle || '',
        parcheId: config.parcheId || null,
        sending: false,
        pollInterval: null,
        readMentionIds: [],

        init() {
            // Cargar estado de menciones vistas desde localStorage
            const storageKey = 'read_mentions_' + (this.currentUserId || '0') + '_' + (this.parcheId || '0');
            try {
                const saved = localStorage.getItem(storageKey);
                this.readMentionIds = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.readMentionIds = [];
            }

            // Scroll garantizado al fondo tras renderizado inicial
            this.scrollToBottom();
            setTimeout(() => this.scrollToBottom(), 100);
            setTimeout(() => this.scrollToBottom(), 350);

            // Smart Polling en segundo plano cada 3.5 segundos
            if (this.parcheId) {
                this.pollInterval = setInterval(() => {
                    this.fetchNewMessages();
                }, 3500);
            }
        },

        // Getters filtrados descartando menciones ya revisadas por el usuario
        get myMentions() {
            if (!this.mensajes || !this.currentUserId) return [];
            
            const userHandle = this.currentUserHandle || '';
            
            return this.mensajes.filter(m => {
                if (m.is_me) return false;
                if (this.readMentionIds.includes(m.id)) return false;

                const matchesReply = m.has_parent && (m.parent_user === 'Tú' || m.parent_user === this.currentUserName);
                const matchesTag = userHandle && m.mensaje && m.mensaje.toLowerCase().includes('@' + userHandle.toLowerCase());
                return matchesReply || matchesTag;
            });
        },

        get myMentionsCount() {
            return this.myMentions.length;
        },

        // Desplazamiento suave y guardado permanente de mención leída
        scrollToMessage(id) {
        if (id && !this.readMentionIds.includes(id)) {
            this.readMentionIds.push(id);
            const storageKey = 'read_mentions_' + (this.currentUserId || '0') + '_' + (this.parcheId || '0');
            try {
                localStorage.setItem(storageKey, JSON.stringify(this.readMentionIds));
            } catch (e) {}
        }

        this.$nextTick(() => {
            const el = document.getElementById('msg-' + id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Destello breve de iluminación (800ms)
                el.classList.add('bg-[#7C5CFF]/25');
                setTimeout(() => {
                    el.classList.remove('bg-[#7C5CFF]/25');
                }, 800);
            }
        });
    },

        async fetchNewMessages() {
            if (!this.parcheId || this.sending) return;

            const lastId = this.mensajes.length > 0 
                ? Math.max(...this.mensajes.map(m => m.id || 0)) 
                : 0;

            try {
                const res = await fetch(`/parches/${this.parcheId}/mensajes?last_id=${lastId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) return;
                const data = await res.json();

                if (data.success && data.mensajes && data.mensajes.length > 0) {
                    let addedAny = false;

                    data.mensajes.forEach(msg => {
                        const exists = this.mensajes.some(m => m.id === msg.id || (m.pending && m.mensaje === msg.mensaje && m.is_me));
                        
                        if (!exists) {
                            this.mensajes.push(msg);
                            addedAny = true;
                        } else if (msg.is_me) {
                            const idx = this.mensajes.findIndex(m => m.pending && m.mensaje === msg.mensaje);
                            if (idx !== -1) {
                                this.mensajes[idx] = msg;
                            }
                        }
                    });

                    if (addedAny) {
                        this.scrollToBottom();
                    }
                }
            } catch (e) {
                // Polling silencioso
            }
        },

        get filteredMentions() {
            if (!this.mentionQuery) return this.following;
            const q = this.mentionQuery.toLowerCase();
            return this.following.filter(u => 
                (u.nombre && u.nombre.toLowerCase().includes(q)) || 
                (u.handle && u.handle.toLowerCase().includes(q))
            );
        },

        handleInput(e) {
            this.commentText = e.target.value;
            const pos = e.target.selectionStart;
            const textBefore = this.commentText.substring(0, pos);
            const match = textBefore.match(/@([a-zA-Z0-9_]*)$/);
            if (match) {
                this.openMentions = true;
                this.mentionQuery = match[1];
            } else {
                this.openMentions = false;
            }
        },

        handlePaste(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const items = (e.clipboardData || window.clipboardData)?.items;
            if (!items) return;

            let fileHandled = false;

            for (let item of items) {
                if (fileHandled) break;

                if (item.type.indexOf('image') !== -1 || item.type.indexOf('video') !== -1) {
                    const blob = item.getAsFile();
                    if (blob) {
                        const isVideo = blob.type.startsWith('video/');
                        const ext = blob.type.split('/')[1] || (isVideo ? 'mp4' : 'png');
                        const file = new File([blob], `pasted-${Date.now()}.${ext}`, { type: blob.type });
                        const url = URL.createObjectURL(file);

                        this.previewMedia.push({ 
                            url: url, 
                            type: isVideo ? 'video' : 'image', 
                            file: file 
                        });
                        fileHandled = true;
                    }
                }
            }
        },

        insertMention(handle) {
            const input = this.$refs.chatInput;
            if (!input) return;
            const pos = input.selectionStart;
            const textBefore = input.value.substring(0, pos);
            const textAfter = input.value.substring(pos);
            const lastAt = textBefore.lastIndexOf('@');
            if (lastAt !== -1) {
                input.value = textBefore.substring(0, lastAt) + '@' + handle + ' ' + textAfter;
            } else {
                input.value = textBefore + '@' + handle + ' ' + textAfter;
            }
            this.commentText = input.value;
            this.openMentions = false;
            input.focus();
        },

        setReply(id, userName, text) {
            this.replyingTo = { id: id, name: userName, text: text };
            this.$nextTick(() => { 
                if (this.$refs.chatInput) this.$refs.chatInput.focus(); 
            });
        },

        cancelReply() {
            this.replyingTo = null;
        },

        handleFilesSelected(event) {
            const files = Array.from(event.target.files || []);
            if (!files.length) return;

            files.forEach(file => {
                const isVideo = file.type.startsWith('video/');
                const isImage = file.type.startsWith('image/');
                if (isImage || isVideo) {
                    const url = URL.createObjectURL(file);
                    this.previewMedia.push({ 
                        url: url, 
                        type: isVideo ? 'video' : 'image', 
                        file: file 
                    });
                }
            });

            event.target.value = '';
        },

        removeMedia(index) {
            const removed = this.previewMedia.splice(index, 1);
            if (removed[0] && removed[0].url) {
                URL.revokeObjectURL(removed[0].url);
            }
        },

        async searchGiphy(query) {
            this.gifSearching = true;
            const apiKey = '6lteB8C7KwPD3KSNLB5YYX8Z2ZEnAqEw';
            const searchQuery = query && query.trim().length > 0 ? query.trim() : 'concert live';
            const url = query && query.trim().length > 0
                ? `https://api.giphy.com/v1/gifs/search?api_key=${apiKey}&q=${encodeURIComponent(searchQuery)}&limit=18`
                : `https://api.giphy.com/v1/gifs/trending?api_key=${apiKey}&limit=18`;
            try {
                const res = await fetch(url);
                const data = await res.json();
                this.gifResults = data.data || [];
            } catch (e) {
                this.gifResults = [];
            } finally {
                this.gifSearching = false;
            }
        },

        toggleMentions() {
            this.openMentions = !this.openMentions;
            this.mentionQuery = '';
            this.openEmoji = false;
            this.openGif = false;
        },

        toggleGifPicker() {
            this.openGif = !this.openGif;
            this.openEmoji = false;
            this.openMentions = false;
            if (this.openGif && this.gifResults.length === 0) {
                this.searchGiphy('');
            }
        },

        toggleEmojiPicker() {
            this.openEmoji = !this.openEmoji;
            this.openGif = false;
            this.openMentions = false;
        },

        selectGif(url) {
            this.selectedGifUrl = url;
            this.openGif = false;
        },

        addEmoji(emoji) {
            this.commentText += emoji;
            this.$nextTick(() => {
                if (this.$refs.chatInput) {
                    this.$refs.chatInput.focus();
                }
            });
        },

        formatMentions(text, isMe = false) {
            if (!text) return '';
            const escaped = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
            
            const mentionClass = isMe 
                ? 'text-[#2FE6D0] font-bold underline decoration-[#2FE6D0]/40' 
                : 'text-[#7C5CFF] font-bold underline decoration-[#7C5CFF]/40';

            return escaped.replace(/@([a-zA-Z0-9_]+)/g, `<span class="${mentionClass}">@$1</span>`);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                setTimeout(() => {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                    }
                }, 30);
            });
        },

        canSubmit() {
            return this.commentText.trim() !== '' || this.previewMedia.length > 0 || this.selectedGifUrl !== '';
        },

        async sendMessage(event) {
            if (this.sending || !this.canSubmit()) return;

            const now = new Date();
            const timeFormatted = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const tempMessage = {
                id: 'temp-' + Date.now(),
                id_usuario: this.currentUserId,
                is_me: true,
                nombre: 'Tú',
                avatar: document.querySelector('img[title="Tú"]')?.src || 'https://ui-avatars.com/api/?name=Yo',
                mensaje: this.commentText,
                gif_url: this.selectedGifUrl || null,
                imagenes: this.previewMedia.map(m => m.url),
                has_parent: !!this.replyingTo,
                parent_id: this.replyingTo?.id || null,
                parent_user: this.replyingTo?.name || '',
                parent_mensaje: this.replyingTo?.text || '',
                created_at: timeFormatted,
                pending: true
            };

            const form = event.target.closest('form') || event.target;
            const formData = new FormData();

            const tokenInput = form.querySelector('input[name="_token"]');
            if (tokenInput) {
                formData.append('_token', tokenInput.value);
            }

            formData.append('mensaje', this.commentText);
            formData.append('contenido', this.commentText);
            
            if (this.replyingTo?.id) {
                formData.append('parent_id', this.replyingTo.id);
            }
            if (this.selectedGifUrl) {
                formData.append('gif_url', this.selectedGifUrl);
            }

            this.previewMedia.forEach((med) => {
                if (med.file) {
                    formData.append('imagenes[]', med.file);
                }
            });

            this.mensajes.push(tempMessage);
            this.scrollToBottom();

            const textToSend = this.commentText;
            this.commentText = '';
            this.selectedGifUrl = '';
            this.replyingTo = null;
            this.previewMedia = [];
            this.openEmoji = false;
            this.openGif = false;
            this.openMentions = false;

            this.sending = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success && data.comentario) {
                    const index = this.mensajes.findIndex(m => m.id === tempMessage.id);
                    if (index !== -1) {
                        this.mensajes[index] = data.comentario;
                    }
                    this.scrollToBottom();
                } else {
                    this.mensajes = this.mensajes.filter(m => m.id !== tempMessage.id);
                    this.commentText = textToSend;
                    alert(data.error || 'Ocurrió un error al guardar el mensaje.');
                }
            } catch (err) {
                this.mensajes = this.mensajes.filter(m => m.id !== tempMessage.id);
                this.commentText = textToSend;
                console.error('Error de red al enviar mensaje:', err);
                alert('Ocurrió un error al conectar con el servidor.');
            } finally {
                this.sending = false;
            }
        }
    }));
});