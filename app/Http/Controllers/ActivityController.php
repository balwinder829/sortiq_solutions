<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LeadActivityLog;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index1(Request $request)
    {
        $query = LeadActivityLog::with(['user', 'lead'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $logs = $query->paginate(30);

        $salesUsers = User::where('role', 3)->get();

        return view('activity.index', compact('logs', 'salesUsers'));
    }

    public function index(Request $request)
    {
        $query = LeadActivityLog::with(['user', 'lead'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $logs = $query->paginate(30);

        $salesUsers = User::where('role', 3)->get();

        return view('activity.index', compact('logs','salesUsers'));
    }


    public function leadTimeline($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);

        $timeline = LeadActivityLog::with('user')
            ->where('lead_id', $lead_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('activity.lead_timeline', compact('lead', 'timeline'));
    }

    // public function userTimeline($user_id)
    // {
    //     $user = User::findOrFail($user_id);

    //     $timeline = LeadActivityLog::with('lead')
    //         ->where('user_id', $user_id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('activity.user_timeline', compact('user', 'timeline'));
    // }

    public function userTimeline(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $query = LeadActivityLog::with('lead')
            ->where('user_id', $user_id);

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $timeline = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('activity.user_timeline', compact('user','timeline'));
    }



}
