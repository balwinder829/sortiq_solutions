<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;


class Employee extends Authenticatable
{
    use SoftDeletes, Notifiable;

       protected $fillable = [
        'user_id',
        'emp_code',
        'emp_name',
        'father_name',
        'username',
        'email',
        'phone',
        'password',
        'role',
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
        'work_mode',
        'job_type',
        'working_hours_per_day',
        'employment_lifecycle_status',
        'is_married',
        'gender',
        'employment_mode',
    ];

     // Automatically hash password when setting it
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Employee.php
    // public function attendances()
    // {
    //     return $this->user->attendances();
    // }

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

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'actor_id')
            ->where('actor_type', 'employee');
    }

    public function acceptedLetters()
    {
        return $this->hasMany(AcceptedLetter::class);
    }

}
