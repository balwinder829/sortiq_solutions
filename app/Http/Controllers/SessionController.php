<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\Course;

class SessionController extends Controller
{
    public function index()
    {
        // $sessions = StudentSession::latest()->paginate(10);
        $sessions = StudentSession::withCount('batches')->latest()->get();
        return view('sessions.index', compact('sessions'));
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
        'session_start' => 'required|date',
        'session_end'   => 'required|date|after_or_equal:session_start',
        // 'session_month' => 'required|string|max:255',
        // 'session_year'  => 'required|string|max:4',
        'status'        => 'required|in:active,inactive',
        // 'department'    => 'nullable|string|max:255',
    ]);

    StudentSession::create([
        'session_name'   => $validated['session_name'],
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
        'session_start' => 'required|date',
        'session_end'   => 'required|date|after_or_equal:session_start',
        // 'session_month' => 'required|string|max:255',
        // 'session_year'  => 'required|string|max:4',
        'status'        => 'required|in:active,inactive',
        // 'department'    => 'nullable|string|max:255',
    ]);

    $session->update([
        'session_name'   => $validated['session_name'],
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
        $session->delete();

        return redirect()->route('sessions.index')
                         ->with('success', 'Session deleted successfully.');
    }

    public function getBatches($id)
    {
        $session = StudentSession::with([
            'batches.courseData',
            'batches.trainerData'
        ])->findOrFail($id);
        // dd($session->batches);
        return response()->json($session->batches);
    }


}
