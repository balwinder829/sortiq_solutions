<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Student;
use App\Models\User;
use App\Models\College;
use App\Models\Registration;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryActivity;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnquiriesImport;
use App\Notifications\LeadAssignedNotification;
use Illuminate\Support\Facades\DB;
use App\Exports\RegistrationsExport;


class EnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LIST WITH FILTER
    // LIST WITH FILTER (ADMIN INDEX)
public function index(Request $request)
{
    $query = Enquiry::query();

    // =========================
    // ROLE BASED ACCESS
    // =========================
    if (!auth()->user()->isAdmin()) {
        $query->where('assigned_to', auth()->id());
    }

    // ADMIN: Filter by salesperson
    if (auth()->user()->isAdmin() && $request->filled('salesperson_id')) {
        $query->where('assigned_to', $request->salesperson_id);
    }


    // =========================
    // BASIC FILTERS (EXISTING)
    // =========================
    if ($request->filled('college')) {
        $query->where('college', $request->college);
    }

    if ($request->filled('study')) {
        $query->where('study', 'like', "%{$request->study}%");
    }

    if ($request->filled('semester')) {
        $query->where('semester', $request->semester);
    }



    // =========================
    // NEW FILTERS (DB ALIGNED)
    // =========================
    if ($request->filled('lead_status')) {
        $query->where('lead_status', $request->lead_status);
    }

    if ($request->filled('call_status')) {
        $query->where('last_call_status', $request->call_status);
    }

    if ($request->filled('source_type')) {
        $query->where('source', $request->source_type);
    }

    if ($request->filled('registered')) {
        if ($request->registered === 'yes') {
            $query->whereNotNull('registered_at');
        } elseif ($request->registered === 'no') {
            $query->whereNull('registered_at');
        }
    }

    // =========================
    // DATE RANGE (CREATED AT)
    // =========================
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59',
        ]);
    }

    // =========================
// QUICK DATE FILTER
// =========================
    if ($request->filled('quick_date')) {

        switch ($request->quick_date) {
            case 'today':
                $query->whereDate('created_at', today());
                break;

            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;

            case 'last7':
                $query->whereBetween('created_at', [
                    now()->subDays(7)->startOfDay(),
                    now()->endOfDay()
                ]);
                break;

            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;

            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                      ->whereYear('created_at', now()->subMonth()->year);
                break;
        }

    }
