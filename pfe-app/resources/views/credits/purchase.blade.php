@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="mb-12">
        <h1 class="text-4xl font-bold mb-2">Search Credits</h1>
        <p class="text-gray-600">Manage your credits and purchase more to continue searching</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        {{-- Current Balance --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <p class="text-gray-600 text-sm mb-2">Current Balance</p>
                <p class="text-4xl font-bold text-blue-600">{{ number_format($userStats['balance'], 0) }}</p>
                <p class="text-gray-600 text-sm mt-4">Searches remaining: {{ $userStats['searches_remaining'] }}</p>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-gray-600 text-sm mb-2">Lifetime Stats</p>
                    <div class="text-sm">
                        <p>Purchased: <span class="font-semibold">{{ number_format($userStats['lifetime_purchased'], 0) }}</span></p>
                        <p>Used: <span class="font-semibold">{{ number_format($userStats['lifetime_used'], 0) }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Credit Packages --}}
        <div class="md:col-span-2">
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($packages as $package)
                <div class="bg-white rounded-lg p-6 border border-gray-200 hover:border-blue-400 transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $package['credits'] }}</p>
                            <p class="text-gray-600 text-sm">Credits</p>
                        </div>
                        @if($package['id'] == 3)
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">Popular</span>
                        @endif
                    </div>

                    <p class="text-3xl font-bold text-gray-900 mb-2">${{ number_format($package['amount'], 2) }}</p>
                    <p class="text-gray-600 text-sm mb-4">{{ number_format($package['amount'] / $package['credits'], 3) }}¢ per credit</p>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition"
                        onclick="selectPackage({{ $package['id'] }})">
                        Buy Now
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Stripe Payment Modal --}}
<div id="payment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-6">Complete Payment</h2>

        {{-- Stripe Card Element --}}
        <form id="payment-form" onsubmit="handlePayment(event)">
            <div id="card-element" class="border border-gray-300 rounded-lg p-3 mb-4"></div>
            <input type="hidden" id="payment-intent-id" />

            <div id="card-errors" class="text-red-600 text-sm mb-4"></div>

            <div class="mb-6">
                <p class="text-gray-600 text-sm">
                    Credits: <span id="credits-amount" class="font-bold">0</span>
                </p>
                <p class="text-gray-600 text-sm">
                    Amount: $<span id="amount-display" class="font-bold">0.00</span>
                </p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition" id="submit-btn">
                Pay Now
            </button>
        </form>

        <button class="w-full mt-3 text-gray-600 hover:text-gray-900 font-semibold py-2" onclick="closePaymentModal()">
            Cancel
        </button>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    console.log('Stripe key from config:', '{{ config("services.stripe.public") }}');
    console.log('Packages data:', @json($packages));
    const stripe = Stripe('{{ config("services.stripe.public") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    let selectedPackageId = null;
    const creditPackages = @json($packages);

    cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        displayError.textContent = event.error ? event.error.message : '';
    });

    function selectPackage(packageId) {
        selectedPackageId = packageId;
        const pkg = creditPackages.find(p => p.id == packageId);
        if (!pkg) {
            alert('Selected package not found.');
            return;
        }

        document.getElementById('credits-amount').textContent = pkg.credits;
        document.getElementById('amount-display').textContent = Number(pkg.amount).toFixed(2);

        document.getElementById('payment-modal').classList.remove('hidden');

        // Create payment intent
        fetch('{{ route("payment.create-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ package_id: packageId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('payment-intent-id').value = data.payment_intent_id;
            } else {
                alert('Error: ' + data.message);
                closePaymentModal();
            }
        });
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
        cardElement.clear();
    }

    async function handlePayment(event) {
        event.preventDefault();
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        const paymentIntentId = document.getElementById('payment-intent-id').value;

        const { error, paymentIntent } = await stripe.confirmCardPayment(
            // Get the client secret from Stripe
            fetch('{{ route("payment.create-intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ package_id: selectedPackageId })
            })
            .then(r => r.json())
            .then(d => d.client_secret),
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: document.querySelector('body').getAttribute('data-user-name') || 'Unknown'
                    }
                }
            }
        );

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Pay Now';
        } else if (paymentIntent.status === 'succeeded') {
            // Confirm payment on server
            fetch('{{ route("payment.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ payment_intent_id: paymentIntentId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Payment successful! ' + data.credits_added + ' credits added to your account.');
                    closePaymentModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Pay Now';
                }
            });
        }
    }
</script>
@endpush

@endsection
