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
    public function index(Request $request)
    {   

         // Active admin session
        $activeSessionNo = session('admin_session_id');

        // Base query with relationships
        $query = Student::with(['collegeData', 'courseData'])
            ->select(
                'id',
                'student_name',
                'college_name',
                'technology',
                'total_fees',
                'reg_fees',
                'pending_fees'
            )
            ->where('session', $activeSessionNo);

        // Filters using relationship keys
        if ($request->filled('college_id')) {
            $query->where('college_name', $request->college_id);
        }

        if ($request->filled('course_id')) {
            $query->where('technology', $request->course_id);
        }

        $students = $query->get();

        /* ===============================
           STATISTICS (AFTER FILTERS)
        =============================== */

        $totalFee    = $students->sum('total_fees');
        $paidFee     = $students->sum('reg_fees');
        $pendingFee  = $students->sum('pending_fees');

        $paidPercent = $totalFee > 0 ? round(($paidFee / $totalFee) * 100, 2) : 0;
        $pendingPercent = 100 - $paidPercent;

        // Add row-level percentages
        $students = $students->map(function ($student) {
            $total = $student->total_fees ?? 0;
            $paid  = $student->reg_fees ?? 0;

            $student->paid_percentage = $total > 0
                ? round(($paid / $total) * 100, 2)
                : 0;

            $student->fee_status =
                $student->pending_fees == 0 ? 'Fully Paid' :
                ($paid > 0 ? 'Partially Paid' : 'Not Paid');

            return $student;
        });

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
        return Excel::download(
            new FeeStatusExport(
                $request->college_id,
                $request->course_id
            ),
            'fee-status.xlsx'
        );
    }

}
