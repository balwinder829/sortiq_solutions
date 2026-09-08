<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeCallStatus extends Model
{
    protected $table = 'college_call_statuses';

    protected $fillable = [
        'session_id',
        'college_id',
        'call_done',
    ];

    protected $casts = [
        'call_done' => 'boolean',
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }
}