<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLeaveRequest extends Model
{
    protected $table = 'student_leave_requests';

    protected $fillable = [
        'student_id',
        'sno',
        'student_name',
        'contact',
        'email',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'status',
        'session_id',
        'ip_address',
    ];
}