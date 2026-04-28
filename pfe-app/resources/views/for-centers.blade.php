<x-layouts.app-main title="For Centers">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/for-centers.css') }}">
    @endpush

    {{-- Hero --}}
    <section class="fc-hero relative overflow-hidden">

        {{-- VIDEO BACKGROUND --}}
        <div style="position:absolute;inset:0;z-index:0">
            <video
                autoplay
                muted
                loop
                playsinline
                style="width:100%;height:100%;object-fit:cover;object-position:center"
            >
                <source src="{{ asset('videos/for_centers_hero.mp4') }}" type="video/mp4">
            </video>
            {{-- Overlay --}}
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(26,26,24,0.65) 0%, rgba(26,26,24,0.55) 50%, rgba(26,26,24,0.75) 100%)"></div>
        </div>

        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <p class="section-label">For Center Owners</p>
            <h1 class="font-display text-white text-5xl md:text-6xl font-bold mb-6">
                Grow your center<br>with Activio
            </h1>
            <p class="text-white/60 text-lg max-w-2xl mx-auto leading-relaxed mb-10">
                Join Lebanon's first AI-powered activity platform and connect with hundreds of people looking for
                exactly what you offer.
            </p>
            <button onclick="openCenterModal('center-modal', 'center-form')"
                class="search-btn inline-block px-8 py-3 text-sm font-medium">
                Register your center
            </button>
        </div>
    </section>

    {{-- Stats --}}
    <div class="max-w-5xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-20">
            <div class="stat-card">
                <div class="stat-number">{{ $usersCount }}+</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $categoriesCount }}</div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div class="stat-label">Free to Join</div>
            </div>
        </div>

        {{-- Why Activio --}}
        <div class="text-center mb-12">
            <p class="section-label-dark">Why join us</p>
            <h2 class="font-display text-4xl font-bold">Everything you need to grow</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-20">
            <div class="fc-feature">
                <div class="fc-icon">📣</div>
                <div>
                    <h3 class="font-display text-lg font-bold mb-2">Reach more people</h3>
                    <p class="text-sm text-[#5a5751] leading-relaxed">Get discovered by users actively searching for
                        activities in your area and category.</p>
                </div>
            </div>
            <div class="fc-feature">
                <div class="fc-icon">📅</div>
                <div>
                    <h3 class="font-display text-lg font-bold mb-2">Manage your schedule</h3>
                    <p class="text-sm text-[#5a5751] leading-relaxed">Add your activities, set schedules, and manage
                        bookings all from one simple dashboard.</p>
                </div>
            </div>
            <div class="fc-feature">
                <div class="fc-icon">⭐</div>
                <div>
                    <h3 class="font-display text-lg font-bold mb-2">Build your reputation</h3>
                    <p class="text-sm text-[#5a5751] leading-relaxed">Collect reviews from real students and build trust
                        with new customers automatically.</p>
                </div>
            </div>
            <div class="fc-feature">
                <div class="fc-icon">🆓</div>
                <div>
                    <h3 class="font-display text-lg font-bold mb-2">Free to get started</h3>
                    <p class="text-sm text-[#5a5751] leading-relaxed">No setup fees, no monthly charges. Create your
                        profile and start receiving bookings today.</p>
                </div>
            </div>
        </div>

        {{-- How it works --}}
        <div class="text-center mb-12">
            <p class="section-label-dark">Simple process</p>
            <h2 class="font-display text-4xl font-bold">How it works</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-20">
            <div class="fc-step">
                <div class="step-number">1</div>
                <h3 class="font-display text-lg font-bold mb-2">Create your account</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">Sign up and register your center to become a center owner in minutes.</p>
            </div>
            <div class="fc-step">
                <div class="step-number">2</div>
                <h3 class="font-display text-lg font-bold mb-2">Add your activities</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">List your classes, set prices, schedules, and age groups.</p>
            </div>
            <div class="fc-step">
                <div class="step-number">3</div>
                <h3 class="font-display text-lg font-bold mb-2">Start receiving bookings</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">Users find you, book instantly, and you manage
                    everything from your dashboard.</p>
            </div>
        </div>

        {{-- CTA --}}
        <div class="fc-cta">
            <div class="relative z-10 text-center">
                <h2 class="font-display text-3xl font-bold text-white mb-4">Ready to get started?</h2>
                <p class="text-white/60 text-sm mb-8 max-w-md mx-auto">Join Activio today and put your center in front
                    of the right people.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="openCenterModal('center-modal', 'center-form')"
                        class="search-btn inline-block px-8 py-3 text-sm font-medium">
                        Register your center
                    </button>
                    <a href="{{ route('contact') }}" class="fc-outline-btn inline-block px-8 py-3 text-sm font-medium">
                        Contact us first
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL --}}
    <x-modal id="center-modal" title="Register your center">
        <x-center.center-form form-id="center-form" />
    </x-modal>

    @push('scripts')
        <script src="{{ asset('js/center.js') }}"></script>
        <script>
            document.getElementById('center-form')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                document.querySelectorAll('.error-msg').forEach(el => el.remove());
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

                if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                if (res.ok) {
                    window.location.href = '{{ route("center.dashboard") }}';
                } else {
                    const data = await res.json();
                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('input-error');
                                const msg = document.createElement('p');
                                msg.className = 'error-msg';
                                msg.textContent = messages[0];
                                input.after(msg);
                            }
                        }
                        const firstError = document.querySelector('.error-msg');
                        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        </script>
    @endpush

</x-layouts.app-main>
