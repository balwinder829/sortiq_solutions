<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoiningStudent extends Model
{	
    use SoftDeletes;
	protected $casts = [
	    'duration' => 'integer',
	];
    protected $fillable = [
        'student_name',
        'father_name',
        'college',
        'duration',
        'technology',
        'date_of_joining'
    ];


    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college', 'id');
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id');
    }

    public function durationData()
    {
        return $this->belongsTo(Duration::class, 'duration', 'duration');
    }
}
