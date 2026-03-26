<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $table = 'college_email_campaigns';

    protected $fillable = [
        'purpose_id',
        'session_id',
        'sender_id',
        'subject',
        'body',
        'total_recipients',
        'sent_count',
        'failed_count',
        'created_by'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function purpose()
    {
        return $this->belongsTo(EmailPurpose::class, 'purpose_id');
    }

    public function sender()
    {
        return $this->belongsTo(EmailSender::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(EmailRecipient::class, 'campaign_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}