<nav class="sticky top-0 z-50 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="font-display text-white text-xl font-bold tracking-tight">
            Acti<span style="color:#D4A350">vio</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('activities') }}" class="nav-link">Browse Activities</a>
            <a href="{{ route('for-centers') }}" class="nav-link">For Centers</a>
            <a href="/#how-it-works" class="nav-link">How it works</a>
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
                @if(auth()->user()->role === 'center_owner')
                    <a href="{{ route('center.dashboard') }}"
                        class="nav-link px-4 py-2 rounded-full border border-white/20 hover:border-white/50 transition-colors text-sm">
                        My Dashboard
                    </a>
                @endif

                @auth
                    {{-- 🔔 NOTIFICATION BELL --}}
                    <div class="notif-wrapper" id="notif-wrapper">
                        <button class="notif-btn" id="notif-btn" onclick="toggleNotifDropdown()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" style="width:22px;height:22px;color:rgba(255,255,255,0.6)">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span class="notif-badge" id="notif-badge" style="display:none">0</span>
                        </button>

                        <div class="notif-dropdown" id="notif-dropdown">
                            <div class="notif-dropdown-header">
                                <span
                                    style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:#1a1a18">Notifications</span>
                                <button onclick="markAllRead()" class="notif-read-all">Mark all read</button>
                            </div>
                            <div id="notif-list">
                                <div class="notif-empty">No notifications yet</div>
                            </div>
                        </div>
                    </div>
                @endauth

                <a href="{{ route('profile') }}" class="flex items-center gap-2 nav-link">
                    <div class="w-8 h-8 bg-[#D4A350] rounded-full flex items-center justify-center text-[#1a1a18]">
                        <!-- Profile Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <span class="text-white/60 text-sm hidden md:block">
                        {{ auth()->user()->name }}
                    </span>
                </a>

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
