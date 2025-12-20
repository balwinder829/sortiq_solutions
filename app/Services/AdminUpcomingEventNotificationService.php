<?php

// app/Services/AdminUpcomingEventNotificationService.php

namespace App\Services;

use App\Models\User;
use App\Models\UpcomingEvent;
use App\Notifications\UpcomingEventNotification;
use Carbon\Carbon;

class AdminUpcomingEventNotificationService
{
    public function sendDailySummary()
    {
        // All active upcoming events
        $events = UpcomingEvent::where('notify', true)
            ->where('dismissed', false)
            ->whereDate('event_date', '>=', now())
            ->get();

        if ($events->isEmpty()) {
            return;
        }

        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {
            foreach ($events as $event) {

                // Prevent duplicate notification per event per day
                $exists = $admin->notifications()
                    ->where('data->template_key', 'upcoming.event')
                    ->where('data->event_id', $event->id)
                    ->whereDate('created_at', Carbon::today())
                    ->exists();

                if ($exists) continue;

                $admin->notify(new UpcomingEventNotification($event));
            }
        }
    }
}
