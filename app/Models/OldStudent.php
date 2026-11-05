<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OldStudent extends Model
{
    use SoftDeletes;

    protected $table = 'old_students';

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
        'transferred_at' => 'datetime',
    ];

    public function originalStudent()
    {
        return $this->belongsTo(
            Student::class,
            'source_student_id'
        );
    }

    public function transferredBy()
    {
        return $this->belongsTo(
            User::class,
            'transferred_by'
        );
    }

    public function collegeData()
    {
        return $this->belongsTo(
            College::class,
            'college_name',
            'id'
        );
    }

    public function getCoursesAttribute()
    {
        $technology = $this->getRawOriginal('technology');

        if (empty($technology)) {
            return collect();
        }

        $values = collect(explode(',', $technology))
            ->map(fn($value) => trim($value))
            ->filter()
            ->unique();

        $courseMap = Course::whereIn(
            'id',
            $values->filter(fn($id) => is_numeric($id))
        )->get(['id', 'course_name'])->keyBy('id');

        return $values->map(function ($value) use ($courseMap) {

            if (is_numeric($value) && $courseMap->has($value)) {
                return $courseMap->get($value)->course_name;
            }

            return $value;
        });
    }

    public function getCourseNameAttribute()
    {
        return $this->courses->implode(', ');
    }
}