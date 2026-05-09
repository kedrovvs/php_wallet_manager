<?php

namespace App\Http\Controllers;

use App\Models\Hold;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::with('holds')->get();
        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Wallet::create([
            'name' => $validated['name'],
            'balance' => 0,
        ]);

        return redirect()->route('wallets.index');
    }

    public function show(Wallet $wallet)
    {
        $wallet->load('holds');
        return view('wallets.show', compact('wallet'));
    }

    public function addMoney(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $wallet->increment('balance', $validated['amount']);

        return redirect()->route('wallets.show', $wallet);
    }

    public function holdMoney(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($wallet->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient available balance.']);
        }

        $wallet->holds()->create([
            'amount' => $validated['amount'],
            'status' => 'active',
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('wallets.show', $wallet);
    }

    public function cancelHold(Hold $hold)
    {
        if ($hold->status !== 'active') {
            return back()->withErrors(['hold' => 'Hold is not active.']);
        }

        $hold->update(['status' => 'released']);

        return redirect()->route('wallets.show', $hold->wallet_id);
    }
}
