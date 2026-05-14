@extends('layouts.app')

@section('title', 'Holds')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">Holds</h1>
                <p class="text-sm text-gray-500">Manage reserved funds</p>
            </div>
        </div>
        @if ($wallet)
        <button onclick="document.getElementById('create-hold').classList.toggle('hidden'); document.getElementById('create-hold').classList.toggle('flex')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Hold
        </button>
        @endif
    </div>

    @if (!$wallet)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10 text-center">
            <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <p class="text-gray-500 text-sm font-medium">Create a wallet first to start using holds.</p>
        </div>
    @else
        <div id="create-hold" class="hidden bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-sm border border-amber-200 p-6 mb-5 flex-col">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-sm font-bold text-gray-900">Create a Hold</h2>
            </div>
            <form method="POST" action="/wallet/hold">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" name="hold_reference" placeholder="Reference (e.g. order_123)" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 text-sm transition-all"
                            value="{{ old('hold_reference') }}">
                    </div>
                    <div class="w-36">
                        <input type="number" name="amount" min="1" placeholder="Amount" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 text-sm transition-all"
                            value="{{ old('amount') }}">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
                        Hold
                    </button>
                </div>
            </form>
        </div>

        @if ($holds->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @foreach ($holds as $hold)
                    <div class="px-5 py-4 flex items-center justify-between {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl {{ $hold->status === 'active' ? 'bg-amber-100' : 'bg-green-100' }} flex items-center justify-center shrink-0">
                                @if ($hold->status === 'active')
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                @else
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $hold->hold_reference }}</p>
                                <p class="text-xs text-gray-500">{{ $hold->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-3 shrink-0">
                            <div>
                                <p class="text-sm font-bold text-gray-900">${{ number_format($hold->amount / 100, 2) }}</p>
                                <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-semibold {{ $hold->status === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($hold->status) }}
                                </span>
                            </div>
                            @if ($hold->status === 'active')
                            <form method="POST" action="/wallet/hold/cancel">
                                @csrf
                                <input type="hidden" name="hold_reference" value="{{ $hold->hold_reference }}">
                                <input type="hidden" name="amount" value="{{ $hold->amount }}">
                                <button type="submit" class="p-2 rounded-xl hover:bg-green-50 text-green-600 transition-colors" title="Release hold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10 text-center">
                <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <p class="text-sm text-gray-500 font-medium mb-3">No holds yet</p>
                <button onclick="document.getElementById('create-hold').classList.remove('hidden'); document.getElementById('create-hold').classList.add('flex'); window.scrollTo({top: 0, behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create a Hold
                </button>
            </div>
        @endif
    @endif
</div>
@endsection
