<x-layouts.admin-app title="Users">

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">All Users <span style="color:#a09890;font-size:14px;font-weight:400">({{ $users->total() }})</span></h2>
            <input type="text" class="admin-search" placeholder="Search users..." id="search-input"
                oninput="filterTable(this.value)">
        </div>
        <div style="overflow-x:auto">
            <table class="admin-table" id="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr id="user-row-{{ $user->id }}">
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
                        <td class="text-xs" style="color:#8a7a6a">{{ $user->phone ?? '—' }}</td>
                        <td class="text-xs" style="color:#a09890">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}" id="user-status-{{ $user->id }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                @if($user->role !== 'admin')
                                <button onclick="toggleUser({{ $user->id }}, this)"
                                    class="admin-action-btn" id="user-toggle-{{ $user->id }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="admin-action-btn danger">Delete</button>
                                @else
                                <span class="text-xs" style="color:#c0b8b0">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="flex justify-center gap-2 p-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Confirm Modal --}}
    <div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:380px;width:100%;text-align:center">
            <h3 class="font-display text-lg font-bold mb-2" id="confirm-title">Are you sure?</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a" id="confirm-msg"></p>
            <div style="display:flex;gap:10px">
                <button onclick="closeModal()"
                    style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">
                    Cancel
                </button>
                <button id="confirm-btn"
                    style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">
                    Delete
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterTable(query) {
            const rows = document.querySelectorAll('#users-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
            });
        }

        async function toggleUser(userId, btn) {
            const res = await fetch(`/admin/users/${userId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            if (res.ok) {
                const data = await res.json();
                const statusBadge = document.getElementById(`user-status-${userId}`);
                if (data.is_active) {
                    statusBadge.textContent = 'Active';
                    statusBadge.className = 'badge badge-green';
                    btn.textContent = 'Deactivate';
                } else {
                    statusBadge.textContent = 'Inactive';
                    statusBadge.className = 'badge badge-red';
                    btn.textContent = 'Activate';
                }
            }
        }

        function confirmDeleteUser(userId, name) {
            document.getElementById('confirm-msg').textContent = `Delete "${name}"? This cannot be undone.`;
            document.getElementById('confirm-modal').style.display = 'flex';
            document.getElementById('confirm-btn').onclick = () => deleteUser(userId);
        }

        async function deleteUser(userId) {
            const res = await fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            if (res.ok) {
                closeModal();
                const row = document.getElementById(`user-row-${userId}`);
                if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
            }
        }

        function closeModal() {
            document.getElementById('confirm-modal').style.display = 'none';
        }

        document.getElementById('confirm-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
    @endpush

</x-layouts.admin-app>
