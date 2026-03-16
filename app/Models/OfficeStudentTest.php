<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeStudentTest extends Model
{

protected $fillable = [

'office_test_id',

'student_name',
'student_email',
'student_mobile',
'gender',

'score',

'session_key',
'ip_address',

'exam_started_at',
'exam_submitted_at',

'exam_locked'

];


protected $casts = [
    'exam_started_at'   => 'datetime',
    'exam_submitted_at' => 'datetime',
];
/* ---------------- RELATIONS ---------------- */


/* Test */

public function test()
{
return $this->belongsTo(
OfficeTest::class,
'office_test_id'
);
}


/* Answers */

public function answers()
{
return $this->hasMany(
OfficeStudentAnswer::class,
'office_student_test_id'
);
}

}