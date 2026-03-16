<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemActivityLog extends Model
{
    protected $table = 'system_activity_logs';

    protected $fillable = [
        'user_id',
        'trainer_id',
        'guard',
        'action',
        'url',
        'method',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function getActorNameAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return ($this->user->name ?? $this->user->username) . ' (User)';
        }
        if ($this->trainer_id && $this->trainer) {
            return ($this->trainer->name ?? $this->trainer->username) . ' (Trainer)';
        }
        return 'Guest (IP: ' . ($this->ip_address ?? '—') . ')';
    }
}
