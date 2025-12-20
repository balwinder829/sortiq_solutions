<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Notifications\FeePendingSummaryNotification;
use Carbon\Carbon;

class AdminFeeNotificationService
{
    public function sendDailyFeeSummary()
    {
        $activeSessionId = session('admin_session_id');

        if (!$activeSessionId) {
            return; // no session selected → do not send notification
        }

        // Count pending fees for active session
        $pendingCount = Student::where('pending_fees', '>', 0)
            ->whereDate('next_due_date', '<=', Carbon::today())
            ->where('session', $activeSessionId)
            ->where('certificate_status', 1)
            ->count();

        if ($pendingCount == 0) {
            return; // nothing to notify
        }

        // Send notification to all admins (role=1)
        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {

            // Prevent duplicate notification for today for this session
            $alreadyNotified = $admin->notifications()
                ->where('data->template_key', 'fee.pending.summary')
                ->where('data->session_id', $activeSessionId)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            // Send final notification
            $admin->notify(new FeePendingSummaryNotification($pendingCount, $activeSessionId));
        }
    }
}
