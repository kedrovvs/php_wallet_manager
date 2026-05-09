@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Register</h1>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <label class="block mb-1 text-sm font-medium">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full border rounded px-3 py-2 mb-3">

            <label class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border rounded px-3 py-2 mb-3">

            <label class="block mb-1 text-sm font-medium">Password</label>
            <input type="password" name="password" required
                   class="w-full border rounded px-3 py-2 mb-3">

            <label class="block mb-1 text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border rounded px-3 py-2 mb-3">

            @error('name')
                <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
            @enderror
            @error('email')
                <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
            @enderror
            @error('password')
                <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
            @enderror

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Register</button>
        </form>

        <p class="text-sm text-center mt-4">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 underline">Log in</a>.
        </p>
    </div>
@endsection
