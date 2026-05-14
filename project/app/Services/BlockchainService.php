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
        'fundUser'            => '26c40c68',
        'withdrawUser'        => 'b3362ce4',
        'withdraw'            => '00f714ce',
        'holdUser'            => 'dafcfbd5',
        'releaseHold'         => 'dc6b41d8',
        'getBalance'          => 'f8b2cb4f',
        'getHold'             => '8a2dafe9',
        'getAvailableBalance' => '6c24a76f',
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

    public function getBalance(string $address): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getBalance'], $this->encodeAddress($address));
    }

    public function getHold(string $address): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getHold'], $this->encodeAddress($address));
    }

    public function getAvailableBalance(string $address): string
    {
        return $this->callRead(self::FUNCTION_SELECTORS['getAvailableBalance'], $this->encodeAddress($address));
    }

    public function fundUser(string $userAddress, float $amountInEth): string
    {
        $valueWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['fundUser'] . $this->encodeAddress($userAddress);
        return $this->sendTransaction($data, $valueWei, $this->masterPrivateKey);
    }

    public function withdrawUser(string $userAddress, float $amountInEth, string $toAddress): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['withdrawUser']
            . $this->encodeAddress($userAddress)
            . $this->encodeUint256($amountWei)
            . $this->encodeAddress($toAddress);
        return $this->sendTransaction($data, '0x0', $this->masterPrivateKey);
    }

    public function withdrawAsUser(float $amountInEth, string $toAddress, string $userPrivateKey): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['withdraw']
            . $this->encodeUint256($amountWei)
            . $this->encodeAddress($toAddress);
        return $this->sendTransaction($data, '0x0', $userPrivateKey);
    }

    public function holdUser(string $userAddress, float $amountInEth): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['holdUser']
            . $this->encodeAddress($userAddress)
            . $this->encodeUint256($amountWei);
        return $this->sendTransaction($data, '0x0', $this->masterPrivateKey);
    }

    public function releaseHold(string $userAddress, float $amountInEth): string
    {
        $amountWei = $this->ethToWei($amountInEth);
        $data = '0x' . self::FUNCTION_SELECTORS['releaseHold']
            . $this->encodeAddress($userAddress)
            . $this->encodeUint256($amountWei);
        return $this->sendTransaction($data, '0x0', $this->masterPrivateKey);
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

    private function sendTransaction(string $data, string $valueHex, string $privateKey): string
    {
        $fromAddress = $this->addressFromPrivateKey($privateKey);
        $nonce = $this->getTransactionCount($fromAddress);
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
        $signed = $transaction->sign($privateKey);
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

    private function addressFromPrivateKey(string $privateKey): string
    {
        $util = new \Web3p\EthereumUtil\Util();
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        return $util->publicKeyToAddress($publicKey);
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
