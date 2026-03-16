<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfileExperience extends Model
{
    protected $table = 'student_cv_profile_experience';

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'company_name',
        'role',
        'location',
        'start_date',
        'end_date',
        'currently_working',
        'summary'
    ];
}