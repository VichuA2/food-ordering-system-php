<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendCustomerOrderPlacedJob;
use App\Jobs\SendDeliveryPersonsNotificationJob;
use App\Jobs\SendHotelOrderNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderNotifications implements ShouldQueue
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
    public function handle(OrderPlaced $event): void
    {
        SendCustomerOrderPlacedJob::dispatch($event->order);
        SendHotelOrderNotificationJob::dispatch($event->order);
        SendDeliveryPersonsNotificationJob::dispatch($event->order);
    }
}
