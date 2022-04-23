<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'wallet_transactions';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'payee_id',
        'payer_id',
        'amount'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}