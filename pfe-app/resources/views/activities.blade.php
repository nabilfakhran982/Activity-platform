<x-layouts.app-main title="Browse Activities">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
    @endpush

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <p class="text-xs uppercase tracking-widest mb-2" style="color:rgba(212,163,80,0.8)">Explore</p>
            <h1 class="font-display text-white text-4xl md:text-5xl font-bold mb-2">Browse Activities</h1>
            <p class="text-sm" style="color:rgba(255,255,255,0.45)">{{ $activities->count() }} activities available in Lebanon</p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- TOP FILTER BAR --}}
        <form id="filters-form" method="GET" action="{{ route('activities') }}">
            <input type="hidden" name="category" id="category-input" value="{{ request('category') }}">

            {{-- Search bar --}}
            <div class="filter-search-bar mb-6">
                <svg width="18" height="18" fill="none" stroke="#a09890" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder='Search activities, e.g. "karate", "pilates"...'
                    class="filter-search-input">
                <button type="submit" class="search-btn px-6 py-2 text-sm">Search</button>
            </div>

            {{-- Filter chips row --}}
            <div class="filter-chips-row mb-6">

                {{-- Category filter --}}
                <div class="filter-chip-group">
                    <div class="filter-chip-dropdown" id="cat-dropdown">
                        <button type="button" class="filter-chip-btn {{ request('category') ? 'active' : '' }}"
                            onclick="toggleDropdown('cat-dropdown')">
                            <span class="material-icons" style="font-size:16px">category</span>
                            {{ request('category') ? $categories->firstWhere('slug', request('category'))?->name ?? 'Category' : 'Category' }}
                            <span class="material-icons" style="font-size:14px">expand_more</span>
                        </button>
                        <div class="dropdown-menu" id="cat-dropdown-menu">
                            <button type="button" onclick="setCategory('')"
                                class="dropdown-item {{ !request('category') ? 'selected' : '' }}">
                                All Categories
                            </button>
                            @foreach($categories as $cat)
                            <button type="button" onclick="setCategory('{{ $cat->slug }}')"
                                class="dropdown-item {{ request('category') == $cat->slug ? 'selected' : '' }}">
                                <img src="{{ asset('images/categories/' . $cat->icon) }}" alt=""
                                    style="width:18px;height:18px;object-fit:contain;margin-right:8px">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Price filter --}}
                <div class="filter-chip-group">
                    <div class="filter-chip-dropdown" id="price-dropdown">
                        <button type="button" class="filter-chip-btn {{ request('max_price') ? 'active' : '' }}"
                            onclick="toggleDropdown('price-dropdown')">
                            <span class="material-icons" style="font-size:16px">attach_money</span>
                            {{ request('max_price') ? 'Max $'.request('max_price') : 'Max Price' }}
                            <span class="material-icons" style="font-size:14px">expand_more</span>
                        </button>
                        <div class="dropdown-menu" id="price-dropdown-menu">
                            <p class="dropdown-section-label">Max price per session</p>
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="e.g. 50" min="0" class="filter-input mb-3">
                            <button type="submit" class="search-btn w-full py-2 text-sm">Apply</button>
                        </div>
                    </div>
                </div>

                {{-- City filter --}}
                <div class="filter-chip-group">
                    <div class="filter-chip-dropdown" id="city-dropdown">
                        <button type="button" class="filter-chip-btn {{ request('city') || request('address') ? 'active' : '' }}"
                            onclick="toggleDropdown('city-dropdown')">
                            <span class="material-icons" style="font-size:16px">location_on</span>
                            {{ request('city') ?: (request('address') ?: 'Location') }}
                            <span class="material-icons" style="font-size:14px">expand_more</span>
                        </button>
                        <div class="dropdown-menu" id="city-dropdown-menu">
                            <p class="dropdown-section-label">City</p>
                            <input type="text" name="city" class="filter-input mb-3" placeholder="e.g. Beirut"
                                value="{{ request('city') }}" list="cities-list">
                            <datalist id="cities-list">
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" />
                                @endforeach
                            </datalist>
                            <p class="dropdown-section-label">Neighborhood</p>
                            <input type="text" name="address" class="filter-input mb-3"
                                placeholder="e.g. Hamra, Gemmayzeh..." value="{{ request('address') }}">
                            <button type="submit" class="search-btn w-full py-2 text-sm">Apply</button>
                        </div>
                    </div>
                </div>

                {{-- Age filter --}}
                <div class="filter-chip-group">
                    <div class="filter-chip-dropdown" id="age-dropdown">
                        <button type="button" class="filter-chip-btn {{ request('age') ? 'active' : '' }}"
                            onclick="toggleDropdown('age-dropdown')">
                            <span class="material-icons" style="font-size:16px">person</span>
                            {{ request('age') ? 'Age '.request('age') : 'Age' }}
                            <span class="material-icons" style="font-size:14px">expand_more</span>
                        </button>
                        <div class="dropdown-menu" id="age-dropdown-menu">
                            <p class="dropdown-section-label">Age of participant</p>
                            <input type="number" name="age" value="{{ request('age') }}"
                                placeholder="e.g. 10" min="1" class="filter-input mb-3">
                            <button type="submit" class="search-btn w-full py-2 text-sm">Apply</button>
                        </div>
                    </div>
                </div>

                {{-- Clear filters --}}
                @if(request()->anyFilled(['search', 'category', 'max_price', 'age', 'city', 'address']))
                    <a href="{{ route('activities') }}" class="filter-clear-btn">
                        <span class="material-icons" style="font-size:15px">close</span>
                        Clear all
                    </a>
                @endif
            </div>

            {{-- Category pills (horizontal scroll) --}}
            <div class="category-pills-row mb-8">
                <button type="button" onclick="setCategory('')"
                    class="category-pill-large {{ !request('category') ? 'active' : '' }}">
                    All
                </button>
                @foreach($categories as $cat)
                <button type="button" onclick="setCategory('{{ $cat->slug }}')"
                    class="category-pill-large {{ request('category') == $cat->slug ? 'active' : '' }}">
                    <img src="{{ asset('images/categories/' . $cat->icon) }}" alt="{{ $cat->name }}"
                        style="width:20px;height:20px;object-fit:contain">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>

        </form>

        {{-- Results count --}}
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm" style="color:#8a7a6a">
                <span class="font-medium" style="color:#1a1a18">{{ $activities->count() }}</span> activities found
                @if(request()->anyFilled(['search', 'category', 'max_price', 'age', 'city', 'address']))
                    <span style="color:#D4A350"> · Filtered</span>
                @endif
            </p>
        </div>

        {{-- ACTIVITIES GRID --}}
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

    @push('scripts')
    <script>
        document.getElementById('filters-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value.trim() !== '') params.append(key, value);
            }
            window.location.href = '{{ route('activities') }}' + (params.toString() ? '?' + params.toString() : '');
        });

        function setCategory(slug) {
            document.getElementById('category-input').value = slug;
            document.getElementById('filters-form').dispatchEvent(new Event('submit'));
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const menu = document.getElementById(id + '-menu');
            const isOpen = dropdown.classList.contains('open');

            // Close all dropdowns
            document.querySelectorAll('.filter-chip-dropdown').forEach(d => d.classList.remove('open'));

            if (!isOpen) {
                dropdown.classList.add('open');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.filter-chip-dropdown')) {
                document.querySelectorAll('.filter-chip-dropdown').forEach(d => d.classList.remove('open'));
            }
        });
    </script>
    @endpush

</x-layouts.app-main>
