<?php

namespace App\Jobs;

use App\Mail\NewOrderDeliveryMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDeliveryPersonsNotificationJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    public $order;
    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deliveryUsers = User::role('Delivery Person')->get();

        foreach ($deliveryUsers as $deliveryBoy) {
            if ($deliveryBoy->email) {
                Mail::to($deliveryBoy->email)->send(new NewOrderDeliveryMail($this->order));
            }
        }
    }
}
