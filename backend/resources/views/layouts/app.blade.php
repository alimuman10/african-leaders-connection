<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'African Leaders Connection')</title>
    <meta name="description" content="@yield('description', 'African Leaders Connection is a professional Pan-African leadership and innovation platform.')">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @endif
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>

    <header class="site-header">
        <div class="shell header-shell">
            <a class="brand" href="{{ route('home') }}" aria-label="African Leaders Connection home">
                <span class="brand-mark">
                    <img src="{{ asset('assets/images/brand-icon-square.png') }}" alt="">
                </span>
                <span>
                    <strong>African Leaders Connection</strong>
                    <small>Leadership. Unity. Progress.</small>
                </span>
            </a>

            <nav class="site-nav" aria-label="Primary navigation">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('mission') }}">Mission</a>
                <a href="{{ route('leadership') }}">Leadership</a>
                <a href="{{ route('advocacy') }}">Advocacy</a>
                <a href="{{ route('stories') }}">Stories</a>
                <a href="{{ route('projects') }}">Projects</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('community') }}">Community</a>
                <a href="{{ route('contact') }}">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="link-button" type="submit">Logout</button>
                    </form>
                @else
                    <a class="button button-secondary" href="{{ route('login') }}">Sign In</a>
                    <a class="button button-primary" href="{{ route('register') }}">Sign Up</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="main-content">
        @if (session('status'))
            <div class="shell">
                <div class="alert alert-success">{{ session('status') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="shell footer-shell">
            <div>
                <strong>African Leaders Connection</strong>
                <p>Building leadership visibility, institutional capacity, and community-centered progress.</p>
            </div>
            <p>&copy; {{ date('Y') }} African Leaders Connection. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
