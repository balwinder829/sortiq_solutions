<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProjectReview extends Model
{
    protected $table = 'student_project_reviews';

    protected $fillable = [
        'submission_id',
        'rating',
        'feedback',
        'reviewed_by',
        'reviewed_at'
    ];

    public function submission()
    {
        return $this->belongsTo(StudentProjectSubmission::class,'submission_id');
    }
}