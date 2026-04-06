<x-layouts.app-main title="Contact">

    {{-- Styles --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    @endpush

    <section class="contact-hero">
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <p class="section-label">Get in touch</p>
            <h1 class="font-display text-white text-5xl font-bold mb-4">Contact Us</h1>
            <p class="text-white/60 text-base max-w-xl mx-auto">
                Have a question or want to list your center on Activio? We'd love to hear from you.
            </p>
        </div>
    </section>

    <div class="max-w-2xl mx-auto px-6 py-16">

        @if(session('success'))
            <div class="success-banner mb-8">
                <button class="cancel-btn">X</button>
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="contact-card">
            <form id="contact-form">
                @csrf

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input @error('name') input-error @enderror"
                        placeholder="Your name" value="{{ old('name') }}">
                    @error('name')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input @error('email') input-error @enderror"
                        placeholder="your@email.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-input @error('subject') input-error @enderror"
                        placeholder="What's this about?" value="{{ old('subject') }}">
                    @error('subject')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" rows="5" class="form-input @error('message') input-error @enderror"
                        placeholder="Write your message here...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="search-btn w-full py-3 text-sm font-medium">
                    Send Message
                </button>

            </form>
        </div>

    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script>
            document.getElementById('contact-form').addEventListener('submit', async function (e) {
                e.preventDefault();

                // مسح الأخطاء القديمة
                document.querySelectorAll('.error-msg').forEach(el => el.remove());
                document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

                const formData = new FormData(this);

                const res = await fetch('{{ route("contact.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (res.ok) {
                    const data = await res.json();

                    // عرض الـ success banner
                    const banner = document.createElement('div');
                    banner.className = 'success-banner mb-8';
                    banner.innerHTML = `<button class="cancel-btn">X</button> ✅ Your message has been sent successfully!`;
                    document.querySelector('.contact-card').before(banner);

                    // مسح الـ form
                    this.reset();

                    // scroll للـ banner
                    banner.scrollIntoView({ behavior: 'smooth', block: 'center' });

                } else {
                    const data = await res.json();

                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.querySelector(`[name=${field}]`);
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

        <script>
            // مسح الـ banner
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('cancel-btn')) {
                    e.target.closest('.success-banner').remove();
                }
            });
        </script>
    @endpush

</x-layouts.app-main>
