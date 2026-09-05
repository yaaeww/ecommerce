@extends('layouts.app')

@section('page_title', 'Chat Pembeli')

@push('style')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Pusat Pesan Langsung
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-display">Chat dengan Pembeli</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Komunikasi langsung dengan pelanggan untuk menjawab pertanyaan produk dan detail pengiriman</p>
        </div>
    </div>

    <!-- Main Chat Workspace Card -->
    <div class="bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden flex flex-col md:flex-row h-[78vh] min-h-[580px]">
        
        <!-- ========================================== -->
        <!-- 👥 LEFT SIDEBAR (DAFTAR PEMBELI)           -->
        <!-- ========================================== -->
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-200/80 flex flex-col bg-slate-50/50 shrink-0">
            
            <!-- Sidebar Top Header -->
            <div class="p-4 border-b border-slate-200/80 bg-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900 font-display">Daftar Pembeli</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $customers->count() }} Kontak
                </span>
            </div>

            <!-- Buyer Search Filter -->
            <div class="p-3 border-b border-slate-200/80 bg-white">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        id="searchBuyerInput" 
                        placeholder="Cari nama atau email pembeli..." 
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:outline-none transition"
                        onkeyup="filterBuyerList()"
                    >
                </div>
            </div>

            <!-- Customer List -->
            <div id="customerListContainer" class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-slate-100 p-2 space-y-1">
                @forelse ($customers as $cust)
                    <div 
                        class="user-item p-3 rounded-2xl cursor-pointer hover:bg-white hover:shadow-2xs transition flex items-center gap-3 relative group"
                        data-name="{{ strtolower($cust->name) }}"
                        data-email="{{ strtolower($cust->email) }}"
                        onclick="openChat({{ $cust->id }}, '{{ e($cust->name) }}', '{{ e($cust->email) }}'); setActiveUser(this);"
                    >
                        <!-- Avatar -->
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-sm flex items-center justify-center shadow-xs shrink-0">
                            {{ strtoupper(substr($cust->name, 0, 1)) }}
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="text-xs font-bold text-slate-900 truncate user-display-name">{{ $cust->name }}</h4>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">{{ $cust->email }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-300 mb-3 text-lg">
                            <i class="fas fa-comments"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-600">Belum Ada Chat</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Percakapan dari pembeli akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 💬 RIGHT CHAT CONVERSATION MAIN PANEL      -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col bg-white">
            
            <!-- Chat Active Header -->
            <div id="chatHeader" class="p-4 border-b border-slate-100 bg-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-base">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 font-display">Pilih Pembeli</h4>
                        <p class="text-[11px] text-slate-400">Pilih salah satu kontak di sebelah kiri untuk membuka pesan</p>
                    </div>
                </div>
            </div>

            <!-- Message Bubbles Scroll Area -->
            <div id="chatMessages" class="flex-1 p-6 overflow-y-auto custom-scrollbar bg-slate-50/60 flex flex-col justify-center items-center">
                <div class="text-center p-8 max-w-sm">
                    <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-slate-900 font-display">UMKM Customer Service Hub</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Kirim respon ramah dan cepat untuk meningkatkan kepercayaan pembeli dan mempercepat transaksi mangga Anda.
                    </p>
                </div>
            </div>

            <!-- Message Input Form Bar -->
            <form id="chatForm" onsubmit="sendMessage(event)" class="p-4 border-t border-slate-100 bg-white">
                <input type="hidden" id="receiver_id" value="">
                <div class="flex items-center gap-3">
                    <div class="flex-1 relative flex items-center">
                        <input 
                            id="messageInput" 
                            type="text" 
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                            placeholder="Ketik pesan balasan untuk pembeli..." 
                            autocomplete="off" 
                            disabled
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="send-button w-11 h-11 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center justify-center transition shadow-xs disabled:opacity-40 disabled:cursor-not-allowed shrink-0" 
                        disabled
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>

