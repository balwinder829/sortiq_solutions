<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Student;
use App\Models\User;
use App\Models\College;
use App\Models\Registration;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryActivity;
use App\Models\SalesStaff;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EnquiriesImport;
use App\Notifications\LeadAssignedNotification;
use Illuminate\Support\Facades\DB;
use App\Exports\RegistrationsExport;
use App\Exports\EnquiriesExport;
use App\Rules\NotBlockedNumber;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
class PassoutController extends Controller
{   
    public function __construct()
    {
        $this->middleware('permission:passouts.view')->only('index');
        $this->middleware('permission:passouts.create')->only(['create','store']);
        $this->middleware('permission:passouts.edit')->only(['edit','update']);
        $this->middleware('permission:passouts.delete')->only('destroy');
        $this->middleware('permission:passouts.imports')->only('import');
    }

    public function index2(Request $request)
    {   
        $activeSessionNo = session('admin_session_id');
        $query = Enquiry::passouts()
        ->where('session_id', $activeSessionNo);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        $passouts = $query->latest()->paginate(20);

        return view('passouts.index', compact('passouts'));
    }

    public function index(Request $request)
{
    $query = Enquiry::passouts();

    // =========================
    // ROLE BASED ACCESS
    // =========================
    // if (!auth()->user()->isAdmin()) {
    //     $query->where('assigned_to', auth()->id());
    // }

    // ADMIN: Filter by salesperson
    if (auth()->user()->isAdmin() && $request->filled('salesperson_id')) {
        $query->where('assigned_to', $request->salesperson_id);
    }


    // =========================
    // BASIC FILTERS (EXISTING)
    // =========================
   

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
        $query->latest('updated_at');
    }

    // =========================
    // DATA
    // =========================
    $enquiries = $query->paginate(20)->appends($request->all());

