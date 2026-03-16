<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProject extends Model
{
    protected $table = 'student_projects';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'project_type',
        'technology',
        'difficulty',
        'estimated_days',
        'attachment',
        'status',
        'created_by'
    ];

    public function assignments()
    {
        return $this->hasMany(StudentProjectAssignment::class,'project_id');
    }
}