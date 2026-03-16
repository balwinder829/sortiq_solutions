<?php

// app/Models/AcceptedLetter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsAcceptedLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'email',
        'file_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
