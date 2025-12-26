<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JoiningStudent;
use App\Mail\StudentJoinedMail;
use App\Models\College;
use App\Models\Course;
use App\Models\Duration;
use Mail;

class JoiningStudentController extends Controller
{
    // Frontend form
    public function create()
    {
        return view('joining-form', [
            'colleges'  => College::orderBy('college_name')->get(),
            'courses'   => Course::orderBy('course_name')->get(),
            'durations' => Duration::orderBy('name')->get(),
        ]);
    }

    // Save + Email
    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required',
            'father_name' => 'required',
            'college' => 'required',
            'duration' => 'required',
            'technology' => 'required',
            'date_of_joining' => 'required|date',
        ]);

        $student = JoiningStudent::create($request->all());
        $student->load(['collegeData', 'courseData', 'durationData']);

        $adminEmail = config('app.admin_email', 'admin@example.com');
        // Send email to admin
        Mail::to($adminEmail)
            ->send(new StudentJoinedMail($student));

        return redirect()->back()
            ->with('success', '🎉 Welcome to joining!');
    }

    // Admin list
    public function index()
    { 
         $students = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData'
        ])->latest()->get();
         // dd($students);
        return view('joining_students.index', compact('students'));
    }

    // Edit form
    public function edit($id)
    {
        $student = JoiningStudent::findOrFail($id);

        return view('joining_students.edit', [
            'student'   => $student,
            'colleges'  => College::orderBy('college_name')->get(),
            'courses'   => Course::orderBy('course_name')->get(),
            'durations' => Duration::orderBy('name')->get(),
        ]);
    }

    // Update student
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_name' => 'required',
            'father_name' => 'required',
            'college' => 'required',
            'duration' => 'required',
            'technology' => 'required',
            'date_of_joining' => 'required|date',
        ]);

        $student = JoiningStudent::findOrFail($id);
        $student->update($request->all());

        return redirect()
            ->route('joined_students.index')
            ->with('success', '✅ Student updated successfully');
    }

    // Soft delete
    public function destroy($id)
    {
        $student = JoiningStudent::findOrFail($id);
        $student->delete();

        return redirect()
            ->back()
            ->with('success', '🗑️ Student deleted successfully');
    }


    public function adminUrl()
    {
        return view('joining_students.link_index');
    }
}
