<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\LeadActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Imports\LeadImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LeadController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // Only restrict users with role = 3 (sales)
            if (auth()->user()->role == 3) {

                // If the route has a lead parameter
                $lead = $request->route('lead');

                if ($lead) {
                    if (
                        $lead->assigned_to != auth()->id() &&
                        $lead->created_by != auth()->id()
                    ) {
                        abort(403, 'You are not allowed to access this lead.');
                    }
                }
            }

            return $next($request);
        });
    }

    // public function index(Request $request)
    // {
    //     // $query = Lead::with('assignedTo');
    //     $query = Lead::query()->with('assignedTo', 'creator');

    //     // if(auth()->user()->role == 3 ) {
    //     //     $query->where(function($q){
    //     //         $q->where('assigned_to', auth()->id())
    //     //           ->orWhere('created_by', auth()->id());
    //     //     });
    //     // }

    //     // Filters
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }
    //     if ($request->filled('assigned_to')) {
    //         $query->where('assigned_to', $request->assigned_to);
    //     }

    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', "%$search%")
    //               ->orWhere('phone', 'like', "%$search%")
    //               ->orWhere('email', 'like', "%$search%");
    //         });
    //     }

    //     $leads = $query->latest()->paginate(20);
    //     // $users = User::all();
    //     $users = User::where('role', 3)->get();


    //     return view('leads.index', compact('leads', 'users'));
    // }

    public function index(Request $request)
    {
        $query = Lead::with('assignedTo', 'creator');

        // SALES RESTRICTION — Sales user sees only their assigned or created leads
        if (auth()->user()->role == 3) {
            $query->where(function ($q) {
                $q->where('assigned_to', auth()->id())
                  ->orWhere('created_by', auth()->id());
            });
        }

        // ------------------ FILTERS ------------------

        // Status filter (new, contacted, follow_up, onboarded, not_interested)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Assigned to filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Follow-up date filter (for dashboard)
        if ($request->filled('follow_up_date')) {
            $query->whereDate('follow_up_date', $request->follow_up_date);
        }

        // Today calls filter (from dashboard)
        if ($request->filled('calls') && $request->calls == 'today') {
            $query->whereHas('calls', function($q){
                $q->whereDate('created_at', today());
            });
        }

        // Search by name, email, phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // ------------------ LOAD DATA ------------------
        $leads = $query->latest()->paginate(20);

        // Only sales users for assign dropdown
        $users = User::where('role', 3)->get();

        return view('leads.index', compact('leads', 'users'));
    }


    public function create()
    {
        $users = User::where('role', 3)->get();
        return view('leads.create', compact('users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name'   => 'nullable|string|max:191',
        'email'  => 'nullable|email',
        'phone'  => 'nullable|string|max:50',
        'source' => 'nullable|string|max:191',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    // Insert lead
    $lead = Lead::create([
        'name'         => $request->name,
        'email'        => $request->email,
        'phone'        => $request->phone,
        'source'       => $request->source,
        'assigned_to'  => $request->assigned_to,
        'status'       => $request->status ?? 'new',
        'follow_up_date' => $request->follow_up_date,
        'notes'        => $request->notes,
        'created_by'   => auth()->id(),
    ]);

    // Log activity - only provided fields, no timestamps
    $newValues = $request->only([
        'name','email','phone','source','assigned_to','status','follow_up_date','notes'
    ]);

    LeadActivityLog::create([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action'  => 'lead_created',
        'new_value' => json_encode($newValues),
    ]);

    return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
}


    public function store12(Request $request)
{
    $request->validate([
        'name'   => 'nullable|string|max:191',
        'email'  => 'nullable|email',
        'phone'  => 'nullable|string|max:50',
        'source' => 'nullable|string|max:191',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    // Save lead first
    $lead = Lead::create($request->only([
        'name','email','phone','source','assigned_to','status','follow_up_date','notes'
    ]) + [
        'status'     => $request->status ?? 'new',
        'created_by' => auth()->id(),
    ]);

    // Log creation activity
    LeadActivityLog::create([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'lead_created',
        'new_value' => json_encode($lead),
    ]);

    return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
}


    public function store_old(Request $request)
    {
        $request->validate([
            'name'   => 'nullable|string|max:191',
            'email'  => 'nullable|email',
            'phone'  => 'nullable|string|max:50',
            'source' => 'nullable|string|max:191',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Lead::create($request->only([
            'name','email','phone','source','assigned_to','status','follow_up_date','notes'
        ]) + [
            'status'     => $request->status ?? 'new',
            'created_by' => auth()->id(),
        ]);

        LeadActivityLog::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'call_made',
            'note' => $request->remarks
        ]);


        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'calls.user']);
        $users = User::where('role', 3)->get();

        return view('leads.show', compact('lead', 'users'));
    }

    public function edit(Lead $lead)
    {
        $users = User::where('role', 3)->get();
        return view('leads.edit', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
{
    $request->validate([
        'name'   => 'nullable|string|max:191',
        'email'  => 'nullable|email',
        'phone'  => 'nullable|string|max:50',
        'source' => 'nullable|string|max:191',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    // Capture old values BEFORE update
    $oldValues = $lead->getOriginal();

    // Update the lead
    $lead->update($request->only([
        'name','email','phone','source','assigned_to','status','follow_up_date','notes'
    ]));

    // Detect actual changed fields
    $changes = $lead->getChanges();

    // Remove auto-updated fields
    unset($changes['updated_at']);
    unset($oldValues['updated_at']);

    // If nothing changed, skip logging
    if (empty($changes)) {
        return redirect()->route('leads.show', $lead->id)
                         ->with('success', 'Lead updated.');
    }

    // Only log the old values FOR the fields that changed
    $oldChanged = array_intersect_key($oldValues, $changes);

    // Create activity log
    LeadActivityLog::create([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'lead_updated',
        'old_value' => json_encode($oldChanged),
        'new_value' => json_encode($changes),
    ]);

    return redirect()->route('leads.show', $lead->id)
                     ->with('success', 'Lead updated.');
}


    public function updateqw(Request $request, Lead $lead)
{
    $request->validate([
        'name'   => 'nullable|string|max:191',
        'email'  => 'nullable|email',
        'phone'  => 'nullable|string|max:50',
        'source' => 'nullable|string|max:191',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    // Get old values BEFORE update
    $oldValues = $lead->getOriginal();

    // Perform update
    $lead->update($request->only([
        'name','email','phone','source','assigned_to','status','follow_up_date','notes'
    ]));

    // Detect what actually changed
    $changes = $lead->getChanges();

    // Log only if something changed
    if (!empty($changes)) {
        LeadActivityLog::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'lead_updated',
            'old_value' => json_encode($oldValues),
            'new_value' => json_encode($changes),
        ]);
    }

    return redirect()->route('leads.show', $lead->id)->with('success', 'Lead updated.');
}


    public function update_old(Request $request, Lead $lead)
    {
        $request->validate([
            'name'   => 'nullable|string|max:191',
            'email'  => 'nullable|email',
            'phone'  => 'nullable|string|max:50',
            'source' => 'nullable|string|max:191',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($request->only([
            'name','email','phone','source','assigned_to','status','follow_up_date','notes'
        ]));

        LeadActivityLog::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'lead_updated',
            'old_value' => json_encode($lead->getOriginal()),
            'new_value' => json_encode($lead->getChanges()),
        ]);

        return redirect()->route('leads.show', $lead->id)->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }

    public function showImportForm()
    {
        return view('leads.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        $userId = auth()->id();
        $batchId = Str::random(20);

        // Create log entry before import
        $log = \DB::table('lead_import_logs')->insertGetId([
            'batch_id' => $batchId,
            'user_id' => $userId,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'created_at' => now(),
        ]);

        Excel::import(new LeadImport($batchId), $request->file('file'));

        $count = \App\Models\Lead::where('batch_id', $batchId)->count();
         // Update log
        \DB::table('lead_import_logs')->where('id', $log)->update([
            'total_imported' => $count
        ]);

        return redirect()->route('leads.index')->with('success', 'Leads imported successfully!');
    }

    public function importHistory()
    {
        $logs = \DB::table('lead_import_logs')
            ->join('users','users.id','=','lead_import_logs.user_id')
            ->select('lead_import_logs.*','users.username')
            ->orderBy('lead_import_logs.id','DESC')
            ->get();

        return view('leads.import_history', compact('logs'));
    }

    public function importBatchView($batchId)
    {
        $leads = Lead::where('batch_id', $batchId)->get();
        return view('leads.import_batch_view', compact('leads', 'batchId'));
    }

    public function bulkAssign(Request $request)
    {
        $ids = explode(',', $request->lead_ids);

        Lead::whereIn('id', $ids)->update([
            'assigned_to' => $request->assigned_to
        ]);

        return back()->with('success', 'Leads assigned successfully!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(',', $request->lead_ids);

        Lead::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected leads deleted!');
    }

    // public function salesStats($id)
    // {
    //     $user = User::findOrFail($id);

    //     $stats = [
    //         'total'        => Lead::where('assigned_to', $id)->count(),
    //         'new'          => Lead::where('assigned_to', $id)->where('status','new')->count(),
    //         'contacted'    => Lead::where('assigned_to', $id)->where('status','contacted')->count(),
    //         'follow_up'    => Lead::where('assigned_to', $id)->where('status','follow_up')->count(),
    //         'onboarded'    => Lead::where('assigned_to', $id)->where('status','onboarded')->count(),
    //         'not_interested'=> Lead::where('assigned_to', $id)->where('status','not_interested')->count(),
    //         'calls_today'  => LeadCall::where('user_id',$id)->whereDate('created_at', today())->count(),
    //     ];

    //     return view('leads.sales_stats', compact('user','stats'));
    // }

    public function salesDashboard_saleuser()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Stats for sales person
        $stats = [
            'total'         => Lead::where('assigned_to', $userId)->count(),
            'new'           => Lead::where('assigned_to', $userId)->where('status', 'new')->count(),
            'contacted'     => Lead::where('assigned_to', $userId)->where('status', 'contacted')->count(),
            'follow_up'     => Lead::where('assigned_to', $userId)->where('status', 'follow_up')->count(),
            'not_interested'=> Lead::where('assigned_to', $userId)->where('status', 'not_interested')->count(),
            'onboarded'     => Lead::where('assigned_to', $userId)->where('status', 'onboarded')->count(),
            'calls_today'   => LeadCall::where('user_id', $userId)
                                      ->whereDate('created_at', today())
                                      ->count(),
            'today_followups' => Lead::where('assigned_to', $userId)
                                      ->whereDate('follow_up_date', today())
                                      ->count(),
        ];

        // Latest 10 leads assigned to sale user
        $recentLeads = Lead::where('assigned_to', $userId)
            ->latest()
            ->take(10)
            ->get();

        return view('sales.dashboard', compact('stats', 'recentLeads'));
    }

    public function salesDashboarda()
    {
        $user = auth()->user();

        // If Admin → show all leads
        // If Sales → show only leads assigned to them
        $filterByUser = $user->role == 3; // role 3 = sales

        // BUILD QUERY
        $baseQuery = Lead::query();

        if ($filterByUser) {
            $baseQuery->where('assigned_to', $user->id);
        }

        // Stats
        $stats = [
            'total'         => (clone $baseQuery)->count(),
            'new'           => (clone $baseQuery)->where('status', 'new')->count(),
            'contacted'     => (clone $baseQuery)->where('status', 'contacted')->count(),
            'follow_up'     => (clone $baseQuery)->where('status', 'follow_up')->count(),
            'not_interested'=> (clone $baseQuery)->where('status', 'not_interested')->count(),
            'onboarded'     => (clone $baseQuery)->where('status', 'onboarded')->count(),
            'calls_today'   => LeadCall::when($filterByUser, function($q) use ($user){
                                    $q->where('user_id', $user->id);
                                })->whereDate('created_at', today())->count(),
            'today_followups' => (clone $baseQuery)
                                ->whereDate('follow_up_date', today())
                                ->count(),
        ];

        // Recent leads (last 10)
        $recentLeads = (clone $baseQuery)->latest()->take(10)->get();

        return view('sales.dashboard', compact('stats', 'recentLeads'));
    }


    public function salesDashboard12(Request $request)
{
    $auth = auth()->user();

    // Admin can select user, Sales user cannot
    if ($auth->role == 3) {
        $userId = $auth->id;
    } else {
        $userId = $request->user_id ?? null;
    }

    // Base Query
    $baseQuery = Lead::query();

    if ($userId) {
        $baseQuery->where('assigned_to', $userId);
    }

    // Stats
    $stats = [
        'total'         => (clone $baseQuery)->count(),
        'new'           => (clone $baseQuery)->where('status', 'new')->count(),
        'contacted'     => (clone $baseQuery)->where('status', 'contacted')->count(),
        'follow_up'     => (clone $baseQuery)->where('status', 'follow_up')->count(),
        'not_interested'=> (clone $baseQuery)->where('status', 'not_interested')->count(),
        'onboarded'     => (clone $baseQuery)->where('status', 'onboarded')->count(),
        'calls_today'   => LeadCall::when($userId, function($q) use ($userId){
                                $q->where('user_id', $userId);
                            })->whereDate('created_at', today())->count(),
        'today_followups' => (clone $baseQuery)
                            ->whereDate('follow_up_date', today())
                            ->count(),
    ];

    // Recent 10 leads
    $recentLeads = (clone $baseQuery)->latest()->take(10)->get();

    // ------------------ LEADERBOARD -----------------
    $leaderboard = User::where('role', 3)
        ->withCount([
            'assignedLeads',
            'assignedLeads as contacted_count' => fn($q) => $q->where('status', 'contacted'),
            'assignedLeads as followup_count'  => fn($q) => $q->where('status', 'follow_up'),
            'assignedLeads as onboarded_count' => fn($q) => $q->where('status', 'onboarded'),
        ])
        ->orderByDesc('assigned_leads_count')
        ->get();

    // ------------------ CHARTS -----------------

    // Last 7 days calls
    $callsChart = LeadCall::selectRaw('DATE(created_at) as day, COUNT(*) as total')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Last 7 days follow-ups
    $followupChart = Lead::selectRaw('DATE(follow_up_date) as day, COUNT(*) as total')
        ->when($userId, fn($q) => $q->where('assigned_to', $userId))
        ->whereDate('follow_up_date', '>=', now()->subDays(6))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Status distribution pie chart
    $statusChart = (clone $baseQuery)
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->get();

    // Sales users (for admin user tabs + dropdown)
    $salesUsers = User::where('role', 3)->get();

    return view('sales.dashboard', compact(
        'stats',
        'recentLeads',
        'leaderboard',
        'callsChart',
        'followupChart',
        'statusChart',
        'salesUsers',
        'userId'
    ));
}

public function salesDashboard(Request $request)
{
    $auth = auth()->user();

    // ------------------- USER FILTER LOGIC -------------------
    // Sales user (role=3) sees only their own dashboard
    if ($auth->role == 3) {
        $userId = $auth->id;
    } 
    // Admin can select any salesperson OR see all
    else {
        $userId = $request->assigned_to ?? null;
    }

    // ------------------- BASE LEAD QUERY ---------------------
    $baseQuery = Lead::query();

    if ($userId) {
        $baseQuery->where('assigned_to', $userId);
    }

    // ---------------- DATE RANGE FILTERS -----------------
    if ($request->range) {
        switch ($request->range) {
            case 'today':
                $baseQuery->whereDate('created_at', today());
                break;

            case 'yesterday':
                $baseQuery->whereDate('created_at', today()->subDay());
                break;

            case 'last_7':
                $baseQuery->whereDate('created_at', '>=', Carbon::now()->subDays(7));
                break;

            case 'this_month':
                $baseQuery->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                break;

            case 'last_month':
                $baseQuery->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                break;
        }
    }

    // Custom Date Range
    if ($request->start_date && $request->end_date) {
        $baseQuery->whereBetween('created_at', [
            $request->start_date,
            $request->end_date
        ]);
    }

    // ------------------- STATS -------------------------------
    $stats = [
        'total'          => (clone $baseQuery)->count(),
        'new'            => (clone $baseQuery)->where('status', 'new')->count(),
        'contacted'      => (clone $baseQuery)->where('status', 'contacted')->count(),
        'follow_up'      => (clone $baseQuery)->where('status', 'follow_up')->count(),
        'not_interested' => (clone $baseQuery)->where('status', 'not_interested')->count(),
        'onboarded'      => (clone $baseQuery)->where('status', 'onboarded')->count(),

        'calls_today' => LeadCall::when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('created_at', today())
            ->count(),

        'today_followups' => (clone $baseQuery)
            ->whereDate('follow_up_date', today())
            ->count(),
    ];

    // ------------------- RECENT LEADS ------------------------
    $recentLeads = (clone $baseQuery)
        ->latest()
        ->take(10)
        ->get();

    // ------------------- LEADERBOARD -------------------------
    $leaderboard = User::where('role', 3)
        ->withCount([
            'assignedLeads',
            'assignedLeads as contacted_count' => fn($q) => $q->where('status', 'contacted'),
            'assignedLeads as followup_count'  => fn($q) => $q->where('status', 'follow_up'),
            'assignedLeads as onboarded_count' => fn($q) => $q->where('status', 'onboarded'),
        ])
        ->orderByDesc('assigned_leads_count')
        ->get();

    // ------------------- CHARTS ------------------------------

    // Calls (last 7 days)
    $callsChart = LeadCall::selectRaw('DATE(created_at) as day, COUNT(*) as total')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Follow-ups (last 7 days)
    $followupChart = Lead::selectRaw('DATE(follow_up_date) as day, COUNT(*) as total')
        ->when($userId, fn($q) => $q->where('assigned_to', $userId))
        ->whereDate('follow_up_date', '>=', now()->subDays(6))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Status Distribution Pie Chart
    $statusChart = (clone $baseQuery)
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->get();

    // ------------------- SALES USERS LIST (Admin only) --------
    $salesUsers = User::where('role', 3)->get();

    // ------------------- RETURN VIEW --------------------------
    return view('sales.dashboard', compact(
        'stats',
        'recentLeads',
        'leaderboard',
        'callsChart',
        'followupChart',
        'statusChart',
        'salesUsers',
        'userId'
    ));
}


}
