<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
        // $this->middleware('auth:retailers');
    }

    public function getMe()
    {
        return response()->json(Auth::guard('users')->user());
    }
}
