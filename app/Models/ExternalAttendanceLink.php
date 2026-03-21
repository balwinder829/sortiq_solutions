<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalAttendanceLink extends Model
{
    protected $fillable = [
        'external_attendance_test_id',
        'college_id',
        'slug'
    ];

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
}