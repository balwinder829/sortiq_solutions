<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentUpiAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_upi_accounts';

    protected $fillable = [
        'name',
        'provider',
        'upi_id',
        'qr_image',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function formAssignments()
    {
        return $this->hasMany(
            PaymentUpiFormAssignment::class,
            'upi_account_id'
        );
    }

    public function assignedForms()
    {
        return $this->hasMany(
            PaymentUpiFormAssignment::class,
            'upi_account_id'
        );
    }
}