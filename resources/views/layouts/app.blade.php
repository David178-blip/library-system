<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Library System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

body.dark-mode .btn {
    border-color: #e53935;
}

body.dark-mode .btn-primary {
    background-color: #1976d2;
    border-color: #1976d2;
}

body.dark-mode .btn-danger {
    background-color: #d32f2f;
    border-color: #d32f2f;
}

body.dark-mode .table thead {
    background-color: #333333;
    color: #e0e0e0;
}

body.dark-mode .table-hover tbody tr:hover {
    background-color: #2c2c2c;
}

body.dark-mode a, 
body.dark-mode .text-primary {
    color: #90caf9 !important;
}


        /* ===== SIDEBAR ===== */
        .sidebar {
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #0d6efd 0%, #dc3545 100%);
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            transition: width 0.3s;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            padding: 1rem;
        }

        .sidebar .logo img {
            width: 40px;
            height: 40px;
        }

        .sidebar .logo span {
            margin-left: 10px;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            transition: 0.2s;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar ul li i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .sidebar.collapsed ul li a span {
            display: none;
        }

        .sidebar.collapsed .logo span {
            display: none;
        }


        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .collapsed + .main-content {
            margin-left: 70px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 20px;
            margin-bottom: 20px;
        }

        .topbar .btn-toggle {
            border: none;
            background: transparent;
            font-size: 1.4rem;
            color: #0d6efd;
        }

        .topbar .btn-sm {
            border-radius: 20px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            font-weight: 500;
        }

        .topbar .btn-sm:hover {
            background: #b02a37;
        }

        /* ===== Chatbot Button ===== */
        #toggleChatbot {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 14px 18px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        #toggleChatbot:hover { background: #b02a37; }

        #chatbotContainer {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 320px;
            height: 420px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        #chatbotHeader {
            background: #0d6efd;
            color: white;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- ===== Sidebar ===== --}}
    <div id="sidebar" class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo">
            <span>HCCD Library System</span>
        </div>
<ul>
    @auth
        @if(Auth::user()->role === 'admin')

            <li><a href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>

            <li><a href="{{ route('admin.scan-qr') }}">
                <i class="bi bi-qr-code-scan"></i><span>Scan QR</span></a></li>

            <li><a href="{{ route('admin.borrows.requests') }}">
                <i class="bi bi-inbox"></i><span>Borrow Requests</span></a></li>

            <li><a href="{{ route('admin.books.create') }}">
                <i class="bi bi-plus-circle"></i><span>Add Book</span></a></li>

            <li><a href="{{ route('admin.reports') }}">
                <i class="bi bi-bar-chart"></i><span>Reports</span></a></li>

            <li><a href="{{ route('admin.attendance.report') }}">
                <i class="bi bi-clipboard-data"></i><span>Attendance Report</span></a></li>

            <li><a href="{{ route('admin.users.index') }}">
                <i class="bi bi-people"></i><span>Users</span></a></li>

        @elseif(Auth::user()->role === 'faculty')

            <li><a href="{{ route('faculty.dashboard') }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>

            <li><a href="{{ route('books.index') }}">
                <i class="bi bi-book"></i><span>Books</span></a></li>

            <li><a href="{{ route('attendance.index') }}">
                <i class="bi bi-pencil-square"></i><span>Library Attendance</span></a></li>

        @elseif(Auth::user()->role === 'student')

            <li><a href="{{ route('student.dashboard') }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>

            <li><a href="{{ route('books.index') }}">
                <i class="bi bi-book"></i><span>Books</span></a></li>

            <li><a href="{{ route('attendance.index') }}">
                <i class="bi bi-pencil-square"></i><span>Library Attendance</span></a></li>

        @endif
    @endauth
</ul>

        <!-- Dark Mode Toggle -->
<div class="text-center mt-3">
    <button id="themeToggle" class="btn btn-outline-primary w-100">
        <i class="bi bi-moon-stars"></i> Toggle Dark Mode
    </button>
</div>

    </div>

    {{-- ===== Main Content ===== --}}
    <div class="main-content">
        <div class="topbar">
            <button class="btn-toggle" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm">🔔 Notifications</a>
                    <a href="{{ route('profile') }}" class="btn btn-sm">Profile</a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm">Logout</button>
                    </form>
                @endauth
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

 {{-- ===== AI Chatbot (Dialogflow) ===== --}}
<!-- Floating Chat Button -->
<button id="toggleChatbot" 
    style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 14px 18px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 2000;
    ">
    💬
</button>

<!-- Chatbot Frame -->
<div id="chatbotContainer"
    style="
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        display: none;
        overflow: hidden;
        z-index: 2000;
    ">
    
    <div style="background:#0d6efd;color:white;padding:10px;font-weight:bold;">
        📚 HCCD Library Chatbot
        <button id="closeChatbot"
            style="float:right;background:none;border:none;color:white;font-size:16px;">
            ✖
        </button>
    </div>


<iframe
    allow="microphone;"
    width="350"
    height="430"
    src="https://console.dialogflow.com/api-client/demo/embedded/89b31e0d-3050-4b24-a16b-b4ff145fca9f">
</iframe>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle
    document.getElementById('toggleSidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Dark Mode Toggle
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('themeToggle');
        const body = document.body;

        // Load saved preference
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
        }

        // Toggle on click
        toggle.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            // Change icon dynamically
            toggle.innerHTML = isDark
                ? '<i class="bi bi-sun"></i> Light Mode'
                : '<i class="bi bi-moon-stars"></i> Dark Mode';
        });
    });

    // Chatbot toggle (Dialogflow widget)
    const chatbot = document.getElementById('chatbotContainer');
    const toggleChat = document.getElementById('toggleChatbot');
    const closeChat = document.getElementById('closeChatbot');

    toggleChat.addEventListener('click', () => {
        chatbot.style.display = chatbot.style.display === 'none' ? 'block' : 'none';
    });

    closeChat.addEventListener('click', () => {
        chatbot.style.display = 'none';
    });
</script>


    

    @yield('scripts')
</body>
</html>
