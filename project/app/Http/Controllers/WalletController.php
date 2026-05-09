<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\BlockchainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    private BlockchainService $blockchain;

    public function __construct(BlockchainService $blockchain)
    {
        $this->blockchain = $blockchain;
    }

    public function index(): View
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $balance = '0';
        $hold = '0';
        $available = '0';
        $configured = $this->blockchain->isConfigured();

        if ($configured) {
            try {
                $balance = $this->blockchain->getBalance($user->id);
                $hold = $this->blockchain->getHold($user->id);
                $available = $this->blockchain->getAvailableBalance($user->id);
            } catch (\Exception $e) {
                $balance = '0';
                $hold = '0';
                $available = '0';
            }
        }

        return view('wallet.index', compact(
            'user', 'transactions', 'balance', 'hold', 'available', 'configured'
        ));
    }

    public function fund(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001|max:100',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        try {
            $txHash = $this->blockchain->fundUser($user->id, $amount);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'fund',
                'amount' => $amount,
                'tx_hash' => $txHash,
                'status' => 'confirmed',
            ]);

            return redirect()->route('wallet.index')
                ->with('success', "Funded $amount ETH. TX: $txHash");
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')
                ->with('error', 'Funding failed: ' . $e->getMessage());
        }
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001|max:100',
            'to_address' => 'required|string|size:42|starts_with:0x',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');
        $toAddress = $request->input('to_address');

        try {
            $txHash = $this->blockchain->withdrawUser($user->id, $amount, $toAddress);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'tx_hash' => $txHash,
                'to_address' => $toAddress,
                'status' => 'confirmed',
            ]);

            return redirect()->route('wallet.index')
                ->with('success', "Withdrew $amount ETH to $toAddress. TX: $txHash");
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')
                ->with('error', 'Withdrawal failed: ' . $e->getMessage());
        }
    }

    public function hold(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001|max:100',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        try {
            $txHash = $this->blockchain->holdUser($user->id, $amount);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'hold',
                'amount' => $amount,
                'tx_hash' => $txHash,
                'status' => 'confirmed',
            ]);

            return redirect()->route('wallet.index')
                ->with('success', "Held $amount ETH. TX: $txHash");
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')
                ->with('error', 'Hold failed: ' . $e->getMessage());
        }
    }

    public function releaseHold(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001|max:100',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        try {
            $txHash = $this->blockchain->releaseHold($user->id, $amount);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'release_hold',
                'amount' => $amount,
                'tx_hash' => $txHash,
                'status' => 'confirmed',
            ]);

            return redirect()->route('wallet.index')
                ->with('success', "Released hold of $amount ETH. TX: $txHash");
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')
                ->with('error', 'Release failed: ' . $e->getMessage());
        }
    }
}
