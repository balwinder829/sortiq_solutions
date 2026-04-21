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
        'file_name',
        'file_type',
        'mime',
        'is_active',
        'start_at',
        'end_at',
        'share_token',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
    ];

    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $model->share_token = Str::random(36);
    //     });
    // }

    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $slug = Str::slug($model->title);

    //         // fallback if title becomes empty (very rare case)
    //         if (empty($slug)) {
    //             $slug = 'brochure';
    //         }

    //         $token = $slug . '-' . now()->format('Ymd-His');

    //         $model->share_token = $token;
    //     });
    // }

    protected static function booted()
    {
        static::creating(function ($model) {
            $baseSlug = Str::slug($model->title) ?: 'brochure';

            $slug = $baseSlug;
            $counter = 2;

            while (static::where('share_token', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $model->share_token = $slug;
        });
    }

    /* =========================
       SCOPE: PUBLICLY VISIBLE
    ========================= */
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

    /* =========================
       HELPER
    ========================= */
    public function isCurrentlyVisible(): bool
    {
        if (! $this->is_active) return false;

        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) return false;
        if ($this->end_at && $now->gt($this->end_at)) return false;

        return true;
    }
}
