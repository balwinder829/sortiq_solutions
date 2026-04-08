<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Registration extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'enquiry_id',
        'student_id',
        'amount_paid',
        'payment_mode',
        'payment_status',
        'collected_by',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    // 🔗 Relationships

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function collector()
    {
        return $this->belongsTo(SalesStaff::class, 'collected_by');
    }
}
