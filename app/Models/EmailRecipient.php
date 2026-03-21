<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRecipient extends Model
{
    protected $table = 'college_email_recipients';

    protected $fillable = [
        'campaign_id',
        'college_id',
        'hod_id',
        'hod_email_id',
        'email',
        'recipient_name',
        'type',
        'status',
        'sent_at',
        'error_message',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id');
    }

    public function hodEmail()
    {
        return $this->belongsTo(HodEmail::class, 'hod_email_id');
    }
}