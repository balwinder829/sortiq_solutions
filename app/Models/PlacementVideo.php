<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementVideo extends Model
{
    protected $fillable = ['placement_id', 'path'];

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }
}
