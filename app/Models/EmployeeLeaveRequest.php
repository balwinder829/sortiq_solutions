<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveRequest extends Model
{
    protected $table = 'employee_leave_requests';

    protected $fillable = [
        'employee_id',
        'emp_code',
        'emp_name',
        'email',
        'contact',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'status',
        'ip_address',
    ];

    // 🔗 Relation
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}