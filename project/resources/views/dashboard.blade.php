@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-xl mx-auto">
    @if (!$wallet)
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-sm border border-green-200 p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mx-auto mb-4 shadow-md">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-500 text-sm mb-6">Create a wallet to start managing your funds</p>
            <form method="POST" action="/wallet/create">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg text-sm transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Wallet
                </button>
            </form>
        </div>
    @else
        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Available Balance</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-xs font-mono text-gray-600">{{ $wallet->ledger_account_address }}</span>
            </div>
            <p class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">${{ number_format($wallet->balance / 100, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                Ledger: ${{ number_format($ledgerBalance / 100, 2) }}
            </p>
        </div>

        <div class="grid grid-cols-4 gap-3 mb-4">
            <a href="/wallet/fund" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:border-green-300 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mx-auto mb-2 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">Deposit</span>
            </a>
            <a href="/wallet/withdraw" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:border-gray-400 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-500 to-gray-700 flex items-center justify-center mx-auto mb-2 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">Withdraw</span>
            </a>
            <button onclick="document.getElementById('quick-hold').classList.toggle('hidden'); document.getElementById('quick-hold').classList.toggle('flex')" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer w-full">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mx-auto mb-2 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">New Hold</span>
            </button>
            <a href="/wallet/holds" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mx-auto mb-2 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">All Holds</span>
            </a>
        </div>

        <div id="quick-hold" class="hidden bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl shadow-sm border border-amber-200 p-5 mb-4 flex-col">
            <h2 class="text-sm font-bold text-gray-900 mb-1">Quick Hold</h2>
            <p class="text-xs text-gray-500 mb-4">Reserve funds for a pending purchase</p>
            <form method="POST" action="/wallet/hold">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" name="hold_reference" placeholder="Reference (e.g. order_123)" required
                            class="w-full px-3 py-2.5 rounded-lg border border-amber-300 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                    </div>
                    <div class="w-28">
                        <input type="number" name="amount" min="1" placeholder="Amount" required
                            class="w-full px-3 py-2.5 rounded-lg border border-amber-300 bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-lg text-sm transition-all shadow-sm hover:shadow-md">
                        Hold
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h2 class="text-sm font-bold text-gray-900">Active Holds</h2>
                </div>
                @if ($activeHolds > 0)
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $activeHolds }}</span>
                @endif
            </div>
            @if ($recentHolds->where('status', 'active')->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($recentHolds->where('status', 'active') as $hold)
                        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $hold->hold_reference }}</p>
                                    <p class="text-xs text-gray-500">{{ $hold->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <p class="text-sm font-bold text-gray-900">${{ number_format($hold->amount / 100, 2) }}</p>
                                <form method="POST" action="/wallet/hold/cancel">
                                    @csrf
                                    <input type="hidden" name="hold_reference" value="{{ $hold->hold_reference }}">
                                    <input type="hidden" name="amount" value="{{ $hold->amount }}">
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-green-50 text-green-600 transition-colors" title="Release hold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="/wallet/holds" class="block px-5 py-3 text-center text-sm font-semibold text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition-colors border-t border-gray-100">
                    View All Holds &rarr;
                </a>
            @else
                <div class="px-5 py-10 text-center">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">No active holds</p>
                    <p class="text-xs text-gray-400">Use "New Hold" above to reserve funds</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
