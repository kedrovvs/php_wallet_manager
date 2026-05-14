<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\BlockchainService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class WalletController extends Controller
{
    private BlockchainService $blockchain;
    private WalletService $walletService;

    public function __construct(BlockchainService $blockchain, WalletService $walletService)
    {
        $this->blockchain = $blockchain;
        $this->walletService = $walletService;
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
        $walletAddress = $user->eth_address;

        if ($configured && $walletAddress) {
            try {
                $balance = $this->blockchain->getBalance($walletAddress);
                $hold = $this->blockchain->getHold($walletAddress);
                $available = $this->blockchain->getAvailableBalance($walletAddress);
            } catch (\Exception $e) {
                $balance = '0';
                $hold = '0';
                $available = '0';
            }
        }

        return view('wallet.index', compact(
            'user', 'transactions', 'balance', 'hold', 'available', 'configured', 'walletAddress'
        ));
    }

    public function fund(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001|max:100',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        if (!$user->eth_address) {
            return redirect()->route('wallet.index')
                ->with('error', 'No wallet configured for your account.');
        }

        try {
            $txHash = $this->blockchain->fundUser($user->eth_address, $amount);

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

        if (!$user->eth_address || !$user->encrypted_private_key) {
            return redirect()->route('wallet.index')
                ->with('error', 'No wallet configured for your account.');
        }

        try {
            $privateKey = $this->walletService->getDecryptedPrivateKey($user);

            $txHash = $this->blockchain->withdrawAsUser($amount, $toAddress, $privateKey);

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

        if (!$user->eth_address) {
            return redirect()->route('wallet.index')
                ->with('error', 'No wallet configured for your account.');
        }

        try {
            $txHash = $this->blockchain->holdUser($user->eth_address, $amount);

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

        if (!$user->eth_address) {
            return redirect()->route('wallet.index')
                ->with('error', 'No wallet configured for your account.');
        }

        try {
            $txHash = $this->blockchain->releaseHold($user->eth_address, $amount);

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

    public function exportPrivateKey(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();

        if (!$user->encrypted_private_key) {
            return redirect()->route('wallet.index')
                ->with('error', 'No private key stored.');
        }

        $privateKey = $this->walletService->getDecryptedPrivateKey($user);

        return redirect()->route('wallet.index')
            ->with('private_key', $privateKey);
    }
}
