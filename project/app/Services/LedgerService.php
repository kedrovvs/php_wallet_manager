<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class LedgerService
{
    protected Client $client;
    protected string $ledgerName = 'wallet_ledger';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('LEDGER_URL', 'http://ledger:8080'),
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function executeTransaction(string $script): array
    {
        $url = "/{$this->ledgerName}/transactions";
        $payload = [
            'script' => [
                'plain' => $script,
                    'vars' => (object) []
            ]
        ];

        Log::info('Ledger API request', [
            'method' => 'POST',
            'url' => $url,
            'base_uri' => env('LEDGER_URL', 'http://ledger:8080'),
            'body' => $payload,
        ]);

        try {
            $response = $this->client->post($url, ['json' => $payload]);
            $body = json_decode($response->getBody(), true);

            Log::info('Ledger API response', [
                'status' => $response->getStatusCode(),
                'body' => $body,
            ]);

            return $body;
        } catch (GuzzleException $e) {
            Log::error('Ledger API error', [
                'message' => $e->getMessage(),
                'url' => $url,
            ]);
            throw $e;
        }
    }

    public function getBalance(string $accountAddress, string $currency = 'USD'): int
    {
        $encodedAddress = rawurlencode($accountAddress);
        $url = "/{$this->ledgerName}/accounts/{$encodedAddress}";

        Log::info('Ledger API request', [
            'method' => 'GET',
            'url' => $url,
            'base_uri' => env('LEDGER_URL', 'http://ledger:8080'),
        ]);

        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            Log::info('Ledger API response', [
                'status' => $response->getStatusCode(),
                'body' => $data,
            ]);

            return $data['data']['balances'][$currency] ?? 0;
        } catch (GuzzleException $e) {
            Log::error('Ledger API error', [
                'message' => $e->getMessage(),
                'url' => $url,
            ]);
            throw $e;
        }
    }
}
