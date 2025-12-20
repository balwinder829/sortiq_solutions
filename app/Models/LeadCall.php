<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCall extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'call_status',
        'lead_status',
        'follow_up_date',
        'remark',
    ];

    protected $dates = ['follow_up_date'];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
