<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\College;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Exports\FeeStatusExport;
use Maatwebsite\Excel\Facades\Excel;


class FeeStatusController extends Controller
{
    protected string $permissionPrefix = 'fee_status';

    protected array $permissionMap = [
        'index'        => 'view',
        'export'         => 'view',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
    public function index(Request $request)
    {   

         // Active admin session
        $activeSessionNo = session('admin_session_id');

        // Base query with relationships
        $query = Student::with(['collegeData'])
            ->select(
                'id',
                'student_name',
                'sno',
                'contact',
                'college_name',
                'technology',
                'total_fees',
                'reg_fees',
                'paid_fees',
                'pending_fees'
            )
            ->where('session', $activeSessionNo);

        // Filters using relationship keys
        if ($request->filled('college_id')) {
            $query->where('college_name', $request->college_id);
        }

        if ($request->filled('course_id')) {
            $query->whereRaw(
                "FIND_IN_SET(?, technology)",
                [$request->course_id]
            );
            // $query->where('technology', $request->course_id);
        }

        // 🔥 Percentage Filter
        // if ($request->filled('percent_range')) {

        //     switch ($request->percent_range) {

        //         case 'upto50':
        //             $query->whereRaw(
        //                 '(CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) <= 50'
        //             );
        //             break;

        //         case '50to80':
        //             $query->whereRaw(
        //                 '(CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) > 50
        //                  AND (CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) <= 80'
        //             );
        //             break;

        //         case '80to99':
        //             $query->whereRaw(
        //                 '(CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) > 80
        //                  AND (CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) < 100'
        //             );
        //             break;

        //         case '100':
        //             $query->whereRaw(
        //                 '(CASE WHEN total_fees > 0 THEN (reg_fees / total_fees) * 100 ELSE 0 END) = 100'
        //             );
        //             break;
        //     }
        // }
            // 🔥 Percentage Filter (SAFE — no divide by zero)
        // 🔥 Percentage Filter (reg_fees + paid_fees)
if ($request->filled('percent_range')) {

    $expr = '(COALESCE((reg_fees + paid_fees) / NULLIF(total_fees,0),0) * 100)';

    switch ($request->percent_range) {

        case 'upto50':
            $query->whereRaw("$expr <= 50");
            break;

        case '50to80':
            $query->whereRaw("$expr > 50 AND $expr <= 80");
            break;

        case '80to99':
            $query->whereRaw("$expr > 80 AND $expr < 100");
            break;

        case '100':
            $query->whereRaw("$expr = 100");
            break;
    }
}



        $students = $query->get();

        /* ===============================
           STATISTICS (AFTER FILTERS)
        =============================== */

        $totalFee    = $students->sum('total_fees');
        // $paidFee     = $students->sum('reg_fees');
        $paidFee = $students->sum(function($s){
            return ($s->reg_fees ?? 0) + ($s->paid_fees ?? 0);
        });
        $pendingFee  = $students->sum('pending_fees');

        $paidPercent = $totalFee > 0 ? round(($paidFee / $totalFee) * 100, 2) : 0;
        $pendingPercent = 100 - $paidPercent;

        // Add row-level percentages
        $students = $students->map(function ($student) {
            $total = $student->total_fees ?? 0;
            // $paid  = $student->reg_fees ?? 0;
            $student->paid = $paid  = ($student->reg_fees ?? 0) + ($student->paid_fees ?? 0);

            $student->paid_percentage = $total > 0
                ? round(($paid / $total) * 100, 2)
                : 0;

            $student->fee_status =
                $student->pending_fees == 0 ? 'Fully Paid' :
                ($paid > 0 ? 'Partially Paid' : 'Not Paid');

            return $student;
        });

        // dd($students);

        // Dropdown data (from master tables)
        // $colleges = College::select('id', 'college_name')->get();
        // $courses  = Course::select('id', 'course_name')->get();
        $colleges = College::orderBy('college_name', 'asc')
            ->select('id', 'college_name')
            ->get();

        $courses = Course::orderBy('course_name', 'asc')
            ->select('id', 'course_name')
            ->get();

        return view('fee-status.index', compact(
            'students',
            'colleges',
            'courses',
            'totalFee',
            'paidFee',
            'pendingFee',
            'paidPercent',
            'pendingPercent'
        ));
    }

    public function export(Request $request)
    {
         $date = strtolower(now()->format('d_F'));
          $parts = ['fee_status'];
         $fileName = implode('_', $parts) . '_' . $date . '.xlsx';
        return Excel::download(
            new FeeStatusExport(
                $request->college_id,
                $request->course_id,
                $request->percent_range
            ),
            $fileName
        );
    }

}
