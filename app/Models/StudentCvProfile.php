<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvProfile extends Model
{
    protected $table = 'student_cv_profiles';

    protected $fillable = [
        'student_id',
        'session_id',
        'template_key',
        'full_name',
        'title',
        'phone',
        'email',
        'location',
        'linkedin',
        'github',
        'portfolio',
        'summary'
    ];

    public function skills()
    {
        return $this->hasMany(StudentCvProfileSkill::class,'cv_profile_id');
    }

    public function education()
    {
        return $this->hasMany(StudentCvProfileEducation::class,'cv_profile_id');
    }

    public function projects()
    {
        return $this->hasMany(StudentCvProfileProject::class,'cv_profile_id');
    }

    public function experience()
    {
        return $this->hasMany(StudentCvProfileExperience::class,'cv_profile_id');
    }

    public function certifications()
    {
        return $this->hasMany(StudentCvProfileCertification::class,'cv_profile_id');
    }
}