<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'MAX Engage Hub')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('app.css') }}">
    </head>
    <body>
        <header class="site-header">
            <div class="container topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">MAX</span>
                    <span>Engage Hub</span>
                </a>

                <nav class="nav">
                    <a href="{{ route('home') }}">Public Forms</a>

                    @auth
                        <a href="{{ route('admin.inbox') }}">Inbox</a>
                        <a href="{{ route('admin.customers.index') }}">Customers</a>
                        <a href="{{ route('admin.cases.index') }}">Cases</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-button">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Admin Login</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="container page">
            @if (session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error">
                    <strong>입력값을 확인해주세요.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
