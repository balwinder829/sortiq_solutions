<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Option;
use App\Models\StudentTest;
use App\Models\StudentAnswer;

class KeyTestController extends Controller
{
    public function showForm() {
    return view('student.enter_key');
}

public function accessTest(Request $request)
{
    $request->validate([
        'student_name' => 'required|string',
        'student_email' => 'required|email',
        'college_name' => 'required|string',
        'test_id' => 'required|exists:tests,id'
    ]);

    $test = Test::findOrFail($request->test_id);

    $studentTest = StudentTest::create([
        'test_id' => $test->id,
        'student_name' => $request->student_name,
        'student_email' => $request->student_email,
        'college_name' => $request->college_name,
        'score' => 0,
        'session_key' => session()->getId()
    ]);

    session([
        'current_test_id' => $test->id,
        'current_student_test_id' => $studentTest->id
    ]);

    return redirect()->route('student.test.show', $test->id);
}

public function showTest($test_id) {
    if(session('current_test_id')!=$test_id) abort(403,'Unauthorized Access');

    $test = Test::with('questions.options')->findOrFail($test_id);
    return view('student.test', compact('test'));
}

public function submitTest(Request $request,$test_id) {
    $studentTestId = session('current_student_test_id');
    $studentTest = StudentTest::findOrFail($studentTestId);

    $correctCount = 0;
    foreach($request->answers as $question_id=>$option_id){
        $option = Option::find($option_id);
        StudentAnswer::create([
            'student_test_id'=>$studentTest->id,
            'question_id'=>$question_id,
            'option_id'=>$option_id
        ]);
        if($option->is_correct) $correctCount++;
    }

    $studentTest->score = $correctCount;
    $studentTest->save();

    return view('student.result', compact('studentTest'));
}

}
