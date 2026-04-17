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

        {{-- PENDING BOOKINGS --}}
        @if($pendingBookingsList->isNotEmpty())
        <div class="mb-12">
            <h2 class="font-display text-2xl font-bold mb-6">
                Pending Bookings
                <span class="text-sm font-normal ml-2 px-2 py-1 rounded-full"
                    style="background:rgba(212,163,80,0.12);color:#D4A350;font-family:'DM Sans',sans-serif">
                    {{ $pendingBookingsList->count() }}
                </span>
            </h2>

            <div class="space-y-3" id="pending-bookings-list">
                @foreach($pendingBookingsList as $booking)
                <div class="pending-booking-card" id="pending-booking-{{ $booking->id }}">
                    {{-- User info --}}
                    <div class="pending-booking-avatar">
                        {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                    </div>

                    <div class="pending-booking-info">
                        <p class="font-medium text-sm">{{ $booking->user->name }}</p>
                        <p class="text-xs mt-0.5" style="color:#8a7a6a">{{ $booking->user->email }}</p>
                    </div>

                    <div class="pending-booking-activity">
                        <p class="font-medium text-sm">{{ $booking->schedule->activity->title }}</p>
                        <p class="text-xs mt-0.5" style="color:#8a7a6a">
                            {{ $booking->schedule->activity->center->name }} ·
                            {{ ucfirst($booking->schedule->day_of_week) }}
                            {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                        </p>
                    </div>

                    <div class="text-xs" style="color:#a09890">
                        {{ $booking->created_at->diffForHumans() }}
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button onclick="updateBooking({{ $booking->id }}, 'confirmed')"
                            class="booking-action-btn confirm-btn">
                            ✓ Confirm
                        </button>
                        <button onclick="updateBooking({{ $booking->id }}, 'cancelled')"
                            class="booking-action-btn cancel-btn">
                            ✕ Cancel
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

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
        <script>
            async function updateBooking(bookingId, status) {
                const card = document.getElementById(`pending-booking-${bookingId}`);

                const res = await fetch(`/booking/${bookingId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status }),
                });

                if (res.ok) {
                    card.style.opacity = '0';
                    card.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        card.remove();

                        // تحديث الـ pending count
                        const countEl = document.querySelector('.stat-number[style*="D4A350"]');
                        if (countEl) {
                            const current = parseInt(countEl.textContent) || 0;
                            countEl.textContent = Math.max(0, current - 1);
                        }

                        // تحديث الـ total bookings إذا confirmed
                        if (status === 'confirmed') {
                            const totalEl = document.querySelectorAll('.stat-number')[2];
                            if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + 1;
                        }

                        // إذا ما في pending bookings، نخفي الـ section
                        const list = document.getElementById('pending-bookings-list');
                        if (list && list.children.length === 0) {
                            list.closest('.mb-12').remove();
                        }
                    }, 300);
                }
            }
        </script>
    @endpush

</x-layouts.app-main>
