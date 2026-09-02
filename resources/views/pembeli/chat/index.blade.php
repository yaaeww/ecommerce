@extends('layouts.public')

@php
    use Carbon\Carbon;
@endphp

@push('style')
    <style>
        .chat-container {
            height: calc(100vh - 12rem);
            min-height: 500px;
        }

        /* Message Bubbles */
        .bubble-appear {
            animation: bubbleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes bubbleIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .message-bubble {
            max-width: 75%;
            word-wrap: break-word;
        }

        .message-mine {
            background-color: #4f46e5; /* indigo-600 */
            color: white;
            border-radius: 1rem 1rem 0 1rem;
        }

        .message-other {
            background-color: #f3f4f6; /* gray-100 */
            color: #1f2937; /* gray-800 */
            border-radius: 1rem 1rem 1rem 0;
        }

        .message-ai {
            background-color: #fef3c7; /* amber-100 */
            color: #92400e; /* amber-900 */
            border-radius: 1rem 1rem 1rem 0;
        }

        /* Loading Animation */
        .loading-dots {
            display: inline-flex;
            gap: 4px;
        }

        .loading-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #9ca3af; /* gray-400 */
            animation: bounce 1.4s infinite ease-in-out;
        }

        .loading-dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .loading-dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        /* Custom Scrollbar for chat area */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8; /* slate-400 */
        }
        
        .contact-list::-webkit-scrollbar {
            width: 4px;
        }
        
        .contact-list::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .contact-list::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 2px;
        }
    </style>

    {{-- 🚀 Library Real-time --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row chat-container relative">
            
            {{-- Tombol Toggle untuk Mobile --}}
            <button class="md:hidden absolute top-4 left-4 z-20 bg-white p-2 rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Overlay untuk mobile --}}
            <div class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-10 transition-opacity md:hidden" id="sidebarOverlay"></div>

            {{-- 🔹 SIDEBAR LIST PENGGUNA --}}
            <div class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-200 flex flex-col bg-white absolute md:relative z-20 h-full transition-transform duration-300 transform -translate-x-full md:translate-x-0" id="sidebar">
                <div class="p-4 border-b border-gray-200 bg-gray-50/80 backdrop-blur-sm flex justify-between items-center h-16 md:h-auto pl-14 md:pl-4">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-comments text-indigo-500"></i>
                        Pesan
                    </h2>
                    {{-- Tombol Hapus --}}
                    <button id="clearChatBtn" class="hidden text-xs px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium transition-colors" onclick="clearChat()">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </div>

                <div id="userList" class="flex-1 overflow-y-auto contact-list">
                    {{-- AI Asisten --}}
                    <a href="#" onclick="openChat(0); return false;" data-user-id="0"
                        class="contact-item block p-4 border-b border-gray-100 hover:bg-indigo-50/50 transition-colors cursor-pointer relative group">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white shadow-sm group-hover:shadow transition-shadow">
                                    <i class="fas fa-robot text-lg"></i>
                                </div>
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">Asisten AI</h3>
                                <p class="text-xs text-gray-500 truncate mt-0.5">Selalu siap membantu Anda</p>
                            </div>
                        </div>
                    </a>

                    {{-- Penjual --}}
                    @foreach ($users as $user)
                        <a href="#" onclick="openChat({{ $user->id }}); return false;" data-user-id="{{ $user->id }}"
                            class="contact-item block p-4 border-b border-gray-100 hover:bg-indigo-50/50 transition-colors cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:shadow transition-shadow flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</h3>
                                        @if(isset($user->unread_count) && $user->unread_count > 0)
                                            <span id="unread-badge-{{ $user->id }}" class="inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold shadow-sm ring-2 ring-white flex-shrink-0">
                                                {{ $user->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- 🔹 CHAT AREA --}}
            <div class="flex-1 flex flex-col h-full bg-gray-50/30">
                {{-- Header --}}
                <div id="chatHeader" class="p-4 border-b border-gray-200 bg-white shadow-sm z-10 flex items-center h-16 md:h-auto pl-14 md:pl-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Pilih Percakapan</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih kontak di samping untuk memulai</p>
                        </div>
                    </div>
                </div>

                {{-- Pesan --}}
                <div id="chatMessages" class="flex-1 overflow-y-auto p-4 md:p-6 chat-messages">
                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-400">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-comments text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm">Pilih percakapan untuk memulai chat</p>
                    </div>
                </div>

                {{-- Input --}}
                <form id="chatForm" onsubmit="sendMessage(event)" class="p-4 bg-white border-t border-gray-200">
                    <input type="hidden" id="receiver_id" value="">
                    <div class="flex items-end gap-2 relative">
                        <div class="relative flex-1">
                            <input id="messageInput" type="text" 
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-full focus:ring-indigo-500 focus:border-indigo-500 block px-5 py-3 pr-12 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed" 
                                placeholder="Pilih percakapan terlebih dahulu..." disabled autocomplete="off">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95" disabled>
                            <i class="fas fa-paper-plane text-sm -ml-0.5 mt-0.5"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 🔹 SCRIPT CHAT --}}
    <script>
        const authUserId = {{ Auth::id() }};
        let currentUserId = null;
        let echo = null;

        const chatBox = document.getElementById('chatMessages');
        const msgInput = document.getElementById('messageInput');
        const chatForm = document.getElementById('chatForm');
        const clearBtn = document.getElementById('clearChatBtn');
        const header = document.getElementById('chatHeader');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Toggle sidebar untuk mobile
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        // Event listener untuk toggle sidebar
        if (sidebarToggle && sidebarOverlay) {
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // Style for active state
        const activeClass = 'bg-indigo-50 border-l-4 border-indigo-500';

        // Inisialisasi Echo
        try {
            echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ env('PUSHER_APP_KEY') }}',
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: false,
                wsHost: window.location.hostname,
                wsPort: 6001,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth'
            });
            console.log("✅ Echo initialized successfully");
        } catch (e) {
            console.error("❌ Echo initialization failed:", e);
        }

        function formatTime(iso) {
            if (!iso) return '';
            return new Date(iso).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatChatMessage(text) {
            if (!text) return '';
            let formatted = escapeHtml(text);
            // Bold **text**
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold">$1</strong>');
            // Italic *text* or _text_
            formatted = formatted.replace(/_(.*?)_/g, '<em class="italic opacity-90">$1</em>');
            formatted = formatted.replace(/\*([^*\n]+)\*/g, '<em class="italic opacity-90">$1</em>');
            // Strikethrough ~~text~~
            formatted = formatted.replace(/~~(.*?)~~/g, '<del class="opacity-60">$1</del>');
            // Inline code `code`
            formatted = formatted.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded text-[11px] font-mono border border-slate-200">$1</code>');
            // Line breaks
            formatted = formatted.replace(/\n/g, '<br>');
            return formatted;
        }

        function createBubble(chat) {
            const senderId = chat.sender_id || (chat.sender ? chat.sender.id : null);
            const isMine = senderId === authUserId;
            const isAI = chat.is_ai === true || chat.is_ai === 1;

            const wrapper = document.createElement('div');
            wrapper.className = `flex ${isMine ? 'justify-end' : 'justify-start'} mb-4`;

            const bubble = document.createElement('div');
            let bubbleClass = 'message-bubble bubble-appear px-4 py-3 shadow-sm max-w-[85%] md:max-w-[75%] ';

            if (isAI) {
                bubbleClass += 'message-ai border border-amber-200 bg-amber-50/90 text-slate-800';
            } else if (isMine) {
                bubbleClass += 'message-mine bg-brand-green text-white';
            } else {
                bubbleClass += 'message-other border border-gray-200 bg-white text-slate-800';
            }

            bubble.className = bubbleClass;

            const timestampText = formatTime(chat.created_at ?? new Date());
            const timestampClass = isMine ? 'text-emerald-200' : (isAI ? 'text-amber-700/70' : 'text-gray-400');

            bubble.innerHTML = `
                <div class="text-xs sm:text-sm leading-relaxed">${formatChatMessage(chat.message)}</div>
                <div class="text-[10px] text-right mt-1.5 ${timestampClass}">${timestampText}</div>
            `;

            wrapper.appendChild(bubble);
            return wrapper;
        }

        function appendBubble(bubbleElement) {
            chatBox.appendChild(bubbleElement);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function setFormEnabled(enabled) {
            msgInput.disabled = !enabled;
            chatForm.querySelector('button[type="submit"]').disabled = !enabled;
            if (enabled) {
                msgInput.placeholder = "Ketik pesan Anda...";
                msgInput.focus();
            } else {
                msgInput.placeholder = "Pilih percakapan terlebih dahulu...";
            }
        }

        function setActiveChat(userId) {
            document.querySelectorAll('.contact-item').forEach(item => {
                item.classList.remove('bg-indigo-50', 'border-l-4', 'border-indigo-500');
                item.classList.add('border-b', 'border-gray-100');
            });
            const activeItem = document.querySelector(`[data-user-id="${userId}"]`);
            if (activeItem) {
                activeItem.classList.add('bg-indigo-50', 'border-l-4', 'border-indigo-500');
                activeItem.classList.remove('border-b', 'border-gray-100');
            }
        }

        async function openChat(userId) {
            // Tutup sidebar di mobile saat chat dibuka
            if (window.innerWidth <= 768 && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }

            currentUserId = userId;
            document.getElementById('receiver_id').value = userId;
            setFormEnabled(true);
            setActiveChat(userId);

            if(userId === 0) {
                clearBtn.classList.remove('hidden');
                clearBtn.classList.add('block');
            } else {
                clearBtn.classList.add('hidden');
                clearBtn.classList.remove('block');
            }

            chatBox.innerHTML = `
                            <div class="flex flex-col items-center justify-center h-full text-center text-gray-500">
                                <div class="loading-dots mb-4">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <p class="text-sm">Memuat percakapan...</p>
                            </div>
                        `;

            if (echo) echo.leaveAllChannels();

            // Set header
            if (userId === 0) {
                header.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white shadow-sm relative">
                                        <i class="fas fa-robot text-sm"></i>
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-bold text-gray-900">Asisten AI</h2>
                                        <p class="text-xs text-green-600 mt-0.5 font-medium">Online • Selalu Tersedia</p>
                                    </div>
                                </div>
                            `;
            } else {
                const userButton = document.querySelector(`a[onclick="openChat(${userId}); return false;"]`);
                const userName = userButton ? userButton.querySelector('h3').textContent : `User #${userId}`;
                const userInitial = userName.charAt(0).toUpperCase();

                header.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                                        ${userInitial}
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-bold text-gray-900">${userName}</h2>
                                        <p class="text-xs text-gray-500 mt-0.5">Penjual</p>
                                    </div>
                                </div>
                            `;
            }

            // Hapus badge unread untuk kontak yang baru dibuka
            const contactBadge = document.getElementById(`unread-badge-${userId}`);
            if (contactBadge) {
                contactBadge.remove();
            }

            try {
                const res = await fetch(`/pembeli/chat/history/${userId}`);
                if (!res.ok) throw new Error('Gagal memuat riwayat chat');

                const data = await res.json();
                if (data.status !== 'ok') throw new Error(data.message || 'Format data tidak valid');

                // Sinkronisasi badge unread di navbar dan mobile dock
                if (typeof data.total_unread !== 'undefined') {
                    const globalBadges = document.querySelectorAll('a[href*="chat"] span.rounded-full');
                    globalBadges.forEach(badge => {
                        if (data.total_unread > 0) {
                            badge.textContent = data.total_unread;
                            badge.classList.remove('hidden');
                        } else {
                            badge.remove();
                        }
                    });
                }

                chatBox.innerHTML = '';

                if (data.chats.length === 0) {
                    chatBox.innerHTML = `
                                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-400">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-paper-plane text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-600">Belum ada pesan</p>
                                        <p class="text-xs mt-1">Kirim pesan pertama Anda sekarang!</p>
                                    </div>
                                `;
                } else {
                    data.chats.forEach(chat => chatBox.appendChild(createBubble(chat)));
                }

                chatBox.scrollTop = chatBox.scrollHeight;

                if (userId !== 0 && echo) {
                    echo.private('chat.' + authUserId)
                        .listen('.chat.message', (data) => {
                            if (data.chat.sender_id == currentUserId) {
                                appendBubble(createBubble(data.chat));
                            }
                        });
                    console.log("🎧 Listening to channel: chat." + authUserId);
                }

            } catch (err) {
                console.error("❌ Gagal memuat riwayat:", err);
                chatBox.innerHTML = `
                                <div class="flex flex-col items-center justify-center h-full text-center text-red-500">
                                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                                    </div>
                                    <p class="text-sm font-medium">Gagal memuat percakapan</p>
                                    <p class="text-xs mt-1 text-red-400">${err.message}</p>
                                </div>
                            `;
            }
        }

        async function sendMessage(e) {
            e.preventDefault();
            const msg = msgInput.value.trim();
            const receiver = document.getElementById('receiver_id').value;
            if (!msg || receiver === '') return;

            // Hapus empty state jika ada
            const emptyState = chatBox.querySelector('.flex-col.items-center.justify-center.h-full');
            if (emptyState) {
                emptyState.remove();
            }

            const optimisticChat = {
                sender_id: authUserId,
                message: msg,
                created_at: new Date().toISOString(),
                is_ai: false
            };

            const myBubble = createBubble(optimisticChat);
            appendBubble(myBubble);
            msgInput.value = '';

            const timestampElement = myBubble.querySelector('.text-\\[10px\\]');

            try {
                const res = await fetch(`/pembeli/chat/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: msg, receiver_id: receiver })
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Server error');

                if (receiver == 0 && data.ai_reply) {
                    appendBubble(createBubble(data.ai_reply));
                }

                if (receiver != 0 && data.status == 'sent') {
                    if (timestampElement) {
                        timestampElement.textContent = formatTime(data.chat.created_at);
                    }
                }

            } catch (error) {
                console.error("❌ Gagal mengirim:", error.message);
                if (timestampElement) {
                    timestampElement.textContent = `(Gagal: ${error.message})`;
                    timestampElement.classList.add('text-red-300', 'font-bold');
                }
            }
        }

        async function clearChat() {
            if (currentUserId !== 0 || !confirm('Yakin ingin menghapus semua riwayat chat AI?')) return;

            try {
                const res = await fetch(`/pembeli/chat/clear/0`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (data.message) {
                    chatBox.innerHTML = `
                                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-400">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-trash-alt text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-600">Riwayat chat telah dihapus</p>
                                    </div>
                                `;
                }
            } catch (e) {
                alert('❌ Gagal menghapus chat AI');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const activeUserId = @json($activeUserId ?? null);

            if (activeUserId !== null) {
                console.log('🔄 Page refreshed, opening chat ID:', activeUserId);
                openChat(activeUserId);
            } else {
                setFormEnabled(false);
            }

            // Handle window resize
            window.addEventListener('resize', function () {
                // Jika ukuran layar > 768px, pastikan sidebar terlihat
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                } else if(!sidebar.classList.contains('-translate-x-full') && sidebarOverlay.classList.contains('hidden')){
                     sidebar.classList.add('-translate-x-full');
                }
            });
        });
    </script>
@endsection