<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
    ];


    protected $fillable = [
        'title',
        'college_id',
        'college_type',
        'status',
        'type',
        'session',
        'duration',
        'tp_hod_no',
        'name',
        'description',
        'event_type',
        'date'
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
