<x-layouts.app-main title="About">

    {{-- Styles --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    @endpush

    <section class="about-hero">
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <p class="section-label">Who we are</p>
            <h1 class="font-display text-white text-5xl font-bold mb-6">Built for Lebanon,<br>Powered by AI</h1>
            <p class="text-white/60 text-lg max-w-2xl mx-auto leading-relaxed">
                Activio is Lebanon's first platform that helps you discover, compare, and book local activities — all in
                one place.
            </p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-6 py-20">

        {{-- Mission --}}
        <div class="about-card mb-12">
            <h2 class="font-display text-3xl font-bold mb-4">Our Mission</h2>
            <p class="text-[#5a5751] leading-relaxed text-base">
                Lebanon has hundreds of sports academies, art studios, music schools, and fitness centers — but finding
                the right one has always been difficult. Activio was built to change that. We connect people with the
                activities that match their age, budget, and location, using intelligent search that understands what
                you're actually looking for.
            </p>
        </div>

        {{-- Why Activio --}}
        <h2 class="font-display text-3xl font-bold mb-8">Why Activio</h2>
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <div class="about-feature">
                <div class="feature-icon">🔍</div>
                <h3 class="font-display text-lg font-bold mb-2">AI-Powered Search</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">Find activities that truly match your needs — not just
                    keyword results.</p>
            </div>
            <div class="about-feature">
                <div class="feature-icon">📍</div>
                <h3 class="font-display text-lg font-bold mb-2">Local First</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">The locations and activities displayed on Activio are
                    sample data created by our team and do not represent real centers or schedules.</p>
            </div>
            <div class="about-feature">
                <div class="feature-icon">⚡</div>
                <h3 class="font-display text-lg font-bold mb-2">Book Instantly</h3>
                <p class="text-sm text-[#5a5751] leading-relaxed">No calls, no back and forth. Browse, choose, and book
                    in minutes.</p>
            </div>
        </div>

        {{-- Built in Lebanon --}}
        <div class="built-in-lebanon">
            <span class="text-4xl">🇱🇧</span>
            <h2 class="font-display text-2xl font-bold mt-4 mb-2">Built in Lebanon</h2>
            <p class="text-[#5a5751] text-sm max-w-md mx-auto leading-relaxed">
                Activio was created as a final year project by Lebanese students who believe local communities deserve
                better digital tools.
            </p>
        </div>

    </div>

</x-layouts.app-main>
