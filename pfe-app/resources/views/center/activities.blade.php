<x-layouts.app-main title="Manage Activities">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/center/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/center/activities.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <a href="{{ route('center.dashboard') }}"
                        class="text-xs uppercase tracking-widest mb-2 block hover:opacity-70 transition-opacity"
                        style="color:rgba(212,163,80,0.8)">
                        ← Back to Dashboard
                    </a>
                    <h1 class="font-display text-white text-3xl font-bold">{{ $center->name }}</h1>
                    <p class="text-white/40 text-sm mt-1">{{ $activities->count() }} activities</p>
                </div>
                <button onclick="openModal('add-activity-modal')" class="search-btn px-5 py-2.5 text-sm">
                    + Add Activity
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- ACTIVITIES GRID --}}
        <div id="activities-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @if($activities->isEmpty())
                <div class="no-results col-span-3">
                    <div class="text-5xl mb-4">🏃</div>
                    <p class="text-lg font-medium mb-2">No activities yet</p>
                    <p class="text-sm">Click "+ Add Activity" to get started</p>
                </div>
            @else
                @foreach($activities as $act)
                    <x-activity.activity-card :act="$act" />
                @endforeach
            @endif
        </div>
    </div>

    {{-- ADD ACTIVITY MODAL --}}
    <x-modal id="add-activity-modal" title="Add New Activity" max-width="max-w-2xl">
        <x-activity.activity-form form-id="add-activity-form" :categories="$categories" />
    </x-modal>

    {{-- EDIT ACTIVITY MODAL --}}
    <x-modal id="edit-activity-modal" title="Edit Activity" max-width="max-w-2xl">
        <div id="edit-activity-form-container"></div>
    </x-modal>

    @push('scripts')
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script>
            const CENTER_ID = {{ $center->id }};
            const STORE_URL = '/center/{{ $center->id }}/activities';
            const CATEGORIES = @json($categories);
        </script>
        <script src="{{ asset('js/center.js') }}"></script>
        <script src="{{ asset('js/activity.js') }}"></script>
    @endpush

</x-layouts.app-main>
