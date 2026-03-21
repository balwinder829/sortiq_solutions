<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAttendanceSubmission extends Model
{
    use HasFactory;

    protected $casts = [
        'is_finalized' => 'boolean',
    ];

    protected $fillable = [
        'external_attendance_test_id',
        'college_id',
        'student_name',
        'student_email',
        'student_mobile',
        'session_key',
        'exam_submitted_at',
        'gender',
        'ip_address',
        'class',
        'semester',
        'is_finalized',
        'course_id'
    ];

    /* ================= SCOPES ================= */

    public function scopeFinalized($q)
    {
        return $q->where('is_finalized', 1);
    }

    /* ================= RELATIONS ================= */

    public function test()
    {
        return $this->belongsTo(
            ExternalAttendanceTest::class,
            'external_attendance_test_id'
        );
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\StudentCourse::class);
    }
}