<?php

namespace App\Repositories;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\InvalidDataProviderException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class AuthRepository {
    public function __construct()
    {
        
    }

    public function authenticate(string $provider, array $fields): array
    {
        $providers = ['user', 'retailer'];

        if (!in_array($provider, $providers)) {
            throw new InvalidDataProviderException('Wrong provider provided');
        }

        $selectedProvider = $this->getProvider($provider);

        $model = $selectedProvider->where('email', $fields['email'])->first();

        if (!$model) {
            throw new AuthorizationException('Wrong credentials');
        }

        if (!Hash::check($fields['password'], $model->password)) {
            throw new AuthorizationException('Wrong credentials');
        }

        $token = $model->createToken($provider);

        return [
            'access_token' => $token->accessToken, 
            'expires_at' => $token->token->expires_at, 
            'provider' => $provider
        ];
    }

    public function getProvider(string $provider): AuthenticatableContract
    {
        if ($provider == "user") {
            return new User();
        } elseif ($provider == "retailer") {
            return new Retailer();
        } else {
            throw new \Exception('Provider not found', 422);
        }
    }
}