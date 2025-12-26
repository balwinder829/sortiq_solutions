<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryFollowup;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\SalesPendingWorkService;

class SalesDashboardController extends Controller
{
    public function index()
    {   
        $userId = Auth::id();
        $today = Carbon::today();

        // ⭐ Generate Pending Work Notifications
        (new SalesPendingWorkService)->generate(Auth::user());

        // Total assigned
        $totalAssigned = Enquiry::where('assigned_to', $userId)->count();

        // Today follow-ups
        // $todayFollowups = EnquiryFollowup::where('user_id', $userId)
        //     ->whereDate('next_followup_date', $today)
        //     ->with('enquiry')
        //     ->get();

        // Upcoming follow-ups (after today)
        $upcomingFollowups = EnquiryFollowup::where('user_id', $userId)
            ->whereDate('next_followup_date', '>', $today)
            ->with('enquiry')
            ->orderBy('next_followup_date')
            ->get();

        // Status wise count
        $statusCount = Enquiry::where('assigned_to', $userId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');


        // Today follow-ups
        $todayFollowups = EnquiryFollowup::where('user_id', $userId)
            ->whereDate('next_followup_date', $today)
            ->with('enquiry')
            ->orderByDesc('updated_at') // 🔥 LATEST STATUS FIRST
            ->get();

        // Missed follow-ups (yesterday)
        $missedFollowups = EnquiryFollowup::where('user_id', $userId)
            ->whereDate('next_followup_date', Carbon::yesterday())
            ->whereNull('status')
            ->count();

        $todaysAssigned = Enquiry::where('assigned_to', $userId)
        ->whereDate('assigned_at', $today)
        ->count();

        $todayRegistered = Enquiry::where('assigned_to', $userId)
            ->whereNotNull('registered_at')
            ->whereDate('registered_at', $today)
            ->count();

        $notPickedStatuses = [
            'Not Answered',
            'Ringing',
            'Switched Off',
            'Busy',
            'Wrong Number'
        ];

        $notPickedCount = EnquiryFollowup::where('user_id', $userId)
            ->whereIn('call_status', $notPickedStatuses)
            ->whereDate('updated_at', $today)
            ->count();

        $collegeWiseAssigned = Enquiry::where('assigned_to', $userId)
            ->whereNotNull('college')
            ->with('collegeData')
            ->selectRaw('college, COUNT(*) as total')
            ->groupBy('college')
            ->orderByDesc('total')
            ->get();

        return view('sales.dashboard', compact(
            'totalAssigned',
            'todayFollowups',
            'upcomingFollowups',
            'statusCount',
            'todaysAssigned',
            'missedFollowups',
            'todayRegistered',   // 🔥 added
            'notPickedCount',
            'collegeWiseAssigned'    
        ));
    }
}
