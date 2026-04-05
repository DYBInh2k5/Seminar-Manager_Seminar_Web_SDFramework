<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Seminar Manager' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Serif:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @endif
</head>
<body class="{{ request()->routeIs('login') ? 'guest-shell' : 'app-shell' }}">
    <div class="shell">
        <aside class="sidebar">
            <div>
                <div class="brand-wrap">
                    <div class="brand-mark">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <div>
                        <div class="brand">Seminar Manager</div>
                        <p class="muted brand-subtitle">Academic Portal</p>
                    </div>
                </div>
            </div>

            @auth
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                        ['route' => 'topics.index', 'group' => 'topics.*', 'label' => 'Topics', 'icon' => 'list_alt'],
                        ['route' => 'ai-chat.index', 'group' => 'ai-chat.*', 'label' => 'AI Chat', 'icon' => 'smart_toy'],
                    ];
                @endphp
                <nav class="nav">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['group'] ?? $item['route']) ? 'active' : '' }}">
                            <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">group</span>
                            <span>Users</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-panel">
                    <div class="sidebar-panel-label">Signed in as</div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <span class="badge role">{{ auth()->user()->role }}</span>
                    <div class="muted small">{{ auth()->user()->email }}</div>
                </div>
            @endauth
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="topbar-search">
                    @auth
                        <div class="search-shell">
                            <span class="material-symbols-outlined">search</span>
                            <input type="text" value="" placeholder="Search seminar archives..." readonly>
                        </div>
                    @endauth
                </div>

                <div class="topbar-copy">
                    <h1>{{ $heading ?? 'Seminar Manager' }}</h1>
                    <p class="muted">{{ $subheading ?? 'Admin dashboard for the Laravel Boost seminar demo.' }}</p>
                </div>

                @auth
                    <div class="topbar-actions">
                        <button type="button" class="icon-button" aria-label="Notifications">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                        <button type="button" class="icon-button" aria-label="Help">
                            <span class="material-symbols-outlined">help_outline</span>
                        </button>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="button secondary">Log out</button>
                        </form>
                    </div>
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
