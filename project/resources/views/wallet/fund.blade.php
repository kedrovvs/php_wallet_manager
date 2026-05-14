@extends('layouts.app')

@section('title', 'Deposit')

@section('content')
<div class="max-w-md mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Deposit Funds</h1>
            <p class="text-sm text-gray-500">Add money to your wallet</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="/wallet/fund">
            @csrf
            <div class="mb-5">
                <label for="amount" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Amount</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xl">$</span>
                    <input type="number" name="amount" id="amount" min="1" required
                        class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-2xl font-bold transition-all"
                        placeholder="0.00">
                </div>
                @error('amount')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="bg-green-50 rounded-xl p-3 mb-5 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-green-700">Enter amount in cents (e.g., <strong>1000</strong> = $10.00)</p>
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl text-sm transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Deposit
            </button>
        </form>
    </div>
</div>
@endsection
