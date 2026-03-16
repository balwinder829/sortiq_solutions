<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfileEducation extends Model
{
    protected $table = 'student_cv_profile_education';

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'degree',
        'institution',
        'start_year',
        'end_year',
        'grade'
    ];
}