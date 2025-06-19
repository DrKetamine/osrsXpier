<head>
    <meta charset="UTF-8">
    <link href="{{ asset('css/header.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<header>
    <div class="headerWrap">
        <div class="headerlogo">
            <a href="/" class="logo">OSRSxpier</a>
        </div>

        <nav>
            <a href="{{ url('/actions') }}">Cooking Calculator</a>
            <a href="{{ url('/actions') }}">About Us</a>
        </nav>

        <div class="logregWrap">
            @auth
                <div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btnClear" type="submit"><i class="bi bi-box-arrow-left"></i></button>
                        <a class="btnClear" href="/dashboard"><i class="bi bi-person"></i></a>
                    </form>
                </div>
            @else
                <a class="btn3" href="{{ url('/login') }}">Login</a>
                <a class="btn2" href="{{ url('/register') }}">Register</a>
            @endauth
        </div>
    </div>
</header>