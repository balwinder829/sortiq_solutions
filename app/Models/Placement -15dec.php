<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'cover_image'
    ];

    public function images()
    {
        return $this->hasMany(PlacementImage::class);
    }

    public function videos()
    {
        return $this->hasMany(PlacementVideo::class);
    }
}
