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

class TrainerController extends Controller
{   
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
            return [
                $trainer->id,
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


    public function index2()
{



    $currentSession = session('admin_session_id');
    $today = now()->toDateString();
    $currentTime = now()->format('H:i:s');

    $trainers = Trainer::whereHas('user', function ($q) {
        $q->whereNull('deleted_at');
    })
    ->with(['user', 'courseData'])
    ->withCount([

        // 🔵 TOTAL batches in current session
        'batches as session_batches_count' => function($q) use ($currentSession) {
            $q->where('session_name', $currentSession);
        },

        // 🔵 ONLINE batches in current session
        'batches as online_batches_count' => function($q) use ($currentSession) {
            $q->where('session_name', $currentSession)
              ->where('batch_mode', 'online');
        },

        // 🔵 OFFLINE batches in current session
        'batches as offline_batches_count' => function($q) use ($currentSession) {
            $q->where('session_name', $currentSession)
              ->where('batch_mode', 'offline');
        },

        // 🔵 TODAY remaining batches (your existing logic, unchanged)
        'batches as today_remaining_batches_count' => function($q) use ($currentSession, $today, $currentTime) {
            $q->whereDate('start_time', $today)
              ->where('end_time', '>', $currentTime);
        },

    ])
    ->latest()
    ->get();


    return view('trainers.index', compact('trainers'));
}
 public function index20jan()
{



    $currentSession = session('admin_session_id');
    $today = now()->toDateString();
    $currentTime = now()->format('H:i:s');

    $trainers = Trainer::whereHas('user', function ($q) {
            $q->whereNull('deleted_at');
        })
        ->with(['user', 'courseData'])
        ->withCount([
            // All batches for this session
            'batches as session_batches_count' => function($q) use ($currentSession) {
                $q->where('session_name', $currentSession);
            },

            // Today remaining batches (pending)
            // 'batches as today_remaining_batches_count' => function($q) use ($currentSession, $today, $currentTime) {
            //     $q->where('session_name', $currentSession)
            //       ->whereDate('start_time', $today)  // USE YOUR BATCH DATE COLUMN HERE
            //       ->where('end_time', '>', $currentTime); // pending
            // },

            'batches as today_remaining_batches_count' => function($q) use ($currentSession, $today, $currentTime) {
                $q->whereDate('start_time', $today)  // USE YOUR BATCH DATE COLUMN HERE
                  ->where('end_time', '>', $currentTime); // pending
            },
        ])
        ->latest()
        ->get();

    return view('trainers.index', compact('trainers'));
}


    public function indexOld()
    {
        $sessions = StudentSession::all();
        $colleges = \App\Models\College::all();
        $courses  = \App\Models\Course::all();
        $batches  = \App\Models\Batch::all();
        $departments = \App\Models\Department::all();

        // $trainers = Trainer::with('user')->latest()->get();
        // $trainers = Trainer::with(['user', 'batches', 'courseData'])->latest()->get();
        // $trainers = Trainer::with([
        //     'user' => function ($q) {
        //         $q->withTrashed();
        //     },
        //     'batches',
        //     'courseData'
        // ])->latest()->get();

        $trainers = Trainer::whereHas('user', function ($q) {
            $q->whereNull('deleted_at'); // only active users
        })
        ->with(['user', 'batches', 'courseData'])
        ->latest()
        ->get();


        return view('trainers.index', compact('trainers', 'sessions', 'colleges','batches', 'courses', 'departments'));
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
            'phone'      => 'required|max:20|unique:trainers,phone',
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

    public function store27jan(Request $request)
    {
        // dd($request->post());
        $validated = $request->validate([
            'trainer_name' => 'required|string|max:100',
            'gender'       => 'required|in:male,female',
            'phone'        => 'required|max:20|unique:users,phone',
            // 'username'        => 'required|max:20|unique:users,username',
            'username'     => [
                'required',
                'string',
                'max:30',
                'regex:/^[a-zA-Z0-9._-]+$/', // ❌ no spaces allowed
                'unique:users,username',
            ],
            'password' => 'required|string|min:6',
            'email'        => 'required|email|unique:users,email',
            'technology'   => 'required|array',
            'technology.*' => 'exists:student_courses,id',

        ],
        [
            // 🔴 Custom messages
            'username.regex'  => 'Username must not contain spaces. Only letters, numbers, dot (.), dash (-), and underscore (_) are allowed.',
            'username.unique' => 'This username is already taken.',
            'username.max'    => 'Username may not be greater than 30 characters.',
            'username.required' => 'Please enter a username.',
        ]);

        // 🔵 STEP 1 — Create User Account
        $user = User::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role'     => 2, // trainer role
            'name'     => $validated['trainer_name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'status'   => 'active',
        ]);

        // 🔵 STEP 2 — Create Trainer Profile
        Trainer::create([
            'user_id'    => $user->id,
            'gender'     => $validated['gender'],
            // 'technology' => $validated['technology'],
            'technology' => implode(',', $validated['technology']),
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
            'phone'    => 'required|max:20|unique:trainers,phone,' . $trainer->id,
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

    public function update27jan(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'trainer_name' => 'required|string|max:100',
            'gender'       => 'required|in:male,female',

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($trainer->user_id),
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($trainer->user_id),
            ],

            // 🔵 MULTIPLE TECHNOLOGY VALIDATION
            'technology'   => 'required|array',
            'technology.*' => 'exists:student_courses,id',
        ]);

        // 🔵 Update user table
        $trainer->user->update([
            'name'  => $validated['trainer_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        // 🔵 Update trainer profile (SAVE MULTIPLE TECHNOLOGIES)
        $trainer->update([
            'gender'     => $validated['gender'],
            'technology' => implode(',', $validated['technology']), // 🔥 SAVE AS "1,3,5"
        ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer updated successfully!');
    }

    public function update2(Request $request, Trainer $trainer)
    {

        $validated = $request->validate([
            'trainer_name' => 'required|string|max:100',
            'gender'       => 'required|in:male,female',
            'phone'        => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($trainer->user_id),
            ],
            'email'        => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($trainer->user_id),
            ],
            'technology'   => 'required|max:100',
        ]);

        // 🔵 Update user table
        $trainer->user->update([
            'name'  => $validated['trainer_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        // 🔵 Update trainer profile
        $trainer->update([
            'gender'     => $validated['gender'],
            'technology' => $validated['technology'],
        ]);

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

    public function destroy(Trainer $trainer)
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

    public function destroy27jan(Trainer $trainer)
    {
        $currentSession = session('admin_session_id');

        // Check if trainer has ANY batches in this session
        $sessionBatchCount = Batch::where('batch_assign', $trainer->id)
            ->where('session_name', $currentSession)
            ->count();

        if ($sessionBatchCount > 0) {
            return redirect()->route('trainers.index')
                ->with('error', 'Cannot delete trainer because they have assigned batches in the active session.');
        }

        // Optional: Check ANY batches globally
        $totalBatchCount = Batch::where('batch_assign', $trainer->id)->count();

        if ($totalBatchCount > 0) {
            return redirect()->route('trainers.index')
                ->with('error', 'Cannot delete trainer because they have assigned batches.');
        }

        // Safe delete both user and trainer
        if ($trainer->user) {
            $trainer->user->delete(); // Soft delete user
        }

        $trainer->delete(); // Soft delete trainer

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
}
