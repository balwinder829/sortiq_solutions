<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use SoftDeletes;

    protected $casts = [
        'is_active'      => 'boolean',
        'exam_start_at'  => 'datetime',
        'exam_end_at'    => 'datetime',
    ];


    protected $fillable = [
        'college_id',
        'student_course_id',
        'semester_id',
        'branch_id',
        'test_category_id',
        'title',
        'slug',
        'access_key',
        'description',
        'is_active',
        'test_date',
        'status',
        'test_mode',
        'exam_start_at',
        'exam_end_at',
        'timer_type',
    ];

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

    public function scopeOnline($q)
    {
        return $q->where('test_mode', 'online');
    }

    public function scopeOffline($q)
    {
        return $q->where('test_mode', 'offline');
    }


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

    // public function branch()   
    // { 
    //     return $this->belongsTo(Branch::class); 
    // }

    public function category() 
    { 
        return $this->belongsTo(TestCategory::class, 'test_category_id'); 
    }

    public function studentCourse()
    {
        return $this->belongsTo(StudentCourse::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function studentTests()
    {
        return $this->hasMany(StudentTest::class);
    }

    public function offlineTests()
    {
        return $this->hasMany(OfflineTestStudent::class);
    }

    // app/Models/Placement.php
    public function getCollegeFullNameAttribute()
    {
        $college  = $this->college;

        if (!$college) {
            return '';
        }

        $parts = [
            $college->college_name ?? '',
            $college->district->name ?? '',
            $college->state->name ?? '',
        ];

        return implode(', ', array_filter($parts));
    }
}

