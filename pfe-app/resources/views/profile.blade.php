<x-layouts.app-main title="My Profile">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header" style="margin-top: 0; padding-top: 60px; padding-bottom: 40px;">
        <div class="max-w-5xl mx-auto px-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-display text-white text-3xl font-bold">{{ $user->name }}</h1>
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
                        <div class="mb-3" style="width:48px;height:48px;margin:0 auto 10px;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
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
                            <div class="mb-3" style="width:48px;height:48px;margin:0 auto 10px;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
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

                                            {{-- Delete overlay --}}
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
        // Update profile
        document.getElementById('profile-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors('profile-form');

            const res = await ajaxPost('/profile/update', new FormData(this));

            if (res.ok) {
                // تحديث الاسم بالـ header
                const nameEl = document.querySelector('.page-header h1');
                const navNameEl = document.querySelector('.nav-user-name'); // إذا موجود بالـ navbar
                const name = document.querySelector('#profile-form [name="name"]').value;

                if (nameEl) nameEl.textContent = name;
                if (navNameEl) navNameEl.textContent = name;

                document.getElementById('profile-success').classList.remove('hidden');
                setTimeout(() => document.getElementById('profile-success').classList.add('hidden'), 3000);
            } else {
                const data = await res.json();
                if (data.errors) showErrors('profile-form', data.errors);
            }
        });

        // Update password
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
    </script>
    @endpush

</x-layouts.app-main>