    $sales    = SalesStaff::where('status', 'active')->get();
    $previousWhatsappUploadedFiles = collect(
            Storage::disk('public')->files('whatsapp-files')
        )->map(function ($file) {
            return [
                'path' => $file,
                'name' => basename($file),
            ];
        });
    return view('passouts.index', compact(
        'enquiries',
        'sales',
        'previousWhatsappUploadedFiles'
    ));
}

    public function create()
    {
        return view('passouts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => ['nullable', 'string', new NotBlockedNumber],
        ]);

        $activeSessionNo = session('admin_session_id');

        Enquiry::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gap' => $request->gap,
            'session_id' => $activeSessionNo,
            'is_passout' => 1,
            'created_by' => Auth::id(),
            'study' => $request->study,
            'source' => 'manual'
        ]);

        return redirect()->route('passouts.index')
            ->with('success', 'Passout created successfully');
    }

    public function show(Enquiry $passout)
    {   
        $enquiry = $passout;
        $enquiry->load(['followups.user', 'activities.user']);

        $callStatuses = DB::table('call_statuses')
        ->orderBy('name')
        ->get();

    return view('passouts.show', compact('enquiry', 'callStatuses'));
        // return view('passouts.show', compact('passout'));
    }

    public function edit(Enquiry $passout)
    {   
        $enquiry = $passout;
        $sales = SalesStaff::where('status', 'active')->get();
        return view('passouts.edit', compact('enquiry','sales'));
    }

    public function update(Request $request, Enquiry $passout)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => ['nullable', 'string', new NotBlockedNumber],
        ]);
        
        $passout->update($request->all());

        return redirect()->route('passouts.index')
            ->with('success', 'Passout updated successfully');
    }

    public function destroy(Enquiry $passout)
    {
        $passout->delete();

        return redirect()->route('passouts.index')
            ->with('success', 'Passout deleted');
    }

    // IMPORT
    public function importForm()
    {
        return view('passouts.import');
    }
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,xls',
    ]);

    \DB::beginTransaction();

    try {

        $importer = new \App\Imports\EnquiriesImport(auth()->id(), 1);

        \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

        $failures = $importer->failures();

        // ❗ IF VALIDATION FAIL → ROLLBACK + STOP
        if ($failures->isNotEmpty()) {

            \DB::rollBack();

            $messages = [];

            foreach ($failures as $failure) {
                $messages[] =
                    "Row {$failure->row()} – {$failure->attribute()} – " .
                    implode(', ', $failure->errors());
            }

            return back()->withErrors($messages);
        }

        \DB::commit();

        // ✅ ONLY HERE counts are valid
        $total    = $importer->totalRows;
        $inserted = $importer->insertedRows;
        $skipped  = $importer->skippedRows;

        $message = "From {$total} rows: {$inserted} inserted successfully, {$skipped} skipped.";

        $warnings = array_merge(
            $importer->blockedNumbers,
            $importer->duplicateEntries
        );

        if (!empty($warnings)) {
            return back()
                ->with('success', $message)
                ->withErrors($warnings);
        }

        return back()->with('success', $message);

    } catch (\Throwable $e) {

        \DB::rollBack();

        return back()->withErrors([
            'Import failed: ' . $e->getMessage()
        ]);
    }
}
    public function import2(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls',
        ]);

        \DB::beginTransaction();

        try {

            // 0 = enquiry, 1 = passout
            $importer = new \App\Imports\EnquiriesImport(auth()->id(), 0);

            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

            $failures = $importer->failures();

            if ($failures->isNotEmpty()) {

                \DB::rollBack();

                $messages = [];

                foreach ($failures as $failure) {
                    $messages[] =
                        "Row {$failure->row()} – {$failure->attribute()} – " .
                        implode(', ', $failure->errors());
                }

                return back()->withErrors($messages);
            }

            \DB::commit();

            // ✅ Final counts
            $total    = $importer->totalRows;
            $inserted = $importer->insertedRows;
            $skipped  = $importer->skippedRows;

            $message = "From {$total} rows: {$inserted} inserted successfully, {$skipped} skipped.";

            // ✅ Warnings (blocked + duplicate)
            $warnings = array_merge(
                $importer->blockedNumbers,
                $importer->duplicateEntries
            );

            if (!empty($warnings)) {
                return back()
                    ->with('success', $message)
                    ->withErrors($warnings);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            dd($e);
            \DB::rollBack();

            return back()->withErrors([
                'Import failed: Please check file format or missing columns.'
            ]);
        }
    }
    public function import11(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        $import = new EnquiriesImport(auth()->id(), 1);
        Excel::import($import, $request->file('file'));
        // dd($import->errors);
        if (!empty($import->errors)) {
            return back()->with('import_errors', $import->errors)
                         ->with('success', 'Import completed with some issues.');
        }

        return back()->with('success', 'Import completed successfully!');
    }

    public function export(Request $request)
    {   
        $parts = [];

    // College
    if ($request->filled('college')) {
        $parts[] = Str::slug($request->college, '_');
    }

    // Study
    if ($request->filled('study')) {
        $parts[] = Str::slug($request->study, '_');
    }

    // Semester
    if ($request->filled('semester')) {
        $parts[] = 'sem_' . $request->semester;
    }

    // Assigned Status
    if ($request->filled('assigned_status')) {
        $parts[] = $request->assigned_status; // assigned / unassigned
    }

    // Lead Status
    if ($request->filled('lead_status')) {
        $parts[] = Str::slug($request->lead_status, '_');
    }

    // Call Status
    if ($request->filled('call_status')) {
        $parts[] = Str::slug($request->call_status, '_');
    }

    // Source Type
    if ($request->filled('source_type')) {
        $parts[] = Str::slug($request->source_type, '_');
    }

    // Registered
    if ($request->filled('registered')) {
        $parts[] = $request->registered; // yes / no
    }

    // Date Range
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $from = Carbon::parse($request->from_date)->format('d_M');
        $to   = Carbon::parse($request->to_date)->format('d_M');
        $parts[] = strtolower($from . '_to_' . $to);
    }

    // Quick Date
    // if ($request->filled('quick_date')) {
    //     $parts[] = $request->quick_date;
    // }

    // Followup
    if ($request->filled('followup_filter')) {
        $parts[] = 'followup_' . $request->followup_filter;
    }

    // Always append today's date
    $parts[] = now()->format('d_F');

    // Build filename
    $fileName = 'passout_' . implode('_', $parts) . '.xlsx';

        return Excel::download(
            new EnquiriesExport($request->all(), 1),
            $fileName
        );
    }
}