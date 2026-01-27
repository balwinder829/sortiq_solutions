<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{   
    protected $appends = ['total_salary'];
    protected $fillable = [
        'employee_id',
        'basic_salary',
        'hra',
        'allowance',
        'deduction',
        'account_number',
        'effective_from',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalSalaryAttribute()
    {
        return
            ($this->basic_salary ?? 0)
            + ($this->hra ?? 0)
            + ($this->allowance ?? 0);
    }
}
