<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'cover_image', 'event_type','college_id',   // ✅ add this
    ];

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function videos()
    {
        return $this->hasMany(EventVideo::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'id');
    }
}

