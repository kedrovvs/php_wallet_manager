<?php

namespace App\Http\Controllers;

use App\Models\Hold;
use App\Models\Wallet;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function showWallet(LedgerService $ledger): View
    {
        $userId = auth()->id();
        $wallet = Wallet::where('user_id', $userId)->first();
        $ledgerBalance = 0;
        if ($wallet) {
            try {
                Log::info('Fetching wallet balance', ['user_id' => $userId, 'account' => $wallet->ledger_account_address]);
                $ledgerBalance = $ledger->getBalance($wallet->ledger_account_address, $wallet->currency);
                Log::info('Wallet balance fetched', ['user_id' => $userId, 'balance' => $ledgerBalance]);
            } catch (\Exception $e) {
                Log::error('Failed to fetch wallet balance', ['user_id' => $userId, 'error' => $e->getMessage()]);
                $ledgerBalance = $wallet->balance;
            }
        }
        return view('wallet.index', compact('wallet', 'ledgerBalance'));
    }

    public function showFund(): View
    {
        return view('wallet.fund');
    }

    public function showWithdraw(): View
    {
        return view('wallet.withdraw');
    }

    public function showHolds(): View
    {
        $userId = auth()->id();
        $wallet = Wallet::where('user_id', $userId)->first();
        $holds = collect();
        if ($wallet) {
            $holds = Hold::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        }
        return view('wallet.holds', compact('wallet', 'holds'));
    }

    public function create(Request $request): RedirectResponse
    {
        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";

        if (Wallet::where('user_id', $userId)->exists()) {
            return redirect()->back()->with('error', 'Wallet already exists');
        }

        try {
            Wallet::create([
                'user_id' => $userId,
                'ledger_account_address' => $walletAddr,
                'currency' => 'USD',
            ]);
            return redirect('/dashboard')->with('success', 'Wallet created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create wallet');
        }
    }

    public function fund(Request $request, LedgerService $ledger): RedirectResponse
    {
        $request->validate(['amount' => 'required|integer|min:1']);
        $amount = $request->amount;
        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";

        $script = <<<EOD
        send [USD {$amount}] (
            source = @world
            destination = @{$walletAddr}
        )
        EOD;

        Log::info('Funding wallet', ['user_id' => $userId, 'amount' => $amount, 'script' => $script]);

        try {
            $result = $ledger->executeTransaction($script);
            Log::info('Funding succeeded', ['user_id' => $userId, 'amount' => $amount, 'ledger_response' => $result]);
            Wallet::where('user_id', $userId)->increment('balance', $amount);
            return redirect('/dashboard')->with('success', 'Funds deposited successfully');
        } catch (\Exception $e) {
            Log::error('Funding failed', ['user_id' => $userId, 'amount' => $amount, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Funding failed')->withInput();
        }
    }

    public function withdraw(Request $request, LedgerService $ledger): RedirectResponse
    {
        $request->validate(['amount' => 'required|integer|min:1']);
        $amount = $request->amount;
        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";

        $script = <<<EOD
        send [USD {$amount}] (
            source = @{$walletAddr}
            destination = @world
        )
        EOD;

        Log::info('Withdrawing from wallet', ['user_id' => $userId, 'amount' => $amount, 'script' => $script]);

        try {
            $result = $ledger->executeTransaction($script);
            Log::info('Withdrawal succeeded', ['user_id' => $userId, 'amount' => $amount, 'ledger_response' => $result]);
            Wallet::where('user_id', $userId)->decrement('balance', $amount);
            return redirect('/dashboard')->with('success', 'Withdrawal successful');
        } catch (\Exception $e) {
            Log::error('Withdrawal failed', ['user_id' => $userId, 'amount' => $amount, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Withdrawal failed. Insufficient funds or system error.')->withInput();
        }
    }

    public function hold(Request $request, LedgerService $ledger): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'hold_reference' => 'required|string'
        ]);

        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";
        $holdAddr = "holds:{$request->hold_reference}";
        $amount = $request->amount;

        $script = <<<EOD
        send [USD {$amount}] (
            source = @{$walletAddr}
            destination = @{$holdAddr}
        )
        EOD;

        Log::info('Creating hold', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'script' => $script]);

        try {
            $result = $ledger->executeTransaction($script);
            Log::info('Hold created', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'ledger_response' => $result]);
            $wallet = Wallet::where('user_id', $userId)->firstOrFail();
            $wallet->decrement('balance', $amount);

            Hold::create([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'hold_reference' => $request->hold_reference,
                'amount' => $amount,
                'currency' => $wallet->currency,
                'status' => 'active',
                'ledger_transaction_id' => $result['data']['txid'] ?? null,
            ]);

            return redirect('/wallet/holds')->with('success', 'Hold created successfully');
        } catch (\Exception $e) {
            Log::error('Hold creation failed', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Insufficient funds or system error')->withInput();
        }
    }

    public function cancelHold(Request $request, LedgerService $ledger): RedirectResponse
    {
        $request->validate([
            'hold_reference' => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";
        $holdAddr = "holds:{$request->hold_reference}";
        $amount = (int) $request->amount;

        $script = <<<EOD
        send [USD {$amount}] (
            source = @{$holdAddr}
            destination = @{$walletAddr}
        )
        EOD;

        Log::info('Cancelling hold', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'script' => $script]);

        try {
            $result = $ledger->executeTransaction($script);
            Log::info('Hold cancelled', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'ledger_response' => $result]);
            $wallet = Wallet::where('user_id', $userId)->firstOrFail();
            $wallet->increment('balance', $amount);

            Hold::where('hold_reference', $request->hold_reference)
                ->where('user_id', $userId)
                ->update(['status' => 'released']);

            return redirect('/wallet/holds')->with('success', 'Hold released successfully');
        } catch (\Exception $e) {
            Log::error('Cancel hold failed', ['user_id' => $userId, 'hold_reference' => $request->hold_reference, 'amount' => $amount, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Cancel hold failed');
        }
    }

    public function balance(LedgerService $ledger): JsonResponse
    {
        $userId = auth()->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            Log::warning('Balance check failed - no wallet', ['user_id' => $userId]);
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        try {
            $ledgerBalance = $ledger->getBalance($wallet->ledger_account_address, $wallet->currency);
            Log::info('Balance checked', ['user_id' => $userId, 'cached' => $wallet->balance, 'ledger' => $ledgerBalance]);
            return response()->json([
                'cached_balance' => $wallet->balance,
                'ledger_balance' => $ledgerBalance,
            ]);
        } catch (\Exception $e) {
            Log::error('Balance check failed', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return response()->json(['cached_balance' => $wallet->balance]);
        }
    }
}
