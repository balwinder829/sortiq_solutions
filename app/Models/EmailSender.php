<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSender extends Model
{
    protected $table = 'college_email_senders';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'host',
        'port',
        'encryption',
        'is_active'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function campaigns()
    {
        return $this->hasMany(EmailCampaign::class, 'sender_id');
    }
}