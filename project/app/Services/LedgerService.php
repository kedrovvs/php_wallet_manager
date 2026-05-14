<?php

namespace App\Services;

use GuzzleHttp\Client;

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
        $response = $this->client->post("/api/{$this->ledgerName}/transactions", [
            'json' => [
                'script' => [
                    'plain' => $script,
                    'vars' => []
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getBalance(string $accountAddress, string $currency = 'USD'): int
    {
        $response = $this->client->get("/api/{$this->ledgerName}/accounts/{$accountAddress}");
        $data = json_decode($response->getBody(), true);

        return $data['data']['balances'][$currency] ?? 0;
    }
}
