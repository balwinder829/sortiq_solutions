<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InterviewQuestion extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'round_type',
        'experience_level',
        'technology_id',
        'is_active'
    ];

    public function technology()
    {
        return $this->belongsTo(Technology::class);
    }
}
