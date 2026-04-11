<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeOnlineTest extends Model
{
    use SoftDeletes;

    protected $table = 'office_online_tests';

    protected $fillable = [

        'session_id',
        'student_course_id',
        'test_category_id',
        'batch_id',
        'trainer_id',

        'title',
        'slug',
        'access_key',
        'description',

        'test_date',
        'exam_start_at',
        'exam_end_at',

        'timer_type',
        'exam_mode',

        'status',
        'is_active',

        'created_by'

    ];

    protected $casts = [
        'test_date' => 'date',
        'exam_start_at' => 'datetime',
        'exam_end_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function session()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function course()
    {
        return $this->belongsTo(StudentCourse::class,'student_course_id');
    }

    public function category()
    {
        return $this->belongsTo(TestCategory::class,'test_category_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    // public function questions()
    // {
    //     return $this->hasMany(OfficeQuestion::class);
    // }

    public function results()
    {
        return $this->hasMany(OfficeStudentResult::class);
    }

    // public function studentTests()
    // {
    //     return $this->hasMany(
    //         OfficeStudentTest::class,
    //         'office_test_id'
    //     );
    // }
     public function questions()
    {
        return $this->hasMany(OfficeOnlineQuestion::class, 'office_online_test_id');
    }

    // Student attempts for online test
    public function studentTests()
    {
        return $this->hasMany(
            StudentOfficeOnlineTest::class,
            'office_online_test_id'
        );
    }
}