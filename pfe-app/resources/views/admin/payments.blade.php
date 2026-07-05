<x-layouts.admin-app title="Payments">

    <style>
        .payment-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-succeeded {
            background: rgba(80, 180, 80, 0.1);
            color: #2d6b2d;
            border: 1px solid rgba(80, 180, 80, 0.3);
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #996b1a;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-failed {
            background: rgba(232, 74, 74, 0.1);
            color: #a32d2d;
            border: 1px solid rgba(232, 74, 74, 0.3);
        }

        .payment-row-hover {
            transition: all 0.2s;
        }

        .payment-row-hover:hover {
            background: rgba(212, 163, 80, 0.04);
        }
    </style>

    {{-- HEADER --}}
    <div class="admin-card" style="margin-bottom: 0; border-bottom: none; border-radius: 16px 16px 0 0;">
        <div class="admin-card-header">
            <div>
                <h2 class="admin-card-title">Payment Transactions <span style="color:#a09890;font-size:14px;font-weight:400">({{ $payments->total() }})</span></h2>
                <p style="color:#8a7a6a;font-size:0.85rem;margin-top:4px;margin-bottom:0">Manage all Stripe payment records</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="admin-card" style="margin-top: 0; border-top: none; border-radius: 0 0 16px 16px;">
        @if($payments->isEmpty())
            <div class="admin-no-results">No payments yet</div>
        @else
            <div class="admin-table-wrapper">
                <table class="admin-table" id="payments-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Credits</th>
                            <th>Intent ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $p)
                        <tr class="payment-row-hover">
                            <td class="font-medium">{{ $p->id }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="admin-avatar" style="width:36px;height:36px;font-size:0.9rem">
                                        {{ strtoupper(substr($p->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm">{{ $p->user?->name ?? '—' }}</p>
                                        <p class="text-xs" style="color:#8a7a6a">{{ $p->user?->email ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">${{ number_format($p->amount / 100, 2) }}</div>
                                <div style="font-size:0.85rem;color:#8a7a6a">{{ strtoupper($p->currency) }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background:#D4A350;color:white;font-weight:600">
                                    {{ number_format($p->credits_purchased, 0) }}
                                </span>
                            </td>
                            <td>
                                <code style="font-size:0.8rem;color:#8a7a6a;word-break:break-all">{{ substr($p->stripe_payment_intent_id, 0, 20) }}...</code>
                            </td>
                            <td>
                                <span class="payment-status-badge badge-{{ $p->status }}">
                                    @if($p->status === 'succeeded')
                                        <span class="material-icons" style="font-size:12px">check_circle</span>
                                    @elseif($p->status === 'pending')
                                        <span class="material-icons" style="font-size:12px">hourglass_empty</span>
                                    @else
                                        <span class="material-icons" style="font-size:12px">cancel</span>
                                    @endif
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="text-xs" style="color:#a09890;white-space:nowrap">{{ $p->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div style="padding:16px 20px;border-top:1px solid #E8E5DF;display:flex;align-items:center;justify-content:space-between;background:#fafaf9">
                <div style="font-size:0.85rem;color:#8a7a6a">
                    Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong> to <strong>{{ $payments->lastItem() ?? 0 }}</strong> of <strong>{{ $payments->total() }}</strong> payments
                </div>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>

</x-layouts.admin-app>
