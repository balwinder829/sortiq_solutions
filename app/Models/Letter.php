<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'employee_id',
        'letter_type',
        'issue_date',

        // experience
        'relieving_date',
        'experience_time',

        // increment
        'new_salary',
        'increment_percentage',
        'effective_date',

        // appointment / bond
        'probation_period',
        'bond_period',
        'check_number',
        'bond_start_date',
        'bond_end_date',
        'bond_amount',
        'bond_terms',

        'is_sent',
        'send_count',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
