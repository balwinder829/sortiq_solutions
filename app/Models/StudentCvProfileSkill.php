<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfileSkill extends Model
{
    protected $table = 'student_cv_profile_skills';

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'skill'
    ];
}