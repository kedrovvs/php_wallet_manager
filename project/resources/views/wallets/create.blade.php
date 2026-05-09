@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Wallet</h1>

    <form action="{{ route('wallets.store') }}" method="POST" class="bg-white rounded shadow p-6 max-w-md">
        @csrf
        <label class="block mb-2 font-medium">Wallet Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full border rounded px-3 py-2 mb-4">
        @error('name')
            <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
        @enderror
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create</button>
    </form>
@endsection
