<?php

namespace App\Events;

use App\Models\Transaction\Transaction;

class SendNotificationEvent extends Event
{
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
