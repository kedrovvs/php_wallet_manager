<?php

return [
    'sepolia' => [
        'rpc_url' => env('SEPOLIA_RPC_URL', 'https://sepolia.infura.io/v3/YOUR_PROJECT_ID'),
        'contract_address' => env('SEPOLIA_CONTRACT_ADDRESS', ''),
        'master_address' => env('SEPOLIA_MASTER_ADDRESS', ''),
        'master_private_key' => env('SEPOLIA_MASTER_PRIVATE_KEY', ''),
    ],
];
