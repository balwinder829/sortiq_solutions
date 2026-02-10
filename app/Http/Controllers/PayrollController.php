<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon; 

class PayrollController extends Controller
{
    /**
     * Payroll index page (list + generate button)
     */
    public function index()
    {
        $payrolls = EmployeePayroll::select(
                'month',
                'year',
                DB::raw('COUNT(*) as total'),
                DB::raw('MAX(status) as status')
            )
            ->groupBy('month','year')
            ->orderBy('year','desc')
            ->orderBy('month','desc')
            ->get();

        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Load payroll by month/year
     * - If exists → load
     * - Else → create & load
     */

public function load(Request $request)
{
    $request->validate([
        'month' => 'required|integer|min:1|max:12',
        'year'  => 'required|integer|min:2020',
    ]);

    $month = (int) $request->month;
    $year  = (int) $request->year;

    // 🚫 Block future payroll
    if (Carbon::create($year, $month, 1)->gt(now()->startOfMonth())) {
        return redirect()
            ->route('admin.payroll.index')
            ->with('error', 'You cannot generate payroll for a future month.');
    }

    $exists = EmployeePayroll::where('month', $month)
        ->where('year', $year)
        ->exists();

    if (!$exists) {
        $employees = Employee::where('status', 'active')
            ->with('salaryStructure')
            ->get();

        DB::transaction(function () use ($employees, $month, $year) {
            foreach ($employees as $emp) {
            	// if (!$emp->salaryStructure) continue;
                $gross = $emp->salaryStructure
                    ? $emp->salaryStructure->total_salary
                    : 0;

                EmployeePayroll::create([
                    'employee_id'     => $emp->id,
                    'month'           => $month,
                    'year'            => $year,
                    'gross_salary'    => $gross,
                    'leave_deduction' => 0,
                    'final_salary'    => $gross,
                    'status'          => 'draft',
                ]);
            }
        });
    }

    // ✅ PRG redirect (THIS SOLVES REFRESH ERROR)
    return redirect()->route('admin.payroll.process', [
        'year'  => $year,
        'month' => $month,
    ]);
}


public function load21(Request $request)
{
    $request->validate([
        'month' => 'required|integer|min:1|max:12',
        'year'  => 'required|integer|min:2020',
    ]);

    $month = (int) $request->month;
    $year  = (int) $request->year;

    // 🚫 Block future payroll
    $selectedDate = Carbon::create($year, $month, 1)->startOfMonth();
    $currentDate  = now()->startOfMonth();

    if ($selectedDate->gt($currentDate)) {
        return redirect()
            ->back()
            ->with('error', 'You cannot generate payroll for a future month.');
    }

    $exists = EmployeePayroll::where('month', $month)
        ->where('year', $year)
        ->exists();

    if (!$exists) {

        $employees = Employee::where('status', 'active')
            ->with('salaryStructure')
            ->get();

        DB::transaction(function () use ($employees, $month, $year) {

            foreach ($employees as $emp) {

                // ✅ ALWAYS create payroll row
                $gross = $emp->salaryStructure
                    ? $emp->salaryStructure->total_salary
                    : 0;

                EmployeePayroll::create([
                    'employee_id'    => $emp->id,
                    'month'          => $month,
                    'year'           => $year,
                    'gross_salary'   => $gross,
                    'leave_deduction'=> 0,
                    'final_salary'   => $gross,
                    'status'         => 'draft',
                ]);
            }
        });
    }

    $payrolls = EmployeePayroll::with('employee')
        ->where('month', $month)
        ->where('year', $year)
        ->get();

    return view('payroll.process', compact('payrolls', 'month', 'year'));
}


    public function loadold(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020',
        ]);

        $month = $request->month;
        $year  = $request->year;

         $selectedDate = Carbon::create($year, $month, 1)->startOfMonth();
	    $currentDate  = now()->startOfMonth();

	    if ($selectedDate->gt($currentDate)) {
	        return redirect()
	            ->back()
	            ->with('error', 'You cannot generate payroll for a future month.');
	    }

        $exists = EmployeePayroll::where('month',$month)
            ->where('year',$year)
            ->exists();

        if (!$exists) {
            $employees = Employee::where('status','active')
                ->with('salaryStructure')
                ->get();

            DB::transaction(function () use ($employees,$month,$year) {
                foreach ($employees as $emp) {
                    if (!$emp->salaryStructure) continue;

                    $gross = $emp->salaryStructure->total_salary;

                    EmployeePayroll::create([
                        'employee_id' => $emp->id,
                        'month' => $month,
                        'year' => $year,
                        'gross_salary' => $gross,
                        'leave_deduction' => 0,
                        'final_salary' => $gross,
                        'status' => 'draft',
                    ]);
                }
            });
        }

        $payrolls = EmployeePayroll::with('employee')
            ->where('month',$month)
            ->where('year',$year)
            ->get();

        return view('payroll.process', compact('payrolls','month','year'));
    }

    /**
     * Save / Update payroll (ALL IN ONE GO)
     */

