<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyProfile extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_type',
        'mime',
        'is_active',
        'start_at',
        'end_at',
        'share_token',
        'download_count',
    ];

    protected $appends = ['full_file_path'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Auto-generate share token
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = Str::random(36);
            }

            if (is_null($model->download_count)) {
                $model->download_count = 0;
            }
        });
    }

    /**
     * Determine if the profile is currently visible.
     */
    // public function isCurrentlyVisible(): bool
    // {
    //     if (!$this->is_active) {
    //         return false;
    //     }

    //     $now = Carbon::now();

    //     if ($this->start_at && $now->lt($this->start_at)) {
    //         return false;
    //     }

    //     if ($this->end_at && $now->gt($this->end_at)) {
    //         return false;
    //     }

    //     return true;
    // }

    /**
     * Full file path stored in storage/app/secure-company-profiles
     */
    public function getFullFilePathAttribute()
    {
        return storage_path('app/secure-company-profiles/' . $this->file_name);
    }


     public function isCurrentlyVisible(): bool
    {
        if (! $this->is_active) return false;

        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) return false;
        if ($this->end_at && $now->gt($this->end_at)) return false;

        return true;
    }

    public function scopePubliclyVisible($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', $now);
            });
    }
}
