<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentOfficeOnlineAnswer extends Model
{
    protected $table = 'student_office_online_answers';

    protected $fillable = [
        'student_office_online_test_id',
        'office_online_question_id',
        'selected_option_id',
        'answer_text'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function studentTest()
    {
        return $this->belongsTo(StudentOfficeOnlineTest::class, 'student_office_online_test_id');
    }

    public function question()
    {
        return $this->belongsTo(OfficeOnlineQuestion::class, 'office_online_question_id');
    }

    public function option()
    {
        return $this->belongsTo(OfficeOnlineOption::class, 'selected_option_id');
    }
}