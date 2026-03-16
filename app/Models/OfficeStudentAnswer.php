<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeStudentAnswer extends Model
{

protected $fillable = [

'office_student_test_id',
'office_question_id',
'answer_text',
'marks'

];



/* ---------------- RELATIONS ---------------- */


/* Student Attempt */

public function studentTest()
{
return $this->belongsTo(
OfficeStudentTest::class,
'office_student_test_id'
);
}


/* Question */

public function question()
{
return $this->belongsTo(
OfficeQuestion::class,
'office_question_id'
);
}

}