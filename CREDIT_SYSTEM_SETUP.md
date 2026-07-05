# Credit System Setup Guide

## Overview
This guide walks you through setting up the credit system with Stripe integration for the Activity Platform.

## 1. Install Required Packages

```bash
composer require stripe/stripe-php
```

## 2. Stripe Configuration

### Get Stripe Keys

1. Go to https://dashboard.stripe.com
2. Sign in or create an account
3. Navigate to **Developers** > **API Keys**
4. Copy your:
   - **Publishable Key** → `STRIPE_PUBLIC_KEY`
   - **Secret Key** → `STRIPE_SECRET_KEY`

### Set Environment Variables

Update your `.env` file:

```env
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_live_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

### Get Webhook Secret (Optional - for production)

1. In Stripe Dashboard, go to **Developers** > **Webhooks**
2. Add endpoint: `https://yourdomain.com/webhook/stripe`
3. Select events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
4. Copy the signing secret → `STRIPE_WEBHOOK_SECRET`

## 3. Run Database Migrations

```bash
cd pfe-app
php artisan migrate
```

This will create:
- `user_credits` table - Stores user credit balances
- `credit_transactions` table - Tracks all credit movements
- `stripe_payments` table - Records Stripe payment intents

## 4. Give Trial Credits to Existing Users (Optional)

You can provide initial trial credits to users:

```bash
php artisan tinker
```

Then in the Tinker prompt:

```php
$user = User::find(1); // Replace with user ID
app(\App\Services\CreditService::class)->giveTrialCredits($user, 5);
```

## 5. Testing with Stripe

### Test Cards

Use these card numbers for testing:

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Expired**: `4000 0000 0000 0069`

Any future expiry date, any CVC

### Test Flow

1. Navigate to `/credits/purchase`
2. Select a credit package
3. Use test card `4242 4242 4242 4242`
4. Any future expiry date
5. Any CVC (e.g., 123)
6. Verify credits were added

## 6. Credit Cost Configuration

Current settings (in `CreditService.php`):

```php
const SEARCH_CREDIT_COST = 1; // Each search costs 1 credit
```

**Predefined Packages** (in `StripeService.php`):

| Package | Credits | Price | Per Credit |
|---------|---------|-------|-----------|
| 1       | 10      | $4.99 | 49.9¢     |
| 2       | 25      | $9.99 | 39.96¢    |
| 3       | 50      | $19.99| 39.98¢    |
| 4       | 100     | $39.99| 39.99¢    |

### Customization

To change:

1. **Cost per search**: Edit `CreditService::SEARCH_CREDIT_COST`
2. **Packages**: Edit `StripeService::getCreditPackages()`

## 7. User Experience Flow

### For New Users

1. User creates account → Automatically gets `UserCredit` record with 0 balance
2. User attempts search → Redirected to buy credits (free trial optional)
3. User purchases package → Credits added immediately after payment
4. User searches → 1 credit deducted per search

### For Existing Users Without Credits

Simply add trial credits:

```bash
php artisan tinker
User::all()->each(fn($u) => 
    app(\App\Services\CreditService::class)->giveTrialCredits($u, 5)
);
```

## 8. Admin/Manual Credit Management

### Award Credits to User

```php
$user = User::find(1);
$user->getCredits()->addCredits(10, 'admin_bonus');
```

### Deduct Credits

```php
$user = User::find(1);
$user->getCredits()->deductCredits(5, 'refund');
```

### Check Balance

```php
$user = User::find(1);
echo $user->getCredits()->balance;
```

## 9. View Credit History

Navigate to `/credits` to see:
- Current balance
- Searches remaining
- Lifetime purchased
- Lifetime used
- Full transaction history

## 10. Payment Endpoints

### API Endpoints

- `GET /credits` - Dashboard
- `GET /credits/purchase` - Purchase page
- `GET /credits/api/stats` - Get credit stats (JSON)
- `POST /payment/create-intent` - Create payment intent
- `POST /payment/confirm` - Confirm payment
- `POST /webhook/stripe` - Stripe webhook (no auth)

## 11. Troubleshooting

### "Stripe package not found"

```bash
composer require stripe/stripe-php
composer update
```

### Credits not deducting

Check:
1. User is authenticated
2. User has UserCredit record
3. SearchController is using `CreditService`

### Webhook not working

1. Verify webhook URL is publicly accessible
2. Check `STRIPE_WEBHOOK_SECRET` is correct
3. Verify endpoints selected in Stripe dashboard

## 12. Production Checklist

- [ ] Use live Stripe keys (not test keys)
- [ ] Enable HTTPS only
- [ ] Configure webhook signing secret
- [ ] Set proper credit costs in `CreditService`
- [ ] Update credit packages based on pricing strategy
- [ ] Test full payment flow with live cards
- [ ] Monitor Stripe dashboard for failed payments
- [ ] Set up email notifications for payment failures
- [ ] Regular backups of `credit_transactions` table

## 13. Monitoring & Analytics

Track revenue and usage:

```php
// Total revenue
$revenue = StripePayment::where('status', 'succeeded')->sum('amount');

// Total credits sold
$credits_sold = StripePayment::where('status', 'succeeded')->sum('credits_purchased');

// Active users with credits
$active_users = User::whereHas('credits', fn($q) => $q->where('balance', '>', 0))->count();
```

## Support

For Stripe-related issues:
- Stripe Documentation: https://stripe.com/docs
- Stripe Status: https://status.stripe.com
- Contact Stripe Support: https://support.stripe.com
