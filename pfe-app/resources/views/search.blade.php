<x-layouts.app-main title="Search Results">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
        <style>
            .near-me-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
                font-size: 12px;
                color: rgba(255, 255, 255, 0.5);
            }

            .near-me-toggle {
                display: flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                padding: 5px 12px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.2s;
                background: transparent;
                color: rgba(255, 255, 255, 0.6);
                font-size: 12px;
                font-family: 'DM Sans', sans-serif;
            }

            .near-me-toggle.active {
                border-color: #D4A350;
                color: #D4A350;
                background: rgba(212, 163, 80, 0.1);
            }

            .near-me-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: currentColor;
                flex-shrink: 0;
            }

            .suggestion-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 16px;
            }

            .suggestion-chip {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.15);
                background: rgba(255, 255, 255, 0.05);
                color: rgba(255, 255, 255, 0.6);
                font-size: 12px;
                cursor: pointer;
                transition: all 0.2s;
                font-family: 'DM Sans', sans-serif;
            }

            .suggestion-chip:hover {
                border-color: #D4A350;
                color: #D4A350;
                background: rgba(212, 163, 80, 0.08);
            }

            .fallback-banner {
                background: rgba(212, 163, 80, 0.1);
                border: 1px solid rgba(212, 163, 80, 0.3);
                border-radius: 12px;
                padding: 12px 16px;
                margin-bottom: 20px;
                font-size: 13px;
                color: #8a6020;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .suggestions-section {
                margin-top: 32px;
            }

            .suggestions-label {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #a09890;
                margin-bottom: 16px;
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <p class="text-xs uppercase tracking-widest mb-3" style="color:rgba(212,163,80,0.8)">✦ AI Search</p>

            {{-- Search bar --}}
            <div class="search-bar flex items-center gap-3 px-5 py-3 max-w-2xl" id="search-form">
                <svg class="text-white/40 flex-shrink-0" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="search-input" placeholder='Try "karate for kids near Achrafieh on Saturday"'
                    class="flex-1 bg-transparent text-white placeholder-white/30 text-sm outline-none py-1"
                    value="{{ request('q') }}">
                <button type="button" onclick="doSearch()"
                    class="search-btn px-5 py-2.5 text-sm font-medium whitespace-nowrap">
                    Search
                </button>
            </div>

            {{-- Near me toggle --}}
            <div class="near-me-bar">
                <button class="near-me-toggle active" id="near-me-btn" onclick="toggleNearMe()">
                    <span class="near-me-dot"></span>
                    <span id="near-me-label">Near me — on</span>
                </button>
                <span id="near-me-status" style="font-size:11px;color:rgba(255,255,255,0.3)">Getting your
                    location...</span>
            </div>

            {{-- Suggestion chips --}}
            <div class="suggestion-chips" id="suggestion-chips">
                @foreach($suggestions as $cat)
                    <button class="suggestion-chip" onclick="searchCategory('{{ $cat->name }}', '{{ $cat->slug }}')">
                        <img src="{{ asset('images/categories/' . $cat->icon) }}" alt=""
                            style="width:14px;height:14px;object-fit:contain">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            {{-- AI summary --}}
            <div id="ai-summary" class="mt-4 text-sm hidden" style="color:rgba(212,163,80,0.9)">
                ✦ <span id="ai-summary-text"></span>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- Loading --}}
        <div id="loading" class="hidden text-center py-20">
            <div
                style="width:40px;height:40px;border:3px solid #E8E5DF;border-top-color:#D4A350;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 16px">
            </div>
            <p class="text-sm" style="color:#a09890">AI is searching for you...</p>
        </div>

        {{-- Fallback banner --}}
        <div id="fallback-banner" class="fallback-banner hidden">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4m0 4h.01" />
            </svg>
            <span id="fallback-text"></span>
        </div>

        {{-- Recommendations Section (shown when no search) --}}
        @auth
            @if($recommendations->isNotEmpty())
            <div id="recommendations-section">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-2" style="color:#D4A350">
                            ✦ {{ $recommendationLabel }}
                        </p>
                        <h2 class="font-display text-3xl md:text-4xl font-bold">{{ $recommendationTitle }}</h2>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommendations as $act)
                        <x-activity-card :act="$act" />
                    @endforeach
                </div>
            </div>
            @endif
        @endauth

        {{-- Results grid --}}
        <div id="results-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5"></div>

        {{-- No results + suggestions --}}
        <div id="no-results" class="hidden">
            <div class="text-center py-16">
                <div
                    style="width:64px;height:64px;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                    <svg width="28" height="28" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <p class="font-display text-xl font-bold text-[#1a1a18] mb-2" id="no-results-title">No activities near
                    you</p>
                <p class="text-sm" style="color:#a09890" id="no-results-subtitle">Showing activities from other
                    locations</p>
            </div>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
                <div style="flex:1;height:1px;background:#E8E5DF"></div>
                <span
                    style="font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:#a09890;font-weight:600">You
                    might also like</span>
                <div style="flex:1;height:1px;background:#E8E5DF"></div>
            </div>

            <div id="suggestions-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5"></div>
        </div>

    </div>

    @push('scripts')
        <style>
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
        <script>
            let userLat = null;
            let userLng = null;
            let nearMeOn = true;
            let locationReady = false;

            // ===== Near Me — get location on page load =====
            function initLocation() {
                if (!navigator.geolocation) {
                    setNearMeOff('Location not supported');
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        userLat = pos.coords.latitude;
                        userLng = pos.coords.longitude;
                        locationReady = true;
                        document.getElementById('near-me-status').textContent = '📍 Location ready';
                        setTimeout(() => {
                            document.getElementById('near-me-status').textContent = '';
                        }, 2000);
                    },
                    () => {
                        setNearMeOff('Location access denied');
                    },
                    { timeout: 5000 }
                );
            }

            function setNearMeOff(msg) {
                nearMeOn = false;
                userLat = null;
                userLng = null;
                const btn = document.getElementById('near-me-btn');
                btn.classList.remove('active');
                document.getElementById('near-me-label').textContent = 'Near me — off';
                document.getElementById('near-me-status').textContent = msg || '';
            }

            function toggleNearMe() {
                if (!nearMeOn) {
                    nearMeOn = true;
                    const btn = document.getElementById('near-me-btn');
                    btn.classList.add('active');
                    document.getElementById('near-me-label').textContent = 'Near me — on';
                    document.getElementById('near-me-status').textContent = 'Getting location...';
                    navigator.geolocation?.getCurrentPosition(
                        (pos) => {
                            userLat = pos.coords.latitude;
                            userLng = pos.coords.longitude;
                            locationReady = true;
                            document.getElementById('near-me-status').textContent = '📍 Location ready';
                            setTimeout(() => document.getElementById('near-me-status').textContent = '', 2000);
                            // ===== Re-search with location =====
                            const query = document.getElementById('search-input').value.trim();
                            if (query) executeSearch(query, userLat, userLng);
                        },
                        () => setNearMeOff('Location access denied')
                    );
                } else {
                    setNearMeOff('');
                    document.getElementById('near-me-label').textContent = 'Near me — off';
                    // ===== Re-search without location =====
                    const query = document.getElementById('search-input').value.trim();
                    if (query) executeSearch(query, null, null);
                }
            }

            // ===== Search =====
            async function doSearch() {
                const query = document.getElementById('search-input').value.trim();
                if (!query) return;

                if (nearMeOn && !locationReady) {
                    // انتظر الـ location قبل ما تبحث
                    document.getElementById('near-me-status').textContent = 'Getting location...';
                    navigator.geolocation?.getCurrentPosition(
                        (pos) => {
                            userLat = pos.coords.latitude;
                            userLng = pos.coords.longitude;
                            locationReady = true;
                            document.getElementById('near-me-status').textContent = '';
                            executeSearch(query, userLat, userLng);
                        },
                        () => {
                            // فشل الـ location — ابحث بدونه
                            setNearMeOff('Location access denied');
                            executeSearch(query, null, null);
                        },
                        { timeout: 5000 }
                    );
                    return;
                }

                const lat = nearMeOn ? userLat : null;
                const lng = nearMeOn ? userLng : null;
                await executeSearch(query, lat, lng);
            }


            function searchCategory(name, slug) {
                document.getElementById('search-input').value = name;
                doSearch();
            }

            async function executeSearch(query, lat, lng) {
                history.pushState({}, '', `/search?q=${encodeURIComponent(query)}`);

                document.getElementById('loading').classList.remove('hidden');
                document.getElementById('results-grid').innerHTML = '';
                document.getElementById('no-results').classList.add('hidden');
                document.getElementById('ai-summary').classList.add('hidden');
                document.getElementById('fallback-banner').classList.add('hidden');
                const recSection = document.getElementById('recommendations-section');
                if (recSection) recSection.classList.add('hidden');

                try {
                    const res = await fetch('/search', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ query, lat, lng }),
                    });

                    const data = await res.json();
                    document.getElementById('loading').classList.add('hidden');

                    console.log('fallback_message:', data.fallback_message);
                    console.log('is_day_fallback:', data.is_day_fallback);
                    // AI Summary
                    if (data.ai_summary) {
                        document.getElementById('ai-summary-text').textContent = data.ai_summary;
                        document.getElementById('ai-summary').classList.remove('hidden');
                    }

                    // 1. Category مش موجودة
                    if (data.category_not_found) {
                        document.getElementById('no-results').classList.remove('hidden');
                        document.getElementById('no-results-title').textContent = 'This activity is not available on Activio yet';
                        document.getElementById('no-results-subtitle').textContent = "We don't offer this activity in Lebanon yet — here are our most popular activities";
                        if (data.suggestions_html) {
                            document.getElementById('suggestions-grid').innerHTML = data.suggestions_html;
                        }
                        return;
                    }

                    // 2. Near me — ما في activities قريبة
                    if (data.is_near_empty) {
                        document.getElementById('no-results').classList.remove('hidden');
                        document.getElementById('no-results-title').textContent = 'No activities near you';
                        document.getElementById('no-results-subtitle').textContent =
                            data.nearest_city ? `Showing activities in ${data.nearest_city} instead` : 'Showing activities from other locations in Lebanon';
                        if (data.suggestions_html) {
                            document.getElementById('suggestions-grid').innerHTML = data.suggestions_html;
                        }
                        return;
                    }

                    // 3. Day exists but not at requested time
                    if (data.is_time_on_day_fallback) {
                        document.getElementById('fallback-text').textContent = data.fallback_message ||
                            `No sessions at your requested time — showing closest available times on ${data.suggested_day || 'that day'}`;
                        document.getElementById('fallback-banner').classList.remove('hidden');
                    }

                    // 4. Day proximity — showing results from closest available day
                    if (data.is_day_fallback) {
                        document.getElementById('fallback-text').textContent = data.fallback_message ||
                            `No activities on your requested day — showing closest sessions on ${data.suggested_day}`;
                        document.getElementById('fallback-banner').classList.remove('hidden');
                    }

                    // 5. Time proximity — showing nearest available sessions
                    if (data.is_time_fallback) {
                        document.getElementById('fallback-text').textContent = data.fallback_message ||
                            `No activities at your requested time — showing nearest available sessions at ${data.suggested_time}`;
                        document.getElementById('fallback-banner').classList.remove('hidden');
                    }

                    // 6. City fallback banner
                    if (data.is_fallback && data.fallback_city) {
                        document.getElementById('fallback-text').textContent =
                            `We couldn't find activities in "${data.fallback_city}" — showing similar activities from other locations in Lebanon.`;
                        document.getElementById('fallback-banner').classList.remove('hidden');
                    }

                    // 7. ما في نتائج عامة
                    if (!data.count || data.count === 0) {
                        document.getElementById('no-results').classList.remove('hidden');
                        document.getElementById('no-results-title').textContent = 'No activities found for your search';
                        document.getElementById('no-results-subtitle').textContent = 'Here are some activities you might like instead';
                        if (data.suggestions_html) {
                            document.getElementById('suggestions-grid').innerHTML = data.suggestions_html;
                        }
                        return;
                    }

                    // 8. عرض النتائج
                    document.getElementById('results-grid').innerHTML = data.html;

                } catch (err) {
                    console.error(err);
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('no-results').classList.remove('hidden');
                }
            }

            // Enter key
            document.getElementById('search-input').addEventListener('keypress', e => {
                if (e.key === 'Enter') doSearch();
            });

            // Init location on load
            initLocation();

            // Auto search if query in URL
            const urlQuery = new URLSearchParams(window.location.search).get('q');
            if (urlQuery) {
                document.getElementById('search-input').value = urlQuery;
                doSearch();
            }
        </script>
    @endpush

</x-layouts.app-main>
