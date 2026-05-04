<?php

namespace App\Http\Controllers;

use App\Models\StudentProject;
use App\Models\StudentProjectAssignment;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentProjectAssignmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:student_project_assignments.view')->only('index');
        $this->middleware('permission:student_project_assignments.create')->only(['create','store']);
        $this->middleware('permission:student_project_assignments.edit')->only(['edit','update']);
        $this->middleware('permission:student_project_assignments.delete')->only('destroy');
    }
    public function index()
    {
        $assignments = StudentProjectAssignment::with('project')
            ->latest()
            ->get();

        return view('student-project-assignments.index', compact('assignments'));
    }


    public function create()
    {
        $projects = StudentProject::where('status',1)->get();
        $students = Student::orderBy('student_name')->get();

        return view('student-project-assignments.create', compact('projects','students'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'project_id' => 'required',
            'students' => 'required|array'
        ]);

        foreach ($request->students as $student) {

            StudentProjectAssignment::create([
                'project_id' => $request->project_id,
                'student_id' => $student,
                'assigned_by' => auth()->id(),
                'deadline' => $request->deadline,
                'status' => 'assigned'
            ]);

        }

        return redirect()
            ->route('student-project-assignments.index')
            ->with('success','Project assigned successfully');
    }

    public function show($id)
    {
        $assignment = StudentProjectAssignment::with(['project','student'])
            ->findOrFail($id);

        return view('student-project-assignments.show', compact('assignment'));
    }

    public function destroy($id)
    {
        $assignment = StudentProjectAssignment::findOrFail($id);

        $assignment->delete();

        return back()->with('success','Assignment deleted successfully');
    }

}