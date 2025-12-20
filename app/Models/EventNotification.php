<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EventNotification extends Model
{
    protected $fillable = ['key', 'dismissed'];

    public static function today()
    {
        return static::firstOrCreate(['key' => 'today']);
    }
}
