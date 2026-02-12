<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', config('app.name', 'Library System'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'HCCD Library System — Discover, borrow, and manage books.')">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ===== Default Light Mode ===== */
        body {
            background-color: #f8f9fa;
            color: #212529;
            transition: all 0.3s ease;
        }

        .card, .table, .navbar, .offcanvas, .modal-content {
            background-color: #ffffff;
            color: #212529;
            transition: all 0.3s ease;
        }

        /* ===== Dark Mode ===== */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode .navbar, 
        body.dark-mode .offcanvas, 
        body.dark-mode .modal-content,
        body.dark-mode .card, 
        body.dark-mode .table {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        body.dark-mode .card-header {
            background-color: #b71c1c !important; /* Crimson Red */
            color: #fff;
        }

        body.dark-mode .btn,
        body.dark-mode .btn-danger,
        body.dark-mode .btn-primary {
            color: #e0e0e0;
        }

        body.dark-mode input,
        body.dark-mode textarea,
        body.dark-mode select {
            background-color: #1e1e1e;
            color: #e0e0e0;
            border-color: #555;
        }

        body.dark-mode .table thead {
            background-color: #333333;
            color: #e0e0e0;
        }

        body.dark-mode .table-hover tbody tr:hover {
            background-color: #2c2c2c;
        }

        body.dark-mode a,
        body.dark-mode span,
        body.dark-mode p,
        body.dark-mode td,
        body.dark-mode th,
        body.dark-mode h1, 
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6,
        body.dark-mode .text-primary {
            color: #e0e0e0 !important;
        }

        body.dark-mode .btn-primary { background-color: #1976d2; border-color: #1976d2; }
        body.dark-mode .btn-danger { background-color: #d32f2f; border-color: #d32f2f; }

        body.dark-mode a { color: #90caf9 !important; }

        body.dark-mode .topbar {
    background-color: #1e1e1e !important;
    color: #e0e0e0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.5);
}

body.dark-mode .topbar .btn,
body.dark-mode .topbar a {
    color: #90caf9 !important;
}

body.dark-mode .topbar .btn-sm {
    background-color: #b71c1c;
    color: #fff;
    border: none;
}

body.dark-mode .alert { background-color: #333 !important; color: #e0e0e0 !important; border-color: #444 !important; }
body.dark-mode .form-control::placeholder { color: #aaa; }
body.dark-mode .table, body.dark-mode .table th, body.dark-mode .table td {
    background-color: #1e1e1e !important; color: #e0e0e0 !important; border-color: #444 !important;
}
body.dark-mode .table thead th { background-color: #333 !important; color: #e0e0e0 !important; }
body.dark-mode .table-hover tbody tr:hover { background-color: #2c2c2c !important; }
body.dark-mode .table-striped tbody tr:nth-of-type(odd) { background-color: #252525 !important; }
body.dark-mode .page-link { background-color: #2c2c2c; color: #e0e0e0; border-color: #444; }
body.dark-mode .page-link:hover { background-color: #3a3a3a; color: #fff; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #0d6efd 0%, #dc3545 100%);
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed { width: 70px; }
        .sidebar .logo { display: flex; align-items: center; padding: 1rem; }
        .sidebar .logo img { width: 40px; height: 40px; }
        .sidebar .logo span { margin-left: 10px; font-weight: bold; font-size: 1.2rem; }
        .sidebar ul { list-style: none; padding: 0; margin-top: 1rem; }
        .sidebar ul li a {
            display: flex; align-items: center; color: white; text-decoration: none;
            padding: 12px 20px; transition: 0.2s;
        }
        .sidebar ul li a:hover { background: rgba(255, 255, 255, 0.15); }
        .sidebar ul li i { font-size: 1.2rem; margin-right: 10px; }
        .sidebar.collapsed ul li a span, .sidebar.collapsed .logo span { display: none; }

        /* ===== MOBILE OFF-CANVAS ===== */
        @media (max-width: 768px) {
            .sidebar { left: -250px; width: 250px; transition: left 0.3s ease; }
            .sidebar.show { left: 0; box-shadow: 2px 0 8px rgba(0,0,0,0.3); }
        }

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: 250px; padding: 20px; transition: margin-left 0.3s; }
        .collapsed + .main-content { margin-left: 70px; }

        /* ===== Clickable Dashboard Cards ===== */
        .clickable-card {
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .clickable-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
        }

        /* ===== TOPBAR ===== */
        .topbar {
            display: flex; justify-content: space-between; align-items: center;
            background: white; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 20px; margin-bottom: 20px;
        }

        .topbar .btn-toggle { border: none; background: transparent; font-size: 1.4rem; color: #0d6efd; }
        .topbar .btn-sm { border-radius: 20px; background: #dc3545; color: white; border: none; padding: 6px 12px; font-weight: 500; }
        .topbar .btn-sm:hover { background: #b02a37; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .topbar { flex-direction: column; align-items: stretch; padding: 10px 15px; }
            .topbar > div:first-child { display: flex; flex-direction: row; align-items: center; justify-content: space-between; margin-bottom: 8px; width: 100%; }
            .topbar > div:last-child { justify-content: flex-end; flex-wrap: wrap; gap: 8px; width: 100%; }
            .topbar form.d-md-flex { flex-grow: 0; }
            .topbar form.d-md-flex input.form-control { width: 200px; min-width: 150px; }
        }

        @guest
        .main-content { margin-left: 0 !important; }
        @endguest
        .alert-dismissible .btn-close { filter: invert(1); }
    </style>
    @stack('styles')
</head>
<body>
    {{-- ===== Sidebar ===== --}}
    @auth
    <div id="sidebar" class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo">
            <span>HCCD Library System</span>
        </div>

        <!-- Mobile Search (only on mobile) -->
        <form action="{{ route('books.search') }}" method="GET" class="d-md-none mb-3 px-3">
            <input type="text" name="query" class="form-control mb-2" placeholder="Search books..." required>
            <button type="submit" class="btn btn-danger w-100">Search</button>
        </form>

        <ul>
            @if(Auth::user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>
                <li><a href="{{ route('admin.scan-qr') }}"><i class="bi bi-qr-code-scan"></i><span>Scan QR</span></a></li>
                <li><a href="{{ route('admin.borrows.requests') }}"><i class="bi bi-inbox"></i><span>Borrow Requests</span></a></li>
                <li><a href="{{ route('books.index') }}"><i class="bi bi-book"></i><span>Books</span></a></li>
                <li><a href="{{ route('admin.books.create') }}"><i class="bi bi-plus-circle"></i><span>Add Book</span></a></li>
                <li><a href="{{ route('admin.reports') }}"><i class="bi bi-bar-chart"></i><span>Reports</span></a></li>
                {{-- <li><a href="{{ route('admin.attendance.report') }}"><i class="bi bi-clipboard-data"></i><span>Attendance Report</span></a></li> --}}
                <li><a href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i><span>Users</span></a></li>
            @elseif(Auth::user()->role === 'faculty' || Auth::user()->role === 'student')
                <li><a href="{{ route(Auth::user()->role . '.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>
                <li><a href="{{ route('books.index') }}"><i class="bi bi-book"></i><span>Books</span></a></li>
                {{-- <li><a href="{{ route('attendance.index') }}"><i class="bi bi-pencil-square"></i><span>Library Attendance</span></a></li> --}}
            @endif
        </ul>

        <!-- Dark Mode Toggle -->
        <div class="text-center mt-3">
            <button id="themeToggleBtn" class="btn btn-outline-primary w-100">
                <i class="bi bi-moon-stars"></i> Dark Mode
            </button>
        </div>
    </div>
    @endauth

    {{-- ===== Main Content ===== --}}
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center gap-3">
                @auth
                    <button class="btn-toggle me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>

                    {{-- Desktop Search --}}
                    <form action="{{ route('books.search') }}" method="GET"
                          class="d-none d-md-flex align-items-center gap-2">
                        <input type="text" name="query" class="form-control" placeholder="Search books..." required>
                        <button type="submit" class="btn btn-danger">Search</button>
                    </form>
                @endauth

                @guest
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/hccd_logo.png') }}" style="width:38px;height:38px;">
                        <span class="fw-bold text-danger fs-5">HCCD Library System</span>
                    </div>
                @endguest
            </div>

            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                @guest
                    <button id="themeToggleBtn" class="btn btn-outline-danger">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                @endguest

                @auth
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm position-relative">
                        <i class="bi bi-bell me-1"></i>Notifications
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-person me-1"></i>Profile</a>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                    </form>
                @endauth
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    @if(config('services.chatbot_url'))
    <button id="toggleChatbot" class="chatbot-fab" type="button" aria-label="Open library chatbot"><i class="bi bi-chat-dots-fill"></i></button>
    <div id="chatbotContainer" class="chatbot-panel">
        <div id="chatbotHeader" class="chatbot-header">
            <span><i class="bi bi-book me-2"></i>Library Chatbot</span>
            <button id="closeChatbot" type="button" class="btn btn-sm btn-light" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="chatbotMessages" class="chatbot-messages"></div>
        <div id="chatbotInputContainer" class="chatbot-input-wrap">
            <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Ask about books, borrowing, due dates..." />
            <button id="chatbotSend" type="button" class="chatbot-send"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
    <style>
    .chatbot-fab { position:fixed;bottom:20px;right:20px;width:56px;height:56px;padding:0;background:linear-gradient(135deg,#0d6efd,#dc3545);color:#fff;border:none;border-radius:50%;font-size:1.4rem;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,0.25);z-index:2000;transition:transform .2s,box-shadow .2s; }
    .chatbot-fab:hover { transform:scale(1.05); box-shadow:0 6px 18px rgba(0,0,0,0.3); }
    .chatbot-panel { position:fixed;bottom:86px;right:20px;width:360px;max-width:calc(100vw - 40px);height:420px;background:#fff;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.15);display:none;flex-direction:column;overflow:hidden;z-index:2000; }
    body.dark-mode .chatbot-panel { background:#1e1e1e; }
    .chatbot-header { background:linear-gradient(135deg,#0d6efd,#c82333);color:#fff;padding:12px 14px;font-weight:600;display:flex;justify-content:space-between;align-items:center; }
    .chatbot-messages { flex:1;padding:12px;overflow-y:auto;background:#f8f9fa;font-size:0.9rem; }
    body.dark-mode .chatbot-messages { background:#252525; }
    .chatbot-messages .bot { color:#0d6efd; margin-bottom:10px; }
    .chatbot-messages .user { color:#dc3545; margin-bottom:10px; text-align:right; }
    .chatbot-input-wrap { display:flex;border-top:1px solid #dee2e6; }
    body.dark-mode .chatbot-input-wrap { border-color:#444; }
    .chatbot-input { flex:1;padding:10px 12px;border:none;outline:none;font-size:0.95rem; }
    body.dark-mode .chatbot-input { background:#1e1e1e;color:#e0e0e0; }
    .chatbot-send { padding:0 16px;border:none;background:#0d6efd;color:#fff;cursor:pointer; }
    .chatbot-send:hover { background:#0b5ed7; }
    </style>
    @endif


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const toggleSidebar = document.getElementById('toggleSidebar');
            if(toggleSidebar){
                toggleSidebar.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle('show');
                        toggleOverlay();
                    } else {
                        sidebar.classList.toggle('collapsed');
                    }
                });
            }
            function toggleOverlay() {
                let overlay = document.getElementById('sidebarOverlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'sidebarOverlay';
                    overlay.style.position = 'fixed';
                    overlay.style.top = 0;
                    overlay.style.left = 0;
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.background = 'rgba(0,0,0,0.3)';
                    overlay.style.zIndex = '999';
                    document.body.appendChild(overlay);
                    overlay.addEventListener('click', () => {
                        sidebar.classList.remove('show');
                        overlay.remove();
                    });
                } else {
                    overlay.remove();
                }
            }

            // Dark Mode Toggle
            const toggleBtn = document.getElementById('themeToggleBtn');
            const body = document.body;
            if(localStorage.getItem('theme')==='dark'){ body.classList.add('dark-mode'); toggleBtn.innerHTML = '<i class="bi bi-sun"></i> Light Mode'; }
            toggleBtn.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDark = body.classList.contains('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                toggleBtn.innerHTML = isDark ? '<i class="bi bi-sun"></i> Light Mode' : '<i class="bi bi-moon-stars"></i> Dark Mode';
            });

            // Chatbot (only when widget and URL are present)
            const chatbotUrl = @json(config('services.chatbot_url'));
            const toggleChat = document.getElementById('toggleChatbot');
            const closeChat = document.getElementById('closeChatbot');
            const chatbot = document.getElementById('chatbotContainer');
            const chatInput = document.getElementById('chatbotInput');
            const chatMessages = document.getElementById('chatbotMessages');
            const chatSend = document.getElementById('chatbotSend');

            if (toggleChat && chatbot && chatbotUrl) {
                const CHAT_STORAGE_KEY = 'library_chat_history';
                function saveChatHistory() {
                    if (chatMessages) localStorage.setItem(CHAT_STORAGE_KEY, chatMessages.innerHTML);
                }
                function loadChatHistory() {
                    if (!chatMessages) return;
                    const saved = localStorage.getItem(CHAT_STORAGE_KEY);
                    if (saved) { chatMessages.innerHTML = saved; chatMessages.scrollTop = chatMessages.scrollHeight; }
                }
                function clearChatHistory() {
                    localStorage.removeItem(CHAT_STORAGE_KEY);
                    if (chatMessages) chatMessages.innerHTML = '';
                }
                loadChatHistory();
                toggleChat.addEventListener('click', () => {
                    chatbot.style.display = (chatbot.style.display === 'flex') ? 'none' : 'flex';
                });
                if (closeChat) closeChat.addEventListener('click', () => { chatbot.style.display = 'none'; });
                async function sendMessage() {
                    const text = chatInput && chatInput.value ? chatInput.value.trim() : '';
                    if (!text || !chatMessages) return;
                    chatMessages.innerHTML += `<div class="user"><strong>You:</strong> ${escapeHtml(text)}</div>`;
                    chatInput.value = '';
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    saveChatHistory();
                    try {
                        const base = chatbotUrl.replace(/\/$/, '');
                        const res = await fetch(base + '/chat', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message: text }) });
                        const data = await res.json();
                        chatMessages.innerHTML += `<div class="bot">${data.reply || 'No response.'}</div>`;
                    } catch (err) {
                        chatMessages.innerHTML += `<div class="bot">Unable to reach the chatbot. Please try again later.</div>`;
                    }
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    saveChatHistory();
                }
                function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
                if (chatSend) chatSend.addEventListener('click', sendMessage);
                if (chatInput) chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });
                const logoutForm = document.getElementById('logoutForm');
                if (logoutForm) logoutForm.addEventListener('submit', clearChatHistory);
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
