<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPendingRegistration extends Model
{
    protected $table = 'student_pending_registration';

    protected $fillable = [
        'student_name',
        'contact',
        'email',
        'gender',
        'father_name',
        'college_id',
        'college_name_input',
        'course_id',
        'is_sent_to_detail',
        'sent_to_detail_at',
        'start_date'
    ];

    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}