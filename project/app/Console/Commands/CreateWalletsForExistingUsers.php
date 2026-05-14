<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Console\Command;

class CreateWalletsForExistingUsers extends Command
{
    protected $signature = 'wallet:create-for-existing';
    protected $description = 'Generate ETH wallets for existing users without one';

    public function handle(WalletService $walletService): void
    {
        $users = User::whereNull('eth_address')->get();

        if ($users->isEmpty()) {
            $this->info('All users already have wallets.');
            return;
        }

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            try {
                $walletService->createForUser($user);
            } catch (\Exception $e) {
                $this->error("Failed for user {$user->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done!');
    }
}
