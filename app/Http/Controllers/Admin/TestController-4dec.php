<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Support\Str;

class TestController extends Controller
{
    // List all tests
    public function index(Request $request)
    {
        $query = Test::with('studentCourse');

        // Apply filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('course')) {
            $query->whereHas('studentCourse', function($q) use ($request) {
                $q->where('course_name', 'like', '%' . $request->course . '%');
            });
        }
        $tests = $query->paginate(10); // Paginate results

        return view('admin.tests.index', compact('tests'));
    }

    // Show form to create test
    public function create()
    {
        $courses = StudentCourse::all();
        return view('admin.tests.create', compact('courses'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'student_course_id' => 'required',
        ]);

        Test::create([
            'title' => $request->title,
            'student_course_id' => $request->student_course_id,
            'description' => $request->description,
            'access_key' => strtoupper(uniqid()),
            'slug' => Str::random(12), // ensures slug is always set
        ]);

        return redirect()->route('admin.tests.index')->with('success','Test Created Successfully');
    }


    // Show single test (optional)
    public function show(Test $test)
    {
        $test->load('questions.options'); // eager load
        return view('admin.tests.show', compact('test'));
    }
    // Show form to edit test
    public function edit(Test $test)
    {
        $courses = StudentCourse::all();
        return view('admin.tests.edit', compact('test','courses'));
    }

    // Update test
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'title'=>'required',
            'student_course_id'=>'required',
        ]);

        $test->update([
            'title'=>$request->title,
            'student_course_id'=>$request->student_course_id,
            'description'=>$request->description,
        ]);

        return redirect()->route('admin.tests.index')->with('success','Test Updated Successfully');
    }

    // Delete test
    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success','Test Deleted Successfully');
    }

    public function results(Request $request, $test_id)
    {
        // Load test with studentTests
        $test = Test::with('studentTests')->findOrFail($test_id);

        // Fetch all students for S.No
        $students = Student::all()->keyBy('email_id');

        // Start query for student tests
        $studentTestsQuery = $test->studentTests();

        // Apply filters from request
        if ($request->filled('sno')) {
            $studentEmails = $students->filter(function ($student) use ($request) {
                return str_contains($student->sno, $request->sno);
            })->keys();

            $studentTestsQuery->whereIn('student_email', $studentEmails);
        }

        if ($request->filled('name')) {
            $studentTestsQuery->where('student_name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $studentTestsQuery->where('student_email', 'like', '%' . $request->email . '%');
        }
         // Top scorer filter
        if ($request->filled('top_scorer') && $request->top_scorer == '1') {
            $maxScore = $test->studentTests()->max('score');
            $studentTestsQuery->where('score', $maxScore);
        }

        if ($request->filled('test')) {
            // Optional if you want to filter by test title
            $testTitle = $request->test;
            if ($test->title !== $testTitle) {
                $studentTestsQuery->where('id', 0); // no match
            }
        }

        $studentTests = $studentTestsQuery->get();

        // Attach S.No to each studentTest
        $studentTests->each(function ($st) use ($students) {
            $st->sno = $students[$st->student_email]->sno ?? '-';
        });

        return view('admin.tests.results', compact('test', 'studentTests'));
    }
    public function studentView($slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        // Display the test to the student
       return redirect()->route('student.enter.key', ['test_id' => $test->id]);

    }
}
