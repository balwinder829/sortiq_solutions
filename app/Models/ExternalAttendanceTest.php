<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalAttendanceTest extends Model
{
    use SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'college_id',
        'student_course_id',
        'title',
        'slug',
        'access_key',
        'description',
        'is_active',
        'test_date',
        'status',
    ];

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /* ================= RELATIONS ================= */

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'student_course_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function studentCourse()
    {
        return $this->belongsTo(StudentCourse::class);
    }

    // ✅ NEW: correct relationship
    public function links()
    {
        return $this->hasMany(
            ExternalAttendanceLink::class,
            'external_attendance_test_id'
        );
    }

    // ✅ NEW: correct relationship
    public function submissions()
    {
        return $this->hasMany(
            ExternalAttendanceSubmission::class,
            'external_attendance_test_id'
        );
    }

    /* ================= ACCESSORS ================= */

    public function getCollegeFullNameAttribute()
    {
        return $this->college?->full_name ?? '';
    }

    public function submissionsCount()
    {
        return $this->submissions()->count();
    }
}