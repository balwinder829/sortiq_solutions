<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Hod extends Model
{
    protected $fillable = [
        'college_id',

        'hod_name',
        'hod_gender',
        'hod_contact',

        'tpo_name',
        'tpo_gender',
        'tpo_contact',
        'description',
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    
    public function emails()
    {
        return $this->hasMany(HodEmail::class, 'hod_id', 'id');
    }

    public function hodEmails()
    {
        return $this->hasMany(HodEmail::class, 'hod_id', 'id')
                    ->where('type', 'hod');
    }

    public function tpoEmails()
    {
        return $this->hasMany(HodEmail::class, 'hod_id', 'id')
                    ->where('type', 'tpo');
    }

    public function primaryHodEmail()
    {
        return $this->hasOne(HodEmail::class, 'hod_id', 'id')
                    ->where('type', 'hod')
                    ->where('is_primary', true);
    }

    public function primaryTpoEmail()
    {
        return $this->hasOne(HodEmail::class, 'hod_id', 'id')
                    ->where('type', 'tpo')
                    ->where('is_primary', true);
    }
    public function emailRecipients()
    {
        return $this->hasMany(EmailRecipient::class, 'hod_id');
    }

    public function firstHodEmail()
    {
        return $this->hasOne(HodEmail::class, 'hod_id')
                    ->where('type', 'hod')
                    ->orderByDesc('is_primary')
                    ->orderBy('id');
    }

    public function firstTpoEmail()
    {
        return $this->hasOne(HodEmail::class, 'hod_id')
                    ->where('type', 'tpo')
                    ->orderByDesc('is_primary')
                    ->orderBy('id');
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class, 'hod_id');
    }
}
