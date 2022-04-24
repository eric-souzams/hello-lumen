<?php

namespace App\Repositories\Transaction;

use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\TransactionDeniedException;
use App\Models\Retailer;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\Wallet;
use App\Models\User;
use PHPUnit\Framework\InvalidDataProviderException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TransactionRepository 
{
    public function handle(array $data): Transaction
    {
        if (!$this->guardCanTransfer()) {
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        if (!$payee = $this->retrievePayer($data)) {
            throw new InvalidDataProviderException('empty');
        }
        
        $myWallet = Auth::guard($data['provider'])->user()->wallet;
        
        if (!$this->checkUserBalance($myWallet, $data['amount'])) {
            throw new NotEnoughBalanceException('You do not have enough balance', 422);
        }

        return $this->makeTransaction($payee, $data);
    }

    public function guardCanTransfer(): bool
    {
        if (Auth::guard('users')->check()) {
            return true;
        } else if (Auth::guard('retailers')->check()) {
            return false;
        } else {
            throw new InvalidDataProviderException('Provider Not found', 422);
        }
    }

    public function getProvider(string $provider): AuthenticatableContract
    {
        if ($provider == "users") {
            return new User();
        } elseif ($provider == "retailers") {
            return new Retailer();
        } else {
            throw new InvalidDataProviderException('Wrong provider provided');
        }
    }

    private function checkUserBalance(Wallet $wallet, $amount)
    {
        return $wallet->balance >= $amount;
    }

    private function retrievePayer(array $data)
    {
        $provider = $this->getProvider($data['provider']);

        return $provider->findOrFail($data['payee_id']);
    }

    private function makeTransaction($payee, array $data)
    {
        $payload = [
            'id' => Uuid::uuid4()->toString(),
            'payer_wallet_id' => Auth::guard($data['provider'])->user()->wallet->id,
            'payee_wallet_id' => $payee->wallet->id,
            'amount' => $data['amount']
        ];

        
        return DB::transaction(function () use ($payload) {
            $transaction = Transaction::create($payload);
            
            $transaction->walletPayer->withdraw($payload['amount']);
            $transaction->walletPayee->deposit($payload['amount']);

            return $transaction;
        });
    }
}