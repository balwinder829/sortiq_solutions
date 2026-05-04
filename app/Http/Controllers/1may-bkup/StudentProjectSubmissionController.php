<?php

namespace App\Http\Controllers;

use App\Models\StudentProjectSubmission;
use App\Models\StudentProjectAssignment;
use Illuminate\Http\Request;

class StudentProjectSubmissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:student_project_submissions.view')->only('index');
        $this->middleware('permission:student_project_submissions.create')->only(['create','store']);
        $this->middleware('permission:student_project_submissions.edit')->only(['edit','update']);
        $this->middleware('permission:student_project_submissions.delete')->only('destroy');
    }
    
    public function index()
    {
        $submissions = StudentProjectSubmission::with('assignment.project')
            ->latest()
            ->get();

        return view('student-project-submissions.index', compact('submissions'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'assignment_id' => 'required',
            'github_link' => 'required|url'
        ]);

        StudentProjectSubmission::create([
            'assignment_id' => $request->assignment_id,
            'github_link' => $request->github_link,
            'live_link' => $request->live_link,
            'notes' => $request->notes,
            'submitted_at' => now()
        ]);

        StudentProjectAssignment::where('id',$request->assignment_id)
            ->update(['status'=>'submitted']);

        return back()->with('success','Submission uploaded successfully');
    }

}