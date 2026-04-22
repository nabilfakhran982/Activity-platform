<x-layouts.admin-app title="Dashboard">

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(212,163,80,0.12)">
                <span class="material-icons" style="color:#D4A350">people</span>
            </div>
            <div>
                <div class="admin-stat-value" id="stat-users">{{ $stats['users'] }}</div>
                <div class="admin-stat-label">Users</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(93,202,165,0.12)">
                <span class="material-icons" style="color:#0F6E56">business</span>
            </div>
            <div>
                <div class="admin-stat-value" id="stat-centers">{{ $stats['centers'] }}</div>
                <div class="admin-stat-label">Centers</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(99,102,241,0.12)">
                <span class="material-icons" style="color:#4F46E5">fitness_center</span>
            </div>
            <div>
                <div class="admin-stat-value" id="stat-activities">{{ $stats['activities'] }}</div>
                <div class="admin-stat-label">Activities</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(232,74,74,0.10)">
                <span class="material-icons" style="color:#e05252">event</span>
            </div>
            <div>
                <div class="admin-stat-value" id="stat-bookings">{{ $stats['bookings'] }}</div>
                <div class="admin-stat-label">Bookings</div>
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
            <table class="admin-table">
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
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="text-xs" style="color:#a09890">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Recent Bookings --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Recent Bookings</h2>
                <a href="{{ route('admin.bookings') }}" class="admin-action-btn">View all</a>
            </div>
            <table class="admin-table">
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
    </div>

</x-layouts.admin-app>
