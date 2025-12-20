<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEventReminder extends Command
{
    protected $signature = 'events:send-reminders {type}';
    protected $description = 'Send event reminder emails for today or tomorrow';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type === 'today') {
            $date = Carbon::today();
            $subject = "Today's Events Reminder";
        }
        elseif ($type === 'tomorrow') {
            $date = Carbon::tomorrow();
            $subject = "Tomorrow's Events Reminder";
        }
        else {
            $this->error("Invalid type. Use: today or tomorrow");
            return;
        }

        $events = Event::whereDate('event_date', $date)->get();

        if ($events->count() === 0) {
            $this->info("No events for $type.");
            return;
        }

        // Define Admin Email (or multiple)
        // $adminEmail = config('app.admin_email', 'admin@example.com');
        // $recipients = "mehlakrish07@gmail.com";
        $recipients = ['mehlakrish07@gmail.com']; // <-- Replace with your admin email

        foreach ($recipients as $email) {
            Mail::to($email)->send(new EventReminderMail($events, $subject));
        }

        $this->info("Event reminder email sent for $type.");
    }
}
