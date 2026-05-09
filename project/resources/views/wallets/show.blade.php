@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('wallets.index') }}" class="text-blue-600 underline text-sm">&larr; Back</a>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
        <h1 class="text-2xl font-bold">{{ $wallet->name }}</h1>
        <div class="mt-2 grid grid-cols-3 gap-4 text-center">
            <div class="bg-green-50 p-3 rounded">
                <span class="block text-sm text-gray-500">Balance</span>
                <span class="text-xl font-bold">{{ number_format($wallet->balance) }}</span>
            </div>
            <div class="bg-yellow-50 p-3 rounded">
                <span class="block text-sm text-gray-500">Held</span>
                <span class="text-xl font-bold">{{ number_format($wallet->held_balance) }}</span>
            </div>
            <div class="bg-blue-50 p-3 rounded">
                <span class="block text-sm text-gray-500">Available</span>
                <span class="text-xl font-bold">{{ number_format($wallet->available_balance) }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded shadow p-6">
            <h2 class="font-bold mb-3">Add Money</h2>
            <form action="{{ route('wallets.add-money', $wallet) }}" method="POST">
                @csrf
                <label class="block mb-1 text-sm">Amount</label>
                <input type="number" name="amount" min="1" required
                       class="w-full border rounded px-3 py-2 mb-3">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Add</button>
            </form>
            @error('amount')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white rounded shadow p-6">
            <h2 class="font-bold mb-3">Hold Money</h2>
            <form action="{{ route('wallets.hold', $wallet) }}" method="POST">
                @csrf
                <label class="block mb-1 text-sm">Amount</label>
                <input type="number" name="amount" min="1" required
                       class="w-full border rounded px-3 py-2 mb-3">
                <label class="block mb-1 text-sm">Description</label>
                <input type="text" name="description" placeholder="e.g. Payment hold"
                       class="w-full border rounded px-3 py-2 mb-3">
                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">Hold</button>
            </form>
            @error('amount')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="bg-white rounded shadow p-6">
        <h2 class="font-bold mb-3">Active Holds</h2>
        @php
            $activeHolds = $wallet->holds->where('status', 'active');
        @endphp
        @if ($activeHolds->isEmpty())
            <p class="text-gray-500 text-sm">No active holds.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Description</th>
                        <th class="pb-2">Date</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activeHolds as $hold)
                        <tr class="border-b">
                            <td class="py-2">{{ number_format($hold->amount) }}</td>
                            <td class="py-2">{{ $hold->description ?? '—' }}</td>
                            <td class="py-2">{{ $hold->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-2">
                                <form action="{{ route('holds.cancel', $hold) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 underline text-xs hover:text-red-800">Cancel Hold</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3 class="font-bold mt-6 mb-3">Released Holds</h3>
        @php
            $releasedHolds = $wallet->holds->where('status', 'released');
        @endphp
        @if ($releasedHolds->isEmpty())
            <p class="text-gray-500 text-sm">No released holds.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Description</th>
                        <th class="pb-2">Released</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($releasedHolds as $hold)
                        <tr class="border-b">
                            <td class="py-2">{{ number_format($hold->amount) }}</td>
                            <td class="py-2">{{ $hold->description ?? '—' }}</td>
                            <td class="py-2">{{ $hold->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
