<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wallet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if (!$configured)
                <div class="mb-4 px-4 py-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                    Sepolia not configured. Set SEPOLIA_RPC_URL, SEPOLIA_CONTRACT_ADDRESS, SEPOLIA_MASTER_ADDRESS, and SEPOLIA_MASTER_PRIVATE_KEY in .env
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">Balance</h3>
                        <p class="text-3xl font-bold text-green-600">{{ number_format((float) $balance, 6) }} ETH</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">On Hold</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ number_format((float) $hold, 6) }} ETH</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">Available</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format((float) $available, 6) }} ETH</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Fund Wallet</h3>
                        <form method="POST" action="{{ route('wallet.fund') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="fund_amount" class="block text-sm font-medium text-gray-700">Amount (ETH)</label>
                                <input type="number" step="0.001" min="0.001" max="100" name="amount" id="fund_amount"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Fund
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Withdraw</h3>
                        <form method="POST" action="{{ route('wallet.withdraw') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="withdraw_amount" class="block text-sm font-medium text-gray-700">Amount (ETH)</label>
                                <input type="number" step="0.001" min="0.001" max="100" name="amount" id="withdraw_amount"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="to_address" class="block text-sm font-medium text-gray-700">To Address</label>
                                <input type="text" name="to_address" id="to_address" placeholder="0x..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Withdraw
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Hold Credits</h3>
                        <form method="POST" action="{{ route('wallet.hold') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="hold_amount" class="block text-sm font-medium text-gray-700">Amount (ETH)</label>
                                <input type="number" step="0.001" min="0.001" max="100" name="amount" id="hold_amount"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Hold
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Cancel Holding</h3>
                        <form method="POST" action="{{ route('wallet.release-hold') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="release_amount" class="block text-sm font-medium text-gray-700">Amount (ETH)</label>
                                <input type="number" step="0.001" min="0.001" max="100" name="amount" id="release_amount"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Release Hold
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Transaction History</h3>
                    @if ($transactions->isEmpty())
                        <p class="text-gray-500">No transactions yet.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">TX Hash</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($transactions as $tx)
                                    <tr>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded
                                                @switch($tx->type)
                                                    @case('fund') bg-green-100 text-green-800 @break
                                                    @case('withdraw') bg-red-100 text-red-800 @break
                                                    @case('hold') bg-yellow-100 text-yellow-800 @break
                                                    @case('release_hold') bg-blue-100 text-blue-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">{{ str_replace('_', ' ', ucfirst($tx->type)) }}</span>
                                        </td>
                                        <td class="px-4 py-2">{{ $tx->amount }} ETH</td>
                                        <td class="px-4 py-2 text-xs font-mono">
                                            @if ($tx->tx_hash)
                                                <a href="https://sepolia.etherscan.io/tx/{{ $tx->tx_hash }}"
                                                   target="_blank" class="text-blue-600 hover:underline">
                                                    {{ substr($tx->tx_hash, 0, 18) }}...
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $tx->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
