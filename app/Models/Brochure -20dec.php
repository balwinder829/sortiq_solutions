<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Brochure extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'mime',
        'is_active',
        'start_at',
        'end_at',
        'share_token',
        'download_count',
        'file_name',
    ];

    protected $appends = ['full_file_path'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Boot to generate share_token
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = Str::random(36);
            }
        });
    }

    public function isCurrentlyVisible(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    public function getFullFilePathAttribute()
    {
        return storage_path('app/secure-brochures/' . $this->file_name);
    }

}
