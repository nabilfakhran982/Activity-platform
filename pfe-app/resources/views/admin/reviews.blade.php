<x-layouts.admin-app title="Reviews">

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">All Reviews <span style="color:#a09890;font-size:14px;font-weight:400">({{ $reviews->total() }})</span></h2>
            <input type="text" class="admin-search" placeholder="Search reviews..." oninput="filterTable(this.value)">
        </div>
        <div style="overflow-x:auto">
            <table class="admin-table" id="reviews-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                    <tr id="review-row-{{ $review->id }}">
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="admin-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                                <p class="font-medium text-sm">{{ $review->user->name }}</p>
                            </div>
                        </td>
                        <td>
                            <p class="text-sm">{{ $review->booking->schedule->activity->title }}</p>
                            <p class="text-xs" style="color:#8a7a6a">{{ $review->booking->schedule->activity->center->name }}</p>
                        </td>
                        <td>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color:{{ $i <= $review->rating ? '#D4A350' : '#E8E5DF' }};font-size:14px">★</span>
                                @endfor
                            </div>
                        </td>
                        <td class="text-sm" style="color:#5a5751;max-width:200px">
                            {{ $review->comment ? Str::limit($review->comment, 60) : '—' }}
                        </td>
                        <td class="text-xs" style="color:#a09890">{{ $review->created_at->format('d M Y') }}</td>
                        <td>
                            <button onclick="confirmDeleteReview({{ $review->id }})"
                                class="admin-action-btn danger">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
        <div class="flex justify-center gap-2 p-4">{{ $reviews->links() }}</div>
        @endif
    </div>

    <div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:380px;width:100%;text-align:center">
            <h3 class="font-display text-lg font-bold mb-2">Delete Review?</h3>
            <p class="text-sm mb-6" style="color:#8a7a6a">This action cannot be undone.</p>
            <div style="display:flex;gap:10px">
                <button onclick="closeModal()" style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Cancel</button>
                <button id="confirm-btn" style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">Delete</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterTable(query) {
            document.querySelectorAll('#reviews-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
            });
        }

        function confirmDeleteReview(id) {
            document.getElementById('confirm-modal').style.display = 'flex';
            document.getElementById('confirm-btn').onclick = async () => {
                const res = await fetch(`/admin/reviews/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    closeModal();
                    const row = document.getElementById(`review-row-${id}`);
                    if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
                }
            };
        }

        function closeModal() { document.getElementById('confirm-modal').style.display = 'none'; }
        document.getElementById('confirm-modal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    </script>
    @endpush

</x-layouts.admin-app>
