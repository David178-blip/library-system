<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Library System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .card { border-radius: 12px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">

        {{-- ✅ Logo & App Name --}}
        <a class="navbar-brand d-flex align-items-center"
           href="@auth
                    @if(Auth::user()->role === 'admin')
                        {{ url('/admin/dashboard') }}
                    @elseif(Auth::user()->role === 'faculty')
                        {{ url('/faculty/dashboard') }}
                    @elseif(Auth::user()->role === 'student')
                        {{ url('/student/dashboard') }}
                    @else
                        {{ url('/') }}
                    @endif
                @else
                    {{ url('/') }}
                @endauth">

            <img src="{{ asset('images/hccd_logo.png') }}"
                 alt="App Logo"
                 class="me-2"
                 width="50"
                 height="50">

            <span class="fs-4 fw-bold">HCCD Library</span>
        </a>

        {{-- ✅ Mobile Toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- ✅ Collapsible Content --}}
        <div class="collapse navbar-collapse" id="navbarNav">

            {{-- ✅ Search Bar (Centered on larger screens) --}}
            <form action="{{ route('books.search') }}" method="GET" class="d-flex mx-auto my-2 my-lg-0" style="max-width: 400px;">
                <input
                    type="text"
                    name="query"
                    class="form-control me-2"
                    placeholder="Search books..."
                    required
                >
                <button type="submit" class="btn btn-light">Search</button>
            </form>

            {{-- ✅ Right Side Buttons --}}
            <ul class="navbar-nav ms-auto d-flex align-items-center">

                @auth
                    {{-- ✅ Emails --}}
                    <li class="nav-item me-2">
                        <a href="{{ route('notifications.index') }}" class="btn btn-warning btn-sm">
                            🔔 Emails
                        </a>
                    </li>

                    {{-- ✅ Profile --}}
                    <li class="nav-item me-2">
                        <a href="{{ route('profile') }}" class="btn btn-light btn-sm">
                            Profile
                        </a>
                    </li>

                    {{-- ✅ Books --}}
                    <li class="nav-item me-2">
                        <a href="{{ route('books.index') }}" class="btn btn-info btn-sm">
                            Books
                        </a>
                    </li>

                    {{-- ✅ Logout --}}
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item me-2">
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-success btn-sm">Register</a>
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</nav>


    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

  <!-- ✅ Chatbot Toggle Button -->
<button id="toggleChatbot" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px 15px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
">
💬
</button>

<!-- ✅ Chatbot Container -->
<div id="chatbotContainer" style="
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 300px;
    height: 400px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
">
    <div style="background: #007bff; color: white; padding: 10px; font-weight: bold;">
        📚 Library Chatbot
        <button id="closeChatbot" style="
            float: right;
            background: transparent;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        ">✖</button>
    </div>

    <div id="chatbotMessages" style="flex: 1; padding: 10px; overflow-y: auto;"></div>

    <div style="padding: 10px; display: flex; gap: 5px;">
        <input type="text" id="chatbotInput" placeholder="Type a message..." style="flex: 1; padding: 5px;">
        <button id="sendChatbotMessage" style="padding: 5px 10px;">Send</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggleChatbot");
    const closeBtn = document.getElementById("closeChatbot");
    const chatbot = document.getElementById("chatbotContainer");
    const messages = document.getElementById("chatbotMessages");
    const input = document.getElementById("chatbotInput");
    const sendBtn = document.getElementById("sendChatbotMessage");

    // ✅ Toggle open
    toggleBtn.addEventListener("click", () => {
        chatbot.style.display = chatbot.style.display === "none" ? "flex" : "none";
    });

    // ✅ Close button
    closeBtn.addEventListener("click", () => {
        chatbot.style.display = "none";
    });

    function appendMessage(sender, text) {
        const msgDiv = document.createElement("div");
        msgDiv.style.marginBottom = "8px";
        msgDiv.innerHTML = `<strong>${sender}:</strong> ${text}`;
        messages.appendChild(msgDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;
        
        appendMessage("You", text);
        input.value = "";

        try {
            const response = await fetch("http://localhost:3000/chat", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            appendMessage("Bot", data.reply);
        } catch (error) {
            appendMessage("Bot", "⚠️ Unable to connect to the server.");
        }
    }

    sendBtn.addEventListener("click", sendMessage);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") sendMessage();
    });
});
</script>


</body>
</html>
