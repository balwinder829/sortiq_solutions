<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Batch;
use App\Models\Course;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use App\Rules\NotBlockedNumber;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;


class TrainerController extends Controller
{   
    use PdfLayoutTrait;
    protected string $permissionPrefix = 'mentors';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

//     public function index(Request $request)
// {
//     $currentSession = session('admin_session_id');
//     $today = now()->toDateString();
//     $currentTime = now()->format('H:i:s');

//     // 🔵 Get all courses for filter dropdown
//     $courses = \App\Models\Course::orderBy('course_name')->get();


//     $trainersQuery = Trainer:: withCount([

//             // TOTAL batches
//             'batches as session_batches_count' => function($q) use ($currentSession) {
//                 $q->where('session_name', $currentSession);
//             },

//             // ONLINE batches
//             'batches as online_batches_count' => function($q) use ($currentSession) {
//                 $q->where('session_name', $currentSession)
//                   ->where('batch_mode', 'online');
//             },

//             // OFFLINE batches
//             'batches as offline_batches_count' => function($q) use ($currentSession) {
//                 $q->where('session_name', $currentSession)
//                   ->where('batch_mode', 'offline');
//             },

//             // TODAY remaining
//             'batches as today_remaining_batches_count' => function($q) use ($today, $currentTime) {
//                 $q->whereDate('start_time', $today)
//                   ->where('end_time', '>', $currentTime);
//             },

//         ]);

//     // 🔴 APPLY TECHNOLOGY FILTER (Course)
//     if ($request->filled('course')) {
//         $courseId = $request->course;

//         // FIND trainers where this course id exists in comma separated technology
//         $trainersQuery->whereRaw("FIND_IN_SET(?, technology)", [$courseId]);
//     }

//     $trainers = $trainersQuery->latest()->get();

//     return view('trainers.index', compact('trainers', 'courses'));
// }


    public function index(Request $request)
    {
        $courses = Course::orderBy('course_name')->get();
        return view('trainers.index', compact('courses'));
    }

