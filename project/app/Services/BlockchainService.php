<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Web3p\EthereumTx\Transaction as EthereumTransaction;
use RuntimeException;

class BlockchainService
{
    private Client $http;
    private string $rpcUrl;
    private string $contractAddress;
    private string $masterAddress;
    private string $masterPrivateKey;

    private const FUNCTION_SELECTORS = [
        'fundUser'           => '39d3e0f9',
        'withdrawUser'       => '5fd8c7c0',
        'holdUser'           => '068fbbc2',
        'releaseHold'        => '99f088c4',
        'getBalance'         => 'f8b2cb4f',
        'getHold'            => 'bcb6a4d7',
        'getAvailableBalance' => '86e05782',
    ];

    public function __construct()
    {
        $config = config('blockchain.sepolia');
        $this->rpcUrl = $config['rpc_url'];
        $this->contractAddress = $config['contract_address'];
        $this->masterAddress = $config['master_address'];
        $this->masterPrivateKey = $config['master_private_key'];
        $this->http = new Client();
    }

    public function getBalance(int $userId): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getBalance'], $this->encodeUint256($userId));
    }

    public function getHold(int $userId): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getHold'], $this->encodeUint256($userId));
    }

    public function getAvailableBalance(int $userId): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getAvailableBalance'], $this->encodeUint256($userId));
    }

    public function fundUser(int $userId, float $amountInEth): string
    {
        $valueWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['fundUser'] . $this->encodeUint256($userId);
        return $this->sendTransaction($data, $valueWei);
    }

    public function withdrawUser(int $userId, float $amountInEth, string $toAddress): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['withdrawUser']
            . $this->encodeUint256($userId)
            . $this->encodeUint256($amountWei)
            . $this->encodeAddress($toAddress);
        return $this->sendTransaction($data, '0x0');
    }

    public function holdUser(int $userId, float $amountInEth): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['holdUser']
            . $this->encodeUint256($userId)
            . $this->encodeUint256($amountWei);
        return $this->sendTransaction($data, '0x0');
    }

    public function releaseHold(int $userId, float $amountInEth): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['releaseHold']
            . $this->encodeUint256($userId)
            . $this->encodeUint256($amountWei);
        return $this->sendTransaction($data, '0x0');
    }

    private function callRead(string $selector, string $encodedParams): string
    {
        $data = '0x' . $selector . $encodedParams;
        $payload = [
            'jsonrpc' => '2.0',
            'method' => 'eth_call',
            'params' => [
                ['to' => $this->contractAddress, 'data' => $data],
                'latest',
            ],
            'id' => 1,
        ];

        $result = $this->rpcCall($payload);
        return $this->decodeUint256($result);
    }

    private function sendTransaction(string $data, string $valueHex): string
    {
        $nonce = $this->getTransactionCount($this->masterAddress);
        $gasPrice = $this->getGasPrice();
        $gas = '0x' . dechex(200000);
        $rawChainId = $this->getChainId();
        $chainId = (int) hexdec(str_replace('0x', '', $rawChainId));

        $txData = [
            'nonce' => $nonce,
            'gasPrice' => $gasPrice,
            'gas' => $gas,
            'to' => $this->contractAddress,
            'value' => $valueHex,
            'data' => $data,
            'chainId' => $chainId,
        ];

        $transaction = new EthereumTransaction($txData);
        $signed = $transaction->sign($this->masterPrivateKey);
        $signedHex = '0x' . $signed;

        return $this->sendRawTransaction($signedHex);
    }

    private function rpcCall(array $payload): string
    {
        try {
            $response = $this->http->post($this->rpcUrl, [
                'json' => $payload,
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $body = json_decode($response->getBody(), true);

            if (isset($body['error'])) {
                throw new RuntimeException('RPC error: ' . json_encode($body['error']));
            }

            return $body['result'] ?? '0x';
        } catch (GuzzleException $e) {
            throw new RuntimeException('HTTP request failed: ' . $e->getMessage());
        }
    }

    private function getTransactionCount(string $address): string
    {
        return $this->rpcCall([
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionCount',
            'params' => [$address, 'pending'],
            'id' => 1,
        ]);
    }

    private function getGasPrice(): string
    {
        return $this->rpcCall([
            'jsonrpc' => '2.0',
            'method' => 'eth_gasPrice',
            'params' => [],
            'id' => 1,
        ]);
    }

    private function getChainId(): string
    {
        return $this->rpcCall([
            'jsonrpc' => '2.0',
            'method' => 'eth_chainId',
            'params' => [],
            'id' => 1,
        ]);
    }

    private function sendRawTransaction(string $signedHex): string
    {
        return $this->rpcCall([
            'jsonrpc' => '2.0',
            'method' => 'eth_sendRawTransaction',
            'params' => [$signedHex],
            'id' => 1,
        ]);
    }

    private function encodeUint256(int $value): string
    {
        return str_pad(gmp_strval(gmp_init($value, 10), 16), 64, '0', STR_PAD_LEFT);
    }

    private function encodeAddress(string $address): string
    {
        $addr = str_replace('0x', '', strtolower($address));
        return str_pad($addr, 64, '0', STR_PAD_LEFT);
    }

    private function decodeUint256(string $hex): string
    {
        $hex = str_replace('0x', '', $hex);
        if (empty($hex)) {
            return '0';
        }
        return gmp_strval(gmp_init($hex, 16), 10);
    }

    private function ethToWei(float $eth): string
    {
        $wei = (int) ($eth * 1e18);
        return '0x' . str_pad(gmp_strval(gmp_init($wei, 10), 16), 64, '0', STR_PAD_LEFT);
    }

    public function isConfigured(): bool
    {
        return !empty($this->rpcUrl)
            && $this->rpcUrl !== 'https://sepolia.infura.io/v3/YOUR_PROJECT_ID'
            && !empty($this->contractAddress)
            && !empty($this->masterAddress)
            && !empty($this->masterPrivateKey);
    }
}
