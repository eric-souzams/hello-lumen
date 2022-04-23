<?php

namespace App\Repositories\Transaction;

use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\TransactionDeniedException;
use App\Models\Retailer;
use App\Models\User;
use PHPUnit\Framework\InvalidDataProviderException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Auth;

class TransactionRepository 
{
    public function handle(array $data): array
    {
        if (!$this->guardCanTransfer()) {
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        $provider = $this->getProvider($data['provider']);

        $user = $provider->findOrFail($data['payee_id']);

        if (!$this->checkUserBalance($user, $data['amount'])) {
            throw new NotEnoughBalanceException('You do not have enough balance', 422);
        }

        return [];
    }

    public function checkUserBalance($user, $amount)
    {
        return $user->wallet->balance >= $amount;
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
        if ($provider == "user") {
            return new User();
        } elseif ($provider == "retailer") {
            return new Retailer();
        } else {
            throw new InvalidDataProviderException('Wrong provider provided');
        }
    }
}