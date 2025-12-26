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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->share_token = Str::random(36);
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
