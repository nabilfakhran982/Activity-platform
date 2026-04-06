<x-layouts.app-main title="Privacy Policy">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/privacy.css') }}">
    @endpush

    <section class="privacy-hero">
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <p class="section-label">Legal</p>
            <h1 class="font-display text-white text-5xl font-bold mb-4">Privacy Policy</h1>
            <p class="text-white/60 text-sm">Last updated: {{ date('F Y') }}</p>
        </div>
    </section>

    <div class="max-w-3xl mx-auto px-6 py-16">
        <div class="privacy-card">

            <div class="privacy-section">
                <h2 class="privacy-heading">1. Information We Collect</h2>
                <p class="privacy-text">When you register on Activio, we collect your name, email address, and phone
                    number. When you book an activity, we store the details of that booking including the activity,
                    center, and date.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">2. How We Use Your Information</h2>
                <p class="privacy-text">We use your information to manage your bookings, send you confirmations, and
                    improve your experience on the platform. We do not sell your personal data to third parties.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">3. Data Storage</h2>
                <p class="privacy-text">Your data is stored securely on our servers. We take reasonable measures to
                    protect your personal information from unauthorized access, loss, or misuse.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">4. Cookies</h2>
                <p class="privacy-text">Activio uses cookies to keep you logged in and remember your preferences. You
                    can disable cookies in your browser settings, but some features may not work correctly.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">5. Third-Party Services</h2>
                <p class="privacy-text">We may use third-party services such as Google Fonts and analytics tools. These
                    services may collect anonymous usage data in accordance with their own privacy policies.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">6. Your Rights</h2>
                <p class="privacy-text">You have the right to access, update, or delete your personal data at any time.
                    To make a request, please contact us through our Contact page.</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-heading">7. Changes to This Policy</h2>
                <p class="privacy-text">We may update this Privacy Policy from time to time. Any changes will be posted
                    on this page with an updated date.</p>
            </div>

            <div class="privacy-contact">
                <p class="text-sm text-[#5a5751]">Questions about this policy?</p>
                <a href="{{ route('contact') }}" class="search-btn inline-block px-6 py-2 text-sm mt-3">Contact Us</a>
            </div>

        </div>
    </div>

</x-layouts.app-main>
