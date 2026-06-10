<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCustomerOrderPlacedJob implements ShouldQueue
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
        $this->order->loadMissing(['customer']);
        if ($this->order->customer && $this->order->customer->email) {
            Mail::to($this->order->customer->email)->send(new OrderPlacedMail($this->order));
        }
    }
}
