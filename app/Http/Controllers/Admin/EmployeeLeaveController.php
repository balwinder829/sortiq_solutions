<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeLeaveRequest;
use App\Models\Employee;
use App\Http\DataTables\DataTablesServerSide;

class EmployeeLeaveController extends Controller
{
    /**
     * List page
     */

    public function __construct()
    {
        $this->middleware('permission:employee_leave.view')->only(['index','data','show']);
        
        $this->middleware('permission:employee_leave.edit')->only(['approve','reject']);
        
    }
    
    public function index()
    {
        $employees = Employee::orderBy('emp_name')->get();

        return view('employee_leave.index', compact('employees'));
    }

    /**
     * Data for DataTable
     */
    public function data(Request $request)
    {
        $query = EmployeeLeaveRequest::with('employee');

        // 🔍 Filter: employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // 🔍 Filter: date
        if ($request->date && !$request->range) {
            $query->whereDate('from_date', $request->date);
        }

        // 🔍 Range filter (reuse your workshop logic)
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

        // 🔍 Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $query->orderBy('id', 'desc');

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','emp_name','from_date','status'],
            'searchable' => ['emp_name','emp_code','email'],
        ], function ($leave, $index, $start) {

            $statusBadge = match($leave->status) {
                'approved' => '<span class="badge bg-success">Approved</span>',
                'rejected' => '<span class="badge bg-danger">Rejected</span>',
                default    => '<span class="badge bg-warning">Pending</span>',
            };

            $actions  = '<a href="'.route('admin.employee.leave.show', $leave->id).'" 
                class="btn btn-sm " 
                data-bs-toggle="tooltip" title="View">
                <i class="fa fa-eye"></i>
            </a> ';

            if ($leave->status == 'pending') {
                $actions .= '<a href="'.route('admin.employee.leave.approve', $leave->id).'" 
                                class="btn btn-sm " 
                                data-bs-toggle="tooltip" title="Approve">
                                <i class="fa fa-check"></i>
                            </a> ';

                $actions .= '<a href="'.route('admin.employee.leave.reject', $leave->id).'" 
                                class="btn btn-sm " 
                                data-bs-toggle="tooltip" title="Reject">
                                <i class="fa fa-times"></i>
                            </a>';
            }
            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($leave->emp_name) . ' (' . e($leave->emp_code) . ')',
                e($leave->email),
                \Carbon\Carbon::parse($leave->from_date)->format('d M Y') . ' - ' .
                \Carbon\Carbon::parse($leave->to_date)->format('d M Y'),
                $leave->total_days,
                $statusBadge,
                $actions,
            ];
        });
    }

    /**
     * View single leave
     */
    public function show($id)
    {
        $leave = EmployeeLeaveRequest::findOrFail($id);

        return view('employee_leave.show', compact('leave'));
    }

    /**
     * Approve
     */
    public function approve($id)
    {
        $leave = EmployeeLeaveRequest::findOrFail($id);
        $leave->update(['status' => 'approved']);

        return back()->with('success', 'Leave approved');
    }

    /**
     * Reject
     */
    public function reject($id)
    {
        $leave = EmployeeLeaveRequest::findOrFail($id);
        $leave->update(['status' => 'rejected']);

        return back()->with('success', 'Leave rejected');
    }
}