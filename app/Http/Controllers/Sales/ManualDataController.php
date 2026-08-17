<?php

namespace App\Http\Controllers\Sales;
use App\Http\Controllers\Controller;

use App\Models\ManualData;
use App\Models\College;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;
use App\Exports\WorkshopsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\State;
use App\Models\District;
use App\Models\Enquiry;
use App\Rules\NotBlockedNumber;
use App\Exports\ManualDataExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManualDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('permission:manual_data.view')->only(['index','show','importForm','import']);
        $this->middleware('permission:manual_data.create')->only(['create','store']);
        $this->middleware('permission:manual_data.edit')->only(['edit','update','moveManualToEnquiries']);
        $this->middleware('permission:manual_data.delete')->only('destroy');
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

        $query = ManualData::with('college')
                    ->where('session_id', $activeSessionNo)
                     ->where('enquiry_status', 'active');

        // FILTERS

        // if ($request->college_id) {
        //     $query->where('college_id', $request->college_id);
        // }

        if ($request->filled('college_id')) {

            $value = $request->college_id;

            if (str_starts_with($value, 'id_')) {

                $query->where('college_id', str_replace('id_', '', $value));

            } elseif (str_starts_with($value, 'txt_')) {

                $query->whereNull('college_id')
                      ->where('college_name', str_replace('txt_', '', $value));
            }
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

        if ($request->is_moved !== null && $request->is_moved !== '') {
            $query->where('is_moved_to_enquiry', (int) $request->is_moved);
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

        // ✅ LATEST RECORDS FIRST
        $query->latest('updated_at');

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','student_name','student_email','created_at'],
            'searchable' => ['student_name','student_email','student_mobile'],
        ], function ($data, $index, $start) {

            $mobile = preg_replace('/\D/', '', $data->student_mobile); // remove non-digits
            if(strlen($mobile) == 10){
                $mobile = '91'.$mobile;
            }
            $actions = '<a href="https://wa.me/'.$mobile.'?text=Hello '.$data->student_name.'," target="_blank"><i class="fa-brands fa-whatsapp"></i></a>';
            $actions .= '<a href="' . route('admin.manual_data.edit', $data->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('admin.manual_data.destroy', $data->id) . '" method="POST" style="display:inline-block;">'
                        . csrf_field()
                        . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                            <i class="fa fa-trash"></i>
                        </button>
                        </form>';

            $rowNum = $start + $index + 1;

            return [
                $rowNum,
                e($data->student_name),
                e($data->college ? $data->college->FullName : $data->college_name),
                e($data->student_email),
                e($data->student_mobile),
                e($data->class),
                e($data->semester),
                e($data->course_type),
                e($data->gender),
                $data->created_at?->format('d M Y'),
                $actions,
                'is_moved_to_enquiry' => $data->is_moved_to_enquiry,
                'row_id' => $data->id
            ];
        });
    }

    $colleges = College::orderBy('college_name')->get();
    $unknownColleges = ManualData::whereNull('college_id')
    ->whereNotNull('college_name')
    ->where('session_id', session('admin_session_id'))
    ->select('college_name')
    ->distinct()
    ->orderBy('college_name')
    ->get();

    $states = State::orderBy('name')->get();

    $districtsGrouped = District::with('state')
        ->orderBy('name')
        ->get()
        ->groupBy('state_id');

    $sessionsList = StudentSession::where('status', 'active') // ✅ string status
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

    // $saleSessions = StudentSession::withoutGlobalScope('normalSession')
    // ->where('session_type', 1)
    // ->where('status', 'active')
    // ->orderBy('start_date', 'desc')
    // ->get();

    $saleSessions = StudentSession::withoutGlobalScopes()
    ->where('status', 'active')
    ->where('session_type', 1)
    ->orderBy('start_date', 'desc')
    ->pluck('session_name', 'id');

    $previousWhatsappUploadedFiles = collect(
            Storage::disk('public')->files('whatsapp-files')
        )->map(function ($file) {
            return [
                'path' => $file,
                'name' => basename($file),
            ];
        });

    return view('manual_data.index', compact('colleges','unknownColleges', 'states','districtsGrouped','sessionsList', 'previousWhatsappUploadedFiles','saleSessions'));
}

    
 
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $colleges = College::orderBy('college_name','asc')->get();
        return view('manual_data.create', compact('colleges'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
public function store(Request $request)
{
    $validated = $request->validate([
        'college_id'      => 'required|integer',
        'student_name'    => 'required|string|max:255',
        'student_email'   => 'required|email|max:255',
        'student_mobile'  => 'required|digits:10',
        'student_mobile' => ['required', 'required', 'digits:10', new NotBlockedNumber],
        'gender'          => 'required|in:male,female',
        'course_type'     => 'required|in:Degree,Diploma',
        'class'           => 'required',
        'semester'        => 'required|integer|min:1|max:8',
    ]);

    // $activeSessionId = session('admin_session_id');
    $activeSessionId = $activeSessionNo = session(
        'admin_header_session_id',
        session('admin_session_id')
    );
    // Optional: default source
    $validated['session_id'] = $activeSessionId;
    $validated['source'] = 'manual';

    ManualData::create($validated);

    return redirect()
        ->route('admin.manual_data.index')
        ->with('success', 'Data created successfully');
}


    /**
     * Display the specified resource.
     */
    public function show(ManualData $manual_data)
    {
        return view('manual_data.show', compact('manual_data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $manualData = ManualData::findOrFail($id);
        $colleges = College::orderBy('college_name','asc')->get();

        return view('manual_data.edit', compact('manualData','colleges'));
    }


    /**
     * Update the specified resource in storage.
     */


public function update(Request $request, $id)
{
    $data = ManualData::findOrFail($id);

    $validated = $request->validate([
        'college_id'      => 'required|integer',
        'student_name'    => 'required|string|max:255',
        'student_email'   => 'required|email|max:255',
        // 'student_mobile'  => 'required|digits:10',
        'student_mobile' => ['required', 'required', 'digits:10', new NotBlockedNumber],
        'gender'          => 'required|in:male,female',
        'course_type'     => 'required|in:Degree,Diploma',
        'class'           => 'required',
        'semester'        => 'required|integer|min:1|max:8',
    ]);

    $data->update($validated);

    return redirect()
        ->route('admin.manual_data.index')
        ->with('success', 'Data updated successfully');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManualData $manual_data)
    {
        // dd($manual_data);
        $manual_data->delete();

        return redirect()
            ->route('admin.manual_data.index')
            ->with('success','Data deleted successfully');
    }


    public function exportExcelo (Request $request)
    {
        return Excel::download(
            new ManualDataExport($request),
            'manual-data.xlsx'
        );
    }


public function exportExcel(Request $request)
{
    $fileName = 'manual-data';

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

    if ($request->college_id) {
        $college = \App\Models\College::find($request->college_id);
        if ($college) {
            $fileName .= '-' . Str::slug($college->college_name);
        }
    }

    // ✅ Always add datetime
    $fileName .= '-' . Carbon::now()->format('d_F');

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\ManualDataExport($request),
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

        $students = ManualData::whereIn('id', $ids)
            ->get();

        $count = 0;

        foreach ($students as $st) {

            $enquiry = Enquiry::firstOrCreate(
                [
                    'source_id'   => $st->id,
                    'source_type' => 'manual_data',
                ],
                [
                    'name'       => $st->student_name,
                    'email'      => $st->student_email,
                    'mobile'     => $st->student_mobile ?? null,

                    'college'    => $st->college_id ?? null,
                    'study'      => $st->class ?? '',
                    'semester'   => $st->semester ?? null,

                    'session_id' => $sessionId,

                    'source'     => 'manual_data',
                    'source_type'     => 'manual_data',
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
        return view('manual_data.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\ManualDataImport();
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

            // College Not Found
            if (!empty($importer->collegeNotFound)) {

                if (!empty($errors)) {
                    $errors[] = "";
                }

                $errors[] = "===== College Not Found (Saved as Text) (" . count($importer->collegeNotFound) . ") =====";

                foreach ($importer->collegeNotFound as $college) {
                    $errors[] = $college;
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
        return back()->with('error', 'No records selected.');
    }

    DB::beginTransaction();

    try {

        $manualData = ManualData::whereIn('id', $ids)->get();

        foreach ($manualData as $record) {

            $oldSession = $record->session_id;

            $oldSessionVal = StudentSession::find($oldSession);

            $newSession = null;


            /*
            |--------------------------------------------------------------------------
            | MOVE RECORD
            |--------------------------------------------------------------------------
            */

            if ($request->action == 'move') {

                // If same session, skip
                if ((int) $oldSession === (int) $request->session_id) {
                    continue;
                }

                $newSession = StudentSession::find(
                    $request->session_id
                );


                $record->update([
                    'session_id' => $request->session_id,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Activity / Movement
                |--------------------------------------------------------------------------
                |
                | Only add these if you have these tables/models for ManualData.
                | For now we only update ManualData.
                |
                */


                // If you want to keep a movement history later,
                // we can add ManualDataMovement here.
            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE RECORD
            |--------------------------------------------------------------------------
            */

            if ($request->action == 'close') {

                $record->update([

                    'enquiry_status' => 'closed',

                    'closed_reason' => $request->reason,

                    'closed_at' => now(),

                    'closed_by' => auth()->id(),

                ]);

            }
        }


        DB::commit();


        return redirect()
            ->back()
            ->with(
                'success',
                'Selected records updated successfully.'
            );


    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->with(
                'error',
                $e->getMessage()
            );
    }
}


}
