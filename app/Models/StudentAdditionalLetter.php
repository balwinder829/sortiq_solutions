<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdditionalLetter extends Model
{
    protected $fillable = [
        // 'student_name',
        'internship_type',
        'subject',
        'student_id',
        'issue_date',
        // 'email',
        'letter_content'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    
}
