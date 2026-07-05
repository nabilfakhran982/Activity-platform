<x-layouts.app-main title="Buy Credits — Activo">

    @push('styles')
        <style>
            .pricing-section {
                min-height: 100vh;
                background: linear-gradient(135deg, #F7F5F0 0%, #fafaf9 100%);
            }

            .header {
                background: #1a1a18;
                color: white;
                padding: 80px 20px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(ellipse 50% 80% at 80% 50%, rgba(212, 163, 80, 0.12) 0%, transparent 70%);
                pointer-events: none;
            }

            .header h1 {
                font-size: 3rem;
                font-weight: 700;
                margin: 0 0 15px 0;
                font-family: 'Playfair Display', serif;
                position: relative;
                z-index: 1;
            }

            .header p {
                font-size: 1.1rem;
                opacity: 0.95;
                position: relative;
                z-index: 1;
                max-width: 500px;
                margin: 0 auto;
            }

            .content {
                max-width: 1200px;
                margin: 0 auto;
                padding: 60px 20px;
            }

            .user-credits-section {
                background: white;
                border: 2px solid rgba(212, 163, 80, 0.3);
                border-radius: 16px;
                padding: 40px;
                margin-bottom: 60px;
                text-align: center;
                box-shadow: 0 4px 20px rgba(212, 163, 80, 0.08);
                position: relative;
            }

            .user-credits-section::before {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(135deg, rgba(212, 163, 80, 0.3), transparent);
                border-radius: 16px;
                z-index: -1;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .user-credits-section:hover::before {
                opacity: 1;
            }

            .user-credits-section h2 {
                color: #8a7a6a;
                font-size: 0.95rem;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                font-weight: 600;
            }

            .credit-balance {
                font-size: 4rem;
                font-weight: 700;
                color: #D4A350;
                margin: 20px 0 15px 0;
                font-family: 'Playfair Display', serif;
            }

            .credit-info {
                color: #8a7a6a;
                font-size: 0.95rem;
                margin-top: 10px;
            }

            .packages-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 25px;
                margin-bottom: 60px;
            }

            .package-card {
                border: 2px solid #E8E5DF;
                border-radius: 16px;
                padding: 45px 30px;
                text-align: center;
                cursor: pointer;
                transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                background: white;
                position: relative;
                overflow: hidden;
            }

            .package-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(212, 163, 80, 0.05), transparent);
                opacity: 0;
                transition: opacity 0.35s;
            }

            .package-card:hover {
                border-color: #D4A350;
                box-shadow: 0 15px 40px rgba(212, 163, 80, 0.15);
                transform: translateY(-8px);
            }

            .package-card:hover::before {
                opacity: 1;
            }

            .package-card.selected {
                border-color: #D4A350;
                background: linear-gradient(135deg, rgba(212, 163, 80, 0.08), white);
                box-shadow: 0 15px 40px rgba(212, 163, 80, 0.25);
                transform: scale(1.02);
            }

            .package-badge {
                display: inline-block;
                background: linear-gradient(135deg, #D4A350 0%, #c0933f 100%);
                color: white;
                padding: 8px 16px;
                border-radius: 25px;
                font-size: 0.8rem;
                font-weight: 700;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                box-shadow: 0 4px 12px rgba(212, 163, 80, 0.3);
            }

            .credits-amount {
                font-size: 3rem;
                font-weight: 700;
                color: #1a1a18;
                margin: 15px 0 8px 0;
                font-family: 'Playfair Display', serif;
            }

            .credits-label {
                color: #8a7a6a;
                font-size: 0.9rem;
                margin-bottom: 20px;
                text-transform: uppercase;
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: 0.05em;
            }

            .package-price {
                font-size: 2rem;
                font-weight: 700;
                color: #D4A350;
                margin: 20px 0 8px 0;
            }

            .price-per-credit {
                color: #8a7a6a;
                font-size: 0.85rem;
                margin-bottom: 30px;
                background: rgba(212, 163, 80, 0.05);
                padding: 8px 12px;
                border-radius: 8px;
                display: inline-block;
            }

            .select-btn {
                width: 100%;
                padding: 14px 20px;
                border: 2px solid #E8E5DF;
                background: white;
                color: #1a1a18;
                border-radius: 10px;
                cursor: pointer;
                font-weight: 600;
                font-size: 0.95rem;
                transition: all 0.3s;
                position: relative;
                overflow: hidden;
            }

            .select-btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                background: rgba(212, 163, 80, 0.1);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .select-btn:hover {
                border-color: #D4A350;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(212, 163, 80, 0.15);
            }

            .select-btn:hover::before {
                width: 300px;
                height: 300px;
            }

            .package-card.selected .select-btn {
                background: linear-gradient(135deg, #D4A350, #c0933f);
                border-color: #D4A350;
                color: white;
                box-shadow: 0 5px 15px rgba(212, 163, 80, 0.3);
            }

            .payment-section {
                max-width: 520px;
                margin: 0 auto;
                padding: 45px;
                background: white;
                border-radius: 16px;
                border: 2px solid #E8E5DF;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            }

            .payment-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1a1a18;
                margin-bottom: 8px;
                font-family: 'Playfair Display', serif;
            }

            .payment-subtitle {
                color: #8a7a6a;
                margin-bottom: 30px;
                font-size: 0.95rem;
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-group label {
                display: block;
                font-weight: 600;
                color: #1a1a18;
                margin-bottom: 10px;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            #card-element {
                padding: 14px 16px;
                background: white;
                border: 2px solid #E8E5DF;
                border-radius: 10px;
                font-family: 'DM Sans', sans-serif;
                transition: all 0.3s;
            }

            #card-element:focus {
                border-color: #D4A350;
                box-shadow: 0 0 0 3px rgba(212, 163, 80, 0.1);
            }

            .StripeElement--focus {
                border-color: #D4A350;
                box-shadow: 0 0 0 3px rgba(212, 163, 80, 0.1);
            }

            #card-errors {
                color: #e05252;
                font-size: 0.85rem;
                margin-top: 8px;
                font-weight: 500;
            }

            .payment-btn {
                width: 100%;
                padding: 16px 20px;
                background: linear-gradient(135deg, #D4A350, #c0933f);
                color: white;
                border: none;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                font-size: 1rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .payment-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.5s;
            }

            .payment-btn:hover:not(:disabled)::before {
                left: 100%;
            }

            .payment-btn:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(212, 163, 80, 0.4);
            }

            .payment-btn:active:not(:disabled) {
                transform: translateY(0);
            }

            .payment-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .no-package-msg {
                text-align: center;
                padding: 40px;
                color: #8a7a6a;
            }

            .success-msg {
                padding: 16px;
                background: rgba(80, 180, 80, 0.1);
                border: 2px solid rgba(80, 180, 80, 0.4);
                border-radius: 10px;
                color: #2d6b2d;
                margin-bottom: 20px;
                display: none;
                font-weight: 500;
                border-left: 4px solid #50b450;
            }

            .error-msg {
                padding: 16px;
                background: rgba(232, 74, 74, 0.1);
                border: 2px solid rgba(232, 74, 74, 0.4);
                border-radius: 10px;
                color: #a32d2d;
                margin-bottom: 20px;
                display: none;
                font-weight: 500;
                border-left: 4px solid #e05252;
            }

            .security-info {
                text-align: center;
                margin-top: 25px;
                padding: 20px;
                background: rgba(212, 163, 80, 0.05);
                border-radius: 10px;
                color: #8a7a6a;
                font-size: 0.85rem;
            }

            .security-info p {
                margin: 6px 0;
            }

            .security-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-top: 10px;
                color: #D4A350;
                font-weight: 600;
            }

            @media (max-width: 768px) {
                .header h1 {
                    font-size: 2rem;
                }

                .packages-grid {
                    grid-template-columns: 1fr;
                }

                .credit-balance {
                    font-size: 3rem;
                }

                .package-card {
                    padding: 35px 20px;
                }
            }
        </style>
    @endpush

    <div class="pricing-section">
        <div class="header">
            <h1>Buy Credits</h1>
            <p>Get more credits to book your favorite activities</p>
        </div>

        <div class="content">
            {{-- User Credits --}}
            <div class="user-credits-section">
                <h2>Your Current Balance</h2>
                <div class="credit-balance">{{ $userStats['balance'] ?? 0 }}</div>
                <div class="credit-info">
                    You have <strong>{{ $userStats['balance'] ?? 0 }} credits</strong>
                </div>
            </div>

            {{-- Packages --}}
            <div class="packages-grid">
                @foreach($packages as $package)
                    <div class="package-card" data-package-id="{{ $package['id'] }}">
                        @if($package['id'] == 4)
                            <div class="package-badge popular">MOST POPULAR</div>
                        @endif
                        <div class="credits-amount">{{ $package['credits'] }}</div>
                        <div class="credits-label">Credits</div>
                        <div class="package-price">${{ number_format($package['amount'], 2) }}</div>
                        <div class="price-per-credit">${{ number_format($package['amount'] / $package['credits'], 3) }} per
                            credit</div>
                        <button type="button" class="select-btn"
                            onclick="selectPackage(this.closest('.package-card'))">Select Package</button>
                    </div>
                @endforeach
            </div>

            {{-- Payment Form --}}
            <div class="payment-section">
                <h3 class="payment-title">Payment Details</h3>
                <p class="payment-subtitle">Complete your purchase securely</p>

                <div class="error-msg" id="error-msg"></div>
                <div class="success-msg" id="success-msg">Payment successful! Credits added to your account.</div>

                <form id="payment-form">
                    @csrf

                    {{-- Selected Package Info --}}
                    <div class="form-group" id="selected-package-info"
                        style="display: none; padding: 15px; background: white; border-radius: 8px; border: 1px solid rgba(212, 163, 80, 0.2); margin-bottom: 25px;">
                        <p style="margin: 0; color: #8a7a6a; font-size: 0.9rem;">Selected Package</p>
                        <p style="margin: 8px 0 0 0; font-weight: 600; color: #1a1a18;">
                            <span id="package-credits">0</span> Credits - $<span id="package-price">0.00</span>
                        </p>
                    </div>

                    {{-- Stripe Card Element --}}
                    <div class="form-group">
                        <label for="card-element">Card Details</label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                    </div>

                    <input type="hidden" id="package-id-input" name="package_id" value="">

                    {{-- Payment Button --}}
                    <button type="submit" class="payment-btn" id="submit-btn" disabled>
                        <span class="material-icons"
                            style="vertical-align: middle; margin-right: 6px; font-size: 20px;">lock</span>
                        Pay Now
                    </button>
                </form>

                <div class="security-info">
                    <p>🔒 Secure payment powered by Stripe</p>
                    <p>Your payment information is encrypted and secure</p>
                    <div class="security-badge">
                        <span class="material-icons" style="font-size: 16px;">verified</span>
                        PCI DSS Compliant
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Stripe setup
            const stripe = Stripe('{{ env('STRIPE_PUBLIC_KEY') }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#1a1a18',
                        fontFamily: '"DM Sans", sans-serif',
                    }
                }
            });
            cardElement.mount('#card-element');

            let selectedPackage = null;

            // Debug: Log packages data
            const packages = @json($packages);
            console.log('Packages loaded:', packages);
            console.log('Packages type:', typeof packages);
            console.log('Packages is array:', Array.isArray(packages));

            // Check initial button state
            window.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('submit-btn');
                console.log('Initial button state:', btn.disabled);
                console.log('Initial button HTML:', btn.outerHTML);
            });

            // Handle card errors
            cardElement.on('change', function (event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                    displayError.style.display = 'block';
                } else {
                    displayError.textContent = '';
                    displayError.style.display = 'none';
                }
            });

            // Select package
            function selectPackage(element) {
                console.log('selectPackage called');
                console.log('Element:', element);
                console.log('Data package id:', element.dataset.packageId);

                document.querySelectorAll('.package-card').forEach(card => {
                    card.classList.remove('selected');
                });
                element.classList.add('selected');

                const packageId = element.dataset.packageId;
                console.log('Package ID extracted:', packageId);

                selectedPackage = packages.find(p => {
                    console.log('Comparing package id:', p.id, 'with packageId:', packageId, 'type of p.id:', typeof p.id, 'type of packageId:', typeof packageId);
                    return p.id == packageId;
                });

                console.log('Selected package:', selectedPackage);

                if (selectedPackage) {
                    console.log('Package found, updating UI');
                    const submitBtn = document.getElementById('submit-btn');
                    console.log('Button before:', submitBtn);
                    console.log('Button disabled before:', submitBtn.disabled);

                    document.getElementById('package-id-input').value = packageId;
                    document.getElementById('package-credits').textContent = selectedPackage.credits;
                    document.getElementById('package-price').textContent = selectedPackage.amount.toFixed(2);
                    document.getElementById('selected-package-info').style.display = 'block';
                    submitBtn.disabled = false;

                    console.log('Button disabled after:', submitBtn.disabled);
                    console.log('Button classes:', submitBtn.className);
                    console.log('Button HTML:', submitBtn.outerHTML);
                } else {
                    console.log('Package NOT found');
                }
            }

            // Payment form submission
            document.getElementById('payment-form').addEventListener('submit', async function (e) {
                e.preventDefault();
                console.log('Form submitted');
                console.log('Selected package:', selectedPackage);

                if (!selectedPackage) {
                    console.log('No package selected');
                    showError('Please select a package');
                    return;
                }

                const btn = document.getElementById('submit-btn');
                btn.disabled = true;
                btn.textContent = 'Processing...';

                try {
                    // Create payment intent
                    console.log('Creating payment intent for package:', document.getElementById('package-id-input').value);
                    const intentRes = await fetch('{{ route("payment.create-intent") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({
                            package_id: parseInt(document.getElementById('package-id-input').value)
                        })
                    });

                    console.log('Intent response status:', intentRes.status);
                    console.log('Intent response ok:', intentRes.ok);

                    // Get the response text first
                    const responseText = await intentRes.text();
                    console.log('Intent response text:', responseText);

                    // Try to parse as JSON
                    let intentData;
                    try {
                        intentData = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('Failed to parse JSON:', parseError);
                        console.error('Response was:', responseText.substring(0, 500));
                        showError('Server error: ' + intentRes.status + ' ' + intentRes.statusText);
                        btn.disabled = false;
                        btn.textContent = 'Pay Now';
                        return;
                    }

                    console.log('Intent response:', intentData);

                    if (!intentData.success) {
                        showError(intentData.message || 'Failed to create payment');
                        btn.disabled = false;
                        btn.textContent = 'Pay Now';
                        return;
                    }

                    // Confirm payment with card
                    console.log('Confirming card payment with client secret:', intentData.client_secret);
                    const { error, paymentIntent } = await stripe.confirmCardPayment(
                        intentData.client_secret,
                        {
                            payment_method: {
                                card: cardElement,
                                billing_details: {
                                    // Can add user details here
                                }
                            }
                        }
                    );

                    console.log('Stripe response - error:', error);
                    console.log('Stripe response - paymentIntent:', paymentIntent);

                    if (error) {
                        showError(error.message);
                        btn.disabled = false;
                        btn.textContent = 'Pay Now';
                    } else if (paymentIntent.status === 'succeeded') {
                        // Confirm on backend
                        const confirmRes = await fetch('{{ route("payment.confirm") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            },
                            body: JSON.stringify({
                                payment_intent_id: paymentIntent.id
                            })
                        });

                        console.log('Confirm response status:', confirmRes.status);
                        const confirmText = await confirmRes.text();
                        console.log('Confirm response text:', confirmText);

                        let confirmData;
                        try {
                            confirmData = JSON.parse(confirmText);
                        } catch (parseError) {
                            console.error('Failed to parse confirm response:', parseError);
                            showError('Server error on confirmation: ' + confirmRes.status);
                            btn.disabled = false;
                            btn.textContent = 'Pay Now';
                            return;
                        }

                        console.log('Confirm response data:', confirmData);

                        if (confirmData.success) {
                            showSuccess('Payment successful! Credits added to your account.');
                            setTimeout(() => {
                                window.location.href = '{{ route("profile") }}';
                            }, 2000);
                        } else {
                            showError(confirmData.message || 'Payment confirmation failed');
                            btn.disabled = false;
                            btn.textContent = 'Pay Now';
                        }
                    }
                } catch (err) {
                    console.error('Payment error:', err);
                    console.error('Error message:', err.message);
                    console.error('Error stack:', err.stack);
                    showError('An error occurred: ' + err.message);
                    btn.disabled = false;
                    btn.textContent = 'Pay Now';
                }
            });

            function showError(msg) {
                console.log('showError called:', msg);
                const errorDiv = document.getElementById('error-msg');
                if (!errorDiv) {
                    console.log('error-msg element not found');
                    return;
                }
                errorDiv.textContent = msg;
                errorDiv.style.display = 'block';
                document.getElementById('success-msg').style.display = 'none';
            }

            function showSuccess(msg) {
                console.log('showSuccess called:', msg);
                const successDiv = document.getElementById('success-msg');
                successDiv.textContent = msg;
                successDiv.style.display = 'block';
                document.getElementById('error-msg').style.display = 'none';
            }
        </script>
    @endpush

</x-layouts.app-main>
