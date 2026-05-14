<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;
use Web3p\EthereumUtil\Util;

class WalletService
{
    public function generateWallet(): array
    {
        $privateKey = bin2hex(random_bytes(32));

        $util = new Util();
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        $address = $util->publicKeyToAddress($publicKey);

        return [
            'address' => $address,
            'private_key' => '0x' . $privateKey,
        ];
    }

    public function createForUser(User $user): void
    {
        if ($user->eth_address) {
            throw new RuntimeException('User already has a wallet.');
        }

        $wallet = $this->generateWallet();

        $user->eth_address = $wallet['address'];
        $user->encrypted_private_key = Crypt::encryptString($wallet['private_key']);
        $user->save();
    }

    public function getDecryptedPrivateKey(User $user): ?string
    {
        if (!$user->encrypted_private_key) {
            return null;
        }

        return Crypt::decryptString($user->encrypted_private_key);
    }
}
