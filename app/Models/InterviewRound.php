<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InterviewRound extends Model
{
    protected $fillable = [
        'interview_id',
        'round_type',
        'round_date',
        'rating',
        'remarks',
    ];

    public function technologies()
    {
        return $this->belongsToMany(
            Technology::class,
            'interview_round_technology'
        )->withPivot('rating');
    }
}
