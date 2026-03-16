<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Model;

class HelpdeskTechnology extends Model
{
    protected $fillable = ['name','slug'];

    public function articles()
    {
        return $this->hasMany(HelpdeskArticle::class,'technology_id');
    }
}
