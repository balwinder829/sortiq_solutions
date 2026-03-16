<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Model;

class HelpdeskAttachment extends Model
{
    protected $fillable = [
        'article_id','file_name','file_path','file_type','expires_at'
    ];

    public function article()
    {
        return $this->belongsTo(HelpdeskArticle::class,'article_id');
    }
}
