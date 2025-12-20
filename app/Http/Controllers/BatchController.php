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

class BatchController extends Controller
{
    // public function index()
    // {
    //     $currentSession = session('admin_session_id');
    //     // dd( $currentSession);
    //     // $batches = Batch::latest()->paginate(10);
    //     $batches = Batch::with(['trainerData.user'])->withCount('students')->where('session_name', $currentSession)->latest()->get();

    //     return view('batches.index', compact('batches'));
    // }

    public function index(Request $request)
{
    $currentSession = session('admin_session_id');

    $query = Batch::with(['trainerData.user', 'courseData', 'durationData'])
        ->withCount('students')
        ->where('session_name', $currentSession)
        ->latest();

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

    // Mode filter
    if ($request->mode) {
        $query->where('batch_mode', $request->mode);
    }

    $batches  = $query->get();
    $trainers = Trainer::with('user')->get();
    // dd($trainers->user);
    
    $courses  = Course::all();

    return view('batches.index', compact('batches','trainers','courses'));
}


    public function create()
    {
        // $sessions = StudentSession::all(); // get all session_start values
        $sessionsData = StudentSession::where('status', 'active')->get();
        //$technologies = ::select('technology')->distinct()->get();
        // $trainers = Trainer::get();
        // $trainers = Trainer::with('user')->get();
        $trainers = Trainer::with('activeUser')->whereHas('activeUser')->get();
        // dd($trainers);
        $courses = Course::get();
        $course_duration = Duration::all();
        return view('batches.create', compact('sessionsData', 'trainers', 'courses','course_duration'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_name'   => 'required|string|max:255',
            'session_name' => 'required|string|max:255', // now store session_start directly
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            'batch_assign' => 'required|max:255',
            'class_assign' => 'required|max:255',
            'duration'     => 'required|max:255',
            'batch_mode'     => 'required|max:255',
            'status'       => 'required|in:active,inactive,completed,cancelled',
        ]);

        $batch = Batch::create([
            'batch_name'   => $request->batch_name,
            'session_name' => $request->session_name, // directly from form
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'department'   => $request->department,
            'batch_assign' => $request->batch_assign,
            'class_assign' => $request->class_assign,
            'batch_mode' => $request->batch_mode,
            'duration'     => $request->duration,
            'status'     => $request->status,
        ]);


        $trainer = Trainer::with('activeUser')->find($request->batch_assign);

        if ($trainer && $trainer->activeUser) {
            $trainerUser = $trainer->activeUser;

            $trainerUser->notify(
                new \App\Notifications\TrainerBatchAssignedNotification($batch)
            );
        }
        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        // $sessions = StudentSession::all();
        $sessionsData = StudentSession::where('status', 'active')->get();
        //$technologies = Trainer::select('technology')->distinct()->get();
        // $trainers = Trainer::get();
        $trainers = Trainer::with('activeUser')->whereHas('activeUser')->get();
        // dd($trainers);
        $courses = Course::get();
        $course_duration = Duration::all();
        // dd($courses);
        return view('batches.edit', compact('batch', 'sessionsData', 'trainers', 'courses','course_duration'));
    }

    public function show(Batch $batch)
    {
        // Load related data
        $batch->load([
            'trainerData.user',         // trainer name
            'courseData',               // technology
            'sessionData',              // session info
            'students',                 // batch students
            'durationData'
        ]);

        return view('batches.show', compact('batch'));
    }


    public function update(Request $request, Batch $batch)
    {
        $request->validate([
            'batch_name'   => 'required|string|max:255',
            'session_name' => 'required|string', // store session_start directly
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            // 'department'   => 'required|string|max:255',
            'batch_assign' => 'required|max:255',
            'class_assign' => 'required|max:255',
             'batch_mode'     => 'required|max:255',
            'duration'     => 'required|string|max:255',
            'status'       => 'required|in:active,inactive,completed,cancelled',
        ]);

        // OLD trainer_id from trainer table
        $oldTrainerId = $batch->batch_assign;

        $batch->update([
            'batch_name'   => $request->batch_name,
            'session_name' => $request->session_name, // directly from form
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'department'   => $request->department,
            'batch_assign' => $request->batch_assign,
            'class_assign' => $request->class_assign,
            'batch_mode' => $request->batch_mode,
            'duration'     => $request->duration,
            'status'     => $request->status,
        ]);

        // ======================================
    // SEND NOTIFICATION *ONLY* IF TRAINER CHANGED
    // ======================================
    if ($oldTrainerId != $request->batch_assign) {

        // Find trainer from trainer table
        $trainer = Trainer::with('activeUser')->find($request->batch_assign);

        // Trainer must exist AND have a related user
        if ($trainer && $trainer->activeUser) {
            $trainerUser = $trainer->activeUser; // User Model

            // Notify the trainer's USER ACCOUNT
            $trainerUser->notify(
                new \App\Notifications\TrainerBatchAssignedNotification($batch)
            );
        }
    }
        return redirect()->route('batches.index')->with('success', 'Batch updated successfully.');
    }

    public function destroy($id)
    {
        $batch = Batch::with('sessionData')->findOrFail($id);

        // Rule 1: Session is active
        if ($batch->sessionData && $batch->sessionData->status === 'active') {
            return back()->with('error', 'Cannot delete batch because the session is active.');
        }

        // Rule 2: Session end date is pending (future)
        if ($batch->sessionData && $batch->sessionData->end_date > now()->toDateString()) {
            return back()->with('error', 'Cannot delete batch because the session end date has not passed yet.');
        }

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



}
