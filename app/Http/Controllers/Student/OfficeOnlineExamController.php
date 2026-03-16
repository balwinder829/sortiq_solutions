<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeTest;
use App\Models\OfficeStudentTest;
use App\Models\OfficeStudentAnswer;

class OfficeOnlineExamController extends Controller
{

/* ---------------- ENTRY ---------------- */

public function studentView($slug)
{
    $test = OfficeTest::where('slug',$slug)->firstOrFail();

    return redirect()->route(
        'student.office.enter',
        ['slug'=>$test->slug]
    );
}


/* ---------------- SHOW FORM ---------------- */

public function showForm($slug)
{
    $test = OfficeTest::where('slug',$slug)
        ->where('status','published')
        ->where('is_active',1)
        ->firstOrFail();

    return view(
        'student.office_exam.enter',
        compact('test')
    );
}


/* ---------------- ACCESS TEST ---------------- */

public function accessTest(Request $request)
{

    $request->validate([

        'student_name'   => 'required|string',
        'student_email'  => 'required|email',
        'student_mobile' => 'required|digits:10',
        'gender'         => 'nullable|string',

        'slug'           => 'required|exists:office_tests,slug',

    ]);


    $test = OfficeTest::where('slug',$request->slug)
        ->where('status','published')
        ->where('is_active',1)
        ->first();

    if (!$test) {
        // return redirect()->route('student.office.exam.unavailable');
        return redirect()->route(
            'student.office.exam.unavailable',
            $request->slug
        );
   }


    /* EXAM TIMING CHECK */

    if(!$test->exam_start_at || !$test->exam_end_at){
        // return redirect()->route('student.exam.closed',$test->slug);
        return redirect()->route(
            'student.office.exam.closed',
            $test->slug
        );
    }

    if(now()->greaterThanOrEqualTo($test->exam_end_at)){
        // return redirect()->route('student.exam.closed',$test->slug);
        return redirect()->route(
            'student.office.exam.closed',
            $test->slug
        );
    }


    /* CHECK EXISTING ATTEMPT */

    $studentTest = OfficeStudentTest::where(
        'office_test_id',
        $test->id
    )
    ->where(
        'student_mobile',
        $request->student_mobile
    )
    ->first();


    /* ALREADY SUBMITTED */

    if($studentTest && $studentTest->exam_locked){
        return redirect()->route(
             'student.office.already.submitted',
            $test->slug
        );
    }


    /* RESUME ATTEMPT */

    if($studentTest && !$studentTest->exam_locked){

        session([

            'current_office_test_id' => $test->id,
            'current_office_student_test_id' => $studentTest->id

        ]);

        return redirect()->route(
            'student.office.exam.show',
            $test->slug
        );
    }


    /* NEW ATTEMPT */

    $studentTest = OfficeStudentTest::create([

        'office_test_id' => $test->id,

        'student_name'   => $request->student_name,
        'student_email'  => $request->student_email,
        'student_mobile' => $request->student_mobile,
        'gender'         => $request->gender,

        'score'          => 0,

        'session_key'    => session()->getId(),
        'exam_started_at'=> null,

        'ip_address'     => $request->ip(),

    ]);


    session([

        'current_office_test_id' => $test->id,
        'current_office_student_test_id' => $studentTest->id

    ]);


    return redirect()->route(
        'student.office.exam.show',
        $test->slug
    );

}


/* ---------------- SHOW EXAM ---------------- */

public function showExam($slug)
{

    $test = OfficeTest::where('slug',$slug)
        ->where('status','published')
        ->where('is_active',1)
        ->with('questions')
        ->firstOrFail();


    if(session('current_office_test_id') != $test->id){
        abort(403,'Unauthorized');
    }


    $studentTest = OfficeStudentTest::findOrFail(
        session('current_office_student_test_id')
    );


    /* ALREADY SUBMITTED */

    if($studentTest->exam_locked){

        return redirect()->route(
            'student.office.result.show'
        );

    }


    /* WAITING ROOM */

    if(now()->lessThan($test->exam_start_at)){

        return view(
            'student.office_exam.waiting',
            compact('test')
        );

    }


    /* MARK EXAM START */

    if(!$studentTest->exam_started_at){

        $studentTest->update([
            'exam_started_at'=>now()
        ]);

    }


    /* AUTO SUBMIT */

    if(now()->greaterThanOrEqualTo($test->exam_end_at)){

        return $this->forceSubmit($studentTest,$test);

    }


    $remainingSeconds = max(

        0,

        (int) now()->diffInSeconds(
            $test->exam_end_at
        )

    );


    $answers = $studentTest->answers
        ->pluck('answer_text','office_question_id')
        ->toArray();


    return view(

        'student.office_exam.exam',

        compact(
            'test',
            'studentTest',
            'remainingSeconds',
            'answers'
        )

    );

}


/* ---------------- AUTO SAVE ---------------- */

public function autoSave(Request $request,$slug)
{

    $studentTest = OfficeStudentTest::find(
        session('current_office_student_test_id')
    );


    if(!$studentTest || $studentTest->exam_locked){

        return response()->json([
            'status'=>'locked'
        ]);

    }


    if($request->answers){

        foreach($request->answers as $questionId=>$answer){

            OfficeStudentAnswer::updateOrCreate(

                [

                    'office_student_test_id'=>$studentTest->id,
                    'office_question_id'=>$questionId

                ],

                [

                    'answer_text'=>$answer

                ]

            );

        }

    }


    return response()->json([
        'status'=>'saved'
    ]);

}


/* ---------------- SUBMIT EXAM ---------------- */

public function submitExam(Request $request,$slug)
{

    $studentTestId = session(
        'current_office_student_test_id'
    );


    if(!$studentTestId){
        abort(403,'Session expired');
    }


    $studentTest = OfficeStudentTest::findOrFail(
        $studentTestId
    );


    if($studentTest->exam_locked){

        return redirect()->route(
            'student.office.result.show'
        );

    }


    if($request->has('answers')){

        foreach($request->answers as $questionId=>$answer){

            OfficeStudentAnswer::updateOrCreate(

                [

                    'office_student_test_id'=>$studentTest->id,
                    'office_question_id'=>$questionId

                ],

                [

                    'answer_text'=>$answer

                ]

            );

        }

    }


    $studentTest->update([

        'exam_locked'=>true,

        'exam_submitted_at'=>now()

    ]);


    return redirect()->route(
        'student.office.result.show'
    );

}


/* ---------------- FORCE SUBMIT ---------------- */

private function forceSubmit(
    OfficeStudentTest $studentTest,
    OfficeTest $test
){

    if($studentTest->exam_locked){

        return redirect()->route(
            'student.office.result.show'
        );

    }


    $studentTest->update([

        'exam_locked'=>true,

        'exam_submitted_at'=>now()

    ]);


    return redirect()->route(
        'student.office.result.show'
    );

}


/* ---------------- RESULT ---------------- */

public function showResult()
{
   

    $studentTestId = session(
        'current_office_student_test_id'
    );
    
    // session()->forget([

    //     'current_office_test_id',
    //     'current_office_student_test_id'

    // ]);


    // if(!$studentTestId){

    //     return redirect()->route(
    //         'student.office.enter'
    //     );

    // }


    $studentTest = OfficeStudentTest::findOrFail(
        $studentTestId
    );


    return view(

        'student.office_exam.result',

        compact('studentTest')

    );

}

}