<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Student extends Model
{
    use HasFactory, SoftDeletes;

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
        'pending_fees',
        'next_due_date',
        // 'department',
        'join_date',
        'status',
        'start_date',
        'end_date',
        'batch_assign',
        'reg_due_amount',
        'reference',
        'due_date',
        'part_time_offer',
        'placement_offer',
        'pg_offer',
        'send_to_close',
        'enquiry_id',
        'is_placed',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(StudentSession::class, 'session', 'session_name');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_name', 'college_name');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'technology', 'course_name');
    }

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

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id');
    }

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

}
