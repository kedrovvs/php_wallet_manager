<?php

namespace App\Http\Controllers;

use App\Models\Hold;
use App\Models\Wallet;
use App\Services\LedgerService;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(LedgerService $ledger): View
    {
        $userId = auth()->id();
        $wallet = Wallet::where('user_id', $userId)->first();
        $ledgerBalance = 0;
        $activeHolds = 0;
        $recentHolds = collect();

        if ($wallet) {
            try {
                Log::info('Dashboard fetching balance', ['user_id' => $userId, 'account' => $wallet->ledger_account_address]);
                $ledgerBalance = $ledger->getBalance($wallet->ledger_account_address, $wallet->currency);
                Log::info('Dashboard balance fetched', ['user_id' => $userId, 'balance' => $ledgerBalance]);
            } catch (\Exception $e) {
                Log::error('Dashboard balance fetch failed', ['user_id' => $userId, 'error' => $e->getMessage()]);
                $ledgerBalance = $wallet->balance;
            }
            $activeHolds = Hold::where('user_id', $userId)->where('status', 'active')->count();
            $recentHolds = Hold::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('wallet', 'ledgerBalance', 'activeHolds', 'recentHolds'));
    }
}
