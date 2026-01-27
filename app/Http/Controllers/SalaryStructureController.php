<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryStructureController extends Controller
{
    public function create(Employee $employee)
	{
	    $salaryStructure = SalaryStructure::where('employee_id', $employee->id)
	        ->where('status', 'active')
	        ->latest()
	        ->first();

	    return view('salary.structure.create', compact('employee', 'salaryStructure'));
	}

    public function store(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'account_number' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
        ]);

        DB::transaction(function () use ($employee, $data) {

            SalaryStructure::where('employee_id', $employee->id)
                ->update(['status' => 'inactive']);

            SalaryStructure::create([
                'employee_id' => $employee->id,
                'basic_salary' => $data['basic_salary'],
                'hra' => $data['hra'] ?? 0,
                'allowance' => $data['allowance'] ?? 0,
                'deduction' => $data['deduction'] ?? 0,
                'account_number' => $data['account_number'] ?? 0,
                'effective_from' => $data['effective_from'],
                'status' => 'active',
            ]);
        });

        return redirect()->route('employees.index')
            ->with('success', 'Salary structure saved');
    }
}