    public function data(Request $request)
{
    $currentSession = session('admin_session_id');
    $today = now()->toDateString();
    $currentTime = now()->format('H:i:s');

    $trainersQuery = Trainer::withCount([
        'batches as session_batches_count' => function ($q) use ($currentSession) {
            $q->where('session_name', $currentSession);
        },
        'batches as online_batches_count' => function ($q) use ($currentSession) {
            $q->where('session_name', $currentSession)->where('batch_mode', 'online');
        },
        'batches as offline_batches_count' => function ($q) use ($currentSession) {
            $q->where('session_name', $currentSession)->where('batch_mode', 'offline');
        },
        'batches as today_remaining_batches_count' => function ($q) use ($today, $currentTime) {
            $q->whereDate('start_time', $today)->where('end_time', '>', $currentTime);
        },
    ]);

    if ($request->filled('course')) {
        $trainersQuery->whereRaw('FIND_IN_SET(?, technology)', [$request->course]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS FILTER - NEW
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
        $trainersQuery->where('status', $request->status);
    }

    $trainersQuery = $trainersQuery->latest('updated_at');

    return DataTablesServerSide::response($request, $trainersQuery, [
        'orderable'  => ['id', 'username', 'name', 'gender', 'phone', 'email', 'technology'],
        'searchable' => ['username', 'name', 'email', 'phone'],
    ], function ($trainer, $index, $start) {

        $techIds = $trainer->technology ? explode(',', $trainer->technology) : [];

        $techNames = Course::whereIn('id', $techIds)->pluck('course_name');

        $techHtml = '';

        foreach ($techNames as $name) {
            $techHtml .= '<span class="badge bg-primary">' . e($name) . '</span> ';
        }

        $totalBat = '<div class="batch-circle batch-link" data-id="' . $trainer->id . '" data-name="' . e($trainer->name ?? 'N/A') . '" data-type="all" title="View All Batches">' . (int) ($trainer->session_batches_count ?? 0) . '</div>';

        $onlineBat = '<div class="batch-circle" style="background:#198754" title="Online Batches">' . (int) ($trainer->online_batches_count ?? 0) . '</div>';

        $offlineBat = '<div class="batch-circle" style="background:#fd7e14" title="Offline Batches">' . (int) ($trainer->offline_batches_count ?? 0) . '</div>';

        $todayBat = '<div class="batch-circle batch-link" data-id="' . $trainer->id . '" data-name="' . e($trainer->name ?? 'N/A') . '" data-type="remaining" title="View Today\'s Remaining Batches">' . (int) ($trainer->today_remaining_batches_count ?? 0) . '</div>';


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

       $actions = '<div class="d-flex align-items-center gap-1">';


// EDIT
$actions .= '
    <a href="' . route('trainers.edit', $trainer->id) . '"
       class="btn btn-sm"
       title="Edit">
        <i class="fa fa-edit"></i>
    </a>
';


// ACTIVE / INACTIVE
if ($trainer->status === 'active') {

    $actions .= '
        <form
            action="' . route('trainers.toggleStatus', $trainer->id) . '"
            method="POST"
            class="d-inline m-0 trainer-action-form"
            data-title="Deactivate Trainer?"
            data-text="Are you sure you want to deactivate this trainer?"
            data-confirm="Yes, Deactivate"
        >
            ' . csrf_field() . '

            <button
                type="submit"
                class="btn btn-sm"
                title="Deactivate"
            >
                <i class="fa fa-toggle-on"></i>
            </button>
        </form>
    ';

} else {

    $actions .= '
        <form
            action="' . route('trainers.toggleStatus', $trainer->id) . '"
            method="POST"
            class="d-inline m-0 trainer-action-form"
            data-title="Activate Trainer?"
            data-text="Are you sure you want to activate this trainer?"
            data-confirm="Yes, Activate"
        >
            ' . csrf_field() . '

            <button
                type="submit"
                class="btn btn-sm"
                title="Activate"
            >
                <i class="fa fa-toggle-off"></i>
            </button>
        </form>
    ';

}


// DELETE
$actions .= '
    <form
        action="' . route('trainers.destroy', $trainer->id) . '"
        method="POST"
        class="d-inline m-0 trainer-action-form"
        data-title="Delete Trainer?"
        data-text="Are you sure you want to delete this trainer?"
        data-confirm="Yes, Delete"
    >
        ' . csrf_field() . '
        ' . method_field('DELETE') . '

        <button
            type="submit"
            class="btn btn-sm"
            title="Delete"
        >
            <i class="fa fa-trash"></i>
        </button>
    </form>
';


$actions .= '</div>';


        $rowNum = $start + $index + 1;

        return [
            $rowNum,
            e($trainer->username ?? ''),
            ucwords($trainer->name ?? ''),
            ucfirst($trainer->gender ?? '-'),
            e($trainer->phone ?? 'N/A'),
            e($trainer->email ?? 'N/A'),
            $techHtml,
            $totalBat,
            $onlineBat,
            $offlineBat,
            $todayBat,
            $actions,
        ];
    });
}

    public function data_26aug(Request $request)
    {
        $currentSession = session('admin_session_id');
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $trainersQuery = Trainer::withCount([
            'batches as session_batches_count' => function ($q) use ($currentSession) {
                $q->where('session_name', $currentSession);
            },
            'batches as online_batches_count' => function ($q) use ($currentSession) {
                $q->where('session_name', $currentSession)->where('batch_mode', 'online');
            },
            'batches as offline_batches_count' => function ($q) use ($currentSession) {
                $q->where('session_name', $currentSession)->where('batch_mode', 'offline');
            },
            'batches as today_remaining_batches_count' => function ($q) use ($today, $currentTime) {
                $q->whereDate('start_time', $today)->where('end_time', '>', $currentTime);
            },
        ]);

        if ($request->filled('course')) {
            $trainersQuery->whereRaw('FIND_IN_SET(?, technology)', [$request->course]);
        }

        $trainersQuery = $trainersQuery->latest('updated_at');
        return DataTablesServerSide::response($request, $trainersQuery, [
            'orderable'  => ['id', 'username', 'name', 'gender', 'phone', 'email', 'technology'],
            'searchable' => ['username', 'name', 'email', 'phone'],
        ], function ($trainer, $index, $start) {
            $techIds = $trainer->technology ? explode(',', $trainer->technology) : [];
            $techNames = Course::whereIn('id', $techIds)->pluck('course_name');
            $techHtml = '';
            foreach ($techNames as $name) {
                $techHtml .= '<span class="badge bg-primary">' . e($name) . '</span> ';
            }
            $totalBat = '<div class="batch-circle batch-link" data-id="' . $trainer->id . '" data-name="' . e($trainer->name ?? 'N/A') . '" data-type="all" title="View All Batches">' . (int) ($trainer->session_batches_count ?? 0) . '</div>';
            $onlineBat = '<div class="batch-circle" style="background:#198754" title="Online Batches">' . (int) ($trainer->online_batches_count ?? 0) . '</div>';
            $offlineBat = '<div class="batch-circle" style="background:#fd7e14" title="Offline Batches">' . (int) ($trainer->offline_batches_count ?? 0) . '</div>';
            $todayBat = '<div class="batch-circle batch-link" data-id="' . $trainer->id . '" data-name="' . e($trainer->name ?? 'N/A') . '" data-type="remaining" title="View Today\'s Remaining Batches">' . (int) ($trainer->today_remaining_batches_count ?? 0) . '</div>';
            $actions = '<a href="' . route('trainers.edit', $trainer->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';

            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($trainer->username ?? ''),
                ucwords($trainer->name ?? ''),
                ucfirst($trainer->gender ?? '-'),
                e($trainer->phone ?? 'N/A'),
                e($trainer->email ?? 'N/A'),
                $techHtml,
                $totalBat,
                $onlineBat,
                $offlineBat,
                $todayBat,
                $actions,
            ];
        });
    }

    public function create()
    {
        $sessions = StudentSession::all();
        $colleges = \App\Models\College::all();
        $courses  = \App\Models\Course::all();
        $batches  = \App\Models\Batch::all();
        $department = \App\Models\Department::all();

        return view('trainers.create', compact('sessions', 'colleges','batches', 'courses', 'department'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'gender'     => 'required|in:male,female',
            // 'phone'      => 'required|max:20|unique:trainers,phone',
            'phone' => ['required', 'string','unique:trainers,phone', new NotBlockedNumber],
            'username'   => 'required|string|max:30|regex:/^[a-zA-Z0-9._-]+$/|unique:trainers,username',
            'password'   => 'required|string|min:6',
            'email'      => 'nullable|email|unique:trainers,email',
            'technology' => 'required|array',
            'technology.*' => 'exists:student_courses,id',
            'status' => 'required',
        ]);

        Trainer::create([
            'name'       => $validated['name'],
            'username'   => $validated['username'],
            'password'   => $validated['password'], // auto-hashed
            'trainer_pswd'   => $validated['password'], // auto-hashed
            'email'      => $validated['email'] ?? null,
            'phone'      => $validated['phone'],
            'gender'     => $validated['gender'],
            'technology' => implode(',', $validated['technology']),
            'status'     => $validated['status'],
        ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer added successfully!');
    }

    

    public function edit(Trainer $trainer)
    {   
        // dd($trainer);
        // if user's account is deleted, block access
        // if ($trainer->status !== 'active') {
        //     abort(403, 'Trainer is inactive.');
        // }
        $sessions = StudentSession::all();
        $colleges = \App\Models\College::all();
        $courses  = \App\Models\Course::all();
        $batches  = \App\Models\Batch::all();
        $department = \App\Models\Department::all();

        return view('trainers.edit', compact(
            'trainer', 'sessions', 'colleges', 'batches', 'courses', 'department'
        ));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'gender'   => 'required|in:male,female',
            // 'phone'    => 'required|max:20|unique:trainers,phone,' . $trainer->id,
            'phone' => [
                'required',
                'max:20',
                'unique:trainers,phone,' . $trainer->id,
                new NotBlockedNumber,
            ],
            'username'   => 'required|string|max:30|regex:/^[a-zA-Z0-9._-]+$/|unique:trainers,username,' . $trainer->id,
            'email'    => 'nullable|email|unique:trainers,email,' . $trainer->id,
            'technology'   => 'required|array',
            'technology.*' => 'exists:student_courses,id',
            'status' => 'required',
            'password' => 'nullable'
        ]);

         // Basic update data
        $data = [
            'name'       => $validated['name'],
            'gender'     => $validated['gender'],
            'username'      => $validated['username'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'],
            'status'      => $validated['status'],
            'technology' => implode(',', $validated['technology']),
        ];

         // ✅ Update password ONLY if filled
        if ($request->filled('password')) {
            $data['trainer_pswd'] = $data['password'] = $request->password;
        }

        $trainer->update($data);

        // $trainer->update([
        //     'name'       => $validated['name'],
        //     'gender'     => $validated['gender'],
        //     'phone'      => $validated['phone'],
        //     'email'      => $validated['email'] ?? null,
        //     'technology' => implode(',', $validated['technology']),
        // ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer updated successfully!');
    }

    
    // public function destroy(Trainer $trainer)
    // {
    //     // Delete user + trainer safely
    //     $trainer->user->delete();
    //     $trainer->delete();

    //     return redirect()->route('trainers.index')
    //         ->with('success', 'Trainer deleted successfully!');
    // }

    public function toggleStatus(Trainer $trainer)
    {
        if ($trainer->status === 'active') {

            $trainer->status = 'inactive';

        } else {

            $trainer->status = 'active';

        }

        $trainer->save();

        return redirect()
            ->route('trainers.index')
            ->with('success', 'Trainer status updated successfully.');
    }

    public function destroy(Trainer $trainer)
{
    $hasBatches = Batch::where('batch_assign', $trainer->id)
        ->whereNull('deleted_at')
        ->exists();

    if ($hasBatches) {
        return redirect()
            ->route('trainers.index')
            ->with('error', 'Cannot delete trainer because batch is assigned.');
    }

    $trainer->delete();

    return redirect()
        ->route('trainers.index')
        ->with('success', 'Trainer deleted successfully.');
}


    public function destroy_26aug(Trainer $trainer)
    {
        $currentSession = session('admin_session_id');

        $hasBatches = Batch::where('batch_assign', $trainer->id)->exists();

        if ($hasBatches) {
            return back()->with('error', 'Cannot delete trainer with assigned batches.');
        }

        $trainer->update(['status' => 'inactive']);
        $trainer->delete(); // soft delete

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer deleted successfully!');
    }

    


    public function batchesAjax($trainerId)
    {
        $type = request('type'); // all or remaining
        $currentSession = session('admin_session_id');
        $currentTime = now()->format('H:i:s');

        // Query builder base
        $query = Batch::where('batch_assign', $trainerId)
            ->with(['sessionData', 'courseData']);

        // If type = remaining → show only batches not yet finished today
        if ($type === 'all') {
            $query->where('session_name', $currentSession);
        }

        if ($type === 'remaining') {
            $query->where('end_time', '>', $currentTime);
        }

        // Get results
        $batches = $query->orderBy('start_time')->get();

        return view('trainers.batches-table', compact('batches'));
    }


    public function importForm()
    {
        return view('trainers.import');
    }

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:csv,txt,xlsx,xls',
    //     ]);

    //     try {
    //         $importer = new \App\Imports\TrainersImport();
    //         \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

    //         $errors = [];

    //         if (!empty($importer->duplicatePhones)) {
    //             foreach ($importer->duplicatePhones as $msg) {
    //                 $errors[] = $msg;
    //             }
    //         }

    //         if (!empty($errors)) {
    //             return back()
    //                 ->with('success', 'Trainers imported with warnings.')
    //                 ->withErrors($errors);
    //         }

    //         return back()->with('success', 'Trainers imported successfully!');

    //     } catch (\Throwable $e) {
    //         return back()->withErrors([
    //             'Import failed: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            // $importer = new TrainersImport();
            $importer = new \App\Imports\TrainersImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));
            // Excel::import($importer, $request->file('file'));

