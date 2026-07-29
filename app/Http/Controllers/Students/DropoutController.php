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
use Illuminate\Support\Facades\View;
use ZipArchive;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentListExport;
use Illuminate\Support\Str;
use App\Imports\StudentsFeeImport;
use App\Exports\StudentsFeeTemplateExport;
use App\Models\Placement;
use App\Rules\NotBlockedNumber;

class DropoutController extends Controller
{
    protected string $permissionPrefix = 'dropout_students';

    protected array $permissionMap = [
        'index'  => 'view',
        'edit'   => 'edit',
        'update' => 'edit',
        'destroy'=> 'delete',
    ];

    // public function __construct()
    // {
    //     $this->middleware('auth');

    //     foreach ($this->permissionMap as $method => $action) {
    //         $this->middleware(
    //             "permission:{$this->permissionPrefix}.{$action}"
    //         )->only($method);
    //     }
    // }

    /**
     * Display Dropout Students
     */
     // List students
    public function index(Request $request)
    {   
        $query = Student::query()->withTrashed();
        
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
            if ($request->filled('is_intern')) {
                $query->where('is_intern', $request->is_intern);
            }
            if ($request->filled('is_online')) {
                $query->where('is_online', $request->is_online);
            }

            if ($request->filled('confirmation_sent')) {
                $query->where('confirmation_sent', $request->confirmation_sent);
            }

            if ($request->filled('registration_fee')) {
                $query->where('reg_fees', $request->registration_fee);
            }
             

            if ($request->filled('technology')) {
                $query->whereRaw(
                    "FIND_IN_SET(?, technology)",
                    [$request->technology]
                );
            }

           
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
                    case 'not_paid':
                        $query->where('paid_fees', "<", 1);
                        break;
                    case 'pending':
                        $query->where('pending_fees', '>', 0);
                        break;

                    case 'pending_high':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'desc');
                        break;

