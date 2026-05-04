<x-layouts.admin-app title="Dashboard">

    <style>
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .chart-card {
            background: #fff;
            border: 1px solid #E8E5DF;
            border-radius: 16px;
            padding: 20px 24px;
        }
        .chart-card-full { grid-column: 1 / -1; }
        .chart-title {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 700;
            color: #1a1a18;
            margin-bottom: 2px;
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
            height: 200px;
        }
        @media (max-width: 768px) {
            .charts-grid { grid-template-columns: 1fr; }
            .chart-card-full { grid-column: 1; }
        }
    </style>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(212,163,80,0.12)">
                <span class="material-icons" style="color:#D4A350">people</span>
            </div>
            <div class="min-w-0">
                <div class="admin-stat-value">{{ $stats['users'] }}</div>
                <div class="admin-stat-label">Users</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(93,202,165,0.12)">
                <span class="material-icons" style="color:#0F6E56">business</span>
            </div>
            <div class="min-w-0">
                <div class="admin-stat-value">{{ $stats['centers'] }}</div>
                <div class="admin-stat-label">Centers</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(99,102,241,0.12)">
                <span class="material-icons" style="color:#4F46E5">fitness_center</span>
            </div>
            <div class="min-w-0">
                <div class="admin-stat-value">{{ $stats['activities'] }}</div>
                <div class="admin-stat-label">Activities</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(232,74,74,0.10)">
                <span class="material-icons" style="color:#e05252">event</span>
            </div>
            <div class="min-w-0">
                <div class="admin-stat-value">{{ $stats['bookings'] }}</div>
                <div class="admin-stat-label">Bookings</div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="charts-grid">

        {{-- Bookings per month --}}
        <div class="chart-card">
            <div class="chart-title">Bookings</div>
            <div class="chart-subtitle">Last 6 months</div>
            <div class="chart-wrap">
                <canvas id="bookingsChart"></canvas>
            </div>
        </div>

        {{-- Popular categories --}}
        <div class="chart-card">
            <div class="chart-title">Popular Categories</div>
            <div class="chart-subtitle">By number of bookings</div>
            <div class="chart-wrap">
                <canvas id="categoriesChart"></canvas>
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

    {{-- RECENT --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- Recent Users --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Recent Users</h2>
                <a href="{{ route('admin.users') }}" class="admin-action-btn">View all</a>
            </div>
            @if($recentUsers->isEmpty())
                <div class="admin-no-results">No users yet</div>
            @else
            <div class="admin-table-wrapper">
                <table class="admin-table" style="min-width:400px">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="admin-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-medium text-sm">{{ $user->name }}</p>
                                        <p class="text-xs" style="color:#8a7a6a">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-red' : ($user->role === 'center_owner' ? 'badge-gold' : 'badge-gray') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="text-xs" style="color:#a09890">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Recent Bookings --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Recent Bookings</h2>
                <a href="{{ route('admin.bookings') }}" class="admin-action-btn">View all</a>
            </div>
            @if($recentBookings->isEmpty())
                <div class="admin-no-results">No recent bookings</div>
            @else
            <div class="admin-table-wrapper">
                <table class="admin-table" style="min-width:400px">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td class="font-medium text-sm">{{ $booking->user->name }}</td>
                            <td class="text-xs" style="color:#8a7a6a">{{ $booking->schedule->activity->title }}</td>
                            <td>
                                <span class="badge {{ $booking->status === 'confirmed' ? 'badge-green' : ($booking->status === 'cancelled' ? 'badge-red' : 'badge-gold') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const accent   = '#D4A350';
        const accentBg = 'rgba(212,163,80,0.12)';
        const grid     = '#F0EDE6';

        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.color       = '#a09890';

        // 1. Bookings per month — Bar
        new Chart(document.getElementById('bookingsChart'), {
            type: 'bar',
            data: {
                labels: @json($months->keys()),
                datasets: [{
                    label: 'Bookings',
                    data: @json($months->values()),
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
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: grid } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Popular categories — Horizontal Bar
        new Chart(document.getElementById('categoriesChart'), {
            type: 'bar',
            data: {
                labels: @json($popularCategories->pluck('category')),
                datasets: [{
                    label: 'Bookings',
                    data: @json($popularCategories->pluck('total')),
                    backgroundColor: [
                        'rgba(212,163,80,0.85)',
                        'rgba(212,163,80,0.65)',
                        'rgba(212,163,80,0.48)',
                        'rgba(212,163,80,0.32)',
                        'rgba(212,163,80,0.18)',
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
                    x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: grid } },
                    y: { grid: { display: false } }
                }
            }
        });

        // 3. Revenue trend — Line
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenue->keys()),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenue->values()),
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
    @endpush

</x-layouts.admin-app>
