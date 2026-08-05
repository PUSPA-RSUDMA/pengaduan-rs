@extends('layouts.admin')
@section('title', 'WhatsApp Gateway & Live Chat')
@section('content')

<style>
    /* === STYLING LIVE CHAT === */
    .chat-wrapper { height: calc(100vh - 220px); min-height: 500px; background: #fff; border-radius: 12px; overflow: hidden; display: flex; border: 1px solid #edf2f9; }
    .chat-sidebar { width: 320px; border-right: 1px solid #edf2f9; background: #f8fafc; display: flex; flex-direction: column; }
    .chat-search { padding: 15px; background: #fff; border-bottom: 1px solid #edf2f9; }
    .chat-list { flex-grow: 1; overflow-y: auto; }
    .chat-item { padding: 15px; border-bottom: 1px solid #edf2f9; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; }
    .chat-item:hover, .chat-item.active { background: #e2e8f0; }
    .chat-avatar { width: 45px; height: 45px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-right: 15px; flex-shrink: 0; }
    .chat-main { flex-grow: 1; display: flex; flex-direction: column; background: #f1f5f9; position: relative; }
    .chat-header { padding: 15px 25px; background: #fff; border-bottom: 1px solid #edf2f9; display: flex; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02); z-index: 10; }
    .chat-messages { flex-grow: 1; padding: 25px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-color: #e5ddd5; background-blend-mode: multiply; opacity: 0.95; }
    .bubble { max-width: 65%; padding: 10px 15px; border-radius: 12px; position: relative; font-size: 0.9rem; line-height: 1.4; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    .bubble.them { background: #fff; align-self: flex-start; border-top-left-radius: 0; }
    .bubble.me { background: #dcf8c6; align-self: flex-end; border-top-right-radius: 0; }
    .bubble-time { font-size: 0.65rem; color: #888; display: block; text-align: right; margin-top: 5px; }
    .chat-input-area { padding: 15px 20px; background: #f0f2f5; display: flex; gap: 15px; align-items: center; }
    .chat-input-area input { border-radius: 25px; padding: 12px 20px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .btn-send-chat { width: 45px; height: 45px; border-radius: 50%; background: #25d366; color: white; border: none; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-send-chat:hover { background: #128c7e; transform: scale(1.05); }
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #64748b; }
    .chat-list::-webkit-scrollbar, .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-list::-webkit-scrollbar-thumb, .chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }
</style>

<div class="container-fluid">
    
    {{-- Notifikasi Form Uji Coba --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- NAVIGASI TAB --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-pills bg-white p-1 rounded-pill shadow-sm" id="waTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill fw-bold px-4" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat-pane" type="button" role="tab">
                    <i class="bi bi-chat-dots-fill me-2"></i> Live Chat
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill fw-bold px-4" id="setting-tab" data-bs-toggle="tab" data-bs-target="#setting-pane" type="button" role="tab">
                    <i class="bi bi-gear-fill me-2"></i> Pengaturan & Device
                </button>
            </li>
        </ul>
        <span id="mini-status-badge" class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-arrow-repeat"></i> Cek status...</span>
    </div>

    {{-- KONTEN TAB --}}
    <div class="tab-content" id="waTabContent">
        
        {{-- TAB 1: LIVE CHAT --}}
        <div class="tab-pane fade show active" id="chat-pane" role="tabpanel" tabindex="0">
            <div class="chat-wrapper shadow-sm">
                <!-- Panel Kiri (Daftar Chat) -->
                <div class="chat-sidebar">
                    <div class="chat-search">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" placeholder="Cari obrolan..." id="searchInput">
                        </div>
                    </div>
                    <div class="chat-list" id="chatList">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                            <br><small>Memuat obrolan...</small>
                        </div>
                    </div>
                </div>
                <!-- Panel Kanan (Isi Chat) -->
                <div class="chat-main" id="chatMain">
                    <div class="empty-state">
                        <i class="bi bi-whatsapp" style="font-size: 5rem; color: #cbd5e1;"></i>
                        <h4 class="mt-3 text-secondary fw-bold">Live Chat RSUD</h4>
                        <p class="text-muted">Pilih obrolan di panel kiri untuk mulai membalas.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: PENGATURAN & UJI COBA --}}
        <div class="tab-pane fade" id="setting-pane" role="tabpanel" tabindex="0">
            <div class="row">
                <!-- Status Perangkat (Kiri) -->
                <div class="col-md-5 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 fw-bold">
                            <i class="bi bi-qr-code-scan text-success me-2"></i> Status Perangkat WhatsApp
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-5">
                            <div id="wa-status-container">
                                <div class="spinner-border text-primary mb-3"></div>
                                <p class="text-muted">Memeriksa status mesin WhatsApp...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Uji Coba (Kanan) -->
                <div class="col-md-7 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 fw-bold">
                            <i class="bi bi-send text-primary me-2"></i> Uji Coba Pesan
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle-fill me-1"></i> Sistem menggunakan <strong>Self-Hosted WhatsApp Gateway</strong> di server Anda.
                            </div>
                            <form action="{{ route('whatsapp.test') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Tujuan WhatsApp</label>
                                    <input type="text" name="nomor" class="form-control" value="085336102800" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Isi Pesan</label>
                                    <textarea name="pesan" class="form-control" rows="4" required>Halo, ini pesan uji coba dari sistem RSUD.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                                    <i class="bi bi-cursor-fill me-2"></i> Kirim Pesan Tes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- SCRIPT GABUNGAN --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // === 1. MANAJEMEN STATUS WA & QR CODE ===
    const statusContainer = document.getElementById('wa-status-container');
    const miniBadge = document.getElementById('mini-status-badge');
    let isConnected = false;

    function fetchWAStatus() {
        fetch("{{ route('whatsapp.status') }}")
            .then(res => res.json())
            .then(data => {
                if (data.status === 'CONNECTED') {
                    isConnected = true;
                    miniBadge.className = 'badge bg-success rounded-pill px-3 py-2';
                    miniBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> WA Terhubung';
                    
                    statusContainer.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="fw-bold mt-3 text-success">Terhubung</h4>
                        <p class="text-muted small">WhatsApp server aktif dan siap beroperasi.</p>
                    `;
                } else {
                    isConnected = false;
                    miniBadge.className = 'badge bg-danger rounded-pill px-3 py-2';
                    miniBadge.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> WA Terputus';

                    if (data.status === 'QR_READY') {
                        statusContainer.innerHTML = `
                            <h6 class="mb-3 fw-bold">Scan QR Code via WhatsApp HP:</h6>
                            <img src="${data.qr}" class="img-fluid border rounded shadow-sm bg-white p-2" style="max-width: 220px;">
                            <p class="text-muted mt-3 small">Buka WhatsApp > Perangkat Tertaut > Tautkan</p>
                        `;
                    } else if (data.status === 'DISCONNECTED') {
                        statusContainer.innerHTML = `
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                            <h4 class="fw-bold mt-3 text-danger">Terputus</h4>
                            <p class="text-muted small">Menunggu engine membuat sesi baru...</p>
                        `;
                    } else {
                        statusContainer.innerHTML = `
                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                            <h4 class="fw-bold mt-3 text-warning">Engine Mati</h4>
                            <p class="text-muted small">Pastikan Node.js / PM2 berjalan di server.</p>
                        `;
                    }
                }
            }).catch(err => console.error(err));
    }

    fetchWAStatus();
    setInterval(fetchWAStatus, 3000); // Polling status 3 detik

    // === 2. MANAJEMEN LIVE CHAT ===
    let currentChatId = null;
    let chatsData = [];

    function formatTime(timestamp) {
        return new Date(timestamp * 1000).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function loadChats() {
        if (!isConnected) return; // Jangan load jika belum scan barcode
        fetch("{{ route('whatsapp.api.chats') }}").then(res => res.json()).then(data => {
            if(data.error) return;
            chatsData = data;
            renderChatList(data);
        });
    }

    function renderChatList(chats) {
        const chatListEl = document.getElementById('chatList');
        if (chats.length === 0) {
            chatListEl.innerHTML = '<div class="text-center p-4 text-muted small">Belum ada obrolan</div>';
            return;
        }
        chatListEl.innerHTML = '';
        chats.forEach(chat => {
            const initial = chat.name.charAt(0).toUpperCase();
            const isActive = currentChatId === chat.id ? 'active' : '';
            const unreadBadge = chat.unreadCount > 0 ? `<span class="badge bg-success rounded-pill ms-auto">${chat.unreadCount}</span>` : '';
            chatListEl.innerHTML += `
                <div class="chat-item ${isActive}" onclick="openChat('${chat.id}', '${chat.name.replace(/'/g, "\\'")}')">
                    <div class="chat-avatar">${initial}</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold text-truncate">${chat.name}</h6>
                            <small class="text-muted" style="font-size: 0.7rem;">${formatTime(chat.timestamp)}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-truncate d-block" style="max-width: 80%;">${chat.lastMessage}</small>
                            ${unreadBadge}
                        </div>
                    </div>
                </div>
            `;
        });
    }

    window.openChat = function(chatId, chatName) {
        currentChatId = chatId;
        renderChatList(chatsData); 
        
        const chatMain = document.getElementById('chatMain');
        chatMain.innerHTML = `
            <div class="chat-header">
                <div class="chat-avatar" style="width: 40px; height: 40px; font-size: 1rem;">${chatName.charAt(0).toUpperCase()}</div>
                <div>
                    <h6 class="mb-0 fw-bold">${chatName}</h6>
                    <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Terhubung</small>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="messageInput" class="form-control" placeholder="Ketik pesan balasan..." onkeypress="handleEnter(event)">
                <button class="btn-send-chat shadow-sm" onclick="sendMessage()"><i class="bi bi-send-fill"></i></button>
            </div>
        `;
        loadMessages(chatId);
    };

    function loadMessages(chatId) {
        fetch(`{{ route('whatsapp.api.messages') }}?chatId=${chatId}`).then(res => res.json()).then(messages => {
            if(messages.error) return;
            const messagesEl = document.getElementById('chatMessages');
            messagesEl.innerHTML = '';
            messages.reverse().forEach(m => {
                const type = m.fromMe ? 'me' : 'them';
                messagesEl.innerHTML += `<div class="bubble ${type}">${m.body} <span class="bubble-time">${formatTime(m.timestamp)}</span></div>`;
            });
            messagesEl.scrollTop = messagesEl.scrollHeight;
        });
    }

    window.sendMessage = function() {
        const input = document.getElementById('messageInput');
        const text = input.value.trim();
        if (!text || !currentChatId) return;

        input.value = '';
        const messagesEl = document.getElementById('chatMessages');
        messagesEl.innerHTML += `<div class="bubble me">${text} <span class="bubble-time">Mengirim...</span></div>`;
        messagesEl.scrollTop = messagesEl.scrollHeight;

        const number = currentChatId.split('@')[0];
        fetch("{{ route('whatsapp.test') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
            body: JSON.stringify({ nomor: number, pesan: text })
        }).then(() => {
            setTimeout(() => loadMessages(currentChatId), 1000);
            setTimeout(() => loadChats(), 1500);
        });
    };

    window.handleEnter = function(e) { if (e.key === 'Enter') sendMessage(); };

    document.getElementById('searchInput').addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase();
        renderChatList(chatsData.filter(c => c.name.toLowerCase().includes(keyword)));
    });

    setInterval(() => {
        if (!document.getElementById('searchInput').value) loadChats();
        if (currentChatId) loadMessages(currentChatId);
    }, 5000); // Polling chat 5 detik
});
</script>
@endsection