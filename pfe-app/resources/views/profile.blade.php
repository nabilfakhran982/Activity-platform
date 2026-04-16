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

    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="grid md:grid-cols-3 gap-8">

            {{-- LEFT: Personal Info + Password --}}
            <div class="md:col-span-1 space-y-6">

                {{-- Personal Info --}}
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
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled
                                style="opacity:0.5;cursor:not-allowed">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-input"
                                value="{{ $user->phone }}" placeholder="+961 70 000 000">
                        </div>
                        <button type="submit" class="search-btn w-full py-3 text-sm mt-2">
                            Save Changes
                        </button>
                        <p id="profile-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">
                            ✓ Profile updated successfully
                        </p>
                    </form>
                </div>

                {{-- Change Password --}}
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
                        <button type="submit" class="search-btn w-full py-3 text-sm mt-2">
                            Update Password
                        </button>
                        <p id="password-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">
                            ✓ Password updated successfully
                        </p>
                    </form>
                </div>

            </div>

            {{-- RIGHT: Bookings + Favourites --}}
            <div class="md:col-span-2 space-y-8">

                {{-- Bookings --}}
                <div>
                    <h2 class="font-display text-xl font-bold mb-4">My Bookings</h2>

                    @if($user->bookings->isEmpty())
                        <div class="no-results">
                            <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                                <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                            </div>
                            <p class="font-medium mb-1">No bookings yet</p>
                            <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Browse activities</a></p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($user->bookings->sortByDesc('created_at') as $booking)
                                @php $act = $booking->schedule?->activity; @endphp
                                @if($act)
                                <div class="booking-card">
                                    {{-- Image --}}
                                    @php $img = $act->images->first(); @endphp
                                    <div class="booking-img">
                                        @if($img)
                                            <img src="{{ asset($img->image_path) }}" alt="{{ $act->title }}">
                                        @else
                                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;background:#F0EDE6">
                                                {{ $act->category->icon ?? '🏃' }}
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
                                        <span class="status-badge {{ $booking->status === 'confirmed' ? 'active' : ($booking->status === 'cancelled' ? 'inactive' : '') }}"
                                            style="{{ $booking->status === 'pending' ? 'background:rgba(212,163,80,0.12);color:#8a6020;border:1px solid rgba(212,163,80,0.4)' : '' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <p class="text-xs mt-1" style="color:#c0b8b0">{{ $booking->created_at->diffForHumans() }}</p>

                                        {{-- Review --}}
                                        @if($booking->status === 'confirmed' && !$booking->review)
                                            <button onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($act->title) }}')"
                                                class="review-btn mt-2">
                                                ★ Leave a Review
                                            </button>
                                        @elseif($booking->review)
                                            <div class="mt-2 flex items-center gap-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span style="color:{{ $i <= $booking->review->rating ? '#D4A350' : '#E8E5DF' }};font-size:13px">★</span>
                                                @endfor
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Favourites --}}
                <div>
                    <h2 class="font-display text-xl font-bold mb-4">Saved Activities</h2>

                    @if($user->favourites->isEmpty())
                        <div class="no-results">
                            <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                                <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </div>
                            <p class="font-medium mb-1">No saved activities</p>
                            <p class="text-sm"><a href="{{ route('activities') }}" class="text-[#D4A350]">Explore activities</a></p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($user->favourites as $fav)
                                @php
                                    $act = $fav->activity;
                                    $img = $act?->images->first();
                                @endphp
                                @if($act)
                                <div class="fav-card" id="fav-card-{{ $act->id }}">
                                    <div class="fav-img">
                                        @if($img)
                                            <img src="{{ asset($img->image_path) }}" alt="{{ $act->title }}">
                                        @else
                                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;background:#F0EDE6">
                                                {{ $act->category->icon ?? '🏃' }}
                                            </div>
                                        @endif
                                        <div class="fav-overlay">
                                            <button onclick="removeFavourite({{ $act->id }}, this)"
                                                class="fav-delete-btn" title="Remove">
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
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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

        // ============ Remove favourite ============
        async function removeFavourite(activityId, btn) {
            const res = await fetch(`/activity/${activityId}/favourite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (res.ok) {
                const card = document.getElementById(`fav-card-${activityId}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        card.remove();
                        const grid = document.querySelector('.grid.grid-cols-2');
                        if (grid && grid.querySelectorAll('.fav-card').length === 0) {
                            grid.outerHTML = `
                                <div class="no-results">
                                    <p class="font-medium mb-1">No saved activities</p>
                                    <p class="text-sm"><a href="/activities" class="text-[#D4A350]">Explore activities</a></p>
                                </div>`;
                        }
                    }, 300);
                }
            }
        }

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

                    <div style="display:flex;gap:8px;margin-bottom:20px" id="stars-container">
                        ${[1,2,3,4,5].map(i => `
                            <button type="button" onclick="setRating(${i})"
                                id="star-${i}"
                                style="background:none;border:none;font-size:32px;cursor:pointer;color:#E8E5DF;transition:color 0.15s;padding:0;line-height:1">
                                ★
                            </button>
                        `).join('')}
                    </div>

                    <textarea id="review-comment" rows="3" placeholder="Tell us what you thought... (optional)"
                        style="width:100%;border:1px solid #E8E5DF;border-radius:10px;padding:12px;font-size:13px;font-family:'DM Sans',sans-serif;resize:none;outline:none;box-sizing:border-box;margin-bottom:8px"
                        onfocus="this.style.borderColor='#D4A350'" onblur="this.style.borderColor='#E8E5DF'"></textarea>

                    <p id="review-error" style="color:#e05252;font-size:12px;margin-bottom:12px;display:none">Please select a rating first</p>

                    <div style="display:flex;gap:10px">
                        <button onclick="document.getElementById('review-modal').remove()"
                            style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                            onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">
                            Cancel
                        </button>
                        <button onclick="submitReview(${bookingId})"
                            class="search-btn" style="flex:1;padding:12px;font-size:13px;border:none;cursor:pointer">
                            Submit Review
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.remove();
            });
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
