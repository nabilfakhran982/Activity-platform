<x-layouts.app-main :title="$activity->title">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activity-detail.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @endpush

    @php
        $image = $activity->images->first();
        $avgRating = $activity->reviews->avg('rating');
        $reviewCount = $activity->reviews->count();
        $isSaved = auth()->check()
            ? $activity->favourites->contains('user_id', auth()->id())
            : false;
    @endphp

    {{-- HERO --}}
    <div class="activity-hero">
        @if($image)
            <img src="{{ asset($image->image_path) }}" alt="{{ $activity->title }}" class="activity-hero-img">
        @else
            <div class="activity-hero-placeholder {{ $activity->getBgClass() }}">
                <img src="{{ asset('images/categories/' . $activity->category->icon) }}" alt="{{ $activity->category->name }}" class="w-12 h-12 mx-auto mb-3 object-contain">
            </div>
        @endif

        <div class="activity-hero-overlay">
            <div class="max-w-5xl mx-auto px-6 h-full flex flex-col justify-end pb-10">
                <div class="ai-badge inline-block mb-3">✦ {{ $activity->category->name }}</div>
                <h1 class="font-display text-white text-4xl md:text-5xl font-bold mb-3">{{ $activity->title }}</h1>
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="text-white/70 text-sm">{{ $activity->center->name }}</span>
                    <span class="text-white/40">·</span>
                    <span class="text-white/70 text-sm">{{ $activity->center->city }}</span>
                    @if($reviewCount > 0)
                        <span class="text-white/40">·</span>
                        <span class="text-sm" style="color:#D4A350">★ {{ number_format($avgRating, 1) }}</span>
                        <span class="text-white/40 text-sm">({{ $reviewCount }} reviews)</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Favourite button --}}
        @auth
        <button onclick="toggleFavourite({{ $activity->id }}, this)"
            class="fav-btn-hero {{ $isSaved ? 'saved' : '' }}">
            <svg width="20" height="20" viewBox="0 0 24 24"
                fill="{{ $isSaved ? 'currentColor' : 'none' }}"
                stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        @endauth
    </div>

    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="grid md:grid-cols-3 gap-8">

            {{-- LEFT: Info + Schedules + Reviews --}}
            <div class="md:col-span-2 space-y-8">

                {{-- Info --}}
                <div class="detail-card">
                    <h2 class="detail-section-title">About this activity</h2>
                    <p class="text-sm leading-relaxed mb-6" style="color:#5a5751">
                        {{ $activity->description ?? 'No description available.' }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="info-stat">
                            <div class="info-stat-value">${{ number_format($activity->price, 0) }}</div>
                            <div class="info-stat-label">Per session</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-value">{{ $activity->capacity }}</div>
                            <div class="info-stat-label">Max spots</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-value">
                                @if($activity->min_age || $activity->max_age)
                                    {{ $activity->min_age ?? '0' }}{{ $activity->max_age ? '–'.$activity->max_age : '+' }}
                                @else
                                    All
                                @endif
                            </div>
                            <div class="info-stat-label">Ages</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-value">{{ ucfirst($activity->level ?? 'Any') }}</div>
                            <div class="info-stat-label">Level</div>
                        </div>
                    </div>

                    @if($activity->is_private)
                        <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full"
                            style="background:rgba(212,163,80,0.12);border:1px solid rgba(212,163,80,0.4)">
                            <span style="color:#8a6020;font-size:13px;font-weight:500">🔒 Private session (1-on-1)</span>
                        </div>
                    @endif
                </div>

                {{-- Schedules --}}
                <div class="detail-card">
                    <h2 class="detail-section-title">Available Schedules</h2>

                    @if($activity->schedules->isEmpty())
                        <p class="text-sm" style="color:#a09890">No schedules available yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($activity->schedules as $schedule)
                                <div class="schedule-book-row">
                                    <div>
                                        <p class="font-medium text-sm">{{ ucfirst($schedule->day_of_week) }}</p>
                                        <p class="text-xs mt-0.5" style="color:#8a7a6a">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    @auth
                                        <button onclick="bookSchedule({{ $schedule->id }}, '{{ ucfirst($schedule->day_of_week) }}', '{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}')"
                                            class="book-slot-btn">
                                            Book this slot
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="book-slot-btn">
                                            Login to book
                                        </a>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Reviews --}}
                <div class="detail-card">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="detail-section-title mb-0">Reviews</h2>
                        @if($reviewCount > 0)
                            <div class="flex items-center gap-2">
                                <span style="color:#D4A350;font-size:20px">★</span>
                                <span class="font-display font-bold text-xl">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-sm" style="color:#a09890">({{ $reviewCount }})</span>
                            </div>
                        @endif
                    </div>

                    @if($activity->reviews->isEmpty())
                        <p class="text-sm" style="color:#a09890">No reviews yet. Be the first to review!</p>
                    @else
                        <div class="space-y-4">
                            @foreach($activity->reviews->sortByDesc('created_at')->take(5) as $review)
                                <div class="review-item">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3">
                                            <div class="review-avatar">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-sm">{{ $review->user->name }}</p>
                                                <p class="text-xs" style="color:#a09890">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span style="color:{{ $i <= $review->rating ? '#D4A350' : '#E8E5DF' }};font-size:13px">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm leading-relaxed" style="color:#5a5751">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT: Price card + Center info + Map --}}
            <div class="md:col-span-1 space-y-5">

                {{-- Price card --}}
                <div class="detail-card">
                    <div class="text-center mb-4">
                        <div class="font-display text-3xl font-bold">${{ number_format($activity->price, 0) }}</div>
                        <div class="text-sm mt-1" style="color:#8a7a6a">per session</div>
                    </div>

                    @auth
                        <button onclick="document.querySelector('.schedule-book-row .book-slot-btn')?.click()"
                            class="search-btn w-full py-3 text-sm mb-3">
                            Book a Session
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="search-btn w-full py-3 text-sm mb-3 text-center block">
                            Login to Book
                        </a>
                    @endauth

                    <p class="text-xs text-center" style="color:#a09890">Select a schedule below to book</p>
                </div>

                {{-- Center info --}}
                <div class="detail-card">
                    <h3 class="font-display font-bold text-base mb-3">About the center</h3>
                    <p class="font-medium text-sm mb-1">{{ $activity->center->name }}</p>
                    <p class="text-xs mb-1" style="color:#8a7a6a">{{ $activity->center->address }}</p>
                    <p class="text-xs mb-3" style="color:#8a7a6a">{{ $activity->center->city }}</p>

                    @if($activity->center->phone)
                        <p class="text-xs" style="color:#8a7a6a">📞 {{ $activity->center->phone }}</p>
                    @endif

                    {{-- Map --}}
                    @if($activity->center->lat && $activity->center->lng)
                        <div id="center-map" class="mt-4" style="height:160px;border-radius:12px;overflow:hidden;z-index:1"></div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Booking Confirmation Modal --}}
    <div id="booking-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:420px;width:100%;text-align:center">
            <div style="width:56px;height:56px;background:rgba(212,163,80,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <svg width="26" height="26" fill="none" stroke="#D4A350" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold mb-2">Confirm Booking</h3>
            <p class="text-sm mb-1" style="color:#5a5751">{{ $activity->title }}</p>
            <p id="booking-slot-info" class="text-sm font-medium mb-6" style="color:#D4A350"></p>

            <p id="booking-error" class="text-sm mb-4 hidden" style="color:#e05252"></p>

            <div style="display:flex;gap:10px">
                <button onclick="closeBookingModal()"
                    style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                    onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">
                    Cancel
                </button>
                <button id="confirm-booking-btn" onclick="confirmBooking()"
                    class="search-btn" style="flex:1;padding:12px;font-size:13px;border:none;cursor:pointer">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    {{-- Booking Success Modal --}}
    <div id="booking-success-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:40px 32px;max-width:380px;width:100%;text-align:center">
            <div style="width:64px;height:64px;background:rgba(93,202,165,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px">
                ✓
            </div>
            <h3 class="font-display text-xl font-bold mb-2">Booking Confirmed!</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a">Your booking is pending confirmation from the center. Check your profile for updates.</p>
            <a href="{{ route('profile') }}" class="search-btn inline-block px-8 py-3 text-sm">
                View My Bookings
            </a>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let currentScheduleId = null;

        // Init map
        @if($activity->center->lat && $activity->center->lng)
        const map = L.map('center-map', { zoomControl: false, dragging: false, scrollWheelZoom: false })
            .setView([{{ $activity->center->lat }}, {{ $activity->center->lng }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([{{ $activity->center->lat }}, {{ $activity->center->lng }}])
            .addTo(map)
            .bindPopup('{{ addslashes($activity->center->name) }}')
            .openPopup();
        @endif

        // Book schedule
        function bookSchedule(scheduleId, day, time) {
            currentScheduleId = scheduleId;
            document.getElementById('booking-slot-info').textContent = day + ' · ' + time;
            document.getElementById('booking-error').classList.add('hidden');
            document.getElementById('booking-modal').style.display = 'flex';
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').style.display = 'none';
            currentScheduleId = null;
        }

        async function confirmBooking() {
            const btn = document.getElementById('confirm-booking-btn');
            btn.textContent = 'Booking...';
            btn.disabled = true;

            const res = await fetch(`/schedule/${currentScheduleId}/book`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            btn.textContent = 'Confirm';
            btn.disabled = false;

            if (res.ok) {
                document.getElementById('booking-modal').style.display = 'none';
                document.getElementById('booking-success-modal').style.display = 'flex';
            } else {
                const data = await res.json();
                const errEl = document.getElementById('booking-error');
                errEl.textContent = data.error ?? 'Something went wrong. Please try again.';
                errEl.classList.remove('hidden');
            }
        }

        // Close modals on backdrop
        document.getElementById('booking-modal').addEventListener('click', function(e) {
            if (e.target === this) closeBookingModal();
        });
    </script>
    @endpush

</x-layouts.app-main>
