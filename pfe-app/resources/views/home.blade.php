<x-layouts.app-main title="Home">

    {{-- CSS --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endpush

    <section class="hero-section min-h-[88vh] flex flex-col justify-center px-6 py-24 relative overflow-hidden">

        {{-- VIDEO BACKGROUND --}}
        <div class="hero-video-wrapper">
            <video
                autoplay
                muted
                loop
                playsinline
                class="hero-video"
                poster="{{ asset('images/hero-poster.jpg') }}"
            >
                <source src="{{ asset('videos/activio_hero.mp4') }}" type="video/mp4">
            </video>
            {{-- Dark overlay --}}
            <div class="hero-overlay"></div>
        </div>

        {{-- CONTENT --}}
        <div class="max-w-3xl mx-auto text-center relative z-10">

            <div class="ai-badge inline-block mb-6">✦ AI-powered search</div>

            <h1 class="font-display text-white text-5xl md:text-7xl font-bold leading-tight mb-6"
                style="line-height:1.1">
                Discover &amp; book<br>
                <span style="color:#D4A350">local activities</span><br>
                you'll love
            </h1>
            <p class="text-white/55 text-lg md:text-xl mb-12 max-w-xl mx-auto leading-relaxed">
                Pilates, karate, boxing, art — find the perfect class for you or your child. Just describe what you're looking for.
            </p>

            {{-- Search bar --}}
            <div class="search-bar flex items-center gap-3 px-5 py-3 max-w-2xl mx-auto">
                <svg class="text-white/40 flex-shrink-0" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="home-search"
                    placeholder='Try "karate for kids near Achrafieh on Saturday"'
                    class="flex-1 bg-transparent text-white placeholder-white/30 text-sm outline-none py-1">
                <button onclick="window.location.href='/search?q='+encodeURIComponent(document.getElementById('home-search').value)"
                    class="search-btn px-5 py-2.5 text-sm font-medium whitespace-nowrap">
                    Search
                </button>
            </div>

            {{-- Quick suggestions --}}
            <div class="flex flex-wrap justify-center gap-2 mt-5">
                @foreach($categories->take(5) as $cat)
                    <button onclick="selectSuggestion(this, '{{ $cat->name }}')"
                        class="suggestion-btn text-white/45 text-xs border border-white/15 rounded-full px-4 py-1.5 hover:border-white/40 hover:text-white/70 transition-colors">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Floating stats --}}
        <div class="max-w-3xl mx-auto w-full mt-16 grid grid-cols-3 gap-4 relative z-10">
            @php
                $avgRating = \App\Models\Review::avg('rating');
                $formattedRating = $avgRating ? number_format($avgRating, 1) : 'N/A';
            @endphp
            @foreach([
                [$activitiesCount . '+', 'Activities'],
                [$centersCount . '+', 'Centers'],
                [$formattedRating, 'Avg Rating'],
            ] as $stat)
                <div class="text-center border border-white/10 rounded-2xl py-4 px-2"
                    style="background:rgba(255,255,255,0.04)">
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
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 category-list">
            @foreach($categories as $cat)
                <div class="category-card p-5 text-center" data-slug="{{ $cat->slug }}">
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
            <a href="{{ route('activities') }}" class="text-sm text-[#8a7a6a] hover:text-[#1a1a18] transition-colors hidden md:block">See all →</a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activities as $act)
                <x-activity-card :act="$act" />
            @endforeach
        </div>
    </section>

    {{-- ============ HOW IT WORKS ============ --}}
    <section id="how-it-works" class="bg-white py-20">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-xs uppercase tracking-widest text-[#a09890] mb-2">Simple &amp; smart</p>
                <h2 class="font-display text-3xl md:text-4xl font-bold">How Activio works</h2>
            </div>

            <div class="space-y-10">
                @php
                    $steps = [
                        ['num' => '1', 'title' => 'Describe what you need', 'desc' => 'Type naturally — "karate for my 7-year-old near Achrafieh on Saturday". Our AI understands you.'],
                        ['num' => '2', 'title' => 'Compare &amp; choose', 'desc' => 'See matched activities with AI-powered summaries, ratings, prices, and schedules — side by side.'],
                        ['num' => '3', 'title' => 'Book in seconds', 'desc' => 'No calls, no WhatsApp. Select your slot and confirm your booking directly on the platform.'],
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
                    Don't browse endless lists. Just say what you're looking for — age, location, day — and our AI finds the perfect match and explains why it's right for you.
                </p>
                <div class="bg-white/8 border border-white/15 rounded-2xl p-5 mb-8"
                    style="background:rgba(255,255,255,0.06)">
                    <p class="text-white/40 text-xs mb-2">Example search</p>
                    <p class="text-white text-sm">"something relaxing for me after work, not too intense"</p>
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <div class="ai-badge inline-block text-xs">✦ AI suggests: Pilates Flow, Swimming, Light Fitness</div>
                    </div>
                </div>
                <a href="{{ route('search') }}" class="search-btn inline-block px-8 py-3.5 text-sm font-medium rounded-full">
                    Try it now
                </a>
            </div>
        </div>
    </section>

    <script>
        const categoryList = document.querySelector(".category-list");
        categoryList.addEventListener("click", (e) => {
            const categoryCard = e.target.closest(".category-card");
            if (categoryCard) {
                const slug = categoryCard.getAttribute("data-slug");
                window.location.href = `/activities?category=${slug}`;
            }
        });

        document.getElementById('home-search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = '/search?q=' + encodeURIComponent(this.value);
            }
        });

        function selectSuggestion(btn, name) {
            document.querySelectorAll('.suggestion-btn').forEach(b => {
                b.classList.remove('active-suggestion');
                b.style.borderColor = '';
                b.style.color = '';
            });
            btn.style.borderColor = '#D4A350';
            btn.style.color = '#D4A350';
            btn.classList.add('active-suggestion');
            const input = document.getElementById('home-search');
            input.value = name;
            input.focus();
        }
    </script>
</x-layouts.app-main>
