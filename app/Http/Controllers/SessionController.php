<?php

namespace App\Http\Controllers;
// dd(__FILE__);
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\Course;
use App\Models\Student;


class SessionController extends Controller
{   

    public function __construct()
    {
        $this->middleware('permission:sessions.view')->only('index');
        $this->middleware('permission:sessions.create')->only(['create','store']);
        $this->middleware('permission:sessions.edit')->only(['edit','update']);
        $this->middleware('permission:sessions.delete')->only('destroy');
    }
 
    public function index()
{
    $activeSessionId = session('admin_session_id');

    $sessionsList = StudentSession::with(['batches.students'])
        ->withCount([
            'students',
            'students as online_students_count' => function ($query) {
                $query->where('is_online', 1);
            },
            'students as offline_students_count' => function ($query) {
                $query->where('is_online', 0);
            }
        ])
        ->latest()
        ->get();

    // dd($sessionsList);
    return view('sessions.index', compact('sessionsList','activeSessionId'));
}
 

    

   




     
    public function create()
    {
        $courses = Course::all();
        return view('sessions.create', compact('courses'));
    }

public function store(Request $request)
{
    // $validated = $request->validate([
    //     'session_name'  => 'required|string|max:255',
    //     'session_start' => 'required|date',
    //     'session_end'   => 'required|date|after_or_equal:session_start',
    //     'status'        => 'required|in:active,inactive',
    //     'department'    => 'nullable|string|max:255',
    // ]);

    // StudentSession::create([
    //     'session_name' => $validated['session_name'],
    //     'start_date'   => $validated['session_start'],
    //     'end_date'     => $validated['session_end'],
    //     'status'       => $validated['status'],
    //     'department'   => $validated['department'] ?? null,
    // ]);

    $validated = $request->validate([
        'session_name'  => 'required|string|max:255',
        'session_display_name'  => 'required|string|max:255',
        'session_start' => 'required|date',
        'session_end'   => 'required|date|after_or_equal:session_start',
        // 'session_month' => 'required|string|max:255',
        // 'session_year'  => 'required|string|max:4',
        'status'        => 'required|in:active,inactive',
        // 'department'    => 'nullable|string|max:255',
    ]);

    StudentSession::create([
        'session_name'   => $validated['session_name'],
        'session_display_name'   => $validated['session_display_name'],
        'start_date'     => $validated['session_start'],
        'end_date'       => $validated['session_end'],
        // 'session_month'  => $validated['session_month'], // NEW
        // 'session_year'   => $validated['session_year'],  // NEW
        'status'         => $validated['status'],
        // 'department'     => $validated['department'] ?? null,
    ]);


    return redirect()->route('sessions.index')
                     ->with('success', 'Session created successfully.');
}
    public function edit(StudentSession $session)
    {
        $courses = Course::all();
        return view('sessions.edit', compact('session', 'courses'));
    }
public function update(Request $request, StudentSession $session)
{
    // $validated = $request->validate([
    //     'session_name'  => 'required|string|max:255',
    //     'session_start' => 'required|date',
    //     'session_end'   => 'required|date|after_or_equal:session_start',
    //     'status'        => 'required|in:active,inactive',
    //     'department'    => 'nullable|string|max:255',
    // ]);

    // $session->update([
    //     'session_name' => $validated['session_name'],
    //     'start_date'   => $validated['session_start'],
    //     'end_date'     => $validated['session_end'],
    //     'status'       => $validated['status'],
    //     'department'   => $validated['department'] ?? null,
    // ]);

    $validated = $request->validate([
        'session_name'  => 'required|string|max:255',
        'session_display_name'  => 'required|string|max:255',
        'session_start' => 'required|date',
        'session_end'   => 'required|date|after_or_equal:session_start',
        // 'session_month' => 'required|string|max:255',
        // 'session_year'  => 'required|string|max:4',
        'status'        => 'required|in:active,inactive',
        // 'department'    => 'nullable|string|max:255',
    ]);

    $session->update([
        'session_name'   => $validated['session_name'],
        'session_display_name'   => $validated['session_display_name'],
        'start_date'     => $validated['session_start'],
        'end_date'       => $validated['session_end'],
        // 'session_month'  => $validated['session_month'], // NEW
        // 'session_year'   => $validated['session_year'],  // NEW
        'status'         => $validated['status'],
        // 'department'     => $validated['department'] ?? null,
    ]);


    return redirect()->route('sessions.index')
                     ->with('success', 'Session updated successfully.');
}


    public function destroy(StudentSession $session)
    {
         // Check if any student is assigned to this session
        $hasStudents = Student::where('session', $session->id)->exists();
        // dd($hasStudents);
        if ($hasStudents) {
            return redirect()->route('sessions.index')
                ->with('error', 'You cannot delete this session because students are assigned to it.');
        }

        $session->delete();

        return redirect()->route('sessions.index')
                         ->with('success', 'Session deleted successfully.');
    }

    public function getBatches($id)
    {
        $session = StudentSession::with([
            'batches.courseData',
            'batches.trainerData:id,name'
        ])->findOrFail($id);
        // dd($session->batches);
        return response()->json($session->batches);
    }


}
