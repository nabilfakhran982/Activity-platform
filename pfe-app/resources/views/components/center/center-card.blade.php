@props(['center', 'showActions' => true])

<div class="center-card" id="center-card-{{ $center->id }}">
    {{-- Logo with overlay actions --}}
    <div class="center-card-logo-wrapper">
        <div class="center-card-logo">
            @if($center->logo)
                <img src="{{ asset('storage/' . $center->logo) }}" alt="{{ $center->name }}">
            @else
                <div class="center-logo-placeholder">
                    {{ strtoupper(substr($center->name, 0, 2)) }}
                </div>
            @endif
        </div>

        @if($showActions)
            {{-- Edit & Delete (overlay on logo) --}}
            <div class="card-actions-overlay">
                <button onclick="openEditModal({{ $center->id }}, {{ $center->toJson() }})" class="icon-btn edit-btn"
                    title="Edit">
                    <span class="material-icons">edit</span>
                </button>
                <button onclick="deleteCenter({{ $center->id }})" class="icon-btn delete-btn" title="Delete">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        @endif
    </div>

    <div class="center-card-body">
        <div class="center-card-header">
            <h3 class="font-display text-lg font-bold">{{ $center->name }}</h3>
            {{-- Active toggle --}}
            <button class="status-badge {{ $center->is_active ? 'active' : 'inactive' }}"
                onclick="toggleActive({{ $center->id }}, this)">
                {{ $center->is_active ? 'Active' : 'Inactive' }}
            </button>
        </div>

        <p class="text-xs mb-1" style="color:#8a7a6a">{{ $center->address }}, {{ $center->city }}</p>
        <p class="text-xs mb-4" style="color:#8a7a6a">{{ $center->activities_count }} activities</p>

        @if($showActions)
            <div class="flex gap-2 w-full pt-4 border-t border-[#F0EDE6]">
                <a href="{{ route('center.activities', $center->id) }}" class="dashboard-btn flex-1 text-center text-xs">
                    Manage Activities
                </a>
                <a href="{{ route('center.bookings', $center->id) }}"
                    class="dashboard-btn-outline flex-1 text-center text-xs" style="position:relative">
                    Bookings
                    @php
                        $pendingCount = \App\Models\Booking::whereHas(
                            'schedule.activity',
                            fn($q) => $q->where('center_id', $center->id)
                        )->where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span
                            style="position:absolute;top:4px;right:4    px;background:#D4A350;color:#1a1a18;border-radius:999px;font-size:10px;font-weight:600;padding:1px 6px;min-width:18px;text-align:center">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </div>
        @endif
    </div>
</div>
