<x-layouts.app-main title="Center Dashboard">

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/center/dashboard.css') }}">
    @endpush

    {{-- HEADER --}}
    <div class="page-header">
        <div class="max-w-6xl mx-auto px-6 relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest mb-2" style="color:rgba(212,163,80,0.8)">Center Owner</p>
                <h1 class="font-display text-white text-3xl font-bold">My Dashboard</h1>
            </div>
            <button onclick="document.getElementById('add-center-modal').style.display='flex'"
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

        {{-- CENTERS --}}
        <h2 class="font-display text-2xl font-bold mb-6">My Centers</h2>

        @if($centers->isEmpty())
        <div class="no-results">
            <p class="text-lg font-medium text-[#1a1a18] mb-2">No centers yet</p>
            <p class="text-sm">Click "Add Center" to get started</p>
        </div>
        @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
            @foreach($centers as $center)
            <div class="center-card">
                <div class="center-card-header">
                    <h3 class="font-display text-lg font-bold">{{ $center->name }}</h3>
                    <span class="status-badge {{ $center->is_active ? 'active' : 'inactive' }}">
                        {{ $center->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-xs mb-3" style="color:#8a7a6a">{{ $center->address }}, {{ $center->city }}</p>
                <p class="text-xs mb-4" style="color:#8a7a6a">{{ $center->activities_count }} activities</p>
                <div class="flex gap-2">
                    <a href="{{ route('center.activities', $center->id) }}"
                        class="dashboard-btn flex-1 text-center">
                        Manage Activities
                    </a>
                    <a href="{{ route('center.bookings', $center->id) }}"
                        class="dashboard-btn-outline flex-1 text-center">
                        Bookings
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ADD CENTER MODAL --}}
    <div id="add-center-modal" class="hidden fixed inset-0 z-50 px-4"
        style="background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
        <div class="bg-white rounded-2xl w-full max-w-lg p-8 relative" style="max-height:90vh; overflow-y:auto">
            <button onclick="document.getElementById('add-center-modal').style.display='none'"
                class="absolute top-4 right-4 text-[#a09890] hover:text-[#1a1a18] text-xl font-bold">✕</button>

            <h2 class="font-display text-2xl font-bold mb-6">Add New Center</h2>

            <form id="add-center-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">Center Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Dragon Academy">
                    <p class="error-msg" id="err-name"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="Tell us about your center..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-input" placeholder="e.g. Rue Gouraud, Gemmayzeh">
                    <p class="error-msg" id="err-address"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <select name="city" class="form-input">
                        <option value="Beirut">Beirut</option>
                        <option value="Jounieh">Jounieh</option>
                        <option value="Tripoli">Tripoli</option>
                        <option value="Sidon">Sidon</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-input" placeholder="+961 70 000 000">
                </div>
                <button type="submit" class="search-btn w-full py-3 text-sm mt-2">Add Center</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add center modal close on backdrop
        document.getElementById('add-center-modal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        // Add center form submit
        document.getElementById('add-center-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

            const formData = new FormData(this);

            const res = await fetch('{{ route("center.register") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (res.ok) {
                window.location.reload();
            } else {
                const data = await res.json();
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const errEl = document.getElementById('err-' + field);
                        const input = document.querySelector(`[name=${field}]`);
                        if (errEl) errEl.textContent = messages[0];
                        if (input) input.classList.add('input-error');
                    }
                }
            }
        });
    </script>
    @endpush

</x-layouts.app-main>