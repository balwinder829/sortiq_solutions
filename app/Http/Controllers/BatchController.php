<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\StudentSession;
use App\Models\Trainer;
use App\Models\Course; 
use App\Models\Duration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BatchController extends Controller
{

    protected string $permissionPrefix = 'batches';

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
public function index(Request $request)
{
    // $sessionId = session('session_id');
    $sessionId = session('admin_session_id');

    // dd($sessionId);

    /*
    |--------------------------------------------------------------------------
    | Tab
    |--------------------------------------------------------------------------
    */

    $tab = $request->get('tab', 'normal');

    // Only allow our three tabs
    if (!in_array($tab, ['normal', 'closed', 'deleted'])) {
        $tab = 'normal';
    }


    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    |
    | withTrashed() allows us to explicitly control deleted_at below.
    |
    */

    $query = Batch::withTrashed()
        ->with([
            'sessionData',
            'trainerData',
            'students',
        ])
        ->withCount('students')
        ->where('session_name', $sessionId);


    /*
    |--------------------------------------------------------------------------
    | TAB FILTER
    |--------------------------------------------------------------------------
    */

    if ($tab === 'normal') {

        // Normal:
        // - Not deleted
        // - Status can be active/inactive/completed/cancelled
        // - Closed batches excluded

        $query->whereNull('deleted_at')
              ->where('status', '!=', 'closed');

    } elseif ($tab === 'closed') {

        // Closed:
        // - Not deleted
        // - Status = closed

        $query->whereNull('deleted_at')
              ->where('status', 'closed');

    } elseif ($tab === 'deleted') {

        // Deleted:
        // - Soft deleted only

        $query->whereNotNull('deleted_at');

    }


    /*
    |--------------------------------------------------------------------------
    | Mentor Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('trainer')) {

        $query->where(
            'batch_assign',
            $request->trainer
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Technology Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('technology')) {

        $technology = $request->technology;

        $query->where(function ($q) use ($technology) {

            $q->where(
                'class_assign',
                $technology
            )
            ->orWhere(
                'class_assign',
                'LIKE',
                $technology . ',%'
            )
            ->orWhere(
                'class_assign',
                'LIKE',
                '%,' . $technology
            )
            ->orWhere(
                'class_assign',
                'LIKE',
                '%,' . $technology . ',%'
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Start Time Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('start_time')) {

        try {

            $startTime = \Carbon\Carbon::createFromFormat(
                'h:i A',
                $request->start_time
            )->format('H:i:s');

            $query->where(
                'start_time',
                $startTime
            );

        } catch (\Exception $e) {

            // Ignore invalid time

        }

    }


    /*
    |--------------------------------------------------------------------------
    | End Time Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('end_time')) {

        try {

            $endTime = \Carbon\Carbon::createFromFormat(
                'h:i A',
                $request->end_time
            )->format('H:i:s');

            $query->where(
                'end_time',
                $endTime
            );

        } catch (\Exception $e) {

            // Ignore invalid time

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    |
    | Status filter is only relevant to Normal tab.
    |
    */

    if (
        $tab === 'normal' &&
        $request->filled('status')
    ) {

        $query->where(
            'status',
            $request->status
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Batch Mode Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('mode')) {

        $query->where(
            'batch_mode',
            $request->mode
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Student Sorting
    |--------------------------------------------------------------------------
    */

    if ($request->student_sort === 'low_to_high') {

        $query->orderBy(
            'students_count',
            'asc'
        );

    } elseif ($request->student_sort === 'high_to_low') {

        $query->orderBy(
            'students_count',
            'desc'
        );

    } else {

        $query->latest('id');

    }


    /*
    |--------------------------------------------------------------------------
    | Get Batches
    |--------------------------------------------------------------------------
    */

    $batches = $query->get();


    /*
    |--------------------------------------------------------------------------
    | Filters Data
    |--------------------------------------------------------------------------
    */

    $trainers = Trainer::orderBy('name')->get();

    $courses = Course::orderBy('course_name')->get();


    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    return view(
        'batches.index',
        compact(
            'batches',
            'trainers',
            'courses',
            'tab'
        )
    );
}
    public function index12(Request $request)
    {
        $sessionId = session('session_id');

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = Batch::with([
            'sessionData',
            'trainerData',
            'students',
        ])
        ->withCount('students')
        ->where('session_name', $sessionId);


        /*
        |--------------------------------------------------------------------------
        | Tabs
        |--------------------------------------------------------------------------
        |
        | normal  = all non-deleted batches except closed
        | closed  = non-deleted closed batches
        | deleted = soft deleted batches
        |
        */

        $tab = $request->get('tab', 'normal');

        if ($tab === 'closed') {

            $query->where('status', 'closed');

        } elseif ($tab === 'deleted') {

            // Deleted records only
            $query->onlyTrashed();

        } else {

            // Normal = everything except closed
            $query->where('status', '!=', 'closed');

        }


        /*
        |--------------------------------------------------------------------------
        | Mentor Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('trainer')) {

            $query->where('batch_assign', $request->trainer);

        }


        /*
        |--------------------------------------------------------------------------
        | Technology Filter
        |--------------------------------------------------------------------------
        |
        | class_assign contains comma-separated course IDs.
        |
        */

        if ($request->filled('technology')) {

            $technology = $request->technology;

            $query->where(function ($q) use ($technology) {

                $q->where('class_assign', $technology)
                    ->orWhere('class_assign', 'LIKE', $technology . ',%')
                    ->orWhere('class_assign', 'LIKE', '%,' . $technology)
                    ->orWhere('class_assign', 'LIKE', '%,' . $technology . ',%');

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Start Time Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_time')) {

            try {

                $startTime = \Carbon\Carbon::createFromFormat(
                    'h:i A',
                    $request->start_time
                )->format('H:i:s');

                $query->where('start_time', $startTime);

            } catch (\Exception $e) {
                // Ignore invalid time
            }

        }


        /*
        |--------------------------------------------------------------------------
        | End Time Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('end_time')) {

            try {

                $endTime = \Carbon\Carbon::createFromFormat(
                    'h:i A',
                    $request->end_time
                )->format('H:i:s');

                $query->where('end_time', $endTime);

            } catch (\Exception $e) {
                // Ignore invalid time
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        |
        | On normal tab, status filter works normally.
        | Closed tab is always closed.
        | Deleted tab uses deleted_at and ignores normal status filtering.
        |
        */

        if (
            $request->filled('status') &&
            $tab === 'normal'
        ) {

            $query->where('status', $request->status);

        }


        /*
        |--------------------------------------------------------------------------
        | Batch Mode Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('mode')) {

            $query->where('batch_mode', $request->mode);

        }


        /*
        |--------------------------------------------------------------------------
        | Student Sorting
        |--------------------------------------------------------------------------
        */

        if ($request->student_sort === 'low_to_high') {

            $query->orderBy('students_count', 'asc');

        } elseif ($request->student_sort === 'high_to_low') {

            $query->orderBy('students_count', 'desc');

        } else {

            $query->latest('id');

        }


        /*
        |--------------------------------------------------------------------------
        | Get Batches
        |--------------------------------------------------------------------------
        */

        $batches = $query->get();


        /*
        |--------------------------------------------------------------------------
        | Filters Data
        |--------------------------------------------------------------------------
        */

        $trainers = Trainer::orderBy('name')->get();

        $courses = Course::orderBy('course_name')->get();


        return view('batches.index', compact(
            'batches',
            'trainers',
            'courses',
            'tab'
        ));
    }


    public function index_25aug(Request $request)
    {
        $currentSession = session('admin_session_id');

        // $query = Batch::with(['trainerData', 'courseData', 'durationData'])
        //     ->withCount('students')
        //     ->where('session_name', $currentSession)
        //     ->latest();

        $query = Batch::with(['trainerData', 'courseData', 'durationData'])
            ->withCount([
                'students as students_count' => function ($q) use ($currentSession) {
                    $q->where('session', $currentSession);
                }
            ])
            ->where('session_name', $currentSession);
            // ->latest();

        // Trainer filter
        if ($request->trainer) {
            $query->where('batch_assign', $request->trainer);
        }

        // Technology filter
        if ($request->technology) {
            $query->where('class_assign', $request->technology);
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Start time filter
        if ($request->filled('start_time')) {
            $startTime = Carbon::createFromFormat('g:i A', $request->start_time)
                           ->format('H:i:s');

            $query->whereTime('start_time', '>=', $startTime);

            // $query->whereTime('start_time', '>=', $request->start_time);
        }

        // End time filter
        if ($request->filled('end_time')) {
             $endTime = Carbon::createFromFormat('g:i A', $request->end_time)
                         ->format('H:i:s');

            $query->whereTime('end_time', '<=', $endTime);
            // $query->whereTime('end_time', '<=', $request->end_time);
        }

        // Mode filter
        if ($request->mode) {
            $query->where('batch_mode', $request->mode);
        }

        // Students Count Sorting
        if ($request->student_sort == 'low_to_high') {
            $query->orderBy('students_count', 'asc');
        } elseif ($request->student_sort == 'high_to_low') {
            $query->orderBy('students_count', 'desc');
        } else {
            // Default order when no student_sort selected
            $query->latest('updated_at');
        }

        $batches  = $query->get();
        // dd($batches);
        // $trainers = Trainer::with('user')->get();
        $trainers = Trainer::where('status', 'active')->orderBy('name', 'asc')->get();
        // dd($trainers->user);
        
        // $courses  = Course::all();
        $courses = Course::orderBy('course_name', 'asc')->get();

        return view('batches.index', compact('batches','trainers','courses'));
    }


    public function create()
    {
        // $sessions = StudentSession::all(); // get all session_start values
        $sessionsData = StudentSession::where('status', 'active')->get();
        $trainers = Trainer::where('status', 'active')->orderBy('name', 'asc')->get();

         
        // $trainers = Trainer::with('activeUser')->whereHas('activeUser')->get();
        
        $courses = Course::orderBy('course_name', 'asc')->get();
        $course_duration = Duration::all();
        return view('batches.create', compact('sessionsData', 'trainers', 'courses','course_duration'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'start_time' => strtoupper(trim($request->start_time)),
            'end_time'   => strtoupper(trim($request->end_time)),
        ]);
        $request->validate([
            'batch_name'   => 'required|string|max:255',
            'session_name' => 'required|string|max:255', // now store session_start directly
            // 'start_time'   => 'required',
            // 'end_time'     => 'required|after:start_time',
            'start_time' => 'required|date_format:g:i A',
            'end_time'   => 'required|date_format:g:i A',

            'batch_assign' => 'required|max:255',
            // 'class_assign' => 'required|max:255',
            'class_assign'   => 'required|array',
            'class_assign.*' => 'exists:student_courses,id',

            'duration'     => 'required|max:255',
            'batch_mode'     => 'required|max:255',
            'status'       => 'required|in:active,inactive,completed,cancelled',
        ]);

         // Convert to Carbon
        $startTime = Carbon::createFromFormat('g:i A', $request->start_time);
        $endTime   = Carbon::createFromFormat('g:i A', $request->end_time);

        // Ensure start_time < end_time
        if ($endTime->lessThanOrEqualTo($startTime)) {
            return back()
                ->withErrors(['end_time' => 'End time must be greater than start time'])
                ->withInput();
        }


        $batch = Batch::create([
            'batch_name'   => $request->batch_name,
            'session_name' => $request->session_name, // directly from form
            // 'start_time'   => $request->start_time,
            // 'end_time'     => $request->end_time,
             'start_time'   => $startTime->format('H:i:s'),
            'end_time'     => $endTime->format('H:i:s'),
            'department'   => $request->department,
            'batch_assign' => $request->batch_assign,
            // 'class_assign' => $request->class_assign,
            'class_assign' => implode(',', $request->class_assign), // 🔥 MULTI TECH SAVED
            'batch_mode' => $request->batch_mode,
            'duration'     => $request->duration,
            'status'     => $request->status,
        ]);


         // ✅ NOTIFY TRAINER (NEW WAY)
            $trainer = Trainer::find($request->batch_assign);

            if ($trainer) {
                $trainer->notify(
                     new \App\Notifications\TrainerBatchAssignedNotification($batch)
                );
            }

        // $trainer = Trainer::with('activeUser')->find($request->batch_assign);

        // if ($trainer && $trainer->activeUser) {
        //     $trainerUser = $trainer->activeUser;

        //     $trainerUser->notify(
        //         new \App\Notifications\TrainerBatchAssignedNotification($batch)
        //     );
        // }
        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        // $sessions = StudentSession::all();
        $sessionsData = StudentSession::where('status', 'active')->get();
        //$technologies = Trainer::select('technology')->distinct()->get();
        // $trainers = Trainer::get();
        // $trainers = Trainer::with('activeUser')->whereHas('activeUser')->get();
        $trainers = Trainer::where('status', 'active')->orderBy('name', 'asc')->get();
        // dd($trainers);
        $courses = Course::get();
        $course_duration = Duration::all();
        // dd($courses);
        return view('batches.edit', compact('batch', 'sessionsData', 'trainers', 'courses','course_duration'));
    }

    public function show(Batch $batch)
    {
        // dd($batch);
        // Load related data
        $batch->load([
            'trainerData',         // trainer name
            'courseData',               // technology
            'sessionData',              // session info
            'students',                 // batch students
            'durationData'
        ]);

        return view('batches.show', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
{
    $request->merge([
        'start_time' => strtoupper(trim($request->start_time)),
        'end_time'   => strtoupper(trim($request->end_time)),
    ]);

    $request->validate([
        'batch_name'   => 'required|string|max:255',
        'session_name' => 'required|string',
        // 'start_time'   => 'required',
        // 'end_time'     => 'required|after:start_time',

        'start_time' => 'required|date_format:g:i A',
        'end_time'   => 'required|date_format:g:i A',
        'batch_assign' => 'required|max:255',

        // 🔵 MULTIPLE TECHNOLOGY VALIDATION
        'class_assign'   => 'required|array',
        'class_assign.*' => 'exists:student_courses,id',

        'batch_mode'  => 'required|max:255',
        'duration'    => 'required|string|max:255',
        'status'      => 'required|in:active,inactive,completed,cancelled',
    ],
    [
        // Optional clean messages
        'class_assign.required'   => 'Please select at least one technology.',
        'class_assign.*.exists'   => 'One of the selected technologies is invalid.',
    ]);

    $startTime = Carbon::createFromFormat('g:i A', $request->start_time);
    $endTime   = Carbon::createFromFormat('g:i A', $request->end_time);

    if ($endTime->lessThanOrEqualTo($startTime)) {
        return back()
            ->withErrors(['end_time' => 'End time must be greater than start time'])
            ->withInput();
    }

    // OLD trainer_id from trainer table
    $oldTrainerId = $batch->batch_assign;

    // 🔵 UPDATE BATCH (SAVE MULTIPLE TECHNOLOGIES)
    $batch->update([
        'batch_name'   => $request->batch_name,
        'session_name' => $request->session_name,
        // 'start_time'   => $request->start_time,
        // 'end_time'     => $request->end_time,
         'start_time' => $startTime->format('H:i:s'),
        'end_time'   => $endTime->format('H:i:s'),
        'department'   => $request->department,
        'batch_assign' => $request->batch_assign,

        // 🔥 MULTI TECH STORED AS "1,3,5"
        'class_assign' => implode(',', $request->class_assign),

        'batch_mode'   => $request->batch_mode,
        'duration'     => $request->duration,
        'status'       => $request->status,
    ]);

    // ======================================
    // SEND NOTIFICATION ONLY IF TRAINER CHANGED
    // ======================================
    if ($oldTrainerId != $request->batch_assign) {

        // $trainer = Trainer::with('activeUser')->find($request->batch_assign);

        // if ($trainer && $trainer->activeUser) {
        //     $trainerUser = $trainer->activeUser;

        //     $trainerUser->notify(
        //         new \App\Notifications\TrainerBatchAssignedNotification($batch)
        //     );
        // }

        // ✅ NOTIFY TRAINER (NEW WAY)
            $trainer = Trainer::find($request->batch_assign);

            if ($trainer) {
                $trainer->notify(
                    new \App\Notifications\TrainerBatchAssignedNotification($batch)
                );
            }
    }

    return redirect()->route('batches.index')
        ->with('success', 'Batch updated successfully.');
}

     

    public function destroy($id)
    {
        $batch = Batch::with('sessionData')->findOrFail($id);

        // Rule 1: Session is active
        // if ($batch->sessionData && $batch->sessionData->status === 'active') {
        //     return back()->with('error', 'Cannot delete batch because the session is active.');
        // }

        // Rule 2: Session end date is pending (future)
        // if ($batch->sessionData && $batch->sessionData->end_date > now()->toDateString()) {
        //     return back()->with('error', 'Cannot delete batch because the session end date has not passed yet.');
        // }

        // OPTIONAL Rule 3: Batch has students
        if ($batch->students()->exists()) {
            return back()->with('error', 'Cannot delete batch because students are assigned.');
        }

        // Soft delete
        $batch->delete();

        return back()->with('success', 'Batch deleted successfully.');
    }


    public function getStudents($id)
    {
        $batch = Batch::with('students.collegeData')->findOrFail($id);

        return response()->json($batch->students);
    }

    public function MyBatches()
    {
        // ✅ Get logged-in trainer
        $trainer = Auth::guard('trainer')->user();

        if (!$trainer) {
            abort(403, 'Unauthorized');
        }

        // ✅ Fetch batches assigned to this trainer
        $batches = Batch::with(['students.collegeData', 'courseData'])
            ->where('batch_assign', $trainer->id)
            ->get();

        return view('trainers.trainer_index', compact('batches'));
    }

    public function MyBatches27jan()
    {
        $user = auth()->user();

        // Allow only trainers (role = 2)
        if ($user->role != 2) {
            abort(403, 'Unauthorized');
        }

        // Get trainer ID using user_id
        $trainer = Trainer::where('user_id', $user->id)->firstOrFail();

        // Fetch batches assigned to this trainer
        $batches = Batch::with('students.collegeData','courseData')
            ->where('batch_assign', $trainer->id) // trainer_id
            ->get();

        // dd($batches);

        return view('trainers.trainer_index', compact('batches'));
    }

     /**
     * Restore soft deleted batch.
     */
    public function restore($id)
    {
        $batch = Batch::onlyTrashed()->findOrFail($id);


        $batch->restore();


        return back()->with(
            'success',
            'Batch restored successfully.'
        );
    }


    /**
     * Get students assigned to batch.
     */
    public function students($id)
    {
        $batch = Batch::with([
            'students.collegeData'
        ])->findOrFail($id);


        return response()->json(
            $batch->students
        );
    }


    /**
     * Close batch.
     */
    public function close($id)
    {
        $batch = Batch::findOrFail($id);


        if ($batch->status === 'closed') {

            return back()->with(
                'error',
                'Batch is already closed.'
            );

        }


        $batch->update([
            'status' => 'closed',
        ]);


        return back()->with(
            'success',
            'Batch closed successfully.'
        );
    }


    /**
     * Reopen closed batch.
     */
    public function reopen($id)
    {
        $batch = Batch::findOrFail($id);


        if ($batch->status !== 'closed') {

            return back()->with(
                'error',
                'Only closed batches can be reopened.'
            );

        }


        $batch->update([
            'status' => 'active',
        ]);


        return back()->with(
            'success',
            'Batch reopened successfully.'
        );
    }




}
