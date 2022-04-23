<?php

namespace App\Models\Transaction;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'balance',
        'user_id',
    ];

    public function transactions() 
    {
        return $this->hasMany(Transaction::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}