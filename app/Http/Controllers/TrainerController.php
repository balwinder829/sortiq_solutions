<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class TrainerController extends Controller
{   

    public function index()
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
            'technology'   => 'required',
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
            'technology' => $validated['technology'],
        ]);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer added successfully!');
    }

    public function edit(Trainer $trainer)
    {   
        // if user's account is deleted, block access
        if ($trainer->user && $trainer->user->trashed()) {
            abort(403, 'This trainer is deactivated.');
        }
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
