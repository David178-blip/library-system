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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">

            {{-- 📚 Logo goes to correct dashboard --}}
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
         width="40" 
         height="40">

    <span>Library</span>
</a>


            <div>
                {{-- Show different buttons for logged-in users vs guests --}}
                @auth
                    <a href="{{ route('profile') }}" class="btn btn-light btn-sm">Profile</a>

                    <a href="{{ route('books.index') }}" class="btn btn-info btn-sm">Books</a>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-success btn-sm">Register</a>
                @endguest
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

    <!-- Floating Chatbot Button -->
    <div id="chatbot-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 999;">
        <button id="chatbot-toggle" class="btn btn-primary rounded-circle p-3">
            💬
        </button>
    </div>

    <!-- Dialogflow Messenger -->
    <df-messenger
        intent="WELCOME"
        chat-title="Library Assistant"
        agent-id="YOUR_DIALOGFLOW_AGENT_ID"
        language-code="en"
    ></df-messenger>

    <script>
        // Show/Hide chatbot
        document.getElementById('chatbot-toggle').addEventListener('click', function() {
            const messenger = document.querySelector('df-messenger');
            messenger.style.display = (messenger.style.display === 'none') ? 'block' : 'none';
        });

        // Hide by default
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('df-messenger').style.display = 'none';
        });
    </script>

    <!-- Dialogflow Web Messenger Library -->
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>

</body>
</html>
