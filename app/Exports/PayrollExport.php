<?php

namespace App\Exports;

use App\Models\EmployeePayroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class PayrollExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function collection()
    {
        return EmployeePayroll::with('employee')
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Emp Code',
            'Employee Name',
            'Allowed Leave',
            'Taken Leave',
            'Gross Salary',
            'Leave Deduction',
            'Final Salary',
        ];
    }

    public function map($payroll): array
    {
        return [
            $payroll->employee->emp_code,
            $payroll->employee->emp_name,
            $payroll->allowed_leave,
            $payroll->taken_leave,
            $payroll->gross_salary,
            $payroll->leave_deduction,
            $payroll->final_salary,
        ];
    }
}
