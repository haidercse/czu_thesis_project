<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Study Czechia Guide' }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>

<body>

    <div class="app-shell">
        <aside class="app-nav" id="appNav">
            <span class="brand-mark">Study Czechia Guide</span>
            <nav>
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('programs.index') }}"
                    class="{{ request()->routeIs('programs.index', 'programs.compare') ? 'active' : '' }}">Program search</a>
                <a href="{{ route('recommendations.index') }}"
                    class="{{ request()->routeIs('recommendations.index') ? 'active' : '' }}">Recommended programs</a>
                <a href="{{ route('saved-programs.index') }}"
                    class="{{ request()->routeIs('saved-programs.index') ? 'active' : '' }}">Saved programs</a>
                <a href="{{ route('profile.edit') }}"
                    class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Profile</a>
                <a href="{{ route('applications.index') }}"
                    class="{{ request()->routeIs('applications.index') ? 'active' : '' }}">My application</a>
                <a href="{{ route('documents.index') }}"
                    class="{{ request()->routeIs('documents.index') ? 'active' : '' }}">Documents</a>
                <a href="{{ route('faq.index') }}" class="{{ request()->routeIs('faq.index') ? 'active' : '' }}">FAQ
                    &amp; help</a>
            </nav>
            <div class="nav-user">
                Signed in as<br /><strong style="color:#D9DCE6">{{ auth()->user()->name }}</strong><br />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="plain"
                        style="background:none;border:none;color:#9BA2B5;cursor:pointer;padding:0;font:inherit;">Sign
                        out</button>
                </form>
            </div>
        </aside>

        <main class="app-main">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>

</html>
