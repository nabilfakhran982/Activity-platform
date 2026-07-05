@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="mb-12">
        <h1 class="text-4xl font-bold mb-2">Credit Dashboard</h1>
        <p class="text-gray-600">Track your search credits and transaction history</p>
    </div>

    <div class="grid md:grid-cols-4 gap-4 mb-12">
        {{-- Balance Card --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 text-white">
            <p class="text-blue-100 text-sm mb-2">Current Balance</p>
            <p class="text-4xl font-bold">{{ number_format($stats['balance'], 0) }}</p>
            <p class="text-blue-100 text-sm mt-2">Credits</p>
        </div>

        {{-- Searches Remaining --}}
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-6 text-white">
            <p class="text-green-100 text-sm mb-2">Searches Left</p>
            <p class="text-4xl font-bold">{{ $stats['searches_remaining'] }}</p>
            <p class="text-green-100 text-sm mt-2">At current rate</p>
        </div>

        {{-- Lifetime Purchased --}}
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg p-6 text-white">
            <p class="text-purple-100 text-sm mb-2">Lifetime Purchased</p>
            <p class="text-4xl font-bold">{{ number_format($stats['lifetime_purchased'], 0) }}</p>
            <p class="text-purple-100 text-sm mt-2">Total</p>
        </div>

        {{-- Lifetime Used --}}
        <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-lg p-6 text-white">
            <p class="text-orange-100 text-sm mb-2">Lifetime Used</p>
            <p class="text-4xl font-bold">{{ number_format($stats['lifetime_used'], 0) }}</p>
            <p class="text-orange-100 text-sm mt-2">Total</p>
        </div>
    </div>

    {{-- Buy Credits Button --}}
    <div class="mb-12">
        <a href="{{ route('credits.purchase') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
            Buy Credits
        </a>
    </div>

    {{-- Transaction History --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold">Transaction History</h2>
        </div>

        @if($transactions->isEmpty())
        <div class="px-6 py-12 text-center">
            <p class="text-gray-600">No transactions yet</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Date</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Type</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Reason</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Amount</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Balance</th>
            </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                {{ $transaction->type === 'addition' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $transaction->reason)) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold
                            {{ $transaction->type === 'addition' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'addition' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right">{{ number_format($transaction->balance_after, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
