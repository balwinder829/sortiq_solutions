<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProjectAssignment extends Model
{
    protected $table = 'student_project_assignments';

    protected $fillable = [
        'project_id',
        'student_id',
        'assigned_by',
        'deadline',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(StudentProject::class,'project_id');
    }

    public function submission()
    {
        return $this->hasOne(StudentProjectSubmission::class,'assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class,'student_id');
    }
}