@push('scripts')
<script>
    const authUserId = {{ Auth::id() }};
    let currentUserId = null;
    let currentUserName = '';
    let echo = null;

    try {
        echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            wsHost: '{{ env('PUSHER_HOST', 'ws.pusherapp.com') }}',
            wsPort: '{{ env('PUSHER_PORT', 6001) }}',
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss']
        });
    } catch (e) {
        console.error("Echo init error:", e);
    }

    function formatTime(iso) {
        if (!iso) return '';
        return new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    // ── Label tanggal ala WhatsApp ─────────────────────────────
    function isSameDay(a, b) {
        const da = new Date(a);
        const db = new Date(b);
        return da.getFullYear() === db.getFullYear() &&
            da.getMonth() === db.getMonth() &&
            da.getDate() === db.getDate();
    }

    function formatDayLabel(iso) {
        if (!iso) return '';
        const date = new Date(iso);
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const day = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        if (day.getTime() === today.getTime()) return 'Hari Ini';
        if (day.getTime() === yesterday.getTime()) return 'Kemarin';
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    let lastRenderedDate = null;

    function createDateSeparator(iso) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex justify-center my-2';
        const span = document.createElement('span');
        span.className = 'px-3 py-1 rounded-full bg-gray-200/80 text-[10px] font-semibold text-gray-500';
        span.textContent = formatDayLabel(iso);
        wrapper.appendChild(span);
        return wrapper;
    }

    function appendChat(chat, bubble) {
        const chatBox = document.getElementById('chatMessages');
        if (chat.created_at) {
            const dateKey = new Date(chat.created_at).toDateString();
            if (dateKey !== lastRenderedDate) {
                chatBox.appendChild(createDateSeparator(chat.created_at));
                lastRenderedDate = dateKey;
            }
        }
        chatBox.appendChild(bubble || createBubble(chat));
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function filterBuyerList() {
        const query = document.getElementById('searchBuyerInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.user-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const email = item.getAttribute('data-email') || '';
            if (query === '' || name.includes(query) || email.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function createBubble(chat) {
        const isMine = chat.sender_id === authUserId;
        const wrapper = document.createElement('div');
        wrapper.className = `flex gap-3 mb-3 w-full ${isMine ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        if (isMine) {
            bubble.className = 'max-w-[75%] md:max-w-[65%] rounded-2xl rounded-tr-xs bg-emerald-600 text-white p-3.5 shadow-xs text-xs sm:text-sm';
            bubble.innerHTML = `
                <div class="leading-relaxed break-words">${escapeHtml(chat.message)}</div>
                <div class="flex items-center justify-end gap-1.5 mt-1.5 text-[10px] text-emerald-200">
                    <span>${formatTime(chat.created_at ?? new Date())}</span>
                    <i class="fas fa-check-double text-[9px]"></i>
                </div>
            `;
        } else {
            bubble.className = 'max-w-[75%] md:max-w-[65%] rounded-2xl rounded-tl-xs bg-white border border-slate-200/80 text-slate-800 p-3.5 shadow-2xs text-xs sm:text-sm';
            bubble.innerHTML = `
                <div class="leading-relaxed break-words text-slate-800">${escapeHtml(chat.message)}</div>
                <div class="flex items-center justify-end gap-1.5 mt-1.5 text-[10px] text-slate-400">
                    <span>${formatTime(chat.created_at ?? new Date())}</span>
                </div>
            `;
        }

        wrapper.appendChild(bubble);
        return wrapper;
    }

    function setActiveUser(element) {
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('bg-emerald-50/80', 'border-l-4', 'border-emerald-600', 'shadow-2xs');
            item.classList.add('bg-transparent');
        });
        element.classList.remove('bg-transparent');
        element.classList.add('bg-emerald-50/80', 'border-l-4', 'border-emerald-600', 'shadow-2xs');
    }

    async function openChat(userId, name, email) {
        currentUserId = userId;
        currentUserName = name;
        document.getElementById('receiver_id').value = userId;
        const chatBox = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.querySelector('.send-button');

        messageInput.disabled = false;
        sendButton.disabled = false;
        messageInput.focus();

        const firstLetter = name.charAt(0).toUpperCase();
        document.getElementById('chatHeader').innerHTML = `
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-sm flex items-center justify-center shadow-xs">
                        ${firstLetter}
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 font-display">${escapeHtml(name)}</h4>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>${escapeHtml(email || 'Pembeli Terdaftar')}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                        <i class="fas fa-shield-check text-emerald-600 mr-1"></i> Transaksi Aman
                    </span>
                </div>
            </div>
        `;

        chatBox.className = 'flex-1 p-6 overflow-y-auto custom-scrollbar bg-slate-50/60 space-y-3';
        chatBox.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-xs text-slate-500 shadow-2xs">
                    <i class="fas fa-spinner fa-spin text-emerald-600"></i> Memuat riwayat chat...
                </div>
            </div>
        `;

        if (echo) echo.leaveAllChannels();

        try {
            const res = await fetch(`/penjual/chat/history/${userId}`);
            const data = await res.json();

            chatBox.innerHTML = '';
            lastRenderedDate = null;

            if (data.chats && data.chats.length > 0) {
                data.chats.forEach(chat => appendChat(chat));
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                chatBox.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-center text-slate-400 p-8">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-300 mb-3 text-lg">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h5 class="text-xs font-bold text-slate-700">Belum Ada Riwayat Pesan</h5>
                        <p class="text-[11px] text-slate-400 mt-0.5">Mulai percakapan dengan mengirimkan salam atau menanyakan kebutuhan pesanan.</p>
                    </div>
                `;
            }

            if (echo) {
                echo.private('chat.' + authUserId)
                    .listen('.chat.message', (data) => {
                        if (data.chat.sender_id == currentUserId) {
                            appendChat(data.chat);
                        }
                    });
            }
        } catch (err) {
            console.error(err);
            chatBox.innerHTML = `
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs text-center">
                    Gagal memuat percakapan. Silakan coba lagi.
                </div>
            `;
        }
    }

    async function sendMessage(e) {
        e.preventDefault();
        const msgInput = document.getElementById('messageInput');
        const msg = msgInput.value.trim();
        const receiver = document.getElementById('receiver_id').value;
        const sendButton = document.querySelector('.send-button');

        if (!msg || receiver === '') return;

        const chatBox = document.getElementById('chatMessages');

        // Append locally
        const optimisticChat = {
            sender_id: authUserId,
            message: msg,
            created_at: new Date().toISOString()
        };
        const myBubble = createBubble(optimisticChat);
        appendChat(optimisticChat, myBubble);

        msgInput.value = '';
        sendButton.disabled = true;
        msgInput.disabled = true;

        try {
            await fetch(`/penjual/chat/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg, receiver_id: receiver })
            });
        } catch (error) {
            console.error("Gagal kirim pesan:", error);
        } finally {
            sendButton.disabled = false;
            msgInput.disabled = false;
            msgInput.focus();
        }
    }
</script>
@endpush
@endsection