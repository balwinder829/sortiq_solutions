<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfileProject extends Model
{
    protected $table = 'student_cv_profile_projects';

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'title',
        'description',
        'technology',
        'github_link',
        'live_link'
    ];
}