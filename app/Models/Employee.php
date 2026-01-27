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
        'alternative_phone',
        'joining_date',
        'dob',
        'blood_group',
        'address',
        'department',
        'employment_type',
        'shift',
        'status',
        'photo',
        'probation_period',
        'emp_pswd',
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

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class)
            ->where('status', 'active');
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function letters()
    {
        return $this->hasMany(Letter::class);
    }


}
