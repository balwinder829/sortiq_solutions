<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipRegistration extends Model
{
    use SoftDeletes;

    protected $table = 'internship_registrations';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'college',
        'college_name',
        'technology',
        'message',
        'status',
        'page_type',
        'slug',
        'ip',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];
    
    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college', 'id')->withTrashed();
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id')->withTrashed();
    }
}
