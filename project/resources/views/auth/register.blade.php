@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center font-bold text-2xl text-white mx-auto mb-4 shadow-lg shadow-green-200">V</div>
            <h1 class="text-3xl font-bold text-gray-900">Get started</h1>
            <p class="text-gray-500 text-sm mt-1.5">Create your account</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="/register">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-sm transition-all">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="you@example.com" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-sm transition-all">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Password</label>
                        <input type="password" name="password" id="password" placeholder="Create a password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-sm transition-all">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repeat your password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-sm transition-all">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
                        Create Account
                    </button>
                </div>
            </form>
            <p class="mt-5 text-center text-sm text-gray-500">
                Already have an account?
                <a href="/login" class="text-green-600 hover:text-green-700 font-semibold">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
