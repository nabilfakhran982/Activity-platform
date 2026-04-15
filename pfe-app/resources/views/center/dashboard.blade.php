<x-layouts.app-main title="Center Dashboard">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/center/dashboard.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest mb-2" style="color:rgba(212,163,80,0.8)">Center Owner</p>
                <h1 class="font-display text-white text-3xl font-bold">My Dashboard</h1>
            </div>
            <button onclick="openCenterModal('add-center-modal', 'add-center-form')" class="search-btn px-5 py-2.5 text-sm">
                + Add Center
            </button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="stat-card">
                <div class="stat-number">{{ $centers->count() }}</div>
                <div class="stat-label">Centers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalActivities }}</div>
                <div class="stat-label">Activities</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalBookings }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:#D4A350">{{ $pendingBookings }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        {{-- CENTERS --}}
        <h2 class="font-display text-2xl font-bold mb-6">My Centers</h2>

        <div id="centers-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
            @if($centers->isEmpty())
                <div class="no-results col-span-3">
                    <p class="text-lg font-medium mb-2">No centers yet</p>
                    <p class="text-sm">Click "Add Center" to get started</p>
                </div>
            @else
                @foreach($centers as $center)
                    <x-center.center-card :center="$center" />
                @endforeach
            @endif
        </div>

    </div>

    {{-- ADD MODAL --}}
    <x-modal id="add-center-modal" title="Add New Center">
        <x-center.center-form form-id="add-center-form" />
    </x-modal>

    {{-- EDIT MODAL --}}
    <x-modal id="edit-center-modal" title="Edit Center">
        <div id="edit-form-container"></div>
    </x-modal>

    @push('scripts')
        <script src="{{ asset('js/center.js') }}"></script>
    @endpush

</x-layouts.app-main>
