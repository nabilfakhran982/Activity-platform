<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activio Admin — {{ $title ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    @stack('styles')
</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="admin-sidebar" id="sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <a href="{{ route('admin.dashboard') }}">
                Acti<span style="color:#D4A350">vio</span>
                <span class="sidebar-logo-badge">Admin</span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <p class="sidebar-nav-label">Main</p>

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-icons">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('admin.users') }}"
                class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <span class="material-icons">people</span>
                Users
            </a>

            <a href="{{ route('admin.centers') }}"
                class="sidebar-link {{ request()->routeIs('admin.centers') ? 'active' : '' }}">
                <span class="material-icons">business</span>
                Centers
            </a>

            <a href="{{ route('admin.activities') }}"
                class="sidebar-link {{ request()->routeIs('admin.activities') ? 'active' : '' }}">
                <span class="material-icons">fitness_center</span>
                Activities
            </a>

            <a href="{{ route('admin.bookings') }}"
                class="sidebar-link {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                <span class="material-icons">event</span>
                Bookings
            </a>

            <a href="{{ route('admin.reviews') }}"
                class="sidebar-link {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                <span class="material-icons">star</span>
                Reviews
            </a>

            <p class="sidebar-nav-label mt-6">Account</p>

            <a href="{{ route('admin.profile') }}"
                class="sidebar-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <span class="material-icons">manage_accounts</span>
                My Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left" style="color:#e05252">
                    <span class="material-icons">logout</span>
                    Logout
                </button>
            </form>
        </nav>

        {{-- Admin info --}}
        <div class="sidebar-footer">
            <div class="sidebar-admin-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-admin-info">
                <p class="font-medium text-sm text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.4)">Administrator</p>
            </div>
        </div>
    </aside>

    {{-- MOBILE OVERLAY --}}
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    {{-- MAIN --}}
    <div class="admin-main">

        {{-- Top bar --}}
        <header class="admin-topbar">
            <button class="topbar-menu-btn" onclick="toggleSidebar()">
                <span class="material-icons">menu</span>
            </button>
            <h1 class="topbar-title font-display">{{ $title ?? 'Dashboard' }}</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="topbar-site-link">
                    <span class="material-icons" style="font-size:16px">open_in_new</span>
                    View Site
                </a>
            </div>
        </header>

        {{-- Content --}}
        <main class="admin-content">
            {{ $slot }}
        </main>
    </div>

    <script src="{{ asset('js/helpers.js') }}"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    </script>
    @stack('scripts')
</body>

</html>
