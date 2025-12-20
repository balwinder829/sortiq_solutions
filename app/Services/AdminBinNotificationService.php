<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Notifications\BinReadySummaryNotification;
use Carbon\Carbon;

class AdminBinNotificationService
{
    public function sendDailySummary()
    {
        $sessionId = session('admin_session_id');

        if (!$sessionId) {
            return; // No session selected
        }

        // Students ready for bin
        $count = Student::where('pending_fees', 0)
            ->where('certificate_status', 2)
               ->where('email_count_certificate','>' ,0)
               ->where('session', $sessionId)
            ->count();

        if ($count == 0) {
            return;
        }

        // All admins
        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {

            // Prevent duplicate for the day & session
            $exists = $admin->notifications()
                ->where('data->template_key', 'bin.ready.summary')
                ->where('data->session_id', $sessionId)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($exists) continue;

            $admin->notify(new BinReadySummaryNotification($count, $sessionId));
        }
    }
}
