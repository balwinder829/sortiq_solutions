<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeEmailStatus extends Model
{
    protected $table = 'college_email_statuses';

    protected $fillable = [
        'session_id',
        'college_id',
        'email_sent',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }
}