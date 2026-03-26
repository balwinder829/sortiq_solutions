<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCampaign extends Model
{
    protected $table = 'college_call_campaigns';

    protected $fillable = [
        'purpose',
        'notes',
        'session_id',
        'total_calls',
        'connected_count',
        'not_connected_count',
        'created_by'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function logs()
    {
        return $this->hasMany(CallLog::class, 'campaign_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}