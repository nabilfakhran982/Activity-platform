@props(['act'])

@php
    $bg = $act->getBgClass();
    $image = $act->images->first();
    $avgRating = $act->reviews->avg('rating');
    $reviewCount = $act->reviews->count();
    $isSaved = auth()->check()
        ? $act->favourites->contains('user_id', auth()->id())
        : false;
@endphp

<div class="activity-card">
    {{-- Image --}}
    <div class="image-box {{ !$image ? $bg : '' }}" style="position:relative">
        @if($image)
            <img src="{{ asset($image->image_path) }}" alt="{{ $act->title }}">
        @else
            <span style="font-size:48px">{{ $act->category->icon }}</span>
        @endif

        {{-- Favourite button --}}
        @auth
        <button onclick="toggleFavourite({{ $act->id }}, this)"
            class="fav-btn {{ $isSaved ? 'saved' : '' }}"
            title="{{ $isSaved ? 'Remove from saved' : 'Save activity' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $isSaved ? 'currentColor' : 'none' }}"
                stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        @endauth
    </div>

    <div class="p-5">
        <div class="ai-badge mb-3 inline-block">✦ {{ $act->category->name }}</div>
        <h3 class="font-display text-base font-bold leading-snug mb-1">{{ $act->title }}</h3>
        <p class="text-xs mb-3" style="color:#8a7a6a">{{ $act->center->name }} · {{ $act->center->address }} · {{ $act->center->city }}</p>

        <div class="flex flex-wrap gap-1.5 mb-3">
            @if($act->min_age || $act->max_age)
                <span class="pill">Ages {{ $act->min_age ?? '0' }}{{ $act->max_age ? '–'.$act->max_age : '+' }}</span>
            @else
                <span class="pill">All ages</span>
            @endif
            @if($act->level)
                <span class="pill">{{ ucfirst($act->level) }}</span>
            @endif
            @if($act->is_private)
                <span class="pill">Private</span>
            @endif
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-[#F0EDE6]">
            <div>
                @if($reviewCount > 0)
                    <span class="stars text-xs">★</span>
                    <span class="text-xs font-medium ml-1">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs ml-1" style="color:#b0a898">({{ $reviewCount }})</span>
                @else
                    <span class="text-xs" style="color:#b0a898">No reviews yet</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium">${{ number_format($act->price, 0) }}/session</span>
                <button class="search-btn px-4 py-2 text-xs">Book now</button>
            </div>
        </div>
    </div>
</div>
