<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternStudent extends Model
{
    use SoftDeletes;

    protected $table = 'intern_students';

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'join_date' => 'date',
        'certificate_issue_date' => 'date',
        'confirmation_issue_date' => 'date',
        'internship_issue_date' => 'date',
        'pursuing_issue_date' => 'date',
        'certificate_send_date' => 'datetime',
        'close_date' => 'datetime',
    ];

    public function sourceStudent()
    {
        return $this->belongsTo(
            Student::class,
            'source_student_id'
        )->withTrashed();
    }

     // College relationship: college_name stores the college ID
    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college_name', 'id');
    }

    // Batch relationship
    public function batchData()
    {
        return $this->belongsTo(Batch::class, 'batch_assign', 'id');
    }

    // Get courses associated with this intern student.
    public function getCoursesAttribute()
    {
        $technology = $this->getRawOriginal('technology');

        if (empty($technology)) {
            return collect();
        }

        $technologyIds = is_array($technology)
            ? $technology
            : array_filter(array_map('trim', explode(',', $technology)));

        $technologyIds = array_filter(
            $technologyIds,
            fn ($id) => $id !== '' && is_numeric($id)
        );

        return Course::whereIn('id', $technologyIds)->get();
    }

    // Display course names instead of IDs.
    public function getCourseNameAttribute()
    {
        return $this->courses
            ->pluck('course_name')
            ->implode(', ');
    }
}
