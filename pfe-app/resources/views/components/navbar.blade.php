<nav class="sticky top-0 z-50 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="font-display text-white text-xl font-bold tracking-tight">
            Acti<span style="color:#D4A350">vio</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('activities') }}" class="nav-link">Browse Activities</a>
            <a href="{{ route('for-centers') }}" class="nav-link">For Centers</a>
            <a href="#" class="nav-link">How it works</a>
        </div>
        <div class="flex items-center gap-3">
            @guest
                <a href="{{ route('login') }}"
                    class="nav-link px-4 py-2 rounded-full border border-white/20 hover:border-white/50 transition-colors text-sm">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                    class="search-btn px-4 py-2 text-sm font-medium rounded-full inline-block">
                    Sign up
                </a>
            @endguest

            @auth
                <span class="text-white/60 text-sm">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="search-btn px-4 py-2 text-sm font-medium rounded-full">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>
