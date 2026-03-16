<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeTest;
use App\Models\OfficeStudentResult;
use App\Models\Student;

class OfficeResultController extends Controller
{

    public function index(OfficeTest $office_test)
    {

        $results = OfficeStudentResult::with('student')
            ->where('office_test_id',$office_test->id)
            ->orderByDesc('score')
            ->get();

        $students = Student::all();

        return view(
            'admin.office-tests.results',
            compact('office_test','results','students')
        );

    }

    public function store(Request $request, OfficeTest $office_test)
    {

        OfficeStudentResult::updateOrCreate(

            [
                'office_test_id' => $office_test->id,
                'student_id' => $request->student_id
            ],

            [
                'score' => $request->score,
                'created_by' => auth()->id()
            ]

        );

        return back()->with('success','Score saved');

    }

}