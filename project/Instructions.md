This is a great stack choice. Using **Formance Ledger** as a microservice separates your core accounting logic (the "source of truth") from your application logic (Laravel), ensuring auditability and correctness for financial transactions.

Below is the comprehensive Architecture Plan and Development Guide.

---

### 1. High-Level Architecture

We will implement an **Application-Ledger Pattern**.
*   **Laravel App (Application Layer):** Handles Users, Authentication, UI, and "Intent". It manages the state of user requests (e.g., "Pending Withdrawal").
*   **PostgreSQL (Operational DB):** Stores user profiles, wallet configurations, and a local mirror of transaction states for quick querying.
*   **Formance Ledger (Accounting Layer):** The immutable source of truth for balances. It ensures double-entry accounting principles are never violated.

#### Architecture Diagram

```mermaid
graph TD
    User[User (Browser)] --> Laravel[Laravel App (Docker)]
    
    subgraph "Internal Network"
        Laravel --> PG_Ops[(Postgres - Ops DB)]
        Laravel -- REST API --> Formance[Formance Ledger]
        Formance --> PG_Ledger[(Postgres - Ledger DB)]
    end
    
    style Laravel fill:#f9f,stroke:#333,stroke-width:2px
    style Formance fill:#bbf,stroke:#333,stroke-width:2px
```

---

### 2. Infrastructure Setup (Docker)

You will need a `docker-compose.yml` to orchestrate the services.

**Key Services:**
1.  **App:** PHP-FPM + Nginx (or Caddy).
2.  **Postgres:** For Laravel.
3.  **Formance Ledger:** The ledger service.
4.  **Ledger-DB:** A separate Postgres instance (or schema) for Formance to keep financial data isolated.

**`docker-compose.yml` snippet:**

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel_app
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    networks:
      - wallet-net
    depends_on:
      - postgres_ops
      - ledger

  postgres_ops:
    image: postgres:15
    environment:
      POSTGRES_DB: wallet_app
      POSTGRES_USER: user
      POSTGRES_PASSWORD: secret
    volumes:
      - ops_data:/var/lib/postgresql/data
    networks:
      - wallet-net

  ledger:
    image: ghcr.io/formancehq/ledger:latest
    container_name: formance_ledger
    environment:
      - STORAGE_DRIVER=postgres
      - STORAGE_POSTGRES_STRING=postgres://ledger_user:ledger_secret@ledger_db:5432/ledger_db?sslmode=disable
      - BIND=:8080
    ports:
      - "3068:8080" # Ledger API Port
    depends_on:
      - ledger_db
    networks:
      - wallet-net

  ledger_db:
    image: postgres:15
    environment:
      POSTGRES_DB: ledger_db
      POSTGRES_USER: ledger_user
      POSTGRES_PASSWORD: ledger_secret
    volumes:
      - ledger_data:/var/lib/postgresql/data
    networks:
      - wallet-net

volumes:
  ops_data:
  ledger_data:

networks:
  wallet-net:
