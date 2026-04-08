<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDescription extends Model
{
    protected $fillable = [
        'title',
        'job_type',
        'status',
        'last_date',
        'description',
        'created_by'
    ];
}