@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Wallets</h1>

    @if ($wallets->isEmpty())
        <p class="text-gray-500">No wallets yet. <a href="{{ route('wallets.create') }}" class="text-blue-600 underline">Create one</a>.</p>
    @else
        <div class="space-y-3">
            @foreach ($wallets as $wallet)
                <a href="{{ route('wallets.show', $wallet) }}" class="block bg-white rounded shadow p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-lg">{{ $wallet->name }}</span>
                        <span class="text-sm text-gray-500">Balance: <strong>{{ number_format($wallet->balance) }}</strong></span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
