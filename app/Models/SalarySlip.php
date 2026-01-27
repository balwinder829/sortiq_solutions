<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'emp_code',
        'emp_name',
        'position',
        'department',
        'employment_type',
        'basic_salary',
        'hra',
        'allowance',
        'deduction',
        'gross_salary',
        'net_salary',
        'account_number',
        'generated_by',
        'generated_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
