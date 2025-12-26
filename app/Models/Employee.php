<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'emp_code',
        'emp_name',
        'position',
        'joining_date',
        'department',
        'employment_type',
        'shift',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Employee.php
    public function attendances()
    {
        return $this->user->attendances();
    }

}
