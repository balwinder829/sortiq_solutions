<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicesRegistration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'location',
        'technology',
        'message',
        'slug',
        'ip',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id')->withTrashed();
    }
}
