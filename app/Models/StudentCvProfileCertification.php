<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfileCertification extends Model
{
    protected $table = 'student_cv_profile_certifications';

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'title',
        'issuer',
        'year'
    ];
}