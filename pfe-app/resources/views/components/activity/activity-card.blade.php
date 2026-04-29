@props(['act'])

@php
    $image = $act->images->first();
    $bg = $act->getBgClass();
    $schedules = $act->schedules;
    $firstSchedule = $schedules->first();
    $remainingCount = $schedules->count() - 1;
@endphp

<div class="activity-mgmt-card" id="activity-card-{{ $act->id }}">

    {{-- Image --}}
    <div class="activity-img-wrapper">
        @if($image)
            <img src="{{ asset($image->image_path) }}" alt="{{ $act->title }}" class="activity-img">
        @else
            <div class="activity-img-placeholder {{ $bg }}">
                <img src="{{ asset('images/categories/' . $act->category->icon) }}" alt="{{ $act->category->name }}" class="w-12 h-12 mx-auto mb-3 object-contain">
            </div>
        @endif

        {{-- Actions overlay --}}
        <div class="activity-actions-overlay">
            <button onclick="openEditActivityModal({{ $act->id }}, {{ $act->toJson() }})"
                class="icon-btn edit-btn" title="Edit">
                <span class="material-icons">edit</span>
            </button>
            <button onclick="deleteActivity({{ $act->id }})"
                class="icon-btn delete-btn" title="Delete">
                <span class="material-icons">delete</span>
            </button>
        </div>
    </div>

    {{-- Body --}}
    <div class="activity-mgmt-body">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-display text-base font-bold leading-snug">{{ $act->title }}</h3>
            <button class="status-badge {{ $act->is_active ? 'active' : 'inactive' }} flex-shrink-0"
                onclick="toggleActivityActive({{ $act->id }}, this)">
                {{ $act->is_active ? 'Active' : 'Inactive' }}
            </button>
        </div>

        <p class="text-xs mb-3" style="color:#8a7a6a">
            {{ $act->category->name }}
            @if($act->level) · {{ ucfirst($act->level) }} @endif
        </p>

        {{-- Pills --}}
        <div class="flex flex-wrap gap-1.5 mb-3">
            <span class="pill">${{ number_format($act->price, 0) }}/session</span>
            @if($act->min_age || $act->max_age)
                <span class="pill">Ages {{ $act->min_age ?? '0' }}{{ $act->max_age ? '–'.$act->max_age : '+' }}</span>
            @else
                <span class="pill">All ages</span>
            @endif
            @if($act->is_private)
                <span class="pill">Private</span>
            @endif
            <span class="pill">{{ $act->capacity }} spots</span>
        </div>

        {{-- Schedules --}}
        <div class="schedules-list">
            @if($firstSchedule)
                <div class="schedule-item">
                    <span class="schedule-day">{{ ucfirst($firstSchedule->day_of_week) }}</span>
                    <span class="schedule-time">
                        {{ \Carbon\Carbon::parse($firstSchedule->start_time)->format('H:i') }}
                        –
                        {{ \Carbon\Carbon::parse($firstSchedule->end_time)->format('H:i') }}
                    </span>
                </div>
                @if($remainingCount > 0)
                    <button onclick="showAllSchedules({{ $act->id }}, '{{ addslashes($act->title) }}')"
                        class="show-more-btn">
                        +{{ $remainingCount }} more {{ $remainingCount === 1 ? 'slot' : 'slots' }}
                    </button>
                @endif
            @else
                <p class="text-xs" style="color:#b0a898">No schedules added</p>
            @endif
        </div>

        {{-- Hidden schedules data --}}
        <script type="application/json" id="schedules-data-{{ $act->id }}">
            {!! $schedules->toJson() !!}
        </script>
    </div>

</div>
