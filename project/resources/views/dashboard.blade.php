<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-3 gap-6 mb-10">
                <div class="card p-6">
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Balance</p>
                    <p class="text-2xl font-bold text-emerald-600">— ETH</p>
                </div>
                <div class="card p-6">
                    <p class="text-sm font-medium text-slate-500 mb-1">On Hold</p>
                    <p class="text-2xl font-bold text-amber-600">— ETH</p>
                </div>
                <div class="card p-6">
                    <p class="text-sm font-medium text-slate-500 mb-1">Available</p>
                    <p class="text-2xl font-bold text-blue-600">— ETH</p>
                </div>
            </div>

            <div class="card p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Welcome back!</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Head over to your wallet to manage your funds, check balances, and view transaction history.
                </p>
                <a href="{{ route('wallet.index') }}" class="btn-primary">
                    Go to Wallet
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
