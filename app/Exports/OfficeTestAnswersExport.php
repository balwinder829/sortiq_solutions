<?php

namespace App\Exports;

use App\Models\OfficeTest;
use App\Models\OfficeStudentTest;
use Maatwebsite\Excel\Concerns\FromArray;

class OfficeTestAnswersExport implements FromArray
{
    protected $test;

    public function __construct($test)
    {
        $this->test = $test;
    }

    public function array(): array
    {
        $questions = $this->test->questions;

        $rows = [];

        $header = ['Name','Email','Mobile','Gender'];

        foreach($questions as $q){
            $header[] = $q->question;
        }

        $rows[] = $header;

        $students = OfficeStudentTest::with('answers')
            ->where('office_test_id',$this->test->id)
            ->get();

        foreach($students as $student){

            $row = [
                ucwords($student->student_name),
                $student->student_email,
                $student->student_mobile,
                ucwords($student->gender)
            ];

            foreach($questions as $q){

                $answer = $student->answers
                    ->where('office_question_id',$q->id)
                    ->first();

                $row[] = $answer->answer_text ?? '';

            }

            $rows[] = $row;
        }

        return $rows;
    }
}