<?php

// app/Models/UpcomingEvent.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UpcomingEvent extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'event_date',
        'notify',
        'dismissed',
        'last_notified_at'
    ];

    protected $casts = [
        'notify' => 'boolean',
        'dismissed' => 'boolean',
        'event_date' => 'date'
    ];
}
