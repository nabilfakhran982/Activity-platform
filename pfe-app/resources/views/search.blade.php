<x-layouts.app-main title="Search Results">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
    @endpush

    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <p class="text-xs uppercase tracking-widest mb-3" style="color:rgba(212,163,80,0.8)">AI Search</p>

            {{-- Search bar --}}
            <div class="search-bar flex items-center gap-3 px-5 py-3 max-w-2xl" id="search-form">
                <svg class="text-white/40 flex-shrink-0" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="search-input" placeholder='Try "karate for kids near Achrafieh"'
                    class="flex-1 bg-transparent text-white placeholder-white/30 text-sm outline-none py-1"
                    value="{{ request('q') }}">
                <button onclick="doSearch()" class="search-btn px-5 py-2.5 text-sm font-medium whitespace-nowrap">
                    Search
                </button>
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

        {{-- No results --}}
        <div id="no-results" class="hidden text-center py-20">
            <div class="text-5xl mb-4">🔍</div>
            <p class="text-lg font-medium text-[#1a1a18] mb-2">No activities found</p>
            <p class="text-sm" style="color:#a09890">Try a different search</p>
        </div>

        {{-- Results grid --}}
        <div id="results-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5"></div>

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
            async function doSearch() {
                const query = document.getElementById('search-input').value.trim();
                if (!query) return;

                // Update URL
                history.pushState({}, '', `/search?q=${encodeURIComponent(query)}`);

                // Show loading
                document.getElementById('loading').classList.remove('hidden');
                document.getElementById('results-grid').innerHTML = '';
                document.getElementById('no-results').classList.add('hidden');
                document.getElementById('ai-summary').classList.add('hidden');

                try {
                    const res = await fetch('/search', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ query }),
                    });

                    const data = await res.json();
                    document.getElementById('loading').classList.add('hidden');

                    // Show AI summary
                    if (data.ai_summary) {
                        document.getElementById('ai-summary-text').textContent = data.ai_summary;
                        document.getElementById('ai-summary').classList.remove('hidden');
                    }

                    if (!data.count || data.count === 0) {
                        document.getElementById('no-results').classList.remove('hidden');
                        return;
                    }

                    document.getElementById('results-grid').innerHTML = data.html;

                } catch (err) {
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('no-results').classList.remove('hidden');
                }
            }

            // Enter key
            document.getElementById('search-input').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') doSearch();
            });

            // Auto search if query in URL
            const urlQuery = new URLSearchParams(window.location.search).get('q');
            if (urlQuery) {
                document.getElementById('search-input').value = urlQuery;
                doSearch();
            }
        </script>
    @endpush

</x-layouts.app-main>
