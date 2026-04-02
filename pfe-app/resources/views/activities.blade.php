<x-layouts.app-main title="Browse Activities">

    {{-- CSS --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
    @endpush

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <p class="text-xs uppercase tracking-widest mb-2" style="color:rgba(212,163,80,0.8)">Explore</p>
            <h1 class="font-display text-white text-4xl md:text-5xl font-bold mb-2">Browse Activities</h1>
            <p class="text-sm" style="color:rgba(255,255,255,0.45)">{{ $activities->count() }} activities available in
                Lebanon</p>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex gap-8 items-start">

            {{-- FILTERS SIDEBAR --}}
            <aside class="w-64 flex-shrink-0 sticky top-20">
                <form method="GET" action="{{ route('activities') }}">
                    <div class="filter-card space-y-6">

                        <div>
                            <span class="filter-label">Search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="e.g. karate, pilates..." class="filter-input">
                        </div>

                        <div>
                            <span class="filter-label">Category</span>
                            <input type="hidden" name="category" id="category-input" value="{{ request('category') }}">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="setCategory('')"
                                    class="category-pill {{ !request('category') ? 'active' : '' }}" id="cat-all">
                                    All
                                </button>
                                @foreach($categories as $cat)
                                    <button type="button" onclick="setCategory('{{ $cat->slug }}')"
                                        class="category-pill {{ request('category') == $cat->slug ? 'active' : '' }}"
                                        id="cat-{{ $cat->slug }}">
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <span class="filter-label">Max price ($/session)</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="e.g. 50" min="0" class="filter-input">
                        </div>

                        <div>
                            <span class="filter-label">Age</span>
                            <input type="number" name="age" value="{{ request('age') }}" placeholder="e.g. 10" min="1"
                                class="filter-input">
                        </div>

                        <div>
                            <span class="filter-label">City</span>
                            <select name="city" class="filter-input">
                                <option value="">All cities</option>
                                <option value="Beirut" {{ request('city') == 'Beirut' ? 'selected' : '' }}>Beirut</option>
                                <option value="Jounieh" {{ request('city') == 'Jounieh' ? 'selected' : '' }}>Jounieh
                                </option>
                                <option value="Tripoli" {{ request('city') == 'Tripoli' ? 'selected' : '' }}>Tripoli
                                </option>
                                <option value="Sidon" {{ request('city') == 'Sidon' ? 'selected' : '' }}>Sidon</option>
                            </select>
                        </div>

                        <button type="submit" class="search-btn w-full py-2.5">Apply Filters</button>

                        @if(request()->anyFilled(['search', 'category', 'max_price', 'age', 'city']))
                            <a href="{{ route('activities') }}" class="block text-center text-xs" style="color:#a09890">
                                Clear all filters
                            </a>
                        @endif

                    </div>
                </form>
            </aside>

            {{-- ACTIVITIES GRID --}}
            <div class="flex-1">
                @if($activities->isEmpty())
                    <div class="no-results">
                        <div class="text-5xl mb-4">🔍</div>
                        <p class="text-lg font-medium text-[#1a1a18] mb-2">No activities found</p>
                        <p class="text-sm">Try adjusting your filters</p>
                    </div>
                @else
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($activities as $act)
                            <x-activity-card :act="$act" />
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }

            window.location.href = '{{ route('activities') }}' + (params.toString() ? '?' + params.toString() : '');
        });

        function setCategory(slug) {
            document.getElementById('category-input').value = slug;
            document.forms[0].dispatchEvent(new Event('submit'));
        }
    </script>
</x-layouts.app-main>
