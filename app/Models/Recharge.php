<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recharge extends Model
{   
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'created_by',
        'employee_name',
        'mobile_number',
        'operator',
        'amount',
        'reference',
        'status',
        'notes',
        'recharged_at',
        'days', // added
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recharged_at' => 'datetime',
        'days' => 'integer',
    ];

    // Optional relation if you have a User model
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}