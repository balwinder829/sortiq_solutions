<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mobile_number',
        'operator',
        'name',
        'status',
        'issued_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'date',
    ];

    /**
     * User who created the SIM record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'created_by'
        );
    }
}