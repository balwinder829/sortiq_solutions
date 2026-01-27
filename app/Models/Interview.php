<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Interview extends Model
{
    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'candidate_contact',
        'candidate_experience',
        'interviewer_name',
        'interview_date',
        'final_result',
        'final_remarks',
    ];

    public function rounds()
    {
        return $this->hasMany(InterviewRound::class);
    }
}
