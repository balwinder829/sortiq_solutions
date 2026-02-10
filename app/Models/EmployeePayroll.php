<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'month_days',
        'allowed_leave',
        'taken_leave',
        'gross_salary',
        'leave_deduction',
        'final_salary',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
