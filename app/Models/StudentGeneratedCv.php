<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGeneratedCv extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_generated_cvs';

    protected $fillable = [
        'student_id',
        'session_id',
        'name',
        'title',
        'professional_title',
        'contact',
        'summary',
        'education',
        'experience',
        'skills',
        'projects',
        'additional_info',
        'photo',
        'template',
        'created_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}