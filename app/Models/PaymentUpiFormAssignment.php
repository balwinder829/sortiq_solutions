<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentUpiFormAssignment extends Model
{
    use HasFactory;

    protected $table = 'payment_upi_form_assignments';

    protected $fillable = [
        'upi_account_id',
        'form_type',
        'form_id',
    ];

    public function upiAccount()
    {
        return $this->belongsTo(
            PaymentUpiAccount::class,
            'upi_account_id'
        );
    }
}