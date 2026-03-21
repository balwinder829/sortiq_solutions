<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailPurpose extends Model
{
    protected $table = 'college_email_purposes';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function campaigns()
    {
        return $this->hasMany(EmailCampaign::class, 'purpose_id');
    }
}