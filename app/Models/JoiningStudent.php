<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoiningStudent extends Model
{
    use SoftDeletes;

    protected $casts = [
        'duration' => 'integer',
        'payment_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'payment_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'student_name',
        'father_name',
        'contact',
        'email',
        'college',
        'duration',
        'technology',
        'is_sent_to_detail',
        'sent_to_detail_at',
        'date_of_joining',

        // Payment fields
        'payment_amount',
        'payment_status',
        'payment_admin_note',
        'payment_upi_account_id',
        'payment_transaction_id',
        'payment_date',
        'payment_proof',
        'payment_verified_at',
        'payment_verified_by',
    ];

    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college', 'id');
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id');
    }

    public function durationData()
    {
        return $this->belongsTo(Duration::class, 'duration', 'duration');
    }

    public function paymentUpiAccount()
    {
        return $this->belongsTo(
            PaymentUpiAccount::class,
            'payment_upi_account_id'
        );
    }
}