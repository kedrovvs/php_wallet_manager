@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-lg font-bold text-gray-900 mb-4">My Wallet</h1>

    @if (!$wallet)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500 mb-4">No wallet yet.</p>
            <form method="POST" action="/wallet/create">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm">Create Wallet</button>
            </form>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-100">
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">Account</span>
                <span class="text-sm font-mono font-medium text-gray-900">{{ $wallet->ledger_account_address }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">Currency</span>
                <span class="text-sm font-semibold text-gray-900">{{ $wallet->currency }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">Cached Balance</span>
                <span class="text-lg font-bold text-gray-900">${{ number_format($wallet->balance / 100, 2) }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">Ledger Balance</span>
                <span class="text-lg font-bold text-gray-900">${{ number_format($ledgerBalance / 100, 2) }}</span>
            </div>
            <div class="px-5 py-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">Created</span>
                <span class="text-sm text-gray-900">{{ $wallet->created_at->format('M d, Y g:i A') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mt-4">
            <a href="/wallet/fund" class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Deposit
            </a>
            <a href="/wallet/holds" class="flex items-center justify-center gap-2 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg text-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Hold
            </a>
            <a href="/wallet/withdraw" class="flex items-center justify-center gap-2 px-4 py-3 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-lg text-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Withdraw
            </a>
        </div>
    @endif
</div>
@endsection
