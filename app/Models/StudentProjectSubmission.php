<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProjectSubmission extends Model
{
    protected $table = 'student_project_submissions';

    protected $fillable = [
        'assignment_id',
        'github_link',
        'live_link',
        'notes',
        'submitted_at',
        'attempt'
    ];

    public function assignment()
    {
        return $this->belongsTo(StudentProjectAssignment::class,'assignment_id');
    }

    public function review()
    {
        return $this->hasOne(StudentProjectReview::class,'submission_id');
    }
}