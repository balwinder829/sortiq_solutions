<?php

namespace App\Http\Controllers\Students;
use App\Http\Controllers\Controller;
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
use ZipArchive;
use Illuminate\Support\Str;
use App\Rules\NotBlockedNumber;

class CertificateController extends Controller
{

    protected string $permissionPrefix = 'certificates';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
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
// Show all students
public function index(Request $request)
{   

    $notificationMode = $request->notification ?? null;

    $query = Student::query();

    if ($notificationMode === 'bin_ready') {
        $query->where('pending_fees', 0);
        $query->where('certificate_status', 2);
        $query->where('email_count_certificate','>' ,0);
    }else if($notificationMode === 'pending_fee'){
        $query->where('pending_fees', '>', 0);
        $query->whereDate('next_due_date', '<=', now());
        $query->where('certificate_status', 1);
    }else{
         // Optional search filters
        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('f_name')) {
            $query->where('f_name', 'like', '%' . $request->f_name . '%');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('session')) {
            $query->where('session_id', $request->session);
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

        if ($request->filled('is_intern')) {
            $query->where('is_intern', $request->is_intern);
        }

        if ($request->filled('certificate_sent')) {
            $query->where('certificate_sent', $request->certificate_sent);
        }
        if ($request->filled('is_online')) {
            $query->where('is_online', $request->is_online);
        }

        // if ($request->filled('technology')) {
        //     $query->where('technology', $request->technology);
        // }

         if ($request->filled('technology')) {
            $query->whereRaw(
                "FIND_IN_SET(?, technology)",
                [$request->technology]
            );
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

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('registration_fee')) {
            $query->where('reg_fees', $request->registration_fee);
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
         
        $query->whereIn('certificate_status', [1, 2]);

    }

    // Always filter students with 0.00 pending fees
    // $query->where('pending_fees', 0.00);
    $activeSessionId = session('admin_session_id');
   
    
    if (auth()->user()->role == 1) {
         
        $query->where('session', $activeSessionId);
    }
    // $query->where('certificate_status', 1);
    $query->where('send_to_close', 0);
    

    // $query->where(function ($q) {
    //     // Always show certificate_status = 1
    //     $q->where('certificate_status', 1)

    //       // Show certificate_status = 2 only if student_consent is pending
    //       ->orWhere(function ($q2) {
    //           $q2->where('certificate_status', 2)
    //              ->where(function ($r) {
    //                  $r->whereNull('student_consent')
    //                    ->orWhere('student_consent', 0);
    //              });
    //       });
    // });
if (!$request->filled('fee_filter')) {
        // $query->orderBy('id', 'desc');
        $query->latest('updated_at');
    }

    $students = $query->get();

    // $students    = $query->latest()->get();
    $sessions    = StudentSession::all();
    // $colleges    = \App\Models\College::all();
    // $courses     = \App\Models\Course::all();
    $colleges = College::orderBy('college_name')->get();
    $courses = Course::orderBy('course_name')->get();
    $batches     = \App\Models\Batch::all();
    $users       = \App\Models\User::all();
    $departments = \App\Models\Department::all();
    $student_status = StudentStatus::all();

    $dismissed = session('dismiss_pending_fee');
    $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionId)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();

    return view('certificates.index', compact(
        'students',
        'sessions',
        'colleges',
        'batches',
        'courses',
        'departments',
        'users',
        'pendingStudents',
        'student_status'
    ));
}