// ===============================
// FOLLOW-UP FILTERS (SNAPSHOT)
// ===============================
if ($request->filled('followup_filter')) {

    switch ($request->followup_filter) {

        case 'today':
            $query->whereDate('next_followup_at', today());
            break;

        case 'overdue':
            $query->whereNotNull('next_followup_at')
                  ->where('next_followup_at', '<', now());
            break;

        case 'upcoming':
            $query->whereDate('next_followup_at', '>', today());
            break;

        case 'none':
            $query->whereNull('next_followup_at');
            break;
    }
}


    // =========================
    // SORTING
    // =========================
    if ($request->filled('alpha')) {
        $query->orderBy('name', 'asc');
    } else {
        $query->latest();
    }

    // =========================
    // DATA
    // =========================
    $enquiries = $query->paginate(20)->appends($request->all());

    $sales    = User::where('role', 3)->get();
    $colleges = College::orderBy('college_name')->get();

    return view('enquiries.index', compact(
        'enquiries',
        'sales',
        'colleges'
    ));
}

    public function index17dec(Request $request)
    {
        $query = Enquiry::query();

        if (!auth()->user()->isAdmin()) {
            $query->where('assigned_to', auth()->id());
        }

        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        if ($request->filled('study')) {
            $query->where('study', $request->study);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('alpha')) {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        $enquiries = $query->paginate(20);
        $sales = User::where('role', 3)->get();
        $colleges = College::all();

        return view('enquiries.index', compact('enquiries', 'sales', 'colleges'));
    }

    // ADD MANUALLY
    public function create()
    {
        $colleges = \App\Models\College::orderBy('college_name')->get();

        return view('enquiries.create', compact('colleges'));
    }

    public function show(Enquiry $enquiry)
{
    $enquiry->load(['followups.user', 'activities.user']);

    $callStatuses = DB::table('call_statuses')
        ->orderBy('name')
        ->get();

    return view('enquiries.show', compact('enquiry', 'callStatuses'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
             // 'college_id' => 'nullable|exists:colleges,id',
        ]);

        Enquiry::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'college' => $request->college,
            'study' => $request->study,
            'semester' => $request->semester,
            'created_by' => auth()->id(),
            'source' => 'manual',
        ]);

        return redirect()->route('enquiries.index');
    }

    public function edit($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $colleges = College::all();
         $sales = User::where('role', 3)->get();

        return view('enquiries.edit', compact('enquiry', 'colleges','sales'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'nullable',
            'email' => 'nullable|email',
            'college' => 'nullable',
            'study' => 'nullable',
            'semester' => 'nullable',
        ]);

        $enquiry = Enquiry::findOrFail($id);
         if ($enquiry->assigned_to != $request->assigned_to) {
            $salesUser = User::find($request->assigned_to);
             $salesUser->notify(new LeadAssignedNotification($enquiry));

              \DB::table('enquiry_assignments')->insert([
                            'enquiry_id'  => $id,
                            'assigned_to' => $request->assigned_to,
                            'assigned_by' => auth()->id(),
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);

        }
        $enquiry->update($request->all());

        return redirect()->route('enquiries.index')
                         ->with('success', 'Record updated successfully!');
    }



    // IMPORT
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        $import = new EnquiriesImport(auth()->id());
        Excel::import($import, $request->file('file'));
        // dd($import->errors);
        if (!empty($import->errors)) {
            return back()->with('import_errors', $import->errors)
                         ->with('success', 'Import completed with some issues.');
        }

        return back()->with('success', 'Import completed successfully!');
    }


    // ASSIGN MULTIPLE
    // public function assign(Request $request)
    // {
    //     $request->validate([
    //         'enquiry_ids' => 'required|array',
    //         'salesperson_id' => 'required|exists:users,id'
    //     ]);

    //     Enquiry::whereIn('id', $request->enquiry_ids)
    //         ->update(['assigned_to' => $request->salesperson_id]);

    //     return response()->json(['message' => 'Assigned']);
    // }

    public function assign_nwbkp(Request $request)
    {
        $request->validate([
            'enquiry_ids' => 'required|array',
            'salesperson_id' => 'required|exists:users,id'
        ]);

        $salespersonId = $request->salesperson_id;
        $reassigned = false;

        foreach ($request->enquiry_ids as $id) {

            $enquiry = Enquiry::find($id);

            // Skip if already assigned to same salesperson
            if ($enquiry->assigned_to == $salespersonId) {
                continue;
            }

            // Detect reassignment
            if (!is_null($enquiry->assigned_to)) {
                $reassigned = true;
            }

            // Save assignment
            \DB::table('enquiry_assignments')->insert([
                'enquiry_id' => $id,
                'assigned_to' => $salespersonId,
                'assigned_by' => auth()->id(),
                'previous_assigned_to' => $enquiry->assigned_to,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $enquiry->update([
                'assigned_to' => $salespersonId,
                'assigned_at' => now(),
            ]);
        }

        return response()->json([
            'message' => $reassigned
                ? 'Leads reassigned successfully'
                : 'Leads assigned successfully'
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'enquiry_ids' => 'required|array',
            'salesperson_id' => 'required|exists:users,id'
        ]);

         // Get the sales user who will receive notifications
        $salesUser = User::find($request->salesperson_id);

        foreach ($request->enquiry_ids as $id) {

           $enquiry = Enquiry::find($id);

            // Skip if it's already assigned to this salesperson
            if ($enquiry->assigned_to == $request->salesperson_id) {
                continue;  // ❌ DO NOT create new assignment history
            }

            // Update assignment
            $enquiry->update([
                'assigned_to' => $request->salesperson_id,
                 'assigned_at' => now(), 

            ]);
            // LOG ASSIGNMENT HISTORY
            \DB::table('enquiry_assignments')->insert([
                'enquiry_id'  => $id,
                'assigned_to' => $request->salesperson_id,
                'assigned_by' => auth()->id(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        // ⭐ SEND NOTIFICATION (only for NEW assignments)
        $salesUser->notify(new LeadAssignedNotification($enquiry));
        return response()->json(['message' => 'Assigned']);
    }


    // CONVERT TO STUDENT
    public function convert(Enquiry $enquiry)
    {
        if ($enquiry->is_converted) {
            return back()->withErrors(['msg' => 'Already converted']);
        }

        Student::create([
            'enquiry_id' => $enquiry->id,
            'student_name' => $enquiry->name,
            'mobile' => $enquiry->mobile,
            'email' => $enquiry->email,
            'college_name' => $enquiry->college,
        ]);

        $enquiry->update(['is_converted' => 1]);

        return back()->with('success', 'Converted to student');
    }

    public function pipeline()
    {
        $statuses = ['new', 'followup', 'closed', 'joined'];

        $enquiries = Enquiry::orderBy('created_at', 'desc')->get()
            ->groupBy('status');

        return view('enquiries.pipeline', compact('statuses', 'enquiries'));
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:enquiries,id',
            'status' => 'required|string'
        ]);

        $enquiry = Enquiry::find($request->id);
        $enquiry->status = $request->status;
        $enquiry->save();

        return response()->json(['success' => true]);
    }

    public function dashboard()
    {
        $today = now()->toDateString();

        return view('enquiries.dashboard', [
            'total'           => Enquiry::count(),
            'today_followups' => EnquiryFollowup::whereDate('next_followup_date', $today)->count(),
            'missed_followups'=> EnquiryFollowup::whereDate('next_followup_date', '<', $today)->count(),
            'upcoming'        => EnquiryFollowup::whereDate('next_followup_date', '>', $today)->count(),

            // Status distribution (new, followup, closed, joined)
            'status_chart'    => Enquiry::selectRaw('status, COUNT(*) as total')->groupBy('status')->get(),

            // Call status chart
            'call_status_chart' => EnquiryFollowup::selectRaw('call_status, COUNT(*) as total')
                ->groupBy('call_status')
                ->orderBy('total', 'desc')
                ->get(),
        ]);
    }

    public function performance()
    {
        $employees = User::where('role', 3)->get(); // role 3: sales

        foreach ($employees as $emp) {
            $emp->followups = EnquiryActivity::where('user_id', $emp->id)
                ->where('type', 'followup')
                ->count();

            $emp->calls = EnquiryFollowup::where('user_id', $emp->id)->count();

            $emp->conversions = EnquiryActivity::where('user_id', $emp->id)
                ->where('type', 'status_change')
                ->where('new_value', 'joined')
                ->count();
        }

        return view('enquiries.performance', compact('employees'));
    }
    // public function salespersons()
    // {   
    //     // Get all salespersons
    //     $salespersons = User::where('role', 3)
    //         ->withCount([
    //             // Total leads assigned to salesperson
    //             'enquiriesAssigned as total_leads' => function($q) {
    //                 $q->whereNotNull('assigned_to');
    //             },

    //             // Leads assigned today
    //             'enquiriesAssigned as today_leads' => function($q) {
    //                 $q->whereDate('created_at', today());
    //             },

    //             // Follow-ups done today
    //             'activities as today_followups' => function($q) {
    //                 $q->where('type', 'followup')
    //                   ->whereDate('created_at', today());
    //             },

    //             // Total follow-ups done
    //             'activities as total_followups' => function($q) {
    //                 $q->where('type', 'followup');
    //             },
    //         ])
    //         ->get();

    //     return view('enquiries.salespersons', compact('salespersons'));
    // }

public function salespersonShow(Request $request, $id)
{
    $salesperson = User::findOrFail($id);

    // Base query
    $leadsQuery = Enquiry::where('assigned_to', $id);

    // ============================
    //  DATE FILTER (assigned_at)
    // ============================
    if ($request->filter_date == 'today') {
        $leadsQuery->whereDate('assigned_at', today());
    }
    elseif ($request->filter_date == 'yesterday') {
        $leadsQuery->whereDate('assigned_at', today()->subDay());
    }
    elseif ($request->filter_date == 'older') {
        $leadsQuery->whereDate('assigned_at', '<', today()->subDay());
    }

    // ============================
    // SEARCH FILTER (name / phone)
    // ============================
    if ($request->search) {
        $leadsQuery->where(function($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('mobile', 'like', "%{$request->search}%");
        });
    }

    // ============================
    // DATE RANGE (Only if no quick date filter used)
    // ============================
    if (!$request->filter_date && $request->from_date && $request->to_date) {
        $leadsQuery->whereBetween('assigned_at', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    }

    // ============================
    //  STATUS FILTER
    // ============================
    if ($request->status) {
        $leadsQuery->where('lead_status', $request->status);
    }

    // ============================
    // PAGINATION WITH FILTERS
    // ============================
    $leads = $leadsQuery->latest()->paginate(100)->appends($request->all());

    // ============================
    // TODAY FOLLOW-UP WORK
    // ============================
    $todayWork = EnquiryActivity::with('enquiry')
        ->where('user_id', $id)
        ->whereDate('created_at', today())
        ->latest()
        ->get();

    return view('enquiries.show_salesperson', compact(
        'salesperson',
        'leads',
        'todayWork'
    ));
}

public function salespersons(Request $request)
{
    $sortBy  = $request->get('sort', 'name');
    $sortDir = $request->get('dir', 'asc');

    $salespersons = User::where('role', 3)
        ->withCount([
            'enquiriesAssigned as total_leads',
            'activities as total_followups' => function ($q) {
                $q->where('type', 'followup');
            },
            'enquiriesAssigned as registered_leads' => function ($q) {
                $q->where('lead_status', 'registered');
            },
        ])
        ->withMax('activities', 'created_at')
        ->get();

    // ✅ SORT COLLECTION (THIS FIXES EVERYTHING)
    $salespersons = $salespersons->sortBy(
        fn ($item) => $item->{$sortBy} ?? '',
        SORT_REGULAR,
        $sortDir === 'desc'
    );

    return view('enquiries.salespersons', [
        'salespersons' => $salespersons,
        'sortBy' => $sortBy,
        'sortDir' => $sortDir,
        'query' => $request->query(), // preserve filters
    ]);
}

    public function salespersons17dec()
{   
    $salespersons = User::where('role', 3)
        ->withCount([
            // Total leads assigned to salesperson
            'enquiriesAssigned as total_leads',

            // Leads assigned today
            'enquiriesAssigned as today_leads' => function($q) {
                $q->whereDate('assigned_at', today());
            },

            // Follow-ups done today
            'activities as today_followups' => function($q) {
                $q->where('type', 'followup')
                  ->whereDate('created_at', today());
            },

            // Total follow-ups done
            'activities as total_followups' => function($q) {
                $q->where('type', 'followup');
            },
        ])
        ->get();

    return view('enquiries.salespersons', compact('salespersons'));
}


    public function salespersonShow12dec($id)
    {
        $salesperson = User::findOrFail($id);

        // Leads assigned to this salesperson
        $leads = Enquiry::where('assigned_to', $id)
            ->latest()
            ->paginate(20);

        // Today’s work (followups)
        $todayWork = EnquiryActivity::with('enquiry')
            ->where('user_id', $id)
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return view('enquiries.show_salesperson', compact('salesperson', 'leads', 'todayWork'));
    }

    public function assignmentReport(Request $request)
{
    $salespersons = User::where('role', 3)->get();
    $colleges = College::all();

    $query = \DB::table('enquiry_assignments')
        ->join('enquiries', 'enquiries.id', '=', 'enquiry_assignments.enquiry_id')
        ->join('users', 'users.id', '=', 'enquiry_assignments.assigned_to')
        ->leftJoin('colleges', 'colleges.id', '=', 'enquiries.college')
        ->select(
            'enquiries.name as enquiry_name',
            'enquiries.mobile',
             'colleges.college_name as college_name', 
            'users.name as salesperson',
             'users.id as salesperson_id', 
            'enquiry_assignments.created_at'
        );

    // --------------------------
    // QUICK FILTERS (Today, Yesterday, 7 Days)
    // --------------------------

    if ($request->filter == 'today') {
        $query->whereDate('enquiry_assignments.created_at', today());
    }

    if ($request->filter == 'yesterday') {
        $query->whereDate('enquiry_assignments.created_at', today()->subDay());
    }

    if ($request->filter == 'last7') {
        $query->whereBetween('enquiry_assignments.created_at', [
            today()->subDays(7),
            today(),
        ]);
    }

    // --------------------------
    // CUSTOM DATE FILTER
    // --------------------------
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('enquiry_assignments.created_at', [
            $request->from_date,
            $request->to_date
        ]);
    }

    // --------------------------
    // FILTER BY SALESPERSON
    // --------------------------
    if ($request->filled('salesperson_id')) {

        $query->where('enquiry_assignments.assigned_to', $request->salesperson_id);

    }

    // --------------------------
    // FILTER BY COLLEGE
    // --------------------------
    if ($request->filled('college')) {
        $query->where('enquiries.college', $request->college);
    }

    // FINAL DATA
    $records = $query->orderBy('enquiry_assignments.created_at', 'desc')->paginate(25);

    // Summary Cards
    $summary = [
        'today' => \DB::table('enquiry_assignments')->whereDate('created_at', today())->count(),
        'yesterday' => \DB::table('enquiry_assignments')->whereDate('created_at', today()->subDay())->count(),
        'last7' => \DB::table('enquiry_assignments')
            ->whereBetween('created_at', [today()->subDays(7), today()])
            ->count(),
    ];

    return view('enquiries.assignments', compact('records', 'summary', 'salespersons', 'colleges'));
}

    public function assignmentReportold(Request $request)
    {
        // Today
        $assignedToday = \DB::table('enquiry_assignments')
            ->whereDate('created_at', today())
            ->count();

        // Yesterday
        $assignedYesterday = \DB::table('enquiry_assignments')
            ->whereDate('created_at', today()->subDay())
            ->count();

        // List with filters
        $query = \DB::table('enquiry_assignments')
            ->join('enquiries', 'enquiries.id', '=', 'enquiry_assignments.enquiry_id')
            ->join('users', 'users.id', '=', 'enquiry_assignments.assigned_to')
            ->select('enquiries.name', 'enquiries.mobile', 'users.name as sales_name', 'enquiry_assignments.created_at');

        // Filters
        if ($request->filled('date')) {
            $query->whereDate('enquiry_assignments.created_at', $request->date);
        }

        if ($request->filled('salesperson_id')) {
            $query->where('enquiry_assignments.assigned_to', $request->salesperson_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        $salespersons = User::where('role', 3)->get();

        return view('enquiries.assignments', compact(
            'assignedToday',
            'assignedYesterday',
            'records',
            'salespersons'
        ));
    }


public function pendingFollowups(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }

    $query = Enquiry::whereNotNull('assigned_to');

    // ======================
    // FOLLOW-UP TYPE FILTER
    // ======================
    if ($request->type === 'today') {
        $query->whereDate('next_followup_at', today());
    }
    elseif ($request->type === 'missed') {
        $query->whereDate('next_followup_at', '<', today());
    }
    elseif ($request->type === 'upcoming') {
        $query->whereDate('next_followup_at', '>', today());
    }

    // ======================
    // SALESPERSON FILTER
    // ======================
    if ($request->filled('salesperson_id')) {
        $query->where('assigned_to', $request->salesperson_id);
    }

    // ======================
    // LEAD STATUS FILTER
    // ======================
    if ($request->filled('lead_status')) {
        $query->where('lead_status', $request->lead_status);
    }

    $enquiries = $query
        ->orderBy('next_followup_at', 'asc')
        ->paginate(25)
        ->appends($request->all());

    $sales = User::where('role', 3)->get();

    // Summary cards
    $summary = [
        'today' => Enquiry::whereDate('next_followup_at', today())->count(),
        'missed' => Enquiry::whereDate('next_followup_at', '<', today())->count(),
        'upcoming' => Enquiry::whereDate('next_followup_at', '>', today())->count(),
    ];

    return view('enquiries.followup_enquiries', compact(
        'enquiries',
        'sales',
        'summary'
    ));
}


public function callDashboard(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }

    // ======================
    // BASE QUERY
    // ======================
    $callsQuery = EnquiryFollowup::query();

    // ======================
    // DATE FILTERS
    // ======================
    if ($request->quick_date === 'today') {
        $callsQuery->whereDate('created_at', today());
    }
    elseif ($request->quick_date === 'yesterday') {
        $callsQuery->whereDate('created_at', today()->subDay());
    }
    elseif ($request->quick_date === 'last7') {
        $callsQuery->whereBetween('created_at', [now()->subDays(7), now()]);
    }
    elseif ($request->filled('from_date') && $request->filled('to_date')) {
        $callsQuery->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    } else {
        // Default: today
        $callsQuery->whereDate('created_at', today());
    }

    // ======================
    // SALESPERSON FILTER
    // ======================
    if ($request->filled('salesperson_id')) {
        $callsQuery->where('user_id', $request->salesperson_id);
    }

    // ======================
    // TOTAL CALLS
    // ======================
    $totalCalls = (clone $callsQuery)->count();

    // ======================
    // CALLS BY SALESPERSON
    // ======================
    $callsByUser = (clone $callsQuery)
        ->selectRaw('user_id, COUNT(*) as total_calls')
        ->groupBy('user_id')
        ->with('user')
        ->get();

    // ======================
    // CALL DETAILS (TABLE)
    // ======================
    $calls = (clone $callsQuery)
        ->with(['user', 'enquiry'])
        ->latest()
        ->paginate(25)
        ->appends($request->all());

    $sales = User::where('role', 3)->get();

    return view('enquiries.call_dashboard', compact(
        'totalCalls',
        'callsByUser',
        'calls',
        'sales'
    ));
}

public function registeredIndex(Request $request)
{   


    $query = Registration::with(['enquiry.student', 'collector']);

    /* ================= DATE RANGE FILTER ================= */
    if ($request->from_date) {
        $query->whereDate('registered_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $query->whereDate('registered_at', '<=', $request->to_date);
    }

    /* ================= SALESPERSON FILTER ================= */
    if ($request->salesperson_id) {
        $query->where('collected_by', $request->salesperson_id);
    }

    $allRegistrations = (clone $query)->latest('registered_at')->get();

    $pendingRegistrations = (clone $query)
        ->whereDoesntHave('enquiry.student')
        ->latest('registered_at')
        ->get();

    $salesUsers = User::where('role', '3')->get();

    return view('enquiries.registered-list', compact(
        'allRegistrations',
        'pendingRegistrations',
        'salesUsers'
    ));
    // All registered (payment done)
    // $allRegistered = Registration::with('enquiry.assignedTo')
    //     ->latest('registered_at')
    //     ->get();

    // // Registered but NOT converted to student
    // $pendingRegistered = Registration::with('enquiry.assignedTo')
    //     ->whereDoesntHave('enquiry.student')
    //     ->latest('registered_at')
    //     ->get();

    // return view('enquiries.registered-list', compact(
    //     'allRegistered',
    //     'pendingRegistered'
    // ));
}


private function createStudentFromEnquiry(\App\Models\Enquiry $enquiry, $amountPaid = null)
{
     // 🔒 Strong duplicate check
    $studentExists = Student::where('enquiry_id', $enquiry->id)
        ->orWhere('contact', $enquiry->mobile)
        ->orWhere('email_id', $enquiry->email)
        ->exists();

    if ($studentExists) {
        return null; // ⛔ Skip if already exists
    }

    $student = Student::create([
        'student_name' => $enquiry->name,
        'f_name'       => '',
        'email_id'     => $enquiry->email,
        'contact'      => $enquiry->mobile,
        'college_name' => $enquiry->college,
        'reg_fees'     => $amountPaid, // optional
        'enquiry_id'   => $enquiry->id,
        'created_by'   => Auth::id(),
    ]);

    // ✅ Notify assigned sales user
    $salesUser = $enquiry->assignedTo;
    if ($salesUser) {
        $salesUser->notify(
            new \App\Notifications\StudentRegisteredSalesNotification($student)
        );
    }

    return $student;
}


public function convertToStudent(Enquiry $enquiry)
{
    if ($enquiry->student) {
        return back()->with('error', 'Already converted to student.');
    }

    // Get registration amount (latest payment)
    $registration = Registration::where('enquiry_id', $enquiry->id)
        ->latest('registered_at')
        ->first();

    $this->createStudentFromEnquiry(
        $enquiry,
        optional($registration)->amount_paid
    );

    EnquiryActivity::create([
        'enquiry_id' => $enquiry->id,
        'user_id'    => Auth::id(),
        'type'       => 'converted_to_student',
        'details'    => 'Converted manually (single)',
    ]);

    return back()->with('success', 'Student converted successfully.');
}

public function bulkConvert(Request $request)
{
    $request->validate([
        'enquiry_ids' => 'required|array'
    ]);

    \DB::transaction(function () use ($request) {

        foreach ($request->enquiry_ids as $enquiryId) {

            $enquiry = Enquiry::find($enquiryId);

            if (! $enquiry || $enquiry->student) {
                continue;
            }

            // Get latest registration payment
            $registration = Registration::where('enquiry_id', $enquiry->id)
                ->latest('registered_at')
                ->first();

            $this->createStudentFromEnquiry(
                $enquiry,
                optional($registration)->amount_paid
            );

            EnquiryActivity::create([
                'enquiry_id' => $enquiry->id,
                'user_id'    => Auth::id(),
                'type'       => 'converted_to_student',
                'details'    => 'Converted via bulk action',
            ]);
        }
    });

    return response()->json([
        'message' => 'Selected registrations converted successfully'
    ]);
}


public function bulkConvertl(Request $request)
{
    $request->validate([
        'enquiry_ids' => 'required|array'
    ]);

    DB::transaction(function () use ($request) {
        foreach ($request->enquiry_ids as $enquiryId) {
            $enquiry = \App\Models\Enquiry::find($enquiryId);


              $student = Student::create([
                'student_name' => $enquiry->name,
                'f_name'       => '',
                'email_id'     => $enquiry->email,
                'contact'      => $enquiry->mobile,
                'college_name' => $enquiry->college,
                'reg_fees'     => $request->amount_paid,
                'enquiry_id'   => $enquiry->id,
                'created_by'   => Auth::id(),
            ]);

            //Notify assigned sales user
            $salesUser = $enquiry->assignedTo;
            if ($salesUser) {
                $salesUser->notify(
                    new \App\Notifications\StudentRegisteredSalesNotification($student)
                );
            }





            if ($enquiry && !$enquiry->student) {
                \App\Models\Student::create([
                    'student_name' => $enquiry->name,
                    'email_id'     => $enquiry->email,
                    'contact'      => $enquiry->mobile,
                    'college_name' => $enquiry->college,
                    'enquiry_id'   => $enquiry->id,
                    'created_by'   => auth()->id(),
                ]);
            }
        }
    });

    return response()->json(['message' => 'Converted successfully']);
}


     


    public function exportAll()
    {
        return Excel::download(new RegistrationsExport(false), 'all_registrations.xlsx');
    }

    public function exportPending()
    {
        return Excel::download(new RegistrationsExport(true), 'pending_registrations.xlsx');
    }
}
