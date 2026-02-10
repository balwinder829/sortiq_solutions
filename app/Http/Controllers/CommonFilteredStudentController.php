<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\EmailCount;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssuedMail;
use App\Mail\StudentConfirmationMail;
use App\Models\College;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Reference;
use App\Models\Duration;
use App\Models\StudentStatus;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use ZipArchive;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentListExport;
use Illuminate\Support\Str;


class CommonFilteredStudentController extends Controller
{   

    protected string $permissionPrefix = 'students';

    protected array $permissionMap = [
        'index'        => 'view',
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
    // Constructor to apply auth middleware

    // List students
    public function index(Request $request)
    {   

        // dd('here');
        $notificationMode = $request->notification ?? null;

        $hasFilter = collect($request->query())
        ->filter(fn ($value) => $value !== null && $value !== '')
        ->isNotEmpty();

        $query = Student::query();

        // $query = Student::query();
        if ($notificationMode === 'registered_today') {
            $query->whereDate('created_at', today());
        }else{
            // Filters
            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', '%' . $request->student_name . '%');
            }
            if ($request->filled('f_name')) {
                $query->where('f_name', 'like', '%' . $request->f_name . '%');
            }
            if ($request->filled('sno')) {
                $query->where('sno', $request->sno);
            }
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('session')) {
                $query->where('session', $request->session);
            }
            if ($request->filled('college_name')) {
                $query->where('college_name', $request->college_name);
            }
            if ($request->filled('email_id')) {
                $query->where('email_id', 'like', '%' . $request->email_id . '%');
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('technology')) {
                $query->where('technology', $request->technology);
            }
            if ($request->filled('batch_assign')) {
                $query->where('batch_assign', $request->batch_assign);
            }
            // if ($request->filled('department')) {
            //     $query->where('department', $request->department);
            // }
            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }
            // Filter by pending fee
            if ($request->filled('pending_fees') && $request->pending_fees == 1) {
                $query->where('pending_fees', '>', 0.00);
            }

            if ($request->filled('part_time_offer')) {
                $query->where('part_time_offer', $request->part_time_offer);
            }

            if ($request->filled('placement_offer')) {
                $query->where('placement_offer', $request->placement_offer);
            }

            if ($request->filled('pg_offer')) {
                $query->where('pg_offer', $request->pg_offer);
            }

            if ($request->filled('fee_filter')) {
                switch ($request->fee_filter) {

                    case 'completed':
                        $query->where('pending_fees', 0);
                        break;

                    case 'pending':
                        $query->where('pending_fees', '>', 0);
                        break;

                    case 'pending_high':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'desc');
                        break;

                    case 'pending_low':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'asc');
                        break;

                    case 'fees_high':
                        $query->orderBy('total_fees', 'desc');
                        break;

                    case 'fees_low':
                        $query->orderBy('total_fees', 'asc');
                        break;
                }
            }


            /* =========================
               AMOUNT SLIDER FILTER
               (ONLY for last 4 options)
               ========================= */

            if (
                $request->filled('fee_filter') &&
                in_array($request->fee_filter, [
                    'pending_high',
                    'pending_low',
                    'fees_high',
                    'fees_low'
                ])
            ) {

                $minAmount = $request->amount_min;
                $maxAmount = $request->amount_max;
                // dd($request->amount_min, $request->amount_max,$request->fee_filter);
                // Decide column
                $amountColumn = in_array($request->fee_filter, ['pending_high', 'pending_low'])
                    ? 'pending_fees'
                    : 'total_fees';

                if ($minAmount !== null && $minAmount !== '') {
                    $query->where($amountColumn, '>=', $minAmount);
                }

                if ($maxAmount !== null && $maxAmount !== '') {
                    $query->where($amountColumn, '<=', $maxAmount);
                }
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            
            // if (auth()->user()->role == 1) {
                $activeSessionId = session('admin_session_id');
                $query->where('session', $activeSessionId);
            // }
            
        }
        
            
            // $query->where('certificate_status', 0);
            //dd($request->all());
            $students = $query->orderBy('id', 'desc')->get();

            // $students = $query->paginate(10);
        if (!$hasFilter) {
           $students = collect();
        }
        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        $references = Reference::all();
        $departments = Department::all();
        $users = User::all();
        $student_status = StudentStatus::all();

        //pending fee
        $dismissed = session('dismiss_pending_fee');
        $activeSessionNo = session('admin_session_id');
            
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionNo)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();
        

        return view('common_listing_students.index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }
}