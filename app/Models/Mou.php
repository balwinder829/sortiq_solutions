<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Mou extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'college_id',
        'mou_title',
        'mou_number',
        'email_to',
        'start_date',
        'end_date',
        'draft_document_path',
        'signed_document_path',
        'signed_received_at',
        'email_sent_at',
        'email_sent_to',
        'status',
        'description',
    ];

    protected $casts = [
	    'start_date' => 'date',
	    'end_date' => 'date',
	    'email_sent_at' => 'datetime',
	    'signed_received_at' => 'datetime',
	    'created_at' => 'datetime',
	];


    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return now()->gt($this->end_date);
    }
}
