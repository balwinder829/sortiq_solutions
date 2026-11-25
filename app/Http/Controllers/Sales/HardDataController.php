<?php

namespace App\Http\Controllers\Sales;
use App\Http\Controllers\Controller;

use App\Models\HardData;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;
use App\Exports\HardDataExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\State;
use App\Models\District;
use App\Models\Enquiry;
use App\Rules\NotBlockedNumber;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HardDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('permission:hard_data.view')->only(['index','show','importForm','import']);
        $this->middleware('permission:hard_data.create')->only(['create','store']);
        $this->middleware('permission:hard_data.edit')->only(['edit','update','moveManualToEnquiries']);
        $this->middleware('permission:hard_data.delete')->only(['destroy', 'bulkDelete']);

        
        // $this->middleware('permission:colleges.import')->only('showImport');
    }

     
public function index(Request $request)
{   
    if ($request->ajax()) {

        // $activeSessionNo = session('admin_session_id');
        $activeSessionNo = session(
            'admin_header_session_id',
            session('admin_session_id')
        );

        $query = HardData::with('college')
                    ->where('session_id', $activeSessionNo)
                    ->where('enquiry_status', 'active');

        // FILTERS: College filtering is based on the saved college_name text.
        if ($request->filled('college_name')) {
            $query->where('college_name', trim($request->input('college_name')));
        }

        if ($request->state_id) {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->district_id) {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        }

        if ($request->college_type !== null && $request->college_type !== '') {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('college_type', $request->college_type);
            });
        }

        if ($request->email) {
            $query->where('student_email', 'like', '%' . $request->email . '%');
        }

        if ($request->mobile) {
            $query->where('student_mobile', 'like', '%' . $request->mobile . '%');
        }
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

         if ($request->is_moved !== null && $request->is_moved !== '') {
            $query->where('is_moved_to_enquiry', (int) $request->is_moved);
        }

        if ($request->course_type) {
            $query->where('course_type', $request->course_type);
        }

        if ($request->class) {
            $query->where('class', $request->class);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        // DATE FILTER
        if ($request->date && !$request->range) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->range) {

            switch ($request->range) {

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'this_week':

                    $query->whereBetween('created_at', [
                        now()->startOfWeek()->startOfDay(),
                        now()->endOfWeek()->endOfDay()
                    ]);

                    break;

                case 'last_week':

                    $query->whereBetween('created_at', [
                        now()->subWeek()->startOfWeek()->startOfDay(),
                        now()->subWeek()->endOfWeek()->endOfDay()
                    ]);

                    break;

                case 'last_7_days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;

                case 'last_30_days':
                    $query->whereBetween('created_at', [now()->subDays(30), now()]);
                    break;

                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        // CUSTOM DATE FILTER
            if ($request->filled('from_date') || $request->filled('to_date')) {

                if ($request->filled('from_date')) {
                    $query->whereDate('created_at', '>=', $request->from_date);
                }

                if ($request->filled('to_date')) {
                    $query->whereDate('created_at', '<=', $request->to_date);
                }
            }

        // ✅ LATEST RECORDS FIRST
        $query->latest('updated_at');

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','student_name','student_email','created_at'],
            'searchable' => ['student_name','student_email','student_mobile'],
        ], function ($data, $index, $start) {

            $actions  = '<div class="d-inline-flex align-items-center gap-1">';

            $actions  .= '<a href="' . route('admin.hard_data.edit', $data->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('admin.hard_data.destroy', $data->id) . '" method="POST" style="display:inline-block;" class="hard-data-delete-form">'
                        . csrf_field()
                        . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                            <i class="fa fa-trash"></i>
                        </button>
                        </form>';
            $actions .= '</div>';
            
            $rowNum = $start + $index + 1;
            
            return [
                $rowNum,
                e($data->student_name),
                // e(optional($data->college)->FullName),
                e($data->college_name ?: ($data->college->FullName ?? '')),
                e($data->student_email),
                e($data->student_mobile),
                e($data->class),
                e($data->semester),
                e($data->course_type),
                e($data->gender),
                $data->created_at?->format('d M Y'),
                $actions,
                // Extra data for JavaScript
                'id' => $data->id,
                'is_moved_to_enquiry' => $data->is_moved_to_enquiry,
                // 'is_moved_to_enquiry' => $data->is_moved_to_enquiry
            ];
        });
    }

    $activeSessionNo = session(
        'admin_header_session_id',
        session('admin_session_id')
    );

    // Unique college names actually stored in Hard Data (not from the colleges table).
    $collegeNames = HardData::where('session_id', $activeSessionNo)
        ->where('enquiry_status', 'active')
        ->whereNotNull('college_name')
        ->whereRaw("TRIM(college_name) <> ''")
        ->select('college_name')
        ->distinct()
        ->orderBy('college_name')
        ->pluck('college_name');

    $states = State::orderBy('name')->get();

    $districtsGrouped = District::with('state')
        ->orderBy('name')
        ->get()
        ->groupBy('state_id');

    $sessionsList = StudentSession::where('status', 'active') // ✅ string status
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

    $saleSessions = StudentSession::withoutGlobalScopes()
    ->where('status', 'active')
    ->where('session_type', 1)
    ->orderBy('start_date', 'desc')
    ->pluck('session_name', 'id');

    $classNames = HardData::where('session_id', $activeSessionNo)
    ->where('enquiry_status', 'active')
    ->whereNotNull('class')
    ->whereRaw("TRIM(class) <> ''")
    ->distinct()
    ->orderBy('class')
    ->pluck('class');

    return view('hard_data.index', compact('collegeNames', 'states', 'districtsGrouped', 'sessionsList', 'saleSessions' ,'classNames'));
}

    
 
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hard_data.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   
public function store(Request $request)
{
    $validated = $request->validate([
        'student_name'   => 'required|string|max:255',
        'student_mobile' => ['required', 'digits:10', new NotBlockedNumber],
        'college_name'   => 'required|string|max:512',
        'student_email'  => 'nullable|email|max:255',
        'gender'         => 'nullable|in:male,female',
        'course_type'    => 'nullable|in:Degree,Diploma',
        'class'          => 'nullable|string|max:500',
        'semester'       => 'nullable|integer|min:1|max:8',
    ]);

    if (\App\Helpers\InternsHelper::exists($request->student_mobile)) {
        return back()
            ->withInput()
            ->withErrors([
                'student_mobile' => 'This mobile number already exists in Intern records. Entry skipped.'
            ]);
    }

    $activeSessionId = session(
        'admin_header_session_id',
        session('admin_session_id')
    );

    if (!$activeSessionId) {
        return back()
            ->withInput()
            ->withErrors(['session_id' => 'No active session is selected.']);
    }

    $collegeName = trim((string) ($validated['college_name'] ?? ''));
    $validated['college_name'] = $collegeName !== '' ? $collegeName : null;

    // College is stored as free text; do not look it up in the colleges table.
    $validated['college_id'] = null;

    $validated['session_id'] = $activeSessionId;
    $validated['source'] = 'manual';

    HardData::create($validated);

    return redirect()
        ->route('admin.hard_data.index')
        ->with('success', 'Data created successfully');
}


    /**
     * Display the specified resource.
     */
    public function show(HardData $hard_data)
    {
        $manual_data = $hard_data;
        return view('hard_data.show', compact('manual_data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $manualData = HardData::with('college')->findOrFail($id);

        return view('hard_data.edit', compact('manualData'));
    }


    /**
     * Update the specified resource in storage.
     */


public function update(Request $request, $id)
{
    $data = HardData::findOrFail($id);

    $validated = $request->validate([
        'student_name'   => 'required|string|max:255',
        'student_mobile' => ['required', 'digits:10', new NotBlockedNumber],
        'college_name'   => 'required|string|max:512',
        'student_email'  => 'nullable|email|max:255',
        'gender'         => 'nullable|in:male,female',
        'course_type'    => 'nullable|in:Degree,Diploma',
        'class'          => 'nullable|string|max:500',
        'semester'       => 'nullable|integer|min:1|max:8',
    ]);

    if (\App\Helpers\InternsHelper::exists($request->student_mobile)) {
        return back()
            ->withInput()
            ->withErrors([
                'student_mobile' => 'This mobile number already exists in Intern records. Entry skipped.'
            ]);
    }

    $collegeName = trim((string) ($validated['college_name'] ?? ''));
    $validated['college_name'] = $collegeName !== '' ? $collegeName : null;

    // College is stored as free text; do not look it up in the colleges table.
    $validated['college_id'] = null;

    $data->update($validated);

    return redirect()
        ->route('admin.hard_data.index')
        ->with('success', 'Data updated successfully');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HardData $hard_data)
    {
        $hard_data->delete();

        return redirect()
            ->route('admin.hard_data.index')
            ->with('success','Data deleted successfully');
    }

    public function exportExcelo(Request $request)
    {
        return Excel::download(
            new HardDataExport($request),
            'hard-data.xlsx'
        );
    }
public function exportExcel(Request $request)
{
    $fileName = 'hard-data';

    // ✅ Add filters to filename
    if ($request->is_moved !== null && $request->is_moved !== '') {
        $fileName .= $request->is_moved == 1 ? '-moved' : '-not-moved';
    }

    if ($request->gender) {
        $fileName .= '-' . strtolower($request->gender);
    }

    if ($request->course_type) {
        $fileName .= '-' . strtolower($request->course_type);
    }

    if ($request->filled('college_name')) {
        $fileName .= '-' . Str::slug($request->input('college_name'));
    }

    // ✅ Always add datetime
    $fileName .= '-' . Carbon::now()->format('d_F');

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\HardDataExport($request),
        $fileName . '.xlsx'
    );
}
    public function moveManualToEnquiries(Request $request)
    {
        $ids = $request->ids;
        $sessionId = $request->session_id;

        if (empty($ids)) {
            return response()->json(['message' => 'No students selected'], 422);
        }

        if (!$sessionId) {
            return response()->json(['message' => 'Session required'], 422);
        }

        $students = HardData::whereIn('id', $ids)
            ->get();

        $count = 0;

        foreach ($students as $st) {

            $enquiry = Enquiry::firstOrCreate(
                [
                    'source_id'   => $st->id,
                    'source_type' => 'hard_data',
                ],
                [
                    'name'       => $st->student_name,
                    'email'      => $st->student_email,
                    'mobile'     => $st->student_mobile ?? null,

                    'college'    => $st->college_id ?? null,
                    'study'      => $st->class ?? '',
                    'course_type' => $st->course_type ?? null,
                    'semester'   => $st->semester ?? null,

                    'session_id' => $sessionId,

                    'source'     => 'hard_data',
                    'source_type'     => 'hard_data',
                    'source_id'     => $st->id,
                    'status'     => 'new',
                    'created_by' => auth()->id(),
                ]
            );

            if ($enquiry->wasRecentlyCreated) {
                $count++;
            }

            // ✅ flag update
            $st->update([
                'is_moved_to_enquiry' => 1
            ]);
        }

        return response()->json([
            'message' => "$count students moved successfully"
        ]);
    }

     public function importForm()
    {
        return view('hard_data.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\HardDataImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

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

            $total    = $importer->totalRows;
            $inserted = $importer->insertedRows;
            $skipped  = $importer->skippedRows;

            $message = "From {$total} rows: {$inserted} inserted, {$skipped} skipped.";

            // if (!empty($importer->duplicateContacts)) {
            //     return back()
            //         ->with('success', $message)
            //         ->withErrors($importer->duplicateContacts);
            // }

            $errors = [];

            // Blocked Numbers
            if (!empty($importer->blockedNumbers)) {

                $errors[] = "===== Blocked Numbers (" . count($importer->blockedNumbers) . ") =====";

                foreach ($importer->blockedNumbers as $number) {
                    $errors[] = $number;
                }
            }

            // Duplicate Numbers
            if (!empty($importer->duplicateContacts)) {

                if (!empty($errors)) {
                    $errors[] = "";
                }

                $errors[] = "===== Duplicate Numbers (" . count($importer->duplicateContacts) . ") =====";

                foreach ($importer->duplicateContacts as $number) {
                    $errors[] = $number;
                }
            }

            if (!empty($errors)) {
                return back()
                    ->with('success', $message)
                    ->withErrors($errors);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {

            \DB::rollBack();

            return back()->withErrors([
                'Import failed. Something went wrong.'
            ]);
        }
    }

    public function bulkAction(Request $request)
{
    $request->validate([
        'ids'        => 'required',
        'action'     => 'required|in:move,close',
        'reason'     => 'nullable|string|max:255',
        'session_id' => 'nullable|exists:student_sessions,id',
    ]);

    $ids = is_array($request->ids)
        ? $request->ids
        : json_decode($request->ids, true);

    if (empty($ids)) {
        return response()->json([
            'message' => 'No records selected.'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | MOVE SESSION VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $request->action === 'move' &&
        !$request->session_id
    ) {
        return response()->json([
            'message' => 'Session is required.'
        ], 422);
    }

    DB::beginTransaction();

    try {

        $records = HardData::whereIn('id', $ids)->get();

        if ($records->isEmpty()) {
            return response()->json([
                'message' => 'No records found.'
            ], 422);
        }

        $count = 0;

        foreach ($records as $record) {

            /*
            |--------------------------------------------------------------------------
            | MOVE TO ANOTHER SALE SESSION
            |--------------------------------------------------------------------------
            */

            if ($request->action === 'move') {

                // Already in selected session
                if (
                    (int) $record->session_id ===
                    (int) $request->session_id
                ) {
                    continue;
                }

                $record->update([
                    'session_id' => $request->session_id,
                ]);

                $count++;
            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE RECORD
            |--------------------------------------------------------------------------
            */

            if ($request->action === 'close') {

                $record->update([
                    'enquiry_status' => 'closed',
                    'closed_reason'  => $request->reason,
                    'closed_at'      => now(),
                    'closed_by'      => auth()->id(),
                ]);

                $count++;
            }
        }

        DB::commit();

        return response()->json([
            'message' => $request->action === 'move'
                ? "$count records moved successfully."
                : "$count records closed successfully."
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}

public function bulkDelete(Request $request)
{
    $validated = $request->validate([
        'ids'   => 'required|array|min:1',
        'ids.*' => 'required|integer|distinct',
    ]);

    $records = HardData::whereIn('id', $validated['ids'])->get();

    if ($records->isEmpty()) {
        return response()->json([
            'message' => 'No records found to delete.',
        ], 404);
    }

    $count = 0;

    foreach ($records as $record) {
        $record->delete();
        $count++;
    }

    return response()->json([
        'message' => $count . ' record(s) deleted successfully.',
    ]);
}
}
