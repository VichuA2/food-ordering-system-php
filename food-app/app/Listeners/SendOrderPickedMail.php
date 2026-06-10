<?php

namespace App\Listeners;

use App\Events\OrderPicked;
use App\Jobs\SendCustomerOrderPickedJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderPickedMail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPicked $event): void
    {
        SendCustomerOrderPickedJob::dispatch($event->order);
    }
}
