<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Vault') }} – Secure Crypto Wallet</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-slate-900">
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-50 via-white to-white pointer-events-none"></div>

        <header class="relative z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <a href="/" class="flex items-center gap-2.5">
                        <x-application-logo class="w-8 h-8 text-emerald-600" />
                        <span class="text-xl font-bold text-slate-900">Vault</span>
                    </a>

                    <nav class="flex items-center gap-3 sm:gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-3 py-2">
                                    Sign in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary text-sm">
                                        Get started
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </nav>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="pt-16 sm:pt-24 pb-16 sm:pb-20">
                <div class="max-w-6xl mx-auto px-4 sm:px-6">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] tracking-tight">
                            Take Control of<br>
                            <span class="text-emerald-600">Your Crypto</span>
                        </h1>
                        <p class="mt-5 sm:mt-6 text-lg sm:text-xl text-slate-500 leading-relaxed max-w-xl mx-auto">
                            A secure, transparent wallet for managing your ETH. 
                            Real-time balance tracking, instant transactions, complete control.
                        </p>
                        <div class="mt-8 sm:mt-10 flex items-center justify-center gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-3">
                                    Get started
                                </a>
                            @endif
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn-secondary text-base px-8 py-3">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-secondary text-base px-8 py-3">
                                        Sign in
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="pb-20 sm:pb-32">
                <div class="max-w-6xl mx-auto px-4 sm:px-6">
                    <div class="grid sm:grid-cols-3 gap-6 sm:gap-8">
                        <div class="card p-6 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Real-time Balance</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Track your ETH balance with live updates. Always know exactly what you have, what's on hold, and what's available.
                            </p>
                        </div>

                        <div class="card p-6 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Secure & Safe</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Industry-standard security practices. Your funds are protected with smart contract verified transactions on the Sepolia testnet.
                            </p>
                        </div>

                        <div class="card p-6 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Full Control</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Withdraw, hold, or transfer your funds anytime. No restrictions, no delays — your crypto, your rules.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
                <p class="text-sm text-slate-400 text-center">
                    &copy; {{ date('Y') }} Vault. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
