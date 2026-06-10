<?php

namespace App\Jobs;

use App\Mail\NewOrderNotificationMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendHotelOrderNotificationJob implements ShouldQueue
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
        $this->order->loadMissing(['hotel']);
        if ($this->order->hotel && $this->order->hotel->user_id) {
            $owner = User::find($this->order->hotel->user_id);
            if ($owner && $owner->email) {
                Mail::to($owner->email)->send(new NewOrderNotificationMail($this->order));
            }
        }
    }
}
