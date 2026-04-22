<x-layouts.admin-app title="Bookings">

    {{-- Tabs --}}
    <div class="admin-tabs">
        <button onclick="switchTab('all')" id="tab-all" class="admin-tab active">All</button>
        <button onclick="switchTab('pending')" id="tab-pending" class="admin-tab">
            Pending
            @if($pending->count() > 0)
                <span class="admin-tab-badge">{{ $pending->count() }}</span>
            @endif
        </button>
        <button onclick="switchTab('confirmed')" id="tab-confirmed" class="admin-tab">Confirmed</button>
        <button onclick="switchTab('cancelled')" id="tab-cancelled" class="admin-tab">Cancelled</button>
    </div>

    @php
        $groups = [
            'all'       => $bookings,
            'pending'   => $pending,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
        ];
    @endphp

    @foreach($groups as $key => $group)
    <div id="section-{{ $key }}" class="{{ $key !== 'all' ? 'hidden' : '' }}">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ ucfirst($key) }} Bookings
                    <span style="color:#a09890;font-size:14px;font-weight:400">({{ $group->count() }})</span>
                </h2>
            </div>
            @if($group->isEmpty())
                <div class="admin-no-results">No {{ $key }} bookings</div>
            @else
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Center</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $booking)
                        <tr>
                            <td>
                                <p class="font-medium text-sm">{{ $booking->user->name }}</p>
                                <p class="text-xs" style="color:#8a7a6a">{{ $booking->user->email }}</p>
                            </td>
                            <td class="text-sm">{{ $booking->schedule->activity->title }}</td>
                            <td class="text-sm">{{ $booking->schedule->activity->center->name }}</td>
                            <td class="text-xs" style="color:#8a7a6a">
                                {{ ucfirst($booking->schedule->day_of_week) }}
                                {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                            </td>
                            <td>
                                <span class="badge {{ $booking->status === 'confirmed' ? 'badge-green' : ($booking->status === 'cancelled' ? 'badge-red' : 'badge-gold') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="text-xs" style="color:#a09890">{{ $booking->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script>
        function switchTab(tab) {
            document.querySelectorAll('[id^="section-"]').forEach(s => s.classList.add('hidden'));
            document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
            document.getElementById(`section-${tab}`).classList.remove('hidden');
            document.getElementById(`tab-${tab}`).classList.add('active');
        }
    </script>
    @endpush

</x-layouts.admin-app>
