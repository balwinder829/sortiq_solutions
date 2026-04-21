<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPendingRegistration;
use App\Models\College;
use App\Models\Course;

class StudentRegistrationController extends Controller
{
    public function create()
    {
         return view('student_registration', [
            'colleges' => College::orderBy('college_name', 'asc')->get(),
            'courses' => Course::orderBy('course_name', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required',
            'contact' => 'required|max:10',
            'email' => 'required',
            'gender' => 'required',
            'father_name' => 'required',
            // 'college_name_input' => 'required',
            'college_id' => 'required|exists:colleges,id',
            // 'college_id' => 'required|exists:colleges,id',
            // 'course_id' => 'required',
            'start_date' => 'required|date',
        ]);

        StudentPendingRegistration::create($validated);

        return back()->with('success', 'Registration submitted successfully!');
    }
}