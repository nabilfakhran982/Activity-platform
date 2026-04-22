<x-layouts.app-main title="Bookings — {{ $center->name }}">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/center/dashboard.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-5xl mx-auto px-6 relative z-10">
            <a href="{{ route('center.dashboard') }}"
                class="text-xs uppercase tracking-widest mb-3 block hover:opacity-70 transition-opacity"
                style="color:rgba(212,163,80,0.8)">
                ← Back to Dashboard
            </a>
            <h1 class="font-display text-white text-3xl font-bold">{{ $center->name }}</h1>
            <p class="text-white/40 text-sm mt-1">Bookings Management</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-10">

        {{-- STATS --}}
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="stat-card">
                <div class="stat-number" style="color:#D4A350">{{ $bookings->where('status', 'pending')->count() }}
                </div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:#0F6E56">{{ $bookings->where('status', 'confirmed')->count() }}
                </div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:#A32D2D">{{ $bookings->where('status', 'cancelled')->count() }}
                </div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>

        {{-- TABS --}}
        <div class="bookings-tabs">
            <button onclick="switchTab('pending')" id="tab-pending" class="booking-tab active">
                Pending
                @if($bookings->where('status', 'pending')->count() > 0)
                    <span class="tab-badge"
                        id="pending-tab-count">{{ $bookings->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('confirmed')" id="tab-confirmed" class="booking-tab">
                Confirmed
            </button>
            <button onclick="switchTab('cancelled')" id="tab-cancelled" class="booking-tab">
                Cancelled
            </button>
        </div>

        {{-- PENDING --}}
        <div id="section-pending" class="bookings-section">
            @php $pending = $bookings->where('status', 'pending'); @endphp
            @if($pending->isEmpty())
                <div class="no-results">
                    <p class="font-medium mb-1">No pending bookings</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pending as $booking)
                        <div class="booking-row" id="booking-row-{{ $booking->id }}">
                            <div class="booking-row-avatar">
                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                            </div>
                            <div class="booking-row-user">
                                <p class="font-medium text-sm">{{ $booking->user->name }}</p>
                                <p class="text-xs" style="color:#8a7a6a">{{ $booking->user->email }}</p>
                            </div>
                            <div class="booking-row-activity">
                                <p class="font-medium text-sm">{{ $booking->schedule->activity->title }}</p>
                                <p class="text-xs" style="color:#8a7a6a">
                                    {{ ucfirst($booking->schedule->day_of_week) }} ·
                                    {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                    –
                                    {{ \Carbon\Carbon::parse($booking->schedule->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-xs" style="color:#a09890">
                                {{ $booking->created_at->diffForHumans() }}
                            </div>
                            <div class="flex gap-2">
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')"
                                    class="booking-action-btn confirm-btn">✓ Confirm</button>
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')"
                                    class="booking-action-btn cancel-btn">✕ Cancel</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CONFIRMED --}}
        <div id="section-confirmed" class="bookings-section hidden">
            @php $confirmed = $bookings->where('status', 'confirmed'); @endphp
            @if($confirmed->isEmpty())
                <div class="no-results">
                    <p class="font-medium mb-1">No confirmed bookings</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($confirmed as $booking)
                        <div class="booking-row" id="booking-row-{{ $booking->id }}">
                            <div class="booking-row-avatar" style="background:#0F6E56">
                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                            </div>
                            <div class="booking-row-user">
                                <p class="font-medium text-sm">{{ $booking->user->name }}</p>
                                <p class="text-xs" style="color:#8a7a6a">{{ $booking->user->email }}</p>
                            </div>
                            <div class="booking-row-activity">
                                <p class="font-medium text-sm">{{ $booking->schedule->activity->title }}</p>
                                <p class="text-xs" style="color:#8a7a6a">
                                    {{ ucfirst($booking->schedule->day_of_week) }} ·
                                    {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                    –
                                    {{ \Carbon\Carbon::parse($booking->schedule->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-xs" style="color:#a09890">
                                {{ $booking->created_at->diffForHumans() }}
                            </div>
                            @if($booking->review)
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span
                                            style="color:{{ $i <= $booking->review->rating ? '#D4A350' : '#E8E5DF' }};font-size:13px">★</span>
                                    @endfor
                                </div>
                            @else
                                <span class="text-xs" style="color:#c0b8b0">No review yet</span>
                            @endif
                            <div class="flex gap-2 mt-2">
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'pending')" class="booking-action-btn"
                                    style="background:rgba(212,163,80,0.12);color:#8a6020;border:1px solid rgba(212,163,80,0.4)">
                                    ↩ Pending
                                </button>
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')"
                                    class="booking-action-btn cancel-btn">✕ Cancel</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CANCELLED --}}
        <div id="section-cancelled" class="bookings-section hidden">
            @php $cancelled = $bookings->where('status', 'cancelled'); @endphp
            @if($cancelled->isEmpty())
                <div class="no-results">
                    <p class="font-medium mb-1">No cancelled bookings</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($cancelled as $booking)
                        <div class="booking-row" id="booking-row-{{ $booking->id }}">
                            <div class="booking-row-avatar" style="background:#e05252">
                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                            </div>
                            <div class="booking-row-user">
                                <p class="font-medium text-sm">{{ $booking->user->name }}</p>
                                <p class="text-xs" style="color:#8a7a6a">{{ $booking->user->email }}</p>
                            </div>
                            <div class="booking-row-activity">
                                <p class="font-medium text-sm">{{ $booking->schedule->activity->title }}</p>
                                <p class="text-xs" style="color:#8a7a6a">
                                    {{ ucfirst($booking->schedule->day_of_week) }} ·
                                    {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                    –
                                    {{ \Carbon\Carbon::parse($booking->schedule->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-xs" style="color:#a09890">
                                {{ $booking->created_at->diffForHumans() }}
                            </div>
                            <span class="text-xs px-3 py-1 rounded-full"
                                style="background:rgba(232,74,74,0.1);color:#A32D2D;border:1px solid rgba(232,74,74,0.3)">
                                Cancelled
                            </span>
                            <div class="flex gap-2 mt-2">
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'pending')" class="booking-action-btn"
                                    style="background:rgba(212,163,80,0.12);color:#8a6020;border:1px solid rgba(212,163,80,0.4)">
                                    ↩ Pending
                                </button>
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')"
                                    class="booking-action-btn confirm-btn">✓ Confirm</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            function switchTab(tab) {
                // إخفاء كل الـ sections
                document.querySelectorAll('.bookings-section').forEach(s => s.classList.add('hidden'));
                document.querySelectorAll('.booking-tab').forEach(t => t.classList.remove('active'));

                // إظهار الـ section المطلوب
                document.getElementById(`section-${tab}`).classList.remove('hidden');
                document.getElementById(`tab-${tab}`).classList.add('active');
            }

            async function updateBookingStatus(bookingId, status) {
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
                    const row = document.getElementById(`booking-row-${bookingId}`);
                    if (!row) return;

                    // عرّف من أي section عم يتحرك
                    const fromSection = row.closest('.bookings-section');
                    const fromStatus = fromSection?.id?.replace('section-', '');

                    const userName = row.querySelector('.booking-row-user p:first-child')?.textContent;
                    const userEmail = row.querySelector('.booking-row-user p:last-child')?.textContent;
                    const activityName = row.querySelector('.booking-row-activity p:first-child')?.textContent;
                    const activityTime = row.querySelector('.booking-row-activity p:last-child')?.textContent;
                    const timeAgo = row.querySelector('[style*="a09890"]')?.textContent;
                    const avatarLetter = row.querySelector('.booking-row-avatar')?.textContent?.trim();

                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s';

                    setTimeout(() => {
                        row.remove();

                        // تحديث الـ stats — ناقص من القديم وزيّد للجديد
                        const stats = {
                            pending: document.querySelectorAll('.stat-number')[0],
                            confirmed: document.querySelectorAll('.stat-number')[1],
                            cancelled: document.querySelectorAll('.stat-number')[2],
                        };

                        if (fromStatus && stats[fromStatus]) {
                            stats[fromStatus].textContent = Math.max(0, parseInt(stats[fromStatus].textContent) - 1);
                        }
                        if (stats[status]) {
                            stats[status].textContent = parseInt(stats[status].textContent) + 1;
                        }

                        // تحديث الـ pending tab badge
                        const pendingBadge = document.getElementById('pending-tab-count');

                        if (fromStatus === 'pending' && pendingBadge) {
                            // ناقص
                            const newCount = Math.max(0, parseInt(pendingBadge.textContent) - 1);
                            if (newCount === 0) pendingBadge.remove();
                            else pendingBadge.textContent = newCount;
                        }

                        if (status === 'pending') {
                            // زيّد
                            const badge = document.getElementById('pending-tab-count');
                            if (badge) {
                                badge.textContent = parseInt(badge.textContent) + 1;
                            } else {
                                const tabPending = document.getElementById('tab-pending');
                                const newBadge = document.createElement('span');
                                newBadge.className = 'tab-badge';
                                newBadge.id = 'pending-tab-count';
                                newBadge.textContent = '1';
                                tabPending.appendChild(newBadge);
                            }
                        }

                        // إذا الـ section فاضي — أظهر no results
                        if (fromSection) {
                            const remaining = fromSection.querySelectorAll('.booking-row');
                            if (remaining.length === 0) {
                                fromSection.innerHTML = `<div class="no-results"><p class="font-medium mb-1">No ${fromStatus} bookings</p></div>`;
                            }
                        }

                        // أضف الـ row للـ target section
                        const avatarColors = { pending: '#D4A350', confirmed: '#0F6E56', cancelled: '#e05252' };
                        const avatarColor = avatarColors[status] || '#D4A350';
                        const targetSection = document.getElementById(`section-${status}`);
                        const noResults = targetSection?.querySelector('.no-results');
                        if (noResults) noResults.remove();

                        let spaceDiv = targetSection?.querySelector('.space-y-3');
                        if (!spaceDiv) {
                            spaceDiv = document.createElement('div');
                            spaceDiv.className = 'space-y-3';
                            targetSection?.appendChild(spaceDiv);
                        }

                        // action buttons حسب الـ status الجديد
                        let actionBtns = '';
                        if (status === 'pending') {
                            actionBtns = `
                        <div class="flex gap-2">
                            <button onclick="updateBookingStatus(${bookingId}, 'confirmed')" class="booking-action-btn confirm-btn">✓ Confirm</button>
                            <button onclick="updateBookingStatus(${bookingId}, 'cancelled')" class="booking-action-btn cancel-btn">✕ Cancel</button>
                        </div>`;
                        } else if (status === 'confirmed') {
                            actionBtns = `
                        <span class="text-xs" style="color:#c0b8b0">No review yet</span>
                        <div class="flex gap-2 mt-2">
                            <button onclick="updateBookingStatus(${bookingId}, 'pending')" class="booking-action-btn" style="background:rgba(212,163,80,0.12);color:#8a6020;border:1px solid rgba(212,163,80,0.4)">↩ Pending</button>
                            <button onclick="updateBookingStatus(${bookingId}, 'cancelled')" class="booking-action-btn cancel-btn">✕ Cancel</button>
                        </div>`;
                        } else if (status === 'cancelled') {
                            actionBtns = `
                        <span class="text-xs px-3 py-1 rounded-full" style="background:rgba(232,74,74,0.1);color:#A32D2D;border:1px solid rgba(232,74,74,0.3)">Cancelled</span>
                        <div class="flex gap-2 mt-2">
                            <button onclick="updateBookingStatus(${bookingId}, 'pending')" class="booking-action-btn" style="background:rgba(212,163,80,0.12);color:#8a6020;border:1px solid rgba(212,163,80,0.4)">↩ Pending</button>
                            <button onclick="updateBookingStatus(${bookingId}, 'confirmed')" class="booking-action-btn confirm-btn">✓ Confirm</button>
                        </div>`;
                        }

                        const newRow = document.createElement('div');
                        newRow.className = 'booking-row';
                        newRow.id = `booking-row-${bookingId}`;
                        newRow.innerHTML = `
                    <div class="booking-row-avatar" style="background:${avatarColor}">${avatarLetter}</div>
                    <div class="booking-row-user">
                        <p class="font-medium text-sm">${userName}</p>
                        <p class="text-xs" style="color:#8a7a6a">${userEmail}</p>
                    </div>
                    <div class="booking-row-activity">
                        <p class="font-medium text-sm">${activityName}</p>
                        <p class="text-xs" style="color:#8a7a6a">${activityTime}</p>
                    </div>
                    <div class="text-xs" style="color:#a09890">${timeAgo}</div>
                    ${actionBtns}
                `;
                        newRow.style.opacity = '0';
                        spaceDiv.insertBefore(newRow, spaceDiv.firstChild);
                        setTimeout(() => {
                            newRow.style.transition = 'opacity 0.3s';
                            newRow.style.opacity = '1';
                        }, 50);

                    }, 300);
                }
            }

            function updateStatCount(status) {
                // ناقص الـ pending stat
                const pendingStat = document.querySelectorAll('.stat-number')[0];
                if (pendingStat) {
                    pendingStat.textContent = Math.max(0, parseInt(pendingStat.textContent) - 1);
                }

                // زيّد الـ confirmed أو cancelled stat
                if (status === 'confirmed') {
                    const confirmedStat = document.querySelectorAll('.stat-number')[1];
                    if (confirmedStat) confirmedStat.textContent = parseInt(confirmedStat.textContent) + 1;
                } else if (status === 'cancelled') {
                    const cancelledStat = document.querySelectorAll('.stat-number')[2];
                    if (cancelledStat) cancelledStat.textContent = parseInt(cancelledStat.textContent) + 1;
                }
            }
        </script>
    @endpush

</x-layouts.app-main>
