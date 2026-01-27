<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyInterview;
use App\Notifications\AdminTodayInterviewNotification;
use Carbon\Carbon;

class AdminTodayInterviewNotificationService
{
    public function sendTodaySummary(): void
    {
        $todayCount = DailyInterview::whereDate('availability_datetime', Carbon::today())
            ->where('interview_status', 'Scheduled')
            ->count();

        if ($todayCount === 0) {
            return;
        }

        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {

            // Prevent duplicate notification per day
            $exists = $admin->notifications()
                ->where('data->template_key', 'admin.interviews.today')
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($exists) continue;

            $admin->notify(
                new AdminTodayInterviewNotification($todayCount)
            );
        }
    }
}
