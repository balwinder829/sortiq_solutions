<?php

// app/Models/Page.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'heading',
        'location',
        'meta_title',
        'meta_description',
        'ads_type',
        'meta_keywords',
        'banner_image',
        'featured_image',
        'is_active',
        'ads_status',
    ];
}
