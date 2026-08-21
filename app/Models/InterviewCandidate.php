<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewCandidate extends Model
{
    use SoftDeletes;

    protected $table = 'interview_candidates';

    protected $fillable = [
        'candidate_name',
        'mobile',
        'email',
        'current_location',
        'current_company',
        'position_applied',
        'qualification',
        'experience',
        'technology_known',
        'preferred_date',
        'preferred_time',
        'resume',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];
}