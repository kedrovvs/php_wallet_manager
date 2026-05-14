<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900">
            {{ __('Wallet') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('private_key'))
                <div class="mb-6 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm break-all">
                    <strong class="block mb-1">Your Private Key (keep secret!):</strong>
                    <code class="text-xs">{{ session('private_key') }}</code>
                </div>
            @endif

            @if (!$configured)
                <div class="mb-6 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                    Sepolia not configured. Set SEPOLIA_RPC_URL, SEPOLIA_CONTRACT_ADDRESS, SEPOLIA_MASTER_ADDRESS, and SEPOLIA_MASTER_PRIVATE_KEY in .env
                </div>
            @endif

            @if ($walletAddress)
                <div class="card p-4 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500 font-medium">Your Wallet Address</p>
                        <p class="text-sm font-mono text-slate-900 truncate">{{ $walletAddress }}</p>
                    </div>
                </div>
            @else
                <div class="card p-4 mb-6 flex items-center gap-3 bg-amber-50 border-amber-200">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <p class="text-sm text-amber-700">No blockchain wallet assigned yet. Contact an administrator.</p>
                </div>
            @endif

            <div class="grid sm:grid-cols-3 gap-6 mb-8">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500">Total Balance</p>
                    </div>
                    <p class="text-3xl font-bold text-emerald-600">{{ number_format((float) $balance, 6) }} <span class="text-lg font-medium text-emerald-500">ETH</span></p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500">On Hold</p>
                    </div>
                    <p class="text-3xl font-bold text-amber-600">{{ number_format((float) $hold, 6) }} <span class="text-lg font-medium text-amber-500">ETH</span></p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500">Available</p>
                    </div>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format((float) $available, 6) }} <span class="text-lg font-medium text-blue-500">ETH</span></p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">+</span>
                        Fund Wallet
                    </h3>
                    <form method="POST" action="{{ route('wallet.fund') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="fund_amount" class="label">Amount (ETH)</label>
                            <input type="number" step="0.001" min="0.001" max="100" name="amount" id="fund_amount"
                                class="input-field" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            Add Funds
                        </button>
                    </form>
                </div>

                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-red-100 text-red-600 flex items-center justify-center text-xs font-bold">↑</span>
                        Withdraw
                    </h3>
                    <form method="POST" action="{{ route('wallet.withdraw') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="withdraw_amount" class="label">Amount (ETH)</label>
                            <input type="number" step="0.001" min="0.001" max="100" name="amount" id="withdraw_amount"
                                class="input-field" placeholder="0.00" required>
                        </div>
                        <div class="mb-4">
                            <label for="to_address" class="label">To Address</label>
                            <input type="text" name="to_address" id="to_address" placeholder="0x..."
                                class="input-field font-mono" required>
                        </div>
                        <button type="submit" class="btn-danger w-full">
                            Withdraw
                        </button>
                    </form>
                </div>

                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold">!</span>
                        Hold Credits
                    </h3>
                    <form method="POST" action="{{ route('wallet.hold') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="hold_amount" class="label">Amount (ETH)</label>
                            <input type="number" step="0.001" min="0.001" max="100" name="amount" id="hold_amount"
                                class="input-field" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-amber-500 text-white font-medium text-sm rounded-lg hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                            Place on Hold
                        </button>
                    </form>
                </div>

                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">↻</span>
                        Cancel Holding
                    </h3>
                    <form method="POST" action="{{ route('wallet.release-hold') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="release_amount" class="label">Amount (ETH)</label>
                            <input type="number" step="0.001" min="0.001" max="100" name="amount" id="release_amount"
                                class="input-field" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-blue-500 text-white font-medium text-sm rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Release Hold
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-6">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Export Private Key</h3>
                </div>
                <div class="p-6 text-sm text-slate-600">
                    <p class="mb-3">Enter your password to reveal your private key. You can use it to import your wallet into MetaMask or any Ethereum wallet.</p>
                    <form method="POST" action="{{ route('wallet.export-key') }}">
                        @csrf
                        <div class="flex items-end gap-3 max-w-md">
                            <div class="flex-1">
                                <label for="export_password" class="label">Confirm Password</label>
                                <input type="password" name="password" id="export_password"
                                    class="input-field" required>
                            </div>
                            <button type="submit" class="btn-primary whitespace-nowrap">
                                Reveal Key
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Transaction History</h3>
                </div>
                <div class="p-6">
                    @if ($transactions->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-sm text-slate-500">No transactions yet.</p>
                            <p class="text-xs text-slate-400 mt-1">Your transaction history will appear here.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">TX Hash</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($transactions as $tx)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @switch($tx->type)
                                                        @case('fund') bg-emerald-100 text-emerald-800 @break
                                                        @case('withdraw') bg-red-100 text-red-800 @break
                                                        @case('hold') bg-amber-100 text-amber-800 @break
                                                        @case('release_hold') bg-blue-100 text-blue-800 @break
                                                        @default bg-slate-100 text-slate-800
                                                    @endswitch
                                                ">{{ str_replace('_', ' ', ucfirst($tx->type)) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $tx->amount }} ETH</td>
                                            <td class="px-4 py-3">
                                                @if ($tx->tx_hash)
                                                    <a href="https://sepolia.etherscan.io/tx/{{ $tx->tx_hash }}"
                                                       target="_blank" class="text-xs font-mono text-blue-600 hover:text-blue-700 hover:underline">
                                                        {{ substr($tx->tx_hash, 0, 18) }}...
                                                    </a>
                                                @else
                                                    <span class="text-xs text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-500">{{ $tx->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
