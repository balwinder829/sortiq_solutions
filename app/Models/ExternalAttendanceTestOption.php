<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAttendanceTestOption extends Model
{
    use HasFactory;

    protected $table = 'external_attendance_test_options';

    protected $fillable = [
        'external_attendance_test_id',
        'category_id',
        'test_id',
        'display_name',
        'test_type',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function attendanceTest()
    {
        return $this->belongsTo(
            ExternalAttendanceTest::class,
            'external_attendance_test_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            AttendanceTestCategory::class,
            'category_id'
        );
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}