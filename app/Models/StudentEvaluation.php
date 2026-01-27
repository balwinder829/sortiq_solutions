<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    protected $fillable = [
        'student_id',
        'trainer_id',
        'attendance_percentage',
        'behavior',
        'technical',
        'live_project',
        'soft_skills',
        'github',
        'projects',
        'assignments',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
