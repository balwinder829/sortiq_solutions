<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Model;

class HelpdeskArticle extends Model
{
    protected $fillable = [
        'technology_id','title','slug','description','status','expires_at'
    ];

    public function technology()
    {
        return $this->belongsTo(HelpdeskTechnology::class,'technology_id');
    }

    public function attachments()
    {
        return $this->hasMany(HelpdeskAttachment::class,'article_id');
    }
}
