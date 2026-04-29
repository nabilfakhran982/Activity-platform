<x-layouts.app-main title="My Profile">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-5xl mx-auto px-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-display text-white text-3xl font-bold" id="profile-name">{{ $user->name }}</h1>
                    <p class="text-white/40 text-sm mt-1">{{ $user->email }}</p>
                    <span class="inline-block mt-2 text-xs px-3 py-1 rounded-full"
                        style="background:rgba(212,163,80,0.15);color:#D4A350;border:1px solid rgba(212,163,80,0.3)">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 md:px-6 py-10">
        <div class="grid md:grid-cols-3 gap-8">

            {{-- LEFT --}}
            <div class="md:col-span-1 space-y-6">
                <div class="profile-card">
                    <h2 class="profile-card-title">Personal Info</h2>
                    <form id="profile-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-input" value="{{ $user->name }}">
                            <p class="error-msg" id="profile-form-err-name"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled style="opacity:0.5;cursor:not-allowed">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-input" value="{{ $user->phone }}" placeholder="+961 70 000 000">
                        </div>
                        <button type="submit" class="search-btn w-full py-3 text-sm mt-2">Save Changes</button>
                        <p id="profile-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">✓ Profile updated successfully</p>
                    </form>
                </div>

                <div class="profile-card">
                    <h2 class="profile-card-title">Change Password</h2>
                    <form id="password-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                            <p class="error-msg" id="password-form-err-current_password"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-input" placeholder="••••••••">
                            <p class="error-msg" id="password-form-err-password"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                        </div>
                        <button type="submit" class="search-btn w-full py-3 text-sm mt-2">Update Password</button>
                        <p id="password-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">✓ Password updated successfully</p>
                    </form>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="md:col-span-2 space-y-5">

                {{-- BOOKINGS HEADER --}}
                <h2 class="font-display text-xl font-bold">My Bookings</h2>

                @php
                    $allBookings      = $user->bookings->sortByDesc('created_at');
                    $pending          = $allBookings->where('status', 'pending');
                    $cancelled        = $allBookings->where('status', 'cancelled');
                    $confirmedNoRev   = $allBookings->where('status', 'confirmed')->filter(fn($b) => !$b->review);
                    $confirmedWithRev = $allBookings->where('status', 'confirmed')->filter(fn($b) => $b->review);

                    $sections = [
                        ['id' => 'pending',           'label' => 'Pending',                    'bookings' => $pending,          'canDelete' => true,  'color' => 'rgba(212,163,80,0.12)', 'textColor' => '#8a6020', 'borderColor' => 'rgba(212,163,80,0.4)'],
                        ['id' => 'cancelled',          'label' => 'Cancelled',                  'bookings' => $cancelled,         'canDelete' => true,  'color' => 'rgba(232,74,74,0.10)',  'textColor' => '#A32D2D', 'borderColor' => 'rgba(232,74,74,0.3)'],
                        ['id' => 'confirmed-noreview', 'label' => 'Confirmed — Leave a Review', 'bookings' => $confirmedNoRev,   'canDelete' => true,  'color' => 'rgba(93,202,165,0.12)', 'textColor' => '#0F6E56', 'borderColor' => 'rgba(93,202,165,0.4)'],
                        ['id' => 'confirmed-reviewed', 'label' => 'Confirmed — Reviewed',       'bookings' => $confirmedWithRev, 'canDelete' => false, 'color' => 'rgba(93,202,165,0.12)', 'textColor' => '#0F6E56', 'borderColor' => 'rgba(93,202,165,0.4)'],
                    ];
                @endphp

                <div id="bookings-container">
                @if($allBookings->isEmpty())
                    <div id="no-bookings-message" class="no-results">
                        <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                        <p class="font-medium mb-1">No bookings yet</p>
                        <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Browse activities</a></p>
                    </div>
                @else
                    <div id="no-bookings-message" class="no-results" style="display:none">
                        <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                        <p class="font-medium mb-1">No bookings yet</p>
                        <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Browse activities</a></p>
                    </div>
                @endif
                    @foreach($sections as $section)
                        @if($section['bookings']->isNotEmpty())
                        <div id="section-{{ $section['id'] }}">
                            {{-- Section header --}}
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-display text-base font-bold" style="color:#1a1a18">
                                    {{ $section['label'] }}
                                    <span class="text-sm font-normal ml-1" style="color:#a09890">({{ $section['bookings']->count() }})</span>
                                </h3>
                                @if($section['bookings']->count() > 1)
                                <div class="flex gap-2">
                                    <button onclick="scrollCarousel('track-{{ $section['id'] }}', -1)" class="carousel-arrow">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                                    </button>
                                    <button onclick="scrollCarousel('track-{{ $section['id'] }}', 1)" class="carousel-arrow">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <div class="carousel-wrapper">
                                <div class="carousel-track" id="track-{{ $section['id'] }}">
                                    @foreach($section['bookings'] as $booking)
                                        @php $act = $booking->schedule?->activity; @endphp
                                        @if($act)
                                        <div class="booking-slide" id="slide-{{ $booking->id }}">
                                            @php $img = $act->images->first(); @endphp
                                            <div class="booking-card">
                                                <div class="booking-img">
                                                    @if($img)
                                                        <img src="{{ asset($img->image_path) }}" alt="{{ $act->title }}">
                                                    @else
                                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#F0EDE6">
                                                            <img src="{{ asset('images/categories/' . ($act->category->icon ?? 'default.png')) }}" alt="{{ $act->category->name ?? '' }}" class="w-12 h-12 mx-auto mb-3 object-contain">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="booking-info">
                                                    <h3 class="font-display font-bold text-sm leading-snug">{{ $act->title }}</h3>
                                                    <p class="text-xs mt-0.5" style="color:#8a7a6a">{{ $act->center->name }} · {{ $act->center->city }}</p>
                                                    @if($booking->schedule)
                                                        <p class="text-xs mt-1" style="color:#a09890">
                                                            {{ ucfirst($booking->schedule->day_of_week) }}
                                                            {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="booking-status">
                                                    <span class="status-badge"
                                                        style="background:{{ $section['color'] }};color:{{ $section['textColor'] }};border:1px solid {{ $section['borderColor'] }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                    <p class="text-xs mt-1" style="color:#c0b8b0">{{ $booking->created_at->diffForHumans() }}</p>

                                                    @if($section['id'] === 'confirmed-noreview')
                                                        <button onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($act->title) }}')"
                                                            class="review-btn mt-2">★ Leave a Review</button>
                                                    @elseif($section['id'] === 'confirmed-reviewed')
                                                        <div class="mt-2 flex items-center gap-0.5">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <span style="color:{{ $i <= $booking->review->rating ? '#D4A350' : '#E8E5DF' }};font-size:13px">★</span>
                                                            @endfor
                                                        </div>
                                                    @endif

                                                    @if($section['canDelete'])
                                                        <button onclick="confirmDeleteBooking({{ $booking->id }})" class="delete-booking-btn mt-1" title="Remove">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <polyline points="3 6 5 6 21 6"/>
                                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                                <path d="M10 11v6M14 11v6"/>
                                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- SAVED ACTIVITIES --}}
                <div class="mt-8">
                <h2 class="font-display text-xl font-bold">Saved Activities</h2>

                <div id="favourites-container">
                @if($user->favourites->isEmpty())
                    <div id="no-favs-message" class="no-results">
                        <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <p class="font-medium mb-1">No saved activities</p>
                        <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Explore activities</a></p>
                    </div>
                @else
                    <div id="no-favs-message" class="no-results" style="display:none">
                        <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <p class="font-medium mb-1">No saved activities</p>
                        <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Explore activities</a></p>
                    </div>
                @endif
                @if(!$user->favourites->isEmpty())
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm" id="favs-saved-count" style="color:#a09890">{{ $user->favourites->count() }} saved</span>
                        @if($user->favourites->count() > 2)
                        <div class="flex gap-2">
                            <button onclick="scrollCarousel('favs-track', -1)" class="carousel-arrow">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button onclick="scrollCarousel('favs-track', 1)" class="carousel-arrow">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="carousel-wrapper">
                        <div class="carousel-track" id="favs-track">
                            @foreach($user->favourites as $fav)
                                @php $act = $fav->activity; $img = $act?->images->first(); @endphp
                                @if($act)
                                <div class="fav-slide">
                                    <div class="fav-card" id="fav-card-{{ $act->id }}">
                                        <div class="fav-img">
                                            @if($img)
                                                <img src="{{ asset($img->image_path) }}" alt="{{ $act->title }}">
                                            @else
                                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#F0EDE6">
                                                    <img src="{{ asset('images/categories/' . ($act->category->icon ?? 'default.png')) }}" alt="{{ $act->category->name ?? '' }}" class="w-12 h-12 mx-auto mb-3 object-contain">
                                                </div>
                                            @endif
                                            <div class="fav-overlay">
                                                <button onclick="confirmRemoveFavourite({{ $act->id }}, '{{ addslashes($act->title) }}')" class="fav-delete-btn" title="Remove">
                                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h3 class="font-display font-bold text-sm leading-snug mb-0.5">{{ $act->title }}</h3>
                                            <p class="text-xs" style="color:#8a7a6a">${{ number_format($act->price, 0) }}/session</p>
                                            <a href="{{ route('activity.show', $act->id) }}"
                                                class="search-btn text-xs py-1.5 px-3 mt-5 text-center block">
                                                View & Book
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Confirm Delete Modal --}}
    <div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:380px;width:100%;text-align:center">
            <div style="width:52px;height:52px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <svg width="22" height="22" fill="none" stroke="#e05252" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                </svg>
            </div>
            <h3 class="font-display text-lg font-bold mb-2" id="confirm-modal-title">Are you sure?</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a" id="confirm-modal-msg">This action cannot be undone.</p>
            <div style="display:flex;gap:10px">
                <button onclick="closeConfirmModal()"
                    style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                    onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">
                    Cancel
                </button>
                <button id="confirm-modal-btn"
                    style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif"
                    onmouseover="this.style.background='#c94040'" onmouseout="this.style.background='#e05252'">
                    Remove
                </button>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // ============ Carousel ============
        function scrollCarousel(trackId, direction) {
            const track = document.getElementById(trackId);
            if (!track) return;
            const slide = track.querySelector('[class$="-slide"]');
            const slideWidth = slide ? slide.offsetWidth + 12 : 320;
            track.scrollBy({ left: direction * slideWidth, behavior: 'smooth' });
        }

        // ============ Confirm modal ============
        let confirmCallback = null;

        function showConfirmModal(title, msg, callback) {
            document.getElementById('confirm-modal-title').textContent = title;
            document.getElementById('confirm-modal-msg').textContent = msg;
            document.getElementById('confirm-modal').style.display = 'flex';
            confirmCallback = callback;
            document.getElementById('confirm-modal-btn').onclick = () => {
                closeConfirmModal();
                callback();
            };
        }

        function closeConfirmModal() {
            document.getElementById('confirm-modal').style.display = 'none';
            confirmCallback = null;
        }

        document.getElementById('confirm-modal').addEventListener('click', function(e) {
            if (e.target === this) closeConfirmModal();
        });

        // ============ Delete booking ============
        function confirmDeleteBooking(bookingId) {
            showConfirmModal(
                'Remove Booking?',
                'Are you sure you want to remove this booking?',
                () => deleteBooking(bookingId)
            );
        }

        async function deleteBooking(bookingId) {
    const slide = document.getElementById(`slide-${bookingId}`);
    if (!slide) return;

    const res = await fetch(`/booking/${bookingId}/delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    });

    if (res.ok) {
        // خذ الـ section قبل ما نشيل الـ slide
        const sectionDiv = slide.closest('[id^="section-"]');
        const track = slide.closest('.carousel-track');

        slide.style.opacity = '0';
        slide.style.transition = 'opacity 0.3s';

        setTimeout(() => {
            slide.remove();

            if (sectionDiv && track) {
                const remainingSlides = track.querySelectorAll('.booking-slide').length;

                if (remainingSlides === 0) {
                    // شيل الـ section header والـ carousel كلهم
                    sectionDiv.style.opacity = '0';
                    sectionDiv.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        sectionDiv.remove();

                        // بعد ما نشيل الـ section، تحقق إذا ما في sections تانية
                        const bookingsContainer = document.getElementById('bookings-container');
                        const remainingSections = bookingsContainer.querySelectorAll('[id^="section-"]').length;

                        if (remainingSections === 0) {
                            const noMsg = document.getElementById('no-bookings-message');
                            if (noMsg) {
                                noMsg.style.display = 'block';
                                setTimeout(() => { noMsg.style.opacity = '1'; }, 50);
                            }
                        }
                    }, 300);
                } else {
                    // update count
                    const countEl = sectionDiv.querySelector('h3 span');
                    if (countEl) {
                        const current = parseInt(countEl.textContent.replace(/\D/g, '')) || 0;
                        countEl.textContent = `(${Math.max(0, current - 1)})`;
                    }
                }
            }
        }, 300);
    }
}

        // ============ Remove favourite ============
        function confirmRemoveFavourite(activityId, title) {
            showConfirmModal(
                'Remove from Saved?',
                `Remove "${title}" from your saved activities?`,
                () => removeFavourite(activityId)
            );
        }

        async function removeFavourite(activityId) {
        const res = await fetch(`/activity/${activityId}/favourite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            const card = document.getElementById(`fav-card-${activityId}`);
            if (!card) return;
            const slide = card.closest('.fav-slide');
            if (!slide) return;

            slide.style.opacity = '0';
            slide.style.transition = 'opacity 0.3s';

            setTimeout(() => {
                slide.remove();

                const favsTrack = document.getElementById('favs-track');
                const remainingFavs = favsTrack ? favsTrack.querySelectorAll('.fav-slide').length : 0;

                // Update count
                const countEl = document.getElementById('favs-saved-count');
                if (countEl) {
                    const current = parseInt(countEl.textContent) || 0;
                    countEl.textContent = `${Math.max(0, current - 1)} saved`;
                }

                if (remainingFavs === 0) {
                    // شيل كل الـ favourites container content
                    const favouritesContainer = document.getElementById('favourites-container');
                    if (favouritesContainer) {
                        favouritesContainer.innerHTML = `
                            <div class="no-results" style="margin-top:12px">
                                <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                                    <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </div>
                                <p class="font-medium mb-1">No saved activities</p>
                                <p class="text-sm"><a href="/activities" class="text-[#D4A350]">Explore activities</a></p>
                            </div>`;
                    }
                }
            }, 300);
        }
    }

         // ============ Update profile ============
        document.getElementById('profile-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors('profile-form');
            const res = await ajaxPost('/profile/update', new FormData(this));
            if (res.ok) {
                const name = document.querySelector('#profile-form [name="name"]').value;
                document.getElementById('profile-name').textContent = name;
                const navName = document.querySelector('.nav-user-name');
                if (navName) navName.textContent = name;
                document.getElementById('profile-success').classList.remove('hidden');
                setTimeout(() => document.getElementById('profile-success').classList.add('hidden'), 3000);
            } else {
                const data = await res.json();
                if (data.errors) showErrors('profile-form', data.errors);
            }
        });

        // ============ Update password ============
        document.getElementById('password-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors('password-form');
            const res = await ajaxPost('/profile/password', new FormData(this));
            if (res.ok) {
                this.reset();
                document.getElementById('password-success').classList.remove('hidden');
                setTimeout(() => document.getElementById('password-success').classList.add('hidden'), 3000);
            } else {
                const data = await res.json();
                if (data.errors) showErrors('password-form', data.errors);
            }
        });

        // ============ Review ============
        let currentRating = 0;

        function openReviewModal(bookingId, activityTitle) {
            currentRating = 0;
            const modal = document.createElement('div');
            modal.id = 'review-modal';
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:20px;padding:32px;max-width:420px;width:100%">
                    <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:4px">${activityTitle}</h3>
                    <p style="font-size:13px;color:#8a7a6a;margin-bottom:24px">Share your experience</p>
                    <div style="display:flex;gap:8px;margin-bottom:20px">
                        ${[1,2,3,4,5].map(i => `
                            <button type="button" onclick="setRating(${i})" id="star-${i}"
                                style="background:none;border:none;font-size:32px;cursor:pointer;color:#E8E5DF;transition:color 0.15s;padding:0;line-height:1">★</button>
                        `).join('')}
                    </div>
                    <textarea id="review-comment" rows="3" placeholder="Tell us what you thought... (optional)"
                        style="width:100%;border:1px solid #E8E5DF;border-radius:10px;padding:12px;font-size:13px;font-family:'DM Sans',sans-serif;resize:none;outline:none;box-sizing:border-box;margin-bottom:8px"
                        onfocus="this.style.borderColor='#D4A350'" onblur="this.style.borderColor='#E8E5DF'"></textarea>
                    <p id="review-error" style="color:#e05252;font-size:12px;margin-bottom:12px;display:none">Please select a rating first</p>
                    <div style="display:flex;gap:10px">
                        <button onclick="document.getElementById('review-modal').remove()"
                            style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                            onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">Cancel</button>
                        <button onclick="submitReview(${bookingId})"
                            class="search-btn" style="flex:1;padding:12px;font-size:13px;border:none;cursor:pointer">Submit Review</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            modal.addEventListener('click', function(e) { if (e.target === this) this.remove(); });
        }

        function setRating(rating) {
            currentRating = rating;
            [1,2,3,4,5].forEach(i => {
                document.getElementById(`star-${i}`).style.color = i <= rating ? '#D4A350' : '#E8E5DF';
            });
        }

        async function submitReview(bookingId) {
            if (!currentRating) {
                document.getElementById('review-error').style.display = 'block';
                return;
            }
            document.getElementById('review-error').style.display = 'none';
            const comment = document.getElementById('review-comment').value;
            const res = await fetch(`/booking/${bookingId}/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ rating: currentRating, comment }),
            });
            if (res.ok) {
                document.getElementById('review-modal').remove();
                window.location.reload();
            } else {
                const data = await res.json();
                if (data.error) {
                    document.getElementById('review-error').textContent = data.error;
                    document.getElementById('review-error').style.display = 'block';
                }
            }
        }
    </script>
    @endpush

</x-layouts.app-main>
