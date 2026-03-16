<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'batch_id',
        'trainer_id',
        'session_date'
    ];

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class,'session_id');
    }
}