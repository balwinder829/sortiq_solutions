<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorRecord extends Model
{
    use SoftDeletes;

    protected $table = 'visitor_records';

    protected $fillable = [
        'visitor_name',
        'mobile',
        'email',
        'organization',
        'purpose',
        'person_to_meet',
        'visit_date',
        'visit_time',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}