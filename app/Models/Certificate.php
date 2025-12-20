<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates_au';

    // Primary key is id
    protected $primaryKey = 'id';

    // Auto-incrementing primary key
    public $incrementing = true;

    // id is integer
    protected $keyType = 'int';

    // Timestamps
    public $timestamps = false; // created_at exists but we can ignore for now

    // Mass assignable fields
    protected $fillable = [
        'sno',
        'first_name',
        'last_name',
        'colleage',
        'start_date',
        'end_date',
        'duration',
        'technology',
        'semester',
        'stream',
        'branch',
    ];
    protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
];
}
