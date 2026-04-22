<x-layouts.admin-app title="Activities">

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">All Activities <span style="color:#a09890;font-size:14px;font-weight:400">({{ $activities->total() }})</span></h2>
            <input type="text" class="admin-search" placeholder="Search activities..." oninput="filterTable(this.value)">
        </div>
        <div style="overflow-x:auto">
            <table class="admin-table" id="activities-table">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Center</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    <tr id="activity-row-{{ $activity->id }}">
                        <td>
                            <p class="font-medium text-sm">{{ $activity->title }}</p>
                            <p class="text-xs" style="color:#8a7a6a">
                                {{ $activity->level ? ucfirst($activity->level) : 'Any level' }}
                                @if($activity->min_age || $activity->max_age)
                                    · Ages {{ $activity->min_age ?? '0' }}{{ $activity->max_age ? '–'.$activity->max_age : '+' }}
                                @endif
                            </p>
                        </td>
                        <td class="text-sm">{{ $activity->center->name }}</td>
                        <td>{{ $activity->category->icon }} {{ $activity->category->name }}</td>
                        <td class="text-sm font-medium">${{ number_format($activity->price, 0) }}</td>
                        <td>
                            <span class="badge {{ $activity->is_active ? 'badge-green' : 'badge-red' }}" id="activity-status-{{ $activity->id }}">
                                {{ $activity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="toggleActivity({{ $activity->id }}, this)" class="admin-action-btn">
                                    {{ $activity->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button onclick="confirmDeleteActivity({{ $activity->id }}, '{{ addslashes($activity->title) }}')"
                                    class="admin-action-btn danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
        <div class="flex justify-center gap-2 p-4">{{ $activities->links() }}</div>
        @endif
    </div>

    <div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:380px;width:100%;text-align:center">
            <h3 class="font-display text-lg font-bold mb-2">Delete Activity?</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a" id="confirm-msg"></p>
            <div style="display:flex;gap:10px">
                <button onclick="closeModal()" style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Cancel</button>
                <button id="confirm-btn" style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Delete</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterTable(query) {
            document.querySelectorAll('#activities-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
            });
        }

        async function toggleActivity(id, btn) {
            const res = await fetch(`/activity/${id}/toggle-active`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            if (res.ok) {
                const data = await res.json();
                const badge = document.getElementById(`activity-status-${id}`);
                badge.textContent = data.is_active ? 'Active' : 'Inactive';
                badge.className = `badge ${data.is_active ? 'badge-green' : 'badge-red'}`;
                btn.textContent = data.is_active ? 'Deactivate' : 'Activate';
            }
        }

        function confirmDeleteActivity(id, name) {
            document.getElementById('confirm-msg').textContent = `Delete "${name}"? This cannot be undone.`;
            document.getElementById('confirm-modal').style.display = 'flex';
            document.getElementById('confirm-btn').onclick = async () => {
                const res = await fetch(`/activity/${id}/delete`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    closeModal();
                    const row = document.getElementById(`activity-row-${id}`);
                    if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
                }
            };
        }

        function closeModal() { document.getElementById('confirm-modal').style.display = 'none'; }
        document.getElementById('confirm-modal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    </script>
    @endpush

</x-layouts.admin-app>
