<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesStaffLetters extends Model
{
    use SoftDeletes;

    protected $table = 'sales_staff_letters';

    protected $fillable = [

        'sales_staff_id',
        'letter_type',
        'letter_content',
        'issue_date',
        'is_sent',
        'send_count',

        // NEW FIELDS
        'emp_id',
        'month_of_deduction',
        'year_of_deduction',
        'sale_target',
        'amount_of_deduction',
    ];

    public function trainer()
    {
        return $this->belongsTo(
            SalesStaff::class,
            'sales_staff_id'
        );
    }
}