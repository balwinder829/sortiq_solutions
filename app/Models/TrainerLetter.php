<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerLetter extends Model
{
    use SoftDeletes;

    protected $table = 'trainers_letters';

    protected $fillable = [
        'trainer_id',
        'letter_type',
        'letter_content',
        'issue_date',
        'is_sent',
        'send_count',
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}