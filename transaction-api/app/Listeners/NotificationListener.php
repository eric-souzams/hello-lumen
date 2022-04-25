<?php

namespace App\Listeners;

use App\Events\SendNotificationEvent;
use App\Services\MockyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendNotificationEvent  $event
     * @return void
     */
    public function handle(SendNotificationEvent $event)
    {
        app(MockyService::class)->notifyUser($event->transaction->wallet->user->id);
    }
}
