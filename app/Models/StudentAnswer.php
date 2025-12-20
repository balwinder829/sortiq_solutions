<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    // ✅ Allow mass assignment
    protected $fillable = [
        'student_test_id',
        'question_id',
        'option_id',
    ];

    public function studentTest()
    {
        return $this->belongsTo(StudentTest::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}