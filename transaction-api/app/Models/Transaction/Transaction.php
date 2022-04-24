<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'wallet_transactions';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'payee_wallet_id',
        'payer_wallet_id',
        'amount'
    ];

    public function walletPayer()
    {
        return $this->belongsTo(Wallet::class, 'payer_wallet_id');
    }

    public function walletPayee()
    {
        return $this->belongsTo(Wallet::class, 'payee_wallet_id');
    }
}