<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'cover_image', 'event_type'
    ];

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function videos()
    {
        return $this->hasMany(EventVideo::class);
    }
}