                    case 'pending_low':
                        // $query->where('pending_fees', '>=', 0)
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
                // $query->where('gender', $request->gender);
            }

            if ($request->filled('next_due_date')) {
                $query->whereDate('next_due_date', $request->next_due_date)
                      ->where('pending_fees', '>', 0);
            }
            
            if (auth()->user()->role == 1) {
                $activeSessionId = session('admin_session_id');
                $query->where('session', $activeSessionId);
            }
            
        
        
            
            $query->where('certificate_status', 4);

            if (!$request->filled('fee_filter')) {
                // $query->orderBy('id', 'desc');
                 $query->latest('updated_at');
            } 

            if ($request->filled('limit')) {
                $query->limit($request->limit);
            }
            $students = $query->get();
            // dd($students);
            // $students = $query->orderBy('id', 'desc')->get();

            // $students = $query->paginate(10);

        $sessions = StudentSession::all();
        // $colleges = College::all();
        $colleges = College::orderBy('college_name')->get();
        $courses = Course::orderBy('course_name')->get();
        // $courses = Course::all();
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
        

        return view('dropout_students.index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }

    // Show edit form
    public function edit(Student $student)
    {   
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        $colleges    = College::orderBy('college_display_name', 'asc')->get();
        $courses     = Course::orderBy('course_name', 'asc')->get();
        // $batches     = Batch::orderBy('batch_name', 'asc')->get();  
        $batches = Batch::where('session_name', $activeSessionId)
                ->orderBy('batch_name', 'asc')
                ->get();      
        $references  = Reference::orderBy('name', 'asc')->get();

        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();

        return view('students.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status','activeSession'));
        // return view('students.edit', compact('student','sessions','colleges','courses','batches','department','references','users'));
    }

    public function update(Request $request, Student $student)
    {
        // dd($request->all());
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(
                trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->f_name))
            )
        ]);

        if ($request->filled('password')) {
            $request->merge([
                'password' => $request->password
            ]);
        }
        
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
            // 'email_id'       => 'required|email|unique:students_detail,email_id,'.$student->id,
            'email_id'       => 'nullable',
            // 'contact'        => 'required|string|max:15',
            'contact' => ['nullable', 'string', new NotBlockedNumber],
            'alternative_phone' => ['nullable', 'string', new NotBlockedNumber],

            'address'         => 'nullable|string',
            'gender'         => 'required|string',
            // 'college_name'   => 'required|string',   // not college_id
            // 'session'        => 'required|string',   // not session_id
            // 'technology'     => 'required|string',   // not technology_id
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place'        => 'required_if:is_place,1|nullable|string',
            'is_place'    => 'nullable',
            'technology'   => 'required|array',
            'technology.*' => 'string',

            'batch_assign'   => 'nullable|string',   // not batch_id
            'reference'      => 'nullable|string',   // not reference_user
            'status'         => 'required|string',
            'duration'         => 'nullable|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'paid_fees'       => 'nullable|numeric',
            // 'pending_fees'   => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',
            'is_intern'         => 'required|boolean',
            'is_married'         => 'nullable|boolean',
            'is_online'         => 'nullable|boolean',
            'password' => 'nullable|min:6',
        ]);
        // dd('Passed validation', $validates);

        if (($validates['reg_fees'] + $validates['paid_fees']) > $validates['total_fees']) {
            return back()
                ->withErrors([
                    'paid_fees' => 'Registration fees + Paid fees cannot be greater than Total fees.',
                ])
                ->withInput();
        }
        // Force lowercase before saving
        

        $validates['student_name'] = Str::of($validates['student_name'])->trim()->lower();
        $validates['f_name']       = Str::of($validates['f_name'])->trim()->lower();

        $activeSessionId = session('admin_session_id');
        if (!empty($validates['contact'])) {
            $contactExists = Student::query()
                ->where('student_name', $validates['student_name'])
                ->where('f_name', $validates['f_name'])
                ->where('contact', $validates['contact'])
                ->where('session', $activeSessionId)
                ->where('id', '!=', $student->id) // 👈 ignore current record
                ->exists();

            if ($contactExists) {
                return back()
                    ->withErrors([
                        // 'contact' => 'This student name with this contact already exists in this session'
                        'contact' => 'A student with the same name, father name and contact already exists in the current session.'
                    ])
                    ->withInput();
            }
        }

        
        $validates['paid_fees'] = $validates['paid_fees'] ?? 0;
        $validates['reg_fees'] = $validates['reg_fees'] ?? 0;

        $validates['pending_fees'] = max(
            $validates['total_fees'] - $validates['reg_fees'] - $validates['paid_fees'],
            0
        );
        unset($validates['password']);
        if ($request->filled('password')) {
            $validates['plain_password'] = $validates['password'] = trim($request->password);
        }
        // dd($validates);
        $student->update($validates);

        return redirect()->route('students.index')
                        ->with('success','Student updated successfully');
    }

    public function oldexportExcel(Request $request)
    {
         
        // $date = strtolower(now()->format('d_F'));

        // default filename
        // $fileName = 'students_list_' . $date;
        // if ($request->filled('technology')) {

        //     $fileName = 'students_technology_' . $date;
        // }
        // if ($request->filled('gender')) {

        //     $fileName = 'students_' . $request->gender . '_' . $date;
        // }
        // // change filename based on fee filter
        // if ($request->filled('fee_filter')) {

        //     $fileName = 'students_' . $request->fee_filter . '_' . $date;
        // }

        $date = strtolower(now()->format('d_F'));

        $parts = ['students'];

        // Basic filters
        if ($request->filled('student_name')) {
            $parts[] = Str::slug($request->student_name, '_');
        }

        if ($request->filled('f_name')) {
            $parts[] = 'fname_' . Str::slug($request->f_name, '_');
        }

        if ($request->filled('sno')) {
            $parts[] = 'sno_' . $request->sno;
        }

        if ($request->filled('gender')) {
            $parts[] = strtolower($request->gender);
        }

        if ($request->filled('session')) {
            $parts[] = 'session_' . $request->session;
        }

        if ($request->filled('college_name')) {
            $parts[] = Str::slug($request->college_name, '_');
        }

        if ($request->filled('email_id')) {
            $parts[] = 'email';
        }

        // Status / flags
        if ($request->filled('status')) {
            $parts[] = strtolower($request->status);
        }

        if ($request->filled('is_intern')) {
            $parts[] = $request->is_intern ? 'intern' : 'non_intern';
        }

        if ($request->filled('is_online')) {
            $parts[] = $request->is_online ? 'online' : 'offline';
        }

        // Fees
        if ($request->filled('registration_fee')) {
            $parts[] = 'fee_' . $request->registration_fee;
        }

        if ($request->filled('fee_filter')) {
            $parts[] = $request->fee_filter;
        }

        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $parts[] = 'pendingfees';
        }

        // Technology
        if ($request->filled('technology')) {
            $parts[] = Str::slug($request->technology, '_');
        }

         

        // Offers
        if ($request->filled('part_time_offer')) {
            $parts[] = 'parttime';
        }

        if ($request->filled('placement_offer')) {
            $parts[] = 'placement';
        }

        if ($request->filled('pg_offer')) {
            $parts[] = 'pg';
        }

        if ($request->filled('confirmation_sent')) {
            $parts[] = 'confirmation_sent';
        }

        if ($request->filled('certificate_sent')) {
            $parts[] = 'certificate_sent';
        }

         

         
        // Final filename
        $fileName = implode('_', $parts) . '_' . $date . '.xlsx';

        // Prevent too long filename
        $fileName = substr($fileName, 0, 150);


        return Excel::download(
            new StudentListExport($request),
             $fileName
        );
    }


    // Add to dropout
    public function moveToConfirmation(Request $request)
    {
        // dd($request);
        $request->validate([
            'ids' => 'required'
        ]);

        // Decode safely
        $ids = json_decode($request->ids, true);
        // dd($ids);
        // If empty / invalid JSON
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No students selected.');
        }

        // Get only existing IDs
        $validIds = Student::withTrashed()->whereIn('id', $ids)->pluck('id')->toArray();

        // If no valid students found
        if (empty($validIds)) {
            return back()->with('error', 'Selected students do not exist.');
        }

         
        // Student::whereIn('id', $validIds)->update([
        //     'certificate_status' => 0,
        //     'deleted_at' => null
        // ]);

        Student::withTrashed()
            ->whereIn('id', $validIds)
            ->update([
                'certificate_status' => 0,
                'deleted_at' => null,
            ]);

        return back()->with('success', 'Students moved to Confirmation successfully.');
    }

    // Add to dropout
    public function moveToCertificate(Request $request)
    {
        // dd($request);
        $request->validate([
            'ids' => 'required'
        ]);

        // Decode safely
        $ids = json_decode($request->ids, true);
        // dd($ids);
        // If empty / invalid JSON
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No students selected.');
        }

        // Get only existing IDs
        $validIds = Student::withTrashed()->whereIn('id', $ids)->pluck('id')->toArray();

        // If no valid students found
        if (empty($validIds)) {
            return back()->with('error', 'Selected students do not exist.');
        }

         
        // Student::whereIn('id', $validIds)->update([
        //     'certificate_status' => 0,
        //     'deleted_at' => null
        // ]);

        Student::withTrashed()
            ->whereIn('id', $validIds)
            ->update([
                'certificate_status' => 1,
                'deleted_at' => null,
            ]);

        return back()->with('success', 'Students moved to Certificate successfully.');
    }


    public function exportExcel(Request $request)
    {
         
        // $date = strtolower(now()->format('d_F'));

        // default filename
        // $fileName = 'students_list_' . $date;
        // if ($request->filled('technology')) {

        //     $fileName = 'students_technology_' . $date;
        // }
        // if ($request->filled('gender')) {

        //     $fileName = 'students_' . $request->gender . '_' . $date;
        // }
        // // change filename based on fee filter
        // if ($request->filled('fee_filter')) {

        //     $fileName = 'students_' . $request->fee_filter . '_' . $date;
        // }

        $date = strtolower(now()->format('d_F'));

        $parts = ['dropout_students'];

        // Basic filters
        if ($request->filled('student_name')) {
            $parts[] = Str::slug($request->student_name, '_');
        }

        if ($request->filled('f_name')) {
            $parts[] = 'fname_' . Str::slug($request->f_name, '_');
        }

        if ($request->filled('sno')) {
            $parts[] = 'sno_' . $request->sno;
        }

        if ($request->filled('gender')) {
            $parts[] = strtolower($request->gender);
        }

        if ($request->filled('session')) {
            $parts[] = 'session_' . $request->session;
        }

        if ($request->filled('college_name')) {
            $parts[] = Str::slug($request->college_name, '_');
        }

        if ($request->filled('email_id')) {
            $parts[] = 'email';
        }

        // Status / flags
        if ($request->filled('status')) {
            $parts[] = strtolower($request->status);
        }

        if ($request->filled('is_intern')) {
            $parts[] = $request->is_intern ? 'intern' : 'non_intern';
        }

        if ($request->filled('is_online')) {
            $parts[] = $request->is_online ? 'online' : 'offline';
        }

        // Fees
        if ($request->filled('registration_fee')) {
            $parts[] = 'fee_' . $request->registration_fee;
        }

        if ($request->filled('fee_filter')) {
            $parts[] = $request->fee_filter;
        }

        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $parts[] = 'pendingfees';
        }

        // Technology
        if ($request->filled('technology')) {
            $parts[] = Str::slug($request->technology, '_');
        }

         

        // Offers
        if ($request->filled('part_time_offer')) {
            $parts[] = 'parttime';
        }

        if ($request->filled('placement_offer')) {
            $parts[] = 'placement';
        }

        if ($request->filled('pg_offer')) {
            $parts[] = 'pg';
        }

        if ($request->filled('confirmation_sent')) {
            $parts[] = 'confirmation_sent';
        }

        if ($request->filled('certificate_sent')) {
            $parts[] = 'certificate_sent';
        }

         

         
        // Final filename
        $fileName = implode('_', $parts) . '_' . $date . '.xlsx';

        // Prevent too long filename
        $fileName = substr($fileName, 0, 150);

        $request->merge([
            'certificate_status' => 4,
        ]);
        return Excel::download(
            new StudentListExport($request),
             $fileName
        );
    }

}