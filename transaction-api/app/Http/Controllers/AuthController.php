<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function postAuthenticate(Request $request, string $provider)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $providers = ['user', 'retailer'];

        if (!in_array($provider, $providers)) {
            return response()->json(['erros' => ['main' => 'Wrong provider provided']], 422);
        }

        $selectedProvider = $this->getProvider($provider);

        $model = $selectedProvider->where('email', $request->input('email'))->first();

        if (!$model) {
            return response()->json(['erros' => ['main' => 'Wrong credentials']], 401);
        }

        if (!Hash::check($request->input('password'), $model->password)) {
            return response()->json(['erros' => ['main' => 'Wrong credentials']], 401);
        }

        return response()->json([]);
    }

    public function getProvider(string $provider): Authenticatable
    {
        if ($provider == "user") {
            return new User();
        } elseif ($provider == "retailer") {
            return new Retailer();
        } else {
            throw new \Exception('Provider not found');
        }
    }
}
