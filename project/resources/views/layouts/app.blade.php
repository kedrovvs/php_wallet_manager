<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wallet') &middot; Vault</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen">
    @auth
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 h-14">
        <div class="max-w-6xl mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/dashboard" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center font-bold text-white text-sm shadow-sm">V</div>
                </a>
                <div class="hidden sm:flex ml-2 items-center bg-gray-100/80 rounded-full px-3 py-1.5 border border-gray-200/50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input class="bg-transparent border-none outline-none text-sm ml-2 w-48 text-gray-600 placeholder-gray-400" placeholder="Search" readonly>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="/dashboard" class="p-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->is('dashboard') ? 'text-green-600 bg-green-50' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                </a>
                <a href="/wallet" class="p-2 rounded-lg hover:bg-gray-100 transition-colors {{ request()->is('wallet') && !request()->is('wallet/*') ? 'text-green-600 bg-green-50' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </a>
                <div class="relative group">
                    <button class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold shadow-sm cursor-pointer hover:shadow-md transition-all">{{ substr(Auth::user()->name, 0, 1) }}</button>
                    <div class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 hidden group-hover:block overflow-hidden">
                        <div class="px-4 py-3.5 border-b border-gray-100">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                            Dashboard
                        </a>
                        <a href="/wallet" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Wallet
                        </a>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 text-gray-700 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="pt-14">
        <div class="max-w-6xl mx-auto px-4 py-6 flex gap-6">
            <aside class="hidden lg:block w-64 shrink-0">
                <nav class="space-y-1 sticky top-20">
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('dashboard') ? 'bg-green-50 text-green-700 shadow-sm border border-green-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                        Dashboard
                    </a>
                    <a href="/wallet" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('wallet') && !request()->is('wallet/*') ? 'bg-green-50 text-green-700 shadow-sm border border-green-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        My Wallet
                    </a>
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Transactions</p>
                    </div>
                    <a href="/wallet/fund" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('wallet/fund') ? 'bg-green-50 text-green-700 shadow-sm border border-green-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Deposit
                    </a>
                    <a href="/wallet/withdraw" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('wallet/withdraw') ? 'bg-green-50 text-green-700 shadow-sm border border-green-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        Withdraw
                    </a>
                    <a href="/wallet/holds" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('wallet/holds*') ? 'bg-amber-50 text-amber-700 shadow-sm border border-amber-200' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Holds
                    </a>
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <main class="flex-1 min-w-0">
                @if (session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm flex items-center gap-2.5 shadow-sm">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-2.5 shadow-sm">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <main class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        @yield('content')
    </main>
    @endauth
</body>
</html>
