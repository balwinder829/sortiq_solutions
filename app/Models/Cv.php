<?php

// app/Models/Cv.php (UPDATED)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cv extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Fields from your previous schema
        'user_id', // Assuming you meant to keep this for tracking uploader
        'employee_name',
        'technology',
        'experience_status',
        'gdrive_link',

        // New fields from your updated migration
        'experience_years',
        'current_job_status',
        'hiring_status',
        'phone_number',
        'location',
        'last_updated_at', 
        'file_name', 
    ];
    
    // Cast dates/enums
    protected $casts = [
        'experience_status' => 'string',
        'hiring_status' => 'string',
        'last_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}