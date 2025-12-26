<?php

namespace App\Services;

use App\Models\EnquiryFollowup;
use App\Models\Enquiry;
use App\Models\User;
use App\Notifications\SalesFollowupsTodayNotification;
use App\Notifications\SalesFollowupsMissedNotification;
use App\Notifications\SalesLeadsLowPercentNotification;

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

         // ======================================================
        // ✅ SALES LEADS LOW (20% LEFT) — NEW LOGIC
        // ======================================================
        $this->checkSalesLeadsLowPercent($user);
    }

      /**
     * Notify Sales + Admin when sales has only 20% leads left
     */
    protected function checkSalesLeadsLowPercent($salesUser)
    {
        // Total assigned leads
        $totalAssigned = Enquiry::where('assigned_to', $salesUser->id)->count();

        if ($totalAssigned === 0) {
            return;
        }

        // Remaining usable leads
        $remainingLeads = Enquiry::where('assigned_to', $salesUser->id)
            ->whereNull('registered_at')
            ->where('lead_status', '!=', 'closed')
            ->count();

        $percentLeft = (int) floor(($remainingLeads / $totalAssigned) * 100);
        // dd($totalAssigned, $remainingLeads, $percentLeft);
        if ($percentLeft > 20) {
            return;
        }

        // Prevent duplicate notification
        // $alreadySent = $salesUser->notifications()
        //     ->whereRaw(
        //         "JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?",
        //         ['sales.leads.low.percent']
        //     )
        //     ->exists();

        // if ($alreadySent) {
        //     return;
        // }

        $alreadySentToday = $salesUser->notifications()
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?",
                ['sales.leads.low.percent']
            )
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadySentToday) {
            return;
        }



        $lastNotification = $salesUser->notifications()
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?",
                ['sales.leads.low.percent']
            )
            ->latest()
            ->first();

        if ($lastNotification) {
            $lastTotal = $lastNotification->data['meta']['total_assigned'] ?? null;

            if ($lastTotal !== null && $totalAssigned > $lastTotal) {
                // Admin has assigned new leads → reset
                return;
            }
        }


        $payload = [
            'name'      => $salesUser->name,
            'percent'   => $percentLeft,
            'remaining' => $remainingLeads,
            'sales_user_id'  => $salesUser->id,   // 👈 IMPORTANT
        ];

        // 🔔 Notify Salesperson
        $salesUser->notify(new SalesLeadsLowPercentNotification($payload));

        // 🔔 Notify Admin(s)
        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {
            $admin->notify(new SalesLeadsLowPercentNotification($payload));
        }
    }
}