    // Show a single student (for view/edit)
    public function edit(Student $student)
    {   
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        $colleges    = College::orderBy('college_display_name', 'asc')->get();
        $courses     = Course::orderBy('course_name', 'asc')->get();
        $batches     = Batch::orderBy('batch_name', 'asc')->get();        
        $references  = Reference::orderBy('name', 'asc')->get();

        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();

        return view('certificates.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status','activeSession'));
        // return view('certificates.edit', compact('student'));
    }

    // Update student data
     public function update(Request $request, Student $student)
    {
        // dd($request->all());
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(
                trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->f_name))
            )
        ]);

        $validates = $request->validate([
            'student_name'   => 'required|string|max:255',
            // 'f_name'         => 'required|string|max:255',
            'f_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Remove "Mr." and validate remaining name
                    $nameOnly = trim(preg_replace('/^mr\.?\s*/i', '', $value));

                    if ($nameOnly === '') {
                        $fail('Father name is required.');
                    }
                }
            ],
            'sno'            => 'required|string|max:255',
            'email_id'       => 'nullable|email',
            // 'contact'        => 'required|string|max:15',
            'contact' => ['required', 'string', new NotBlockedNumber],
            'alternative_phone' => ['nullable', 'string', new NotBlockedNumber],

            'address'         => 'nullable|string',
            'certificate_issue_date'         => 'nullable|date',
            'gender'         => 'required|string',
            // 'college_name'   => 'required|string',   // not college_id
            // 'technology'     => 'required|string',   // not technology_id
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place'        => 'required_if:is_place,1|nullable|string',
            'is_place'    => 'nullable|boolean',
            'technology'   => 'nullable|array',
            'technology.*' => 'string',
            // 'batch_assign'   => 'required|string',   // not batch_id
            'reference'      => 'string',   // not reference_user
            'status'         => 'required|string',
            'duration'         => 'nullable|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'paid_fees'       => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',
            'send_to_close'         => 'required|boolean',
            'is_placed'         => 'required|boolean',
            'is_intern'         => 'required|boolean',
            'is_married'         => 'nullable|boolean',
            'is_online'         => 'nullable|boolean',
        ]);
        // dd('Passed validation', $validates);
         /**
     * 🔴 BUSINESS RULE CHECK
     * Only when send_to_close = 1
     */
         if (($validates['reg_fees'] + $validates['paid_fees']) > $validates['total_fees']) {
            return back()
                ->withErrors([
                    'paid_fees' => 'Registration fees + Paid fees cannot be greater than Total fees.',
                ])
                ->withInput();
        }
        
        $validates['student_name'] = Str::of($validates['student_name'])->trim()->lower();
        $validates['f_name']       = Str::of($validates['f_name'])->trim()->lower();

        $activeSessionId = session('admin_session_id');
        if (!empty($validates['contact'])) {
            $contactExists = Student::withTrashed()
                ->where('student_name', $validates['student_name'])
                ->where('f_name', $validates['f_name'])
                ->where('contact', $validates['contact'])
                ->where('session', $activeSessionId)
                ->where('id', '!=', $student->id) // 👈 ignore current record
                ->exists();

            if ($contactExists) {
                return back()
                    ->withErrors([
                        'contact' => 'This student name with this contact already exists in this session'
                    ])
                    ->withInput();
            }
        }

        


        // old working

        // if ($validates['send_to_close'] == 1) {

        //     if (
        //         ($student->email_count_confirmation ?? 0) <= 0 ||
        //         ($student->email_count_certificate ?? 0) <= 0 ||
        //         ($student->count_receipt_download ?? 0) <= 0 ||
        //         ($student->pending_fees ?? 0) > 0
        //     ) {
        //         return back()
        //             ->withInput()
        //             ->with('error', 'Student cannot be sent to close. Please ensure:
        //                 • Confirmation email sent
        //                 • Certificate email sent
        //                 • Receipt downloaded
        //                 • No pending fees');
        //     }

        //     // All checks passed
        //     $validates['certificate_status'] = 3;

        // } else {
        //     $validates['certificate_status'] = 2;
        // }

        // Force lowercase before saving
        // $validates['student_name'] = Str::lower($validates['student_name']);
        // $validates['f_name']       = Str::lower($validates['f_name']);

        $validates['paid_fees'] = $validates['paid_fees'] ?? 0;
        $validates['reg_fees'] = $validates['reg_fees'] ?? 0;

        $validates['pending_fees'] = max(
            $validates['total_fees'] - $validates['reg_fees'] - $validates['paid_fees'],
            0
        );

        // if ($validates['send_to_close'] == 1) {
        //     $validates['certificate_status'] = 3;
        // } else {
        //     $validates['certificate_status'] = 2;
        // }

        // dd($validates);
        $student->update($validates);

        if ($validates['send_to_close'] == 1) {

            if (
                ($student->pending_fees ?? 0) > 0
            ) {
                return back()
                    ->withInput()
                    ->with('error', 'Student cannot be sent to close. Please ensure:
                        • No pending fees');
            }

            // All checks passed
            $validates['certificate_status'] = 3;

        } else {
            $validates['certificate_status'] = 2;
        }

        return redirect()->route('certificates.index')
                        ->with('success','Student updated successfully');
    }

    public function updateold(Request $request, Student $student)
    {
        $request->validate([
            'student_name'   => 'required|string|max:255',
            'father_name'    => 'required|string|max:255',
            'email'          => 'required|email|unique:students,email,' . $student->id,
            'contact_no'     => 'nullable|string|max:15',
            'department'     => 'required|string',
            'session_name'   => 'required|string',
            'pending_fees'   => 'nullable|numeric',
        ]);

        $student->update($request->only([
            'student_name',
            'father_name',
            'email',
            'contact_no',
            'department',
            'session_name',
            'pending_fees'
        ]));

        return redirect()->route('certificates.index')
                         ->with('success', 'Student data updated successfully');
    }

    public function toggleCertificateSent(Request $request)
    {
        $student = Student::findOrFail($request->id);

        $student->certificate_sent = $request->status;

        $student->save();

        return response()->json([
            'success' => true,
            'message' => $request->status
                ? 'Certificate marked as sent'
                : 'Certificate marked as not sent'
        ]);
    }

    public function bulkCertificateStatus(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');

        if (empty($idsPayload)) {

            return back()->with(
                'error',
                'No students selected.'
            );
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload)
            ? $idsPayload
            : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {

            return back()->with(
                'error',
                'Invalid selection.'
            );
        }

        Student::whereIn('id', $ids)
            ->update([
                'certificate_sent' => $request->status
            ]);

        return back()->with(
            'success',
            $request->status
                ? 'Certificates marked as sent.'
                : 'Certificates marked as not sent.'
        );
    }
}
