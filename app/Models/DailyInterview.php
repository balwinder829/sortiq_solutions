<?php

// app/Models/DailyInterview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyInterview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_interviews';

    protected $fillable = [
        'candidate_name',
        'mobile_no',
        'technology',
        'notice_period',
        'exp_ctc',
        'current_ctc',
        'availability_datetime',
        'joining_date',
        'interview_status',
        'interviewer_name',
        'interview_type',
    ];

    protected $casts = [
        'availability_datetime' => 'datetime',
        'joining_date' => 'date',
        'interview_status' => 'string',
        'interview_type' => 'string',
    ];

    // Assuming you have a user tracking system, though not in the migration:
    /* public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}