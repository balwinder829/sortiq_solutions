<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCvTemplate extends Model
{
    protected $table = 'student_cv_templates';

    protected $fillable = [
        'name',
        'template_key',
        'sample_cv',
        'status'
    ];
}