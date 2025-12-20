<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentSession extends Model
{   
    use SoftDeletes;
    protected $table = 'student_sessions';

    protected $fillable = [
        'session_name',
        'start_date',
        'end_date',
        'status',
        // 'department',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Accessor for session_start
    public function getSessionStartAttribute()
    {
        return $this->start_date;
    }

    // Mutator for session_start
    public function setSessionStartAttribute($value)
    {
        $this->attributes['start_date'] = $value;
    }

    // Accessor for session_end
    public function getSessionEndAttribute()
    {
        return $this->end_date;
    }

    // Mutator for session_end
    public function setSessionEndAttribute($value)
    {
        $this->attributes['end_date'] = $value;
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'session_name', 'id');
    }


    // public function getDisplayNameAttribute()
    // {
    //     return "{$this->session_name} ({$this->session_month} {$this->session_year})";
    // }

     

    public function getDisplayNameAttribute()
    {
        // If start_date is missing
        if (!$this->start_date) {
            return "{$this->session_name} (-)";
        }

        // Format: "Jan 2025"
        $start = $this->start_date->format('M Y');

        return "{$this->session_name} ({$start})";
    }



}
    