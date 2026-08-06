<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryFollowup extends Model
{
    use SoftDeletes;

    protected $casts = [
        'next_followup_date' => 'datetime', // 🔥 REQUIRED
    ];

    protected $fillable = [
        'enquiry_id',
        'session_id',
        'user_id',
        'status',
        'call_status',
        'note',
        'next_followup_date',
    ];


    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user()
    {
        return $this->belongsTo(SalesStaff::class);
    }

    public function session()
    {
        return $this->belongsTo(StudentSession::class,'session_id');
    }
}
