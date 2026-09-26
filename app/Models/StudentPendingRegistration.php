<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPendingRegistration extends Model
{
    use SoftDeletes;
    protected $table = 'student_pending_registration';

    protected $fillable = [
        'student_name',
        'contact',
        'email',
        'gender',
        'father_name',
        'college_id',
        'college_name_input',
        'course_id',
        'is_sent_to_detail',
        'course_name_input',
        'sent_to_detail_at',
        'semester',
        'study_mode',
        'start_date',
        // Payment fields
        'payment_amount',
        'payment_status',
        'payment_upi_account_id',
        'payment_transaction_id',
        'payment_date',
        'payment_proof',
        'payment_verified_at',
        'payment_verified_by',
        'payment_admin_note',
    ];

    public function collegeData()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function paymentUpiAccount()
    {
        return $this->belongsTo(
            \App\Models\PaymentUpiAccount::class,
            'payment_upi_account_id'
        );
    }
}