<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentCustomLetter extends Model
{
    use SoftDeletes;

    protected $table = 'student_custom_letters';

    protected $fillable = [
        'student_name',
        'letter_type',
        'father_name',
        'college',

        // NEW FIELDS
        'course_branch',
        'contact_no',
        'email_id',
        'training_domain',
        'batch_mode',
        'joining_date',
        'completion_date',
        'reporting_mentor',
        'internship_mode',

        // EXISTING
        'location',
        'session_id',
        'start_date',
        'end_date',
        'issue_date',
        'training_start_date',
        'training_duration',
        'probation_period',
        'working_hours',
        'bond_duration',
        'position',
        'letter_content'
    ];

    public function sessionData()
    {
        return $this->belongsTo(
            StudentSession::class,
            'session_id',
            'id'
        );
    }
}