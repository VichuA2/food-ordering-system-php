<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('send:remainders')]
#[Description('Command description')]
class SendAppReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Sending Reminders");
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                if ($user->hasRole('Customer') && $user->email) {
                    Mail::to($user->email)->queue(new ReminderMail());
                }
            }
        });
        $this->info("Reminders Sent");
    }
}
