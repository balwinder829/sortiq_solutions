<?php

// app/Models/Tutorial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutorial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'youtube_id',
        'description',
        'channel_name',
        'level',
        'technology',
    ];

    /**
     * Accessor to get the full YouTube embed URL.
     */
    public function getEmbedUrlAttribute()
    {
        // Example format: https://www.youtube.com/embed/dQw4w9WgXcQ
        return 'https://www.youtube.com/embed/' . $this->youtube_id;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}