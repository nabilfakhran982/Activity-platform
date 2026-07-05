@if(Auth::check())
    @php
        $credits = Auth::user()->getCredits();
        $balance = $credits->balance;
        $isLow = $balance < 3;
    @endphp

    <div class="flex items-center gap-3">
        <a href="{{ route('credits.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold text-gray-900">
                {{ number_format($balance, 0) }} Credits
            </span>
            @if($isLow)
                <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-semibold">Low</span>
            @endif
        </a>

        @if($isLow)
            <a href="{{ route('credits.purchase') }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                Buy
            </a>
        @endif
    </div>
@endif
