<x-layouts.app-main title="Center Dashboard">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/center/dashboard.css') }}">
        <style>
            .charts-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 40px;
            }
            .chart-card {
                background: #fff;
                border: 1px solid #E8E5DF;
                border-radius: 16px;
                padding: 20px 24px;
            }
            .chart-card-full {
                grid-column: 1 / -1;
            }
            .chart-title {
                font-family: 'Playfair Display', serif;
                font-size: 15px;
                font-weight: 700;
                color: #1a1a18;
                margin-bottom: 4px;
            }
            .chart-subtitle {
                font-size: 11px;
                color: #a09890;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                margin-bottom: 16px;
            }
            .chart-wrap {
                position: relative;
                height: 220px;
            }
            @media (max-width: 768px) {
                .charts-grid { grid-template-columns: 1fr; }
                .chart-card-full { grid-column: 1; }
            }
        </style>
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest mb-2" style="color:rgba(212,163,80,0.8)">Center Owner</p>
                <h1 class="font-display text-white text-3xl font-bold">My Dashboard</h1>
            </div>
            <button onclick="openCenterModal('add-center-modal', 'add-center-form')"
                class="search-btn px-5 py-2.5 text-sm">
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

        {{-- CHARTS --}}
        <h2 class="font-display text-2xl font-bold mb-6">Analytics</h2>

        <div class="charts-grid">

            {{-- Bookings per month --}}
            <div class="chart-card">
                <div class="chart-title">Bookings</div>
                <div class="chart-subtitle">Last 6 months</div>
                <div class="chart-wrap">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>

            {{-- Most popular activities --}}
            <div class="chart-card">
                <div class="chart-title">Popular Activities</div>
                <div class="chart-subtitle">By number of bookings</div>
                <div class="chart-wrap">
                    <canvas id="activitiesChart"></canvas>
                </div>
            </div>

            {{-- Revenue trend --}}
            <div class="chart-card chart-card-full">
                <div class="chart-title">Revenue</div>
                <div class="chart-subtitle">Confirmed bookings — last 6 months ($)</div>
                <div class="chart-wrap">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </div>

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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('js/center.js') }}"></script>
        <script>
            // ===== Chart Data from PHP =====
            const bookingLabels = @json($months->keys());
            const bookingData   = @json($months->values());

            const activityLabels = @json($popularActivities->pluck('title'));
            const activityData   = @json($popularActivities->pluck('total'));

            const revenueLabels  = @json($revenue->keys());
            const revenueData    = @json($revenue->values());

            const accent   = '#D4A350';
            const accentBg = 'rgba(212,163,80,0.12)';
            const dark     = '#1a1a18';
            const grid     = '#F0EDE6';

            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color       = '#a09890';

            // 1. Bookings per month — Bar
            new Chart(document.getElementById('bookingsChart'), {
                type: 'bar',
                data: {
                    labels: bookingLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingData,
                        backgroundColor: accentBg,
                        borderColor: accent,
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: grid },
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Popular activities — Horizontal Bar
            new Chart(document.getElementById('activitiesChart'), {
                type: 'bar',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: activityData,
                        backgroundColor: [
                            'rgba(212,163,80,0.8)',
                            'rgba(212,163,80,0.6)',
                            'rgba(212,163,80,0.45)',
                            'rgba(212,163,80,0.3)',
                            'rgba(212,163,80,0.15)',
                        ],
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: grid },
                        },
                        y: { grid: { display: false } }
                    }
                }
            });

            // 3. Revenue trend — Line
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: revenueData,
                        borderColor: accent,
                        backgroundColor: accentBg,
                        borderWidth: 2.5,
                        pointBackgroundColor: accent,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: grid },
                            ticks: { callback: v => '$' + v }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        </script>
        <script>
            async function updateBooking(bookingId, status) {
                const card  = document.getElementById(`pending-booking-${bookingId}`);
                const slide = card?.closest('.pending-slide');
                const res   = await fetch(`/booking/${bookingId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status }),
                });
                if (res.ok && slide) {
                    slide.style.opacity = '0';
                    slide.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        slide.remove();
                        const pendingStat = document.querySelector('.stat-number[style*="D4A350"]');
                        if (pendingStat) pendingStat.textContent = Math.max(0, parseInt(pendingStat.textContent) - 1);
                        const track = document.getElementById('pending-track');
                        if (track && track.children.length === 0) track.closest('.mb-12').remove();
                    }, 300);
                }
            }

            function scrollCarousel(trackId, direction) {
                const track = document.getElementById(trackId);
                if (!track) return;
                const slideWidth = track.querySelector('[class$="-slide"]')?.offsetWidth + 12 || 400;
                track.scrollBy({ left: direction * slideWidth, behavior: 'smooth' });
            }
        </script>
    @endpush

</x-layouts.app-main>