public function store(Request $request)
{
    $request->validate([
        'payroll_ids'  => 'required|array',
        'gross_salary' => 'required|array',
    ]);

    $missing = [];

    foreach ($request->payroll_ids as $id) {
        if ((float) ($request->gross_salary[$id] ?? 0) <= 0) {
            $payroll = EmployeePayroll::with('employee')->find($id);
            if ($payroll?->employee) {
                $missing[] = $payroll->employee->emp_code;
            }
        }
    }

    // 🚫 HARD BLOCK (NO SAVE)
    if (!empty($missing)) {
        $first = EmployeePayroll::find($request->payroll_ids[0]);

        return redirect()
            ->route('admin.payroll.process', [
                'year'  => $first->year,
                'month' => $first->month,
            ])
            ->withErrors([
                'salary' =>
                    'Cannot save payroll. Salary structure missing for employees: '
                    . implode(', ', $missing),
            ]);
    }

    // ✅ SAVE ONLY IF ALL SALARIES ARE VALID
    DB::transaction(function () use ($request) {
        foreach ($request->payroll_ids as $id) {
            $gross   = $request->gross_salary[$id];
            $allowed = $request->allowed_leave[$id] ?? 0;
            $taken   = $request->taken_leave[$id] ?? 0;

            $deduction = max(0, $taken - $allowed) * ($gross / 30);

            EmployeePayroll::where('id', $id)->update([
                'allowed_leave'   => $allowed,
                'taken_leave'     => $taken,
                'leave_deduction' => round($deduction, 2),
                'final_salary'    => round($gross - $deduction, 2),
                'status'          => 'finalized',
            ]);
        }
    });

    return redirect()
        ->route('admin.payroll.index')
        ->with('success', 'Payroll finalized successfully.');
}

public function storew(Request $request)
{
    $request->validate([
        'payroll_ids'  => 'required|array',
        'gross_salary' => 'required|array',
    ]);

    $missingSalaryEmployees = [];

    // 🔍 FIRST PASS: validation only (NO DB writes)
    foreach ($request->payroll_ids as $payrollId) {

        $gross = (float) ($request->gross_salary[$payrollId] ?? 0);

        if ($gross <= 0) {
            $payroll = EmployeePayroll::with('employee')->find($payrollId);
            if ($payroll && $payroll->employee) {
                $missingSalaryEmployees[] = $payroll->employee->emp_code;
            }
        }
    }

    // 🚫 BLOCK SAVE IF ANY SALARY IS MISSING
    if (!empty($missingSalaryEmployees)) {

        // Reload same payroll page
        $firstPayroll = EmployeePayroll::find($request->payroll_ids[0]);

        $month = $firstPayroll->month;
        $year  = $firstPayroll->year;

        $payrolls = EmployeePayroll::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // return view('payroll.process', compact('payrolls', 'month', 'year'))
		//     ->with('error',
		//         'Cannot save payroll. Salary structure missing for employees: '
		//         . implode(', ', $missingSalaryEmployees)
		//     );

            return view('payroll.process', compact('payrolls', 'month', 'year'))
    ->withErrors([
        'salary' =>
            'Cannot save payroll. Salary structure missing for employees: '
            . implode(', ', $missingSalaryEmployees)
    ]);


    }

    // ✅ SECOND PASS: SAVE (SAFE)
    DB::transaction(function () use ($request) {

        foreach ($request->payroll_ids as $payrollId) {

            $gross = (float) $request->gross_salary[$payrollId];
            $allowed = (int) ($request->allowed_leave[$payrollId] ?? 0);
            $taken   = (int) ($request->taken_leave[$payrollId] ?? 0);

            $perDay = $gross / 30;
            $extraLeave = max(0, $taken - $allowed);
            $deduction = $extraLeave * $perDay;

            EmployeePayroll::where('id', $payrollId)->update([
                'allowed_leave'   => $allowed,
                'taken_leave'     => $taken,
                'leave_deduction' => round($deduction, 2),
                'final_salary'    => round($gross - $deduction, 2),
                'status'          => 'finalized',
            ]);
        }
    });

    return redirect()
        ->route('admin.payroll.index')
        ->with('success', 'Payroll finalized successfully.');
}

public function process($year, $month)
{
    $payrolls = EmployeePayroll::with('employee')
        ->where('month', $month)
        ->where('year', $year)
        ->get();

    if ($payrolls->isEmpty()) {
        return redirect()
            ->route('admin.payroll.index')
            ->with('error', 'Payroll not found.');
    }

    return view('payroll.process', compact('payrolls', 'month', 'year'));
}




   
 

    public function export($month, $year)
	{
	    $fileName = 'Payroll_' .
	        date('F', mktime(0,0,0,$month,1)) .
	        '_' . $year . '.xlsx';

	    return Excel::download(
	        new PayrollExport($month, $year),
	        $fileName
	    );
	}
}
