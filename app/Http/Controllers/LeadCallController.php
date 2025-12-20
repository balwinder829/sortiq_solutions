<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\LeadActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadCallController extends Controller
{

    public function store(Request $request, Lead $lead)
{
    $request->validate([
        'call_status'    => 'required|string|max:191',
        'lead_status'    => 'nullable|string|max:191',
        'follow_up_date' => 'nullable|date',
        'remark'         => 'nullable|string',
    ]);

    // ===== 1. CREATE CALL LOG ENTRY =====
    $call = LeadCall::create([
        'lead_id'        => $lead->id,
        'user_id'        => Auth::id(),
        'call_status'    => $request->call_status,
        'lead_status'    => $request->lead_status,
        'follow_up_date' => $request->follow_up_date,
        'remark'         => $request->remark,
    ]);

    // ===== 2. Prepare Activity Log Data =====
    $activityDetails = [
        'call_status'    => $request->call_status,
        'lead_status'    => $request->lead_status,
        'follow_up_date' => $request->follow_up_date,
        'remark'         => $request->remark,
    ];

    LeadActivityLog::create([
        'lead_id' => $lead->id,
        'user_id' => Auth::id(),
        'action'  => 'call_made',
        'new_value' => json_encode($activityDetails),
    ]);

    // ===== 3. UPDATE LEAD MAIN DATA =====
    $oldValues = $lead->getOriginal();

    if ($request->lead_status) {
        $lead->status = $request->lead_status;
    }
    $lead->follow_up_date = $request->follow_up_date;
    $lead->save();

    // ===== 4. LOG LEAD UPDATE (only changed fields) =====
    $changes = $lead->getChanges();

    unset($changes['updated_at']);
    unset($oldValues['updated_at']);

    if (!empty($changes)) {
        $oldChanged = array_intersect_key($oldValues, $changes);

        LeadActivityLog::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action'  => 'lead_updated_after_call',
            'old_value' => json_encode($oldChanged),
            'new_value' => json_encode($changes),
        ]);
    }

    return redirect()->route('leads.show', $lead->id)
                     ->with('success', 'Call logged successfully.');
}

    public function storeold(Request $request, Lead $lead)
    {
        $request->validate([
            'call_status'    => 'required|string|max:191',
            'lead_status'    => 'nullable|string|max:191',
            'follow_up_date' => 'nullable|date',
            'remark'         => 'nullable|string',
        ]);

        LeadCall::create([
            'lead_id'        => $lead->id,
            'user_id'        => Auth::id(),
            'call_status'    => $request->call_status,
            'lead_status'    => $request->lead_status,
            'follow_up_date' => $request->follow_up_date,
            'remark'         => $request->remark,
        ]);

        // Update main lead record
        if ($request->lead_status) {
            $lead->status = $request->lead_status;
        }
        $lead->follow_up_date = $request->follow_up_date;
        $lead->save();

        return redirect()->route('leads.show', $lead->id)->with('success', 'Call logged successfully.');
    }
}
