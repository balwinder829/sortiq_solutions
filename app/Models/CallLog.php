<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $table = 'college_call_logs';

    protected $fillable = [
        'campaign_id',
        'session_id',
        'college_id',
        'hod_id',
        'contact_number',
        'recipient_name',
        'type',
        'status',
        'called_at',
        'notes',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'called_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function campaign()
    {
        return $this->belongsTo(CallCampaign::class, 'campaign_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}