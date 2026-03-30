<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Seminar Manager' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div>
                <div class="brand">SeminarBoost</div>
                <p class="muted">Student seminar management built with Laravel.</p>
            </div>

            @auth
                <nav class="nav">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('topics.index') }}" class="{{ request()->routeIs('topics.*') ? 'active' : '' }}">Seminar Topics</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
                    @endif
                </nav>

                <div class="card compact">
                    <div class="label">Signed in as</div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <span class="badge role">{{ auth()->user()->role }}</span>
                    <div class="muted small">{{ auth()->user()->email }}</div>
                </div>
            @endauth
        </aside>

        <main class="main">
            <header class="topbar">
                <div>
                    <h1>{{ $heading ?? 'Seminar Manager' }}</h1>
                    <p class="muted">{{ $subheading ?? 'Admin dashboard for the Laravel Boost seminar demo.' }}</p>
                </div>

                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button secondary">Log out</button>
                    </form>
                @endauth
            </header>

            @if (session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert danger">
                    <strong>Please review the following issues:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
