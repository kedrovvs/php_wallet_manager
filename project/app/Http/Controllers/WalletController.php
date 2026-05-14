<?php

namespace App\Http\Controllers;

use App\Models\Hold;
use App\Models\Wallet;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function create(LedgerService $ledger): JsonResponse
    {
        $userId = auth()->id();
        $walletAddr = "wallets:{$userId}";

        if (Wallet::where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'Wallet already exists'], 409);
        }

        try {
            $wallet = Wallet::create([
                'user_id' => $userId,
                'ledger_account_address' => $walletAddr,
                'currency' => 'USD',
            ]);

            return response()->json(['wallet' => $wallet]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create wallet'], 500);
        }
    }

    public function fund(Request $request, LedgerService $ledger): JsonResponse
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

        try {
            $result = $ledger->executeTransaction($script);
            Wallet::where('user_id', $userId)->increment('balance', $amount);
            return response()->json(['status' => 'funded', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Funding failed'], 500);
        }
    }

    public function withdraw(Request $request, LedgerService $ledger): JsonResponse
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

        try {
            $result = $ledger->executeTransaction($script);
            Wallet::where('user_id', $userId)->decrement('balance', $amount);
            return response()->json(['status' => 'withdrawn', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Withdrawal failed. Insufficient funds or system error.'], 400);
        }
    }

    public function hold(Request $request, LedgerService $ledger): JsonResponse
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

        try {
            $result = $ledger->executeTransaction($script);
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

            return response()->json(['status' => 'hold_created', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Insufficient funds or System error'], 400);
        }
    }

    public function cancelHold(Request $request, LedgerService $ledger): JsonResponse
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

        try {
            $result = $ledger->executeTransaction($script);
            $wallet = Wallet::where('user_id', $userId)->firstOrFail();
            $wallet->increment('balance', $amount);

            Hold::where('hold_reference', $request->hold_reference)
                ->where('user_id', $userId)
                ->update(['status' => 'released']);

            return response()->json(['status' => 'hold_canceled', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cancel hold failed'], 400);
        }
    }

    public function balance(LedgerService $ledger): JsonResponse
    {
        $userId = auth()->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        try {
            $ledgerBalance = $ledger->getBalance($wallet->ledger_account_address, $wallet->currency);
            return response()->json([
                'cached_balance' => $wallet->balance,
                'ledger_balance' => $ledgerBalance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['cached_balance' => $wallet->balance]);
        }
    }
}
