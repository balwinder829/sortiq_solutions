<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scanner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image_path',
        'source',
        'source_url',
        'description',
        'is_active',
        'is_public',
        'share_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

     

    protected static function booted()
    {
        static::creating(function ($scanner) {
            if ($scanner->is_public && empty($scanner->share_token)) {
                $scanner->share_token = \Str::uuid();
            }
        });
    }
}
