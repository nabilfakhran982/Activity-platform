<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activio — Find & Book Local Activities</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/activio-home.css') }}">
</head>
<!-- ================= HTML ================= -->

<body>

    {{-- ============ NAVBAR ============ --}}
    <nav class="sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="/" class="font-display text-white text-xl font-bold tracking-tight">
                Acti<span style="color:#D4A350">vio</span>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="#" class="nav-link">Browse Activities</a>
                <a href="#" class="nav-link">For Centers</a>
                <a href="#" class="nav-link">How it works</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="nav-link px-4 py-2 rounded-full border border-white/20 hover:border-white/50 transition-colors text-sm">
                    Login
                </a>
                <a href="{{ route('register') }}"
                  class="search-btn px-4 py-2 text-sm font-medium rounded-full inline-block">
                    Sign up
                </a>
            </div>
        </div>
    </nav>

    {{-- ============ HERO ============ --}}
    <section class="hero-bg min-h-[88vh] flex flex-col justify-center px-6 py-24">
        <div class="max-w-3xl mx-auto text-center relative z-10">

            <div class="ai-badge inline-block mb-6">✦ AI-powered search</div>

            <h1 class="font-display text-white text-5xl md:text-7xl font-bold leading-tight mb-6" style="line-height:1.1">
                Discover &amp; book<br>
                <span style="color:#D4A350">local activities</span><br>
                you'll love
            </h1>
            <p class="text-white/55 text-lg md:text-xl mb-12 max-w-xl mx-auto leading-relaxed">
                Pilates, karate, boxing, art — find the perfect class for you or your child. Just describe what you're looking
                for.
            </p>

            {{-- Search bar --}}
            <div class="search-bar flex items-center gap-3 px-5 py-3 max-w-2xl mx-auto">
                <svg class="text-white/40 flex-shrink-0" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" placeholder='Try "karate for kids near Achrafieh on Saturday"'
                    class="flex-1 bg-transparent text-white placeholder-white/30 text-sm outline-none py-1">
                <button class="search-btn px-5 py-2.5 text-sm font-medium whitespace-nowrap">
                    Search
                </button>
            </div>

            {{-- Quick suggestions --}}
            <div class="flex flex-wrap justify-center gap-2 mt-5">
                @foreach(['Pilates', 'Boxing', 'Swimming', 'Football', 'Arts & Crafts'] as $tag)
                <button
                    class="text-white/45 text-xs border border-white/15 rounded-full px-4 py-1.5 hover:border-white/40 hover:text-white/70 transition-colors">
                    {{ $tag }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Floating stats --}}
        <div class="max-w-3xl mx-auto w-full mt-16 grid grid-cols-3 gap-4 relative z-10">
            @foreach([['300+', 'Activities'], ['50+', 'Centers'], ['4.9★', 'Avg rating']] as $stat)
            <div class="text-center border border-white/10 rounded-2xl py-4 px-2" style="background:rgba(255,255,255,0.04)">
                <div class="font-display text-white text-2xl font-bold">{{ $stat[0] }}</div>
                <div class="text-white/40 text-xs mt-1">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ============ CATEGORIES ============ --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-[#a09890] mb-2">Explore</p>
                <h2 class="font-display text-3xl md:text-4xl font-bold">Browse by category</h2>
            </div>
            <a href="#" class="text-sm text-[#8a7a6a] hover:text-[#1a1a18] transition-colors hidden md:block">View all →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
            @foreach($categories as $cat)
            <div class="category-card p-5 text-center">
                <div class="text-4xl mb-3">{{ $cat->icon }}</div>
                <div class="font-medium text-sm">{{ $cat->name }}</div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ============ FEATURED ACTIVITIES ============ --}}
    <section class="max-w-6xl mx-auto px-6 pb-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-[#a09890] mb-2">Trending now</p>
                <h2 class="font-display text-3xl md:text-4xl font-bold">Popular activities</h2>
            </div>
            <a href="#" class="text-sm text-[#8a7a6a] hover:text-[#1a1a18] transition-colors hidden md:block">See all →</a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $activities = [
            [
            'emoji' => '🥋',
            'bg' => '#2A1F3D',
            'title' => 'Kids Karate — Beginners',
            'center' => 'Dragon Academy',
            'location' => 'Achrafieh, Beirut',
            'age' => 'Ages 5–12',
            'price' => '$25/session',
            'rating' => '4.9',
            'reviews' => '128',
            'tags' => ['Kids-friendly', 'Beginner'],
            'ai_note' => 'Great for building confidence',
            ],
            [
            'emoji' => '🏃',
            'bg' => '#1A2F2A',
            'title' => 'Morning Pilates Flow',
            'center' => 'Zen Studio',
            'location' => 'Hamra, Beirut',
            'age' => 'All ages',
            'price' => '$30/session',
            'rating' => '4.8',
            'reviews' => '94',
            'tags' => ['Morning', 'Flexible schedule'],
            'ai_note' => 'Perfect after-work unwind',
            ],
            [
            'emoji' => '🥊',
            'bg' => '#2F1A1A',
            'title' => 'Boxing — Beginners',
            'center' => 'Fight Club Gym',
            'location' => 'Mar Mikhael, Beirut',
            'age' => 'Ages 14+',
            'price' => '$28/session',
            'rating' => '4.9',
            'reviews' => '83',
            'tags' => ['Fitness', 'Beginner'],
            'ai_note' => 'Best for stress relief',
            ],
            ];
            @endphp

            @foreach($activities as $act)
            <div class="activity-card group">
                {{-- Image / color block --}}
                @php $bg = $act['bg']; @endphp
                <div class="img-placeholder" style="background-color: <?= $bg ?>">
                    {{ $act['emoji'] }}
                </div>

                <div class="p-5">
                    {{-- AI insight --}}
                    <div class="ai-badge mb-3">✦ {{ $act['ai_note'] }}</div>

                    <h3 class="font-display text-lg font-bold leading-snug mb-1">{{ $act['title'] }}</h3>
                    <p class="text-[#8a7a6a] text-sm mb-3">{{ $act['center'] }} · {{ $act['location'] }}</p>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="pill">{{ $act['age'] }}</span>
                        @foreach($act['tags'] as $tag)
                        <span class="pill">{{ $tag }}</span>
                        @endforeach
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-4 border-t border-[#F0EDE6]">
                        <div>
                            <span class="stars text-sm">★</span>
                            <span class="text-sm font-medium ml-1">{{ $act['rating'] }}</span>
                            <span class="text-[#b0a898] text-xs ml-1">({{ $act['reviews'] }})</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-sm">{{ $act['price'] }}</span>
                            <button class="search-btn px-4 py-2 text-xs">Book now</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ============ HOW IT WORKS ============ --}}
    <section class="bg-white py-20">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-xs uppercase tracking-widest text-[#a09890] mb-2">Simple &amp; smart</p>
                <h2 class="font-display text-3xl md:text-4xl font-bold">How Activio works</h2>
            </div>

            <div class="space-y-10">
                @php
                $steps = [
                ['num' => '1', 'title' => 'Describe what you need', 'desc' => 'Type naturally — "karate for my 7-year-old near
                Achrafieh on Saturday". Our AI understands you.'],
                ['num' => '2', 'title' => 'Compare &amp; choose', 'desc' => 'See matched activities with AI-powered summaries,
                ratings, prices, and schedules — side by side.'],
                ['num' => '3', 'title' => 'Book in seconds', 'desc' => 'No calls, no WhatsApp. Select your slot and confirm your
                booking directly on the platform.'],
                ];
                @endphp

                @foreach($steps as $step)
                <div class="flex items-start gap-6">
                    <div class="step-number font-display">{{ $step['num'] }}</div>
                    <div class="pt-2">
                        <h3 class="font-semibold text-lg mb-1">{!! $step['title'] !!}</h3>
                        <p class="text-[#8a7a6a] leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ AI HIGHLIGHT ============ --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="cta-section px-8 py-14 md:px-16 md:py-20">
            <div class="relative z-10 max-w-xl">
                <div class="ai-badge inline-block mb-6">✦ Powered by AI</div>
                <h2 class="font-display text-white text-4xl md:text-5xl font-bold mb-6 leading-tight">
                    Search that actually <span style="color:#D4A350">understands</span> you
                </h2>
                <p class="text-white/55 mb-8 leading-relaxed">
                    Don't browse endless lists. Just say what you're looking for — age, location, day — and our AI finds the
                    perfect match and explains why it's right for you.
                </p>
                <div class="bg-white/8 border border-white/15 rounded-2xl p-5 mb-8" style="background:rgba(255,255,255,0.06)">
                    <p class="text-white/40 text-xs mb-2">Example search</p>
                    <p class="text-white text-sm">"something relaxing for me after work, not too intense"</p>
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <div class="ai-badge inline-block text-xs">✦ AI suggests: Pilates Flow, Swimming, Light Fitness</div>
                    </div>
                </div>
                <a href="#" class="search-btn inline-block px-8 py-3.5 text-sm font-medium rounded-full">
                    Try it now
                </a>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="bg-[#1a1a18] text-white/40 py-12 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8 mb-10">
                <div>
                    <div class="font-display text-white text-xl font-bold mb-3">
                        Acti<span style="color:#D4A350">vio</span>
                    </div>
                    <p class="text-sm max-w-xs leading-relaxed">
                        Lebanon's first AI-powered platform to discover, compare, and book local activities.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-x-16 gap-y-3 text-sm">
                    <a href="#" class="hover:text-white/70 transition-colors">Browse Activities</a>
                    <a href="#" class="hover:text-white/70 transition-colors">For Centers</a>
                    <a href="#" class="hover:text-white/70 transition-colors">How it works</a>
                    <a href="#" class="hover:text-white/70 transition-colors">About</a>
                    <a href="#" class="hover:text-white/70 transition-colors">Contact</a>
                    <a href="#" class="hover:text-white/70 transition-colors">Privacy</a>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 text-xs">
                © {{ date('Y') }} Activio. Built in Lebanon 🇱🇧
            </div>
        </div>
    </footer>

</body>
<!-- ================= JS ================= -->
<!-- JS is not required for this home layout (no interactivity yet). -->

</html>