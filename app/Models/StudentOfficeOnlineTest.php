<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentOfficeOnlineTest extends Model
{
    protected $table = 'student_office_online_tests';

    protected $fillable = [
        'office_online_test_id',
        'student_sno',
        'student_name',
        'student_email',
        'student_mobile',
        'session_key',
        'ip_address',
        'exam_started_at',
        'exam_submitted_at',
        'exam_locked',
        'score'
    ];

    protected $casts = [
        'exam_started_at' => 'datetime',
        'exam_submitted_at' => 'datetime',
        'exam_locked' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function test()
    {
        return $this->belongsTo(OfficeOnlineTest::class, 'office_online_test_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentOfficeOnlineAnswer::class);
    }
}