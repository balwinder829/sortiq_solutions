<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Student;
use App\Models\User;
use App\Models\College;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryActivity;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnquiriesImport;
use App\Notifications\LeadAssignedNotification;



class EnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LIST WITH FILTER
    public function index(Request $request)
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
    // Load follow-up history
    $enquiry->load(['followups.user']);

    // Show page
    return view('enquiries.show', compact('enquiry'));
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
        ]);

        return redirect()->route('enquiries.index');
    }

    public function edit($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $colleges = College::all();

        return view('enquiries.edit', compact('enquiry', 'colleges'));
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
        $leadsQuery->where('status', $request->status);
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


    public function salespersons()
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

}
