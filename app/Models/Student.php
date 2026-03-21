<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'students_detail';

    protected $fillable = [
        'student_name',
        'f_name',
        'sno',
        'gender',
        'contact',
        'email_id',
        'college_name',
        'duration',
        'technology',
        'session',
        'total_fees',
        'reg_fees',
        'paid_fees',
        'pending_fees',
        'next_due_date',
        // 'department',
        'join_date',
        'status',
        'start_date',
        'end_date',
        'batch_assign',
        'reference',
        'due_date',
        'part_time_offer',
        'placement_offer',
        'pg_offer',
        'send_to_close',
        'enquiry_id',
        'is_placed',
        'is_intern',
        'is_married',
        'is_online',
        'is_place',
        'place',
        'password',
        'plain_password',
        'remember_token',
        'last_login',
    ];

     // Automatically hash password when setting it
    public function setPasswordAttribute($password)
    {   
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);
        }
        // $this->attributes['password'] = Hash::make($password);
    }
    // Relationships
    public function session()
    {
        return $this->belongsTo(StudentSession::class, 'session', 'session_name');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_name', 'college_name');
    }

    // public function course()
    // {
    //     return $this->belongsTo(Course::class, 'technology', 'course_name');
    // }

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department', 'name');
    }

    public function sessionData()
    {
        return $this->belongsTo(StudentSession::class, 'session', 'id');
    }

    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college_name', 'id');
    }

    // public function courseData()
    // {
    //     return $this->belongsTo(Course::class, 'technology', 'id');
    // }

    public function batchData()
    {
        return $this->belongsTo(Batch::class, 'batch_assign', 'id');
    }

    public function durationData()
    {
        return $this->belongsTo(Duration::class, 'duration', 'duration');
    }

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class, 'enquiry_id');
    }

    public function getFatherNameWithTitleAttribute()
    {
        $name = trim($this->f_name);

        if (!preg_match('/^mr\.?/i', $name)) {
            $name = 'Mr. ' . $name;
        }

        return ucwords($name);
    }

    // Convert array → comma separated before save
    public function setTechnologyAttribute($value)
    {
        $this->attributes['technology'] = is_array($value)
            ? implode(',', $value)
            : $value;
    }

    // Convert comma separated → array when fetching
    public function getTechnologyAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    // public function getCoursesAttribute()
    // {
    //     return empty($this->technology)
    //         ? collect()
    //         : Course::whereIn('course_name', $this->technology)->get();
    // }

    public function getCoursesAttribute()
    {
        return empty($this->technology)
            ? collect()
            : Course::whereIn('id', $this->technology)->get();
    }

    // public function getCourseDataAttribute()
    // {
    //     if (empty($this->technology)) {
    //         return collect();
    //     }

    //     return Course::whereIn('id', $this->technology)->get();
    // }

    // DISPLAY accessor (string)
    public function getCourseNameAttribute()
    {
        return $this->courses
            ->pluck('course_name')
            ->implode(', ');
    }


}
