<x-layouts.admin-app title="Centers">

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">All Centers <span style="color:#a09890;font-size:14px;font-weight:400">({{ $centers->total() }})</span></h2>
            <input type="text" class="admin-search" placeholder="Search centers..." oninput="filterTable(this.value, 'centers-table')">
        </div>
        <div style="overflow-x:auto">
            <table class="admin-table" id="centers-table">
                <thead>
                    <tr>
                        <th>Center</th>
                        <th>Owner</th>
                        <th>City</th>
                        <th>Activities</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($centers as $center)
                    <tr id="center-row-{{ $center->id }}">
                        <td>
                            <p class="font-medium text-sm">{{ $center->name }}</p>
                            <p class="text-xs" style="color:#8a7a6a">{{ $center->address }}</p>
                        </td>
                        <td>
                            <p class="text-sm">{{ $center->user->name }}</p>
                            <p class="text-xs" style="color:#8a7a6a">{{ $center->user->email }}</p>
                        </td>
                        <td class="text-sm">{{ $center->city }}</td>
                        <td class="text-sm">{{ $center->activities_count }}</td>
                        <td>
                            <span class="badge {{ $center->is_active ? 'badge-green' : 'badge-red' }}" id="center-status-{{ $center->id }}">
                                {{ $center->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="toggleCenter({{ $center->id }}, this)"
                                    class="admin-action-btn" id="center-toggle-{{ $center->id }}">
                                    {{ $center->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button onclick="confirmDelete('center', {{ $center->id }}, '{{ addslashes($center->name) }}')"
                                    class="admin-action-btn danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($centers->hasPages())
        <div class="flex justify-center gap-2 p-4">{{ $centers->links() }}</div>
        @endif
    </div>

    <div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:380px;width:100%;text-align:center">
            <h3 class="font-display text-lg font-bold mb-2">Are you sure?</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a" id="confirm-msg"></p>
            <div style="display:flex;gap:10px">
                <button onclick="closeModal()" style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Cancel</button>
                <button id="confirm-btn" style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Delete</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterTable(query, tableId) {
            document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
            });
        }

        async function toggleCenter(id, btn) {
            const res = await fetch(`/admin/centers/${id}/toggle`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            if (res.ok) {
                const data = await res.json();
                const badge = document.getElementById(`center-status-${id}`);
                badge.textContent = data.is_active ? 'Active' : 'Inactive';
                badge.className = `badge ${data.is_active ? 'badge-green' : 'badge-red'}`;
                btn.textContent = data.is_active ? 'Deactivate' : 'Activate';
            }
        }

        function confirmDelete(type, id, name) {
            document.getElementById('confirm-msg').textContent = `Delete "${name}"? This cannot be undone.`;
            document.getElementById('confirm-modal').style.display = 'flex';
            document.getElementById('confirm-btn').onclick = () => deleteItem(type, id);
        }

        async function deleteItem(type, id) {
            const res = await fetch(`/admin/${type}s/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            if (res.ok) {
                closeModal();
                const row = document.getElementById(`${type}-row-${id}`);
                if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
            }
        }

        function closeModal() { document.getElementById('confirm-modal').style.display = 'none'; }
        document.getElementById('confirm-modal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    </script>
    @endpush

</x-layouts.admin-app>
