<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    // Add fillable fields
    protected $fillable = [
        'title',
        'description',
        'student_course_id',
        'access_key',
        'slug'
    ];

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

}
