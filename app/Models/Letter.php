<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'letter_type',
        'emp_name',
        'emp_code',
        'position',
        'joining_date',
        'relieving_date',
        'experience_time',
        'is_sent',
        'send_count',
        'issue_date',
        'email',
        'salary',
        'bond_period',
        'probation_period',
    ];
}
