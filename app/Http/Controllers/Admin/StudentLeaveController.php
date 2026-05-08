<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentLeaveRequest;
use App\Models\Student;
use App\Http\DataTables\DataTablesServerSide;

class StudentLeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student_leave.view')->only(['index','data','show']);
        
        $this->middleware('permission:student_leave.edit')->only(['approve','reject']);
        
    }
    public function index()
    {
        $students = Student::orderBy('student_name')->get();

        return view('student_leave.index', compact('students'));
    }

    public function data(Request $request)
    {
        // $query = StudentLeaveRequest::query();

        $activeSessionNo = session('admin_session_id');

        $query = StudentLeaveRequest::where('session_id', $activeSessionNo);

        // 🔍 Filter: student
        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // 🔍 Date filter
        if ($request->date && !$request->range) {
            $query->whereDate('from_date', $request->date);
        }

        // 🔍 Range filter
        if ($request->range) {
            switch ($request->range) {
                case 'today':
                    $query->whereDate('from_date', today());
                    break;

                case 'yesterday':
                    $query->whereDate('from_date', today()->subDay());
                    break;

                case 'last_7_days':
                    $query->whereBetween('from_date', [now()->subDays(7), now()]);
                    break;

                case 'last_30_days':
                    $query->whereBetween('from_date', [now()->subDays(30), now()]);
                    break;

                case 'this_month':
                    $query->whereMonth('from_date', now()->month);
                    break;
            }
        }

        // 🔍 Status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $query->orderBy('id', 'desc');

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','student_name','from_date','status'],
            'searchable' => ['student_name','sno','contact'],
        ], function ($leave, $index, $start) {

            $statusBadge = match($leave->status) {
                'approved' => '<span class="badge bg-success">Approved</span>',
                'rejected' => '<span class="badge bg-danger">Rejected</span>',
                default    => '<span class="badge bg-warning">Pending</span>',
            };

            $actions  = '<a href="'.route('admin.student.leave.show', $leave->id).'" 
                            class="btn btn-sm" 
                            data-bs-toggle="tooltip" title="View">
                            <i class="fa fa-eye"></i>
                        </a> ';

            if ($leave->status == 'pending') {
                $actions .= '<a href="'.route('admin.student.leave.approve', $leave->id).'" 
                                class="btn btn-sm confirm-action" 
                                data-bs-toggle="tooltip" title="Approve">
                                <i class="fa fa-check"></i>
                            </a> ';

                $actions .= '<a href="'.route('admin.student.leave.reject', $leave->id).'" 
                                class="btn btn-sm confirm-action" 
                                data-bs-toggle="tooltip" title="Reject">
                                <i class="fa fa-times"></i>
                            </a>';
            }

            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($leave->student_name) . ' (SNO: ' . e($leave->sno) . ')',
                e($leave->contact ?? '-'),
                \Carbon\Carbon::parse($leave->from_date)->format('d M Y') . ' - ' .
                \Carbon\Carbon::parse($leave->to_date)->format('d M Y'),
                $leave->total_days,
                $leave->mentor,
                $statusBadge,
                $actions,
            ];
        });
    }

    public function show($id)
    {
        $leave = StudentLeaveRequest::findOrFail($id);
        return view('student_leave.show', compact('leave'));
    }

    public function approve($id)
    {
        StudentLeaveRequest::where('id', $id)->update(['status' => 'approved']);
        return back()->with('success', 'Leave approved');
    }

    public function reject($id)
    {
        StudentLeaveRequest::where('id', $id)->update(['status' => 'rejected']);
        return back()->with('success', 'Leave rejected');
    }
}