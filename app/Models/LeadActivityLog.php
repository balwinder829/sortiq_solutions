<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivityLog extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'action',
        'old_value', 'new_value', 'note'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lead() {
        return $this->belongsTo(Lead::class);
    }
}