```

---

### 3. Data Modeling & Ledger Design

#### A. Laravel Database (Operational)
You need a table to map your Users to Formance Accounts.

**Wallets Table:**
*   `id`
*   `user_id`
*   `ledger_account_address` (string, e.g., `wallets:102`)
*   `currency` (string, e.g., `USD`)
*   `balance` (integer - cached for UI performance, updated via webhooks or polling)

#### B. Formance Ledger Design
Formance uses **Numscript** to define transaction logic. We need a strategy for "Hold".

**The Chart of Accounts:**
1.  `world`: The external source of money (Bank/Stripe).
2.  `wallets:{id}`: The user's main available balance.
3.  `holds:{id}`: A specific account to track money locked during a hold.

---

### 4. Feature Implementation Plan

Here is how each requirement maps to the backend logic.

#### Feature 1: Create a Wallet
*   **Laravel Action:** Create a record in the local DB.
*   **Formance Action:** Ensure the account exists in the ledger (Formance creates accounts on-the-fly when referenced, or you can create metadata).
*   **Logic:**
    ```php
    // Laravel Controller
    $wallet = Wallet::create([
        'user_id' => auth()->id(),
        'ledger_account_address' => 'wallets:' . auth()->id(),
        'currency' => 'USD'
    ]);
    ```

#### Feature 2: Fund Money (Deposit)
*   **Flow:** External Source -> User Wallet.
*   **Numscript:**
    ```text
    send [COIN 100] (
        source = @world
        destination = @wallets:102
    )
    ```
*   **Laravel Implementation:** Call the Formance API `POST /{ledger}/transactions` with the script above.

#### Feature 3: Withdraw Money
*   **Flow:** User Wallet -> External Source.
*   **Logic:** Same as deposit, but reverse source/destination.
*   **Check:** Ensure the user has enough balance *before* sending the request (Formance will reject it if insufficient funds, but a pre-check is good UX).

#### Feature 4: Hold Configurable Amount
This is the core logic. We move money from the user's available wallet to a temporary "Hold" account. This ensures the user cannot spend money that is reserved.

*   **Scenario:** User wants to hold $50 for a pending purchase.
*   **Numscript:**
    ```text
    send [COIN 50] (
        source = @wallets:102
        destination = @holds:order_55
    )
    ```
*   **Result:** The `wallets:102` balance decreases. The `holds:order_55` balance increases.
*   **Laravel Logic:**
    1.  Receive request (Amount, Reference ID).
    2.  Construct Numscript.
    3.  Send to Formance.
    4.  Store the transaction ID in your local DB as "Hold Active".

#### Feature 5: Cancel Hold
We move the money back from the Hold account to the Wallet.

*   **Numscript:**
    ```text
    send [COIN 50] (
        source = @holds:order_55
        destination = @wallets:102
    )
    ```
*   **Note:** "Capture" (finalizing the payment) would involve moving from `@holds:order_55` to `@merchant_account`.

---

### 5. Development Steps (Laravel Code)

I recommend using the `guzzlehttp/guzzle` package to communicate with Formance.

#### Step 1: Create a Ledger Service
Create a service class to handle the HTTP calls.

```php
// app/Services/LedgerService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

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

    /**
     * Execute a Numscript transaction
     */
    public function executeTransaction(string $script): array
    {
        $response = $this->client->post("/api/{$this->ledgerName}/transactions", [
            'json' => [
                'script' => [
                    'plain' => $script,
                    'vars' => [] // You can pass variables here to make scripts dynamic
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get Account Balance
     */
    public function getBalance(string $accountAddress): int
    {
        $response = $this->client->get("/api/{$this->ledgerName}/accounts/{$accountAddress}");
        $data = json_decode($response->getBody(), true);
        
        // Formance returns balances as an object of assets, e.g. {"USD": 1000}
        return $data['data']['balances']['USD'] ?? 0;
    }
}
```

#### Step 2: Implement Wallet Funding
```php
// app/Http/Controllers/WalletController.php

public function fund(Request $request, LedgerService $ledger)
{
    $request->validate(['amount' => 'required|integer|min:1']);
    $amount = $request->amount;
    $userId = auth()->id();
    $walletAddr = "wallets:{$userId}";

    // Numscript for Funding
    $script = <<<EOD
    send [USD {$amount}] (
        source = @world
        destination = @{$walletAddr}
    )
    EOD;

    try {
        $result = $ledger->executeTransaction($script);
        
        // Update local cache balance (optional)
        // Wallet::where('user_id', $userId)->increment('balance', $amount);

        return response()->json(['status' => 'funded', 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Funding failed'], 500);
    }
}
```

#### Step 3: Implement Hold Logic
```php
public function hold(Request $request, LedgerService $ledger)
{
    $request->validate([
        'amount' => 'required|integer|min:1',
        'hold_reference' => 'required|string' // e.g. "order_123"
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
        // This will fail if user balance is insufficient (HTTP 400 from Ledger)
        $result = $ledger->executeTransaction($script);
        return response()->json(['status' => 'hold_created']);
    } catch (\Exception $e) {
        // Handle insufficient funds
        return response()->json(['error' => 'Insufficient funds or System error'], 400);
    }
}
```

#### Step 4: Cancel Hold
```php
public function cancelHold(Request $request, LedgerService $ledger)
{
    $request->validate(['hold_reference' => 'required|string']);
    
    $userId = auth()->id();
    $walletAddr = "wallets:{$userId}";
    $holdAddr = "holds:{$request->hold_reference}";

    // Ideally, you look up the exact amount of the hold from your DB first
    // For this example, we assume we know the amount or fetch it from ledger metadata
    $amount = 100; // This should be dynamic based on the hold state

    $script = <<<EOD
    send [USD {$amount}] (
        source = @{$holdAddr}
        destination = @{$walletAddr}
    )
    EOD;

    $ledger->executeTransaction($script);
    
    return response()->json(['status' => 'hold_canceled']);
}
```

### Summary of Benefits
1.  **Atomicity:** The `hold` operation happens in a single transaction within Formance. You cannot accidentally spend held money.
2.  **Audit Trail:** Every movement (Fund, Hold, Cancel, Withdraw) is recorded immutably in the Ledger.
3.  **Scalability:** The Ledger can handle high throughput, and the Laravel app focuses solely on the UI/API orchestration.