            if (!empty($importer->warnings)) {
                // pass warnings to session for UI and for download routes
                session()->put('warnings_download', $importer->warnings);

                return back()
                    ->with('success', 'Import completed with some skipped rows.')
                    ->with('warnings', $importer->warnings);
            }

            return back()->with('success', 'Trainers imported successfully!');

        } catch (\Throwable $e) {
            return back()->withErrors(['Import failed: ' . $e->getMessage()]);
        }
    }

    public function downloadSkipped($type)
    {
        $warnings = session('warnings_download');

        if (!$warnings || count($warnings) == 0) {
            return back()->with('error', 'No skipped rows available to download.');
        }

        switch (strtolower($type)) {
            case 'txt':
                return $this->downloadTxt($warnings);
            case 'csv':
                return $this->downloadCsv($warnings);
            case 'xlsx':
                return $this->downloadExcel($warnings);
            default:
                abort(404);
        }
    }

    private function downloadTxt(array $warnings)
    {
        $content = "Skipped Rows Report\n-----------------------\n\n";

        foreach ($warnings as $w) {
            $content .= "Row: {$w['row']}\n";
            $content .= "Reason: {$w['reason']}\n";
            $content .= "Value: {$w['value']}\n";
            $content .= "---------------------------------\n";
        }
        session()->forget('warnings_download');
        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="skipped_rows.txt"',
        ]);
    }

    private function downloadCsv(array $warnings)
    {
        // Stream CSV output (no temp file)
        $callback = function() use ($warnings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Row', 'Reason', 'Value']);
            foreach ($warnings as $w) {
                fputcsv($file, [$w['row'], $w['reason'], $w['value']]);
            }
            fclose($file);
        };

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=skipped_rows.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        session()->forget('warnings_download');
        return response()->stream($callback, 200, $headers);
    }

    private function downloadExcel(array $warnings)
    {   
        session()->forget('warnings_download');
        $downloader = new \App\Exports\SkippedRowsExport($warnings);
        return \Maatwebsite\Excel\Facades\Excel::download($downloader, 'skipped_rows.xlsx');
    }

    private function generate_pdf($trainer,$letter_type): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => storage_path('app/mpdf'),
        ]);
 
        if($letter_type == 'res'){
            $view = 'trainers.mentors_responsibilities_letter_pdf';
                    
            $html = View::make($view)->render();
        }else{
            $view = 'student_additional_letters.pdf';
                    
            $html = View::make($view, compact('letter'))->render();
        }
         
        
        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
              
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');
        // RETURN STRING ONLY
        return $mpdf->Output('', 'S');
    }

    public function downloadResponsiblitiesLetter()
    {   
        
        $trainer = [];
        $pdfContent = $this->generate_pdf($trainer,'res');

        $fileName = "mentors_responsiblities_letter.pdf";

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }
}
