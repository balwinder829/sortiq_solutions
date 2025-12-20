<?php

namespace App\Services;

use App\Models\EnquiryFollowup;
use App\Notifications\SalesFollowupsTodayNotification;
use App\Notifications\SalesFollowupsMissedNotification;
use Carbon\Carbon;

class SalesPendingWorkService
{
    public function generate($user)
    {
        $userId = $user->id;

        // ================== TODAY PENDING FOLLOWUPS ==================
        $todayCount = EnquiryFollowup::where('user_id', $userId)
            ->whereDate('next_followup_date', Carbon::today())
            ->whereNull('status')   // null = pending
            ->count();

        if ($todayCount > 0) {
            $user->notify(new SalesFollowupsTodayNotification($todayCount));
        }

        // ================== MISSED FOLLOWUPS (YESTERDAY) ==================
        $missedCount = EnquiryFollowup::where('user_id', $userId)
            ->whereDate('next_followup_date', Carbon::yesterday())
            ->whereNull('status')
            ->count();

        if ($missedCount > 0) {
            $user->notify(new SalesFollowupsMissedNotification($missedCount));
        }
    }
}
