<footer class="bg-[#1a1a18] text-white/40 py-12 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start gap-8 mb-10">
            <div>
                <div class="font-display text-white text-xl font-bold mb-3">
                    Acti<span style="color:#D4A350">vio</span>
                </div>
                <p class="text-sm max-w-xs leading-relaxed">
                    Lebanon's first AI-powered platform to discover, compare, and book local activities.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-x-16 gap-y-3 text-sm">
                <a href="{{ route('activities') }}" class="hover:text-white/70 transition-colors">Browse Activities</a>
                <a href="{{ route('for-centers') }}" class="hover:text-white/70 transition-colors">For Centers</a>
                <a href="#" class="hover:text-white/70 transition-colors">How it works</a>
                <a href="{{ route('about') }}" class="hover:text-white/70 transition-colors">About</a>
                <a href="{{ route('contact') }}" class="hover:text-white/70 transition-colors">Contact</a>
                <a href="{{ route('privacy') }}" class="hover:text-white/70 transition-colors">Privacy</a>
            </div>
        </div>
        <div class="border-t border-white/10 pt-8 text-xs">
            © {{ date('Y') }} Activio. Built in Lebanon 🇱🇧
        </div>
    </div>
</footer>
