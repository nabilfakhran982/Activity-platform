<x-layouts.admin-app title="My Profile">

    <div class="grid md:grid-cols-2 gap-6 max-w-2xl">

        {{-- Personal Info --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Personal Info</h2>
            </div>
            <div class="p-5">
                <form id="profile-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-input" value="{{ auth()->user()->name }}">
                        <p class="error-msg" id="profile-form-err-name"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="{{ auth()->user()->email }}" disabled style="opacity:0.5;cursor:not-allowed">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" value="{{ auth()->user()->phone }}" placeholder="+961 70 000 000">
                    </div>
                    <button type="submit" class="search-btn w-full py-3 text-sm">Save Changes</button>
                    <p id="profile-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">✓ Updated successfully</p>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Change Password</h2>
            </div>
            <div class="p-5">
                <form id="password-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                        <p class="error-msg" id="password-form-err-current_password"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••">
                        <p class="error-msg" id="password-form-err-password"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                    </div>
                    <button type="submit" class="search-btn w-full py-3 text-sm">Update Password</button>
                    <p id="password-success" class="text-xs text-center mt-2 hidden" style="color:#0F6E56">✓ Password updated</p>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.getElementById('profile-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors('profile-form');
            const res = await ajaxPost('/profile/update', new FormData(this));
            if (res.ok) {
                document.getElementById('profile-success').classList.remove('hidden');
                setTimeout(() => document.getElementById('profile-success').classList.add('hidden'), 3000);
            } else {
                const data = await res.json();
                if (data.errors) showErrors('profile-form', data.errors);
            }
        });

        document.getElementById('password-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors('password-form');
            const res = await ajaxPost('/profile/password', new FormData(this));
            if (res.ok) {
                this.reset();
                document.getElementById('password-success').classList.remove('hidden');
                setTimeout(() => document.getElementById('password-success').classList.add('hidden'), 3000);
            } else {
                const data = await res.json();
                if (data.errors) showErrors('password-form', data.errors);
            }
        });
    </script>
    @endpush

</x-layouts.admin-app>
