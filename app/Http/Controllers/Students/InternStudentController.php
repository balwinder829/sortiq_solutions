<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\InternStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InternStudentListExport;
use App\Imports\InternStudentsImport;
use Throwable;
use App\Models\College;
use App\Models\Batch;
use App\Models\Course;
use App\Rules\NotBlockedNumber;
use App\Models\BlockedNumber;
use App\Models\StudentAdditionalLetter;
use App\Models\StudentSession;
use Mpdf\Mpdf;
use Carbon\Carbon;
use ZipArchive;

use Illuminate\Support\Facades\View;

class InternStudentController extends Controller
{
    use \App\Traits\PdfLayoutTrait;
    protected string $permissionPrefix = 'intern_students';

    protected array $permissionMap = [
         'index'         => 'view',
        'create'        => 'create',
        'store'         => 'create',
        'edit'          => 'edit',
        'update'        => 'edit',
        'destroy'       => 'delete',
        'bulkDelete'    => 'delete',
        'importForm'    => 'import',
        'import'        => 'import',
        'exportExcel'   => 'view',
        'moveOne'       => 'view',
        'moveSelected'  => 'view',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /**
     * Columns that must never be transferred/imported into intern_students.
     */
    private array $excludedFields = [
        'id',
        'total_fees',
        'reg_fees',
        'paid_fees',
        'pending_fees',
        'next_due_date',
        'reg_due_amount',
        'due_date',
        'deleted_at',
        'password',
        'plain_password',
        'remember_token',
        'last_login',
    ];

    /**
     * Apply all listing filters in one place.
     * The same query is used for listing and filtered Excel export.
     */
    private function filteredQuery(Request $request)
    {
        $query = InternStudent::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")
                    ->orWhere('email_id', 'like', "%{$search}%")
                    ->orWhere('sno', 'like', "%{$search}%")
                    ->orWhere('college_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('student_name')) {
            $query->where(
                'student_name',
                'like',
                '%' . trim($request->student_name) . '%'
            );
        }

        if ($request->filled('f_name')) {
            $query->where(
                'f_name',
                'like',
                '%' . trim($request->f_name) . '%'
            );
        }

        if ($request->filled('sno')) {
            $query->where('sno', $request->sno);
        }

        if ($request->filled('contact')) {
            $query->where('contact', 'like', '%' . trim($request->contact) . '%');
        }

        if ($request->filled('email_id')) {
            $query->where('email_id', 'like', '%' . trim($request->email_id) . '%');
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('technology')) {
            $technology = $request->technology;

            if (is_array($technology)) {
                $query->where(function ($q) use ($technology) {
                    foreach ($technology as $item) {
                        $q->orWhereRaw('FIND_IN_SET(?, technology)', [$item]);
                    }
                });
            } else {
                $query->whereRaw('FIND_IN_SET(?, technology)', [$technology]);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        if ($request->filled('join_date')) {
            $query->whereDate('join_date', $request->join_date);
        }

        foreach ([
            'is_intern',
            'is_online',
            'is_married',
            'is_place',
            'certificate_status',
            'certificate_sent',
            'confirmation_sent',
            'student_consent',
        ] as $flag) {
            if ($request->filled($flag)) {
                $query->where($flag, $request->input($flag));
            }
        }

        if ($request->filled('reference')) {
            $query->where('reference', $request->reference);
        }

        if ($request->filled('referred_by')) {
            if ($request->referred_by === 'direct') {
                $query->whereNull('referred_by');
            } else {
                $query->where('referred_by', $request->referred_by);

                if ($request->filled('referred_by_type')) {
                    $query->where(
                        'referred_by_type',
                        $request->referred_by_type
                    );
                }
            }
        }

         $activeSessionId = session('admin_session_id');
         $query->where('session', $activeSessionId);

        if ($request->filled('limit')) {
            $limit = (int) $request->limit;

            if ($limit > 0) {
                $query->limit(min($limit, 5000));
            }
        }

        return $query;
    }

    /**
     * Intern Students listing with filters and pagination.
     */
    

/**
 * Intern Students listing.
 * DataTables handles pagination on the complete filtered result set.
 */
public function index(Request $request)
{
    $internStudents = $this->filteredQuery($request)
        ->with(['collegeData', 'batchData'])
        ->orderByDesc('id')
        ->get();


     

        $collegeSessionId = $request->filled('session')
            ? $request->session
            : session('admin_session_id');

        $collegeIds = InternStudent::query()
            ->where('session', $collegeSessionId)
            ->whereNotNull('college_name')
            ->where('college_name', '!=', '')
            ->distinct()
            ->pluck('college_name')
            ->filter()
            ->unique()
            ->values();

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get();
     

         $technologySessionId = $request->filled('session')
    ? $request->session
    : session('admin_session_id');

$technologyIds = InternStudent::query()
    ->where('session', $technologySessionId)
    ->whereNotNull('technology')
    ->where('technology', '!=', '')
    ->pluck('technology')
    ->flatMap(function ($value) {
        return array_map('trim', explode(',', $value));
    })
    ->filter(fn ($id) => $id !== '' && is_numeric($id))
    ->unique()
    ->values();

$technologies = Course::query()
    ->whereIn('id', $technologyIds)
    ->orderBy('course_name')
    ->get(['id', 'course_name']);

    

    return view('intern-students.index', compact(
        'internStudents',
        
        'colleges',
        'technologies'
    ));
}


    /**
     * Manual creation form.
     */
    public function create()
    {
        $activeSessionId = session('admin_session_id');
        $activeSession = \App\Models\StudentSession::find($activeSessionId);
        $sessions = \App\Models\StudentSession::all();

        $colleges = \App\Models\College::where('status', 'active')
            ->orderBy('college_display_name', 'asc')->get();
        $batches = \App\Models\Batch::where('session_name', $activeSessionId)
            ->where('status', 'active')->whereNull('deleted_at')
            ->orderBy('batch_name', 'asc')->get();
        $courses = \App\Models\Course::orderBy('course_name', 'asc')->get();
        $references = \App\Models\Reference::orderBy('name', 'asc')->get();
        $course_duration = \App\Models\Duration::all();
        $student_status = \App\Models\StudentStatus::all();
        $departments = \App\Models\Department::orderBy('name', 'asc')->get();

        // Keep the same serial-number source and display format as the Student module.
        $lastStudent = \App\Models\Student::orderBy('id', 'desc')->first();
        if (!empty($lastStudent) && !empty($lastStudent->sno)) {
            $lastSnArr = explode('-', $lastStudent->sno);
            $lastSn = end($lastSnArr);
            $nextSno = !empty($lastSn) ? str_pad(((int) $lastSn) + 1, 5, '0', STR_PAD_LEFT) : 2000;
        } else {
            $nextSno = 1;
        }

        $defaultTechnology = \App\Models\Course::where('course_name', 'Not Selected')->value('id');
        $salesStaff = DB::table('sales_staff')->where('status', 'active')
            ->whereNull('deleted_at')->orderBy('name')->get();

        return view('intern-students.create', compact(
            'sessions', 'activeSession', 'colleges', 'courses', 'batches',
            'references', 'course_duration', 'student_status', 'nextSno',
            'defaultTechnology', 'salesStaff', 'departments'
        ));
    }

    /**
     * Manually create an intern student.
     */
    public function store(Request $request)
{
    // Normalize Father/Husband name prefix.
    $request->merge([
        'f_name' => 'Mr. ' . ucwords(
            trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->input('f_name', '')))
        ),
    ]);

    $validated = $request->validate([
        'student_name' => ['required', 'string', 'max:255'],

        'f_name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                $name = trim(preg_replace('/^mr\.?\s*/i', '', $value));

                if ($name === '') {
                    $fail('Father name is required.');
                }
            },
        ],

        'sno' => ['nullable', 'string', 'max:255'],
        'email_id' => ['nullable', 'email', 'max:255'],
        'contact' => ['nullable', 'string', new NotBlockedNumber],
        'alternative_phone' => ['nullable', 'string', new NotBlockedNumber],
        'address' => ['nullable', 'string'],
        'gender' => ['required', 'string', 'max:50'],

        // College dropdown submits the selected college ID.
        'college_name' => 'nullable',
        'place' => ['required_if:is_place,1', 'nullable', 'string', 'max:255'],
        'is_place' => ['nullable', 'boolean'],
        'is_married' => ['nullable', 'boolean'],

        // Technology is a multi-select.
        'technology' => ['nullable', 'array'],
        'technology.*' => ['required', 'string', 'max:255'],

        // 'status' => ['required', 'string', 'max:100'],
        'duration' => ['nullable', 'string', 'max:100'],
        'batch_assign' => ['nullable', 'string', 'max:255'],

        // 'total_fees' => ['required', 'numeric', 'min:0', 'max:200000'],
        // 'reg_fees' => ['required', 'numeric', 'min:0'],
        // 'paid_fees' => ['required', 'numeric', 'min:0'],
        // 'pending_fees' => ['nullable', 'numeric', 'min:0'],
        // 'next_due_date' => ['nullable', 'date', 'after:join_date'],

        'join_date' => ['nullable', 'date'],
        'start_date' => ['nullable', 'date', 'after_or_equal:join_date'],
        'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],

        'part_time_offer' => ['nullable', 'boolean'],
        'placement_offer' => ['nullable', 'boolean'],
        'pg_offer' => ['nullable', 'boolean'],
        'is_online' => ['nullable', 'boolean'],

        // Optional fields used only by Offer / Strict Offer Letter.
        'offer_training_start_date' => ['required', 'date'],
        'offer_training_duration_months' => ['required', 'integer', 'min:1'],
        'internship_type' => ['required', 'string', 'max:100'],
        'offer_ppo_evaluation_months' => ['required', 'integer', 'min:1', 'max:24'],
        'offer_probation_duration_months' => ['nullable', 'integer', 'min:1'],
        'offer_working_hours' => ['nullable', 'string', 'max:100'],
        'offer_service_agreement_duration' => ['nullable', 'string', 'max:100'],
        'offer_annual_salary' => ['nullable', 'numeric', 'min:0'],

        'referred_by' => ['nullable', 'integer'],
        'referred_by_type' => ['nullable', Rule::in(['staff', 'reference'])],
    ]);

    // Validate fee calculation.
    // if (
    //     ($validated['reg_fees'] + $validated['paid_fees'])
    //     > $validated['total_fees']
    // ) {
    //     return back()
    //         ->withErrors([
    //             'paid_fees' =>
    //                 'Registration fees + Paid fees cannot be greater than Total fees.',
    //         ])
    //         ->withInput();
    // }

    // Use the admin's currently selected session.
    $activeSessionId = session('admin_session_id');

    if (!$activeSessionId) {
        return back()
            ->withErrors([
                'session' => 'No active session found. Please select an active session.',
            ])
            ->withInput();
    }

    // Normalize names.
    $validated['student_name'] = Str::lower(
        trim($validated['student_name'])
    );

    $validated['f_name'] = Str::lower(
        trim($validated['f_name'])
    );

    // Prevent duplicate records in the same session.
    if (!empty($validated['contact'])) {
        $duplicate = InternStudent::query()
            ->where('student_name', $validated['student_name'])
            ->where('f_name', $validated['f_name'])
            ->where('contact', $validated['contact'])
            ->where('session', $activeSessionId)
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors([
                    'contact' =>
                        'An intern student with the same name, father name and contact already exists in this session.',
                ])
                ->withInput();
        }
    }

    // Validate referral according to its selected type.
    if (!empty($validated['referred_by'])) {
        if (($validated['referred_by_type'] ?? '') === 'staff') {
            $request->validate([
                'referred_by' => ['exists:sales_staff,id'],
            ]);
        } elseif (($validated['referred_by_type'] ?? '') === 'reference') {
            $request->validate([
                'referred_by' => ['exists:references,id'],
            ]);
        } else {
            return back()
                ->withErrors([
                    'referred_by_type' => 'Please select a valid referral type.',
                ])
                ->withInput();
        }
    } else {
        $validated['referred_by'] = null;
        $validated['referred_by_type'] = null;
    }

    // Generate the serial number from the Intern Students table,
    // independently of the main Student table.
    // $lastSno = InternStudent::query()
    //     ->orderByDesc('id')
    //     ->value('sno');

    // if (!empty($lastSno)) {
    //     $parts = explode('-', $lastSno);
    //     $lastNumber = (int) end($parts);
    //     $nextNumber = $lastNumber + 1;
    // } else {
    //     $nextNumber = 2000;
    // }

    // $newSno = 'SN-' . now()->format('Ym') . '-'
    //     . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    // Calculate pending fees on the server.
    // $validated['paid_fees'] = $validated['paid_fees'] ?? 0;

    // $validated['pending_fees'] = max(
    //     $validated['total_fees']
    //         - $validated['reg_fees']
    //         - $validated['paid_fees'],
    //     0
    // );

    // Store the current session and generated serial number.
    // $validated['sno'] = $newSno;
    $validated['session'] = $activeSessionId;

    // Convert multi-select technology IDs into the existing
    // comma-separated storage format.
    $validated['technology'] = !empty($validated['technology'])
        ? implode(',', $validated['technology'])
        : null;

    // Normalize checkbox values.
    foreach ([
        'is_place',
        'is_married',
        'is_online',
        'part_time_offer',
        'placement_offer',
        'pg_offer',
    ] as $field) {
        $validated[$field] = $request->boolean($field);
    }

    // Only save columns that actually exist in intern_students.
    // Unlike the previous prepareManualData() helper, this does not
    // automatically discard the fee fields.
    $columns = Schema::getColumnListing('intern_students');

    $data = array_intersect_key(
        $validated,
        array_flip($columns)
    );

    DB::transaction(function () use ($data) {
        InternStudent::create($data);
    });

    return redirect()
        ->route('intern-students.index')
        ->with('success', 'Intern student added successfully.');
}

    /**
     * Edit form.
     */
    public function edit(InternStudent $internStudent)
{
    $activeSessionId = session('admin_session_id');

    $activeSession = \App\Models\StudentSession::find($activeSessionId);
    $sessions = \App\Models\StudentSession::all();

    $colleges = \App\Models\College::where('status', 'active')
        ->orderBy('college_display_name', 'asc')
        ->get();

    $batches = \App\Models\Batch::where('session_name', $activeSessionId)
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->orderBy('batch_name', 'asc')
        ->get();

    $courses = \App\Models\Course::orderBy('course_name', 'asc')->get();

    $references = \App\Models\Reference::orderBy('name', 'asc')->get();

    $course_duration = \App\Models\Duration::all();

    $student_status = \App\Models\StudentStatus::all();

    $defaultTechnology = \App\Models\Course::where(
        'course_name',
        'Not Selected'
    )->value('id');

    $salesStaff = DB::table('sales_staff')
        ->where('status', 'active')
        ->whereNull('deleted_at')
        ->orderBy('name')
        ->get();

    return view('intern-students.edit', compact(
        'internStudent',
        'sessions',
        'activeSession',
        'colleges',
        'courses',
        'batches',
        'references',
        'course_duration',
        'student_status',
        'defaultTechnology',
        'salesStaff'
    ));
}

    /**
     * Update intern student.
     */
    public function update(Request $request, InternStudent $internStudent)
{

    // Keep the same Father/Husband name prefix behavior as Student.
    $request->merge([
        'f_name' => 'Mr. ' . ucwords(
            trim(preg_replace(
                '/^(mr\.?\s*)+/i',
                '',
                $request->input('f_name', '')
            ))
        ),
    ]);

    $validated = $request->validate([
        'student_name' => ['required', 'string', 'max:255'],
        'f_name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                $name = trim(preg_replace('/^mr\.?\s*/i', '', $value));

                if ($name === '') {
                    $fail('Father name is required.');
                }
            },
        ],

        'email_id' => ['nullable', 'email', 'max:255'],
        'contact' => ['nullable', 'string', new NotBlockedNumber],
        'alternative_phone' => ['nullable', 'string', new NotBlockedNumber],
        'gender' => ['required', 'string', 'max:50'],

        'college_name' => 'nullable',
        'place' => 'nullable',
        'is_place' => ['nullable', 'boolean'],
        'is_married' => ['nullable', 'boolean'],

        'technology' => ['nullable', 'array'],
        'technology.*' => ['required', 'string', 'max:255'],
 

        'duration' => ['nullable', 'string', 'max:100'],
        'batch_assign' => ['nullable', 'string', 'max:255'],
        'address' => ['nullable', 'string'],

        // 'total_fees' => ['required', 'numeric', 'min:0', 'max:200000'],
        // 'reg_fees' => ['required', 'numeric', 'min:0'],
        // 'paid_fees' => ['required', 'numeric', 'min:0'],
        // 'pending_fees' => ['nullable', 'numeric', 'min:0'],
        // 'next_due_date' => ['nullable', 'date'],

        'join_date' => ['nullable', 'date'],
        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],

        'part_time_offer' => ['nullable', 'boolean'],
        'placement_offer' => ['nullable', 'boolean'],
        'pg_offer' => ['nullable', 'boolean'],
        'is_online' => ['nullable', 'boolean'],

        // Optional fields used only by Offer / Strict Offer Letter.
        'offer_training_start_date' => ['required', 'date'],
        'offer_training_duration_months' => ['required', 'integer', 'min:1'],
        'internship_type' => ['required', 'string', 'max:100'],
        'offer_ppo_evaluation_months' => ['required', 'integer', 'min:1', 'max:24'],
        'offer_probation_duration_months' => ['nullable', 'integer', 'min:1'],
        'offer_working_hours' => ['nullable', 'string', 'max:100'],
        'offer_service_agreement_duration' => ['nullable', 'string', 'max:100'],
        'offer_annual_salary' => ['nullable', 'numeric', 'min:0'],

        'reference' => ['nullable', 'string', 'max:255'],
        'referred_by' => ['nullable', 'integer'],
        'referred_by_type' => ['nullable', Rule::in(['staff', 'reference'])],

        'is_intern' => ['nullable', 'boolean'],
        'certificate_status' => ['nullable', 'boolean'],
        'certificate_sent' => ['nullable', 'boolean'],
        'confirmation_sent' => ['nullable', 'boolean'],
        'student_consent' => ['nullable', 'boolean'],
    ]);

    // Validate fee totals.
    // if (
    //     $validated['reg_fees'] + $validated['paid_fees']
    //     > $validated['total_fees']
    // ) {
    //     return back()
    //         ->withErrors([
    //             'paid_fees' =>
    //                 'Registration fees + Paid fees cannot be greater than Total fees.',
    //         ])
    //         ->withInput();
    // }

    // Validate the referral against the selected source.
    if (!empty($validated['referred_by'])) {
        if (($validated['referred_by_type'] ?? '') === 'staff') {
            $request->validate([
                'referred_by' => ['exists:sales_staff,id'],
            ]);
        } elseif (($validated['referred_by_type'] ?? '') === 'reference') {
            $request->validate([
                'referred_by' => ['exists:references,id'],
            ]);
        } else {
            return back()
                ->withErrors([
                    'referred_by_type' => 'Please select a valid referral type.',
                ])
                ->withInput();
        }
    } else {
        $validated['referred_by'] = null;
        $validated['referred_by_type'] = null;
    }

    // Normalize names.
    $validated['student_name'] = Str::lower(trim($validated['student_name']));
    $validated['f_name'] = Str::lower(trim($validated['f_name']));

    // Prevent duplicate student details within the same session,
    // excluding the record currently being edited.
    if (!empty($validated['contact'])) {
        $duplicate = InternStudent::query()
            ->where('student_name', $validated['student_name'])
            ->where('f_name', $validated['f_name'])
            ->where('contact', $validated['contact'])
            ->where('session', $internStudent->session)
            ->where('id', '!=', $internStudent->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors([
                    'contact' =>
                        'An intern student with the same name, father name and contact already exists in this session.',
                ])
                ->withInput();
        }
    }

    // Calculate pending fees on the server.
    // $validated['pending_fees'] = max(
    //     $validated['total_fees']
    //         - $validated['reg_fees']
    //         - $validated['paid_fees'],
    //     0
    // );

    // Convert selected technology IDs to the existing CSV format.
    $validated['technology'] = !empty($validated['technology'])
        ? implode(',', $validated['technology'])
        : null;

    // Handle checkbox values even when unchecked.
    foreach ([
        'is_place',
        'is_married',
        'is_online',
        'part_time_offer',
        'placement_offer',
        'pg_offer',
        // 'is_intern',
        // 'certificate_status',
        // 'certificate_sent',
        // 'confirmation_sent',
        // 'student_consent',
    ] as $field) {
        $validated[$field] = $request->boolean($field);
    }

    // Never change the original serial number, source link or session
    // through this edit form.
    unset(
        // $validated['sno'],
        $validated['session'],
        $validated['source_student_id']
    );

    // Retain only columns that exist in intern_students.
    // This intentionally does not call prepareManualData(), because
    // that helper removes fee fields.
    $columns = Schema::getColumnListing('intern_students');

    $data = array_intersect_key(
        $validated,
        array_flip($columns)
    );

    DB::transaction(function () use ($internStudent, $data) {
        $internStudent->update($data);
    });


    return redirect()
        ->route('intern-students.index')
        ->with('success', 'Intern student updated successfully.');
}

    /**
     * Export Excel using the exact same filters as index().
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ]);

        $parts = ['intern_students'];

        foreach ([
            'student_name',
            'f_name',
            'sno',
            'gender',
            'session',
            'college_name',
            'status',
            'department',
        ] as $filter) {
            if ($request->filled($filter)) {
                $parts[] = Str::slug($filter . '_' . $request->input($filter), '_');
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'search_' . Str::slug($request->search, '_');
        }

        $fileName = substr(
            implode('_', $parts) . '_' . now()->format('d_F_Y') . '.xlsx',
            0,
            150
        );

        return Excel::download(
            new InternStudentListExport($this->filteredQuery($request)),
            $fileName
        );
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        return view('intern-students.import');
    }

    /**
     * Import intern students.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                Excel::import(
                    new InternStudentsImport(),
                    $request->file('file')
                );
            });

            return redirect()
                ->route('intern-students.index')
                ->with('success', 'Intern students imported successfully.');
        } catch (Throwable $e) {
            Log::error('Intern students import failed.', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'file' => 'Import failed. Check the file headers and data, then try again.',
            ]);
        }
    }

    /**
     * Transfer one student.
     */
    public function moveOne(Student $student)
    {
        return $this->transferStudents([$student->id]);
    }

    /**
     * Transfer selected students.
     */
    public function moveSelected(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);

        return $this->transferStudents(
            array_map('intval', $validated['ids'])
        );
    }

    /**
     * Atomic transfer from students_detail to intern_students.
     */
    private function transferStudents(array $ids)
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        try {
            $count = DB::transaction(function () use ($ids) {
                $students = Student::query()
                    ->whereIn('id', $ids)
                    ->lockForUpdate()
                    ->get();

                if ($students->count() !== count($ids)) {
                    throw new \RuntimeException(
                        'One or more source students were not found.'
                    );
                }

                $destinationColumns = Schema::getColumnListing('intern_students');

                foreach ($students as $student) {
                    $exists = InternStudent::withTrashed()
                        ->where('source_student_id', $student->id)
                        ->exists();

                    if ($exists) {
                        throw new \RuntimeException(
                            "Student ID {$student->id} was already transferred."
                        );
                    }

                    // Read raw DB values so the existing Student accessors
                    // do not alter the stored technology or password hash.
                    $sourceData = $student->getAttributes();

                    $data = collect($sourceData)
                        ->except($this->excludedFields)
                        ->all();

                    $data = array_intersect_key(
                        $data,
                        array_flip($destinationColumns)
                    );

                    unset($data['id'], $data['deleted_at']);

                    $data['source_student_id'] = $student->id;

                    if (in_array('created_at', $destinationColumns, true)) {
                        $data['created_at'] = now();
                    }

                    if (in_array('updated_at', $destinationColumns, true)) {
                        $data['updated_at'] = now();
                    }

                    // Insert destination first.
                    InternStudent::create($data);

                    // Then permanently delete the source.
                    $student->forceDelete();
                }

                return $students->count();
            });

            return back()->with(
                'success',
                "{$count} student(s) transferred to Intern Students successfully."
            );
        } catch (Throwable $e) {
            Log::error('Intern student transfer failed.', [
                'ids' => $ids,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Transfer failed. The database transaction was rolled back. Check the application log for details.'
            );
        }
    }

    /**
     * Delete one intern student.
     */
    public function destroy(InternStudent $internStudent)
    {
        $internStudent->delete();

        return back()->with(
            'success',
            'Intern student deleted successfully.'
        );
    }

    /**
     * Delete selected intern students.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('intern_students', 'id'),
            ],
        ]);

        $count = InternStudent::whereIn('id', $validated['ids'])->delete();

        return back()->with(
            'success',
            "{$count} intern student(s) deleted successfully."
        );
    }

    /**
     * Validation for manually entered records.
     */
    private function studentRules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'f_name' => ['nullable', 'string', 'max:255'],
            'sno' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:50'],
            'contact' => ['nullable', 'string', 'max:30'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'alternative_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'college_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:100'],
            'technology' => ['nullable'],
            'session' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'join_date' => ['nullable', 'date'],
            'status' => [
                'required',
                Rule::in([
                    'joined',
                    'dropout',
                    'certificate_only',
                    'shift_patiala',
                    'passout',
                ]),
            ],
            'reference' => ['nullable', 'string', 'max:255'],
            'referred_by' => ['nullable', 'string', 'max:255'],
            'referred_by_type' => ['nullable', 'string', 'max:100'],
            'is_intern' => ['nullable', 'boolean'],
            'is_married' => ['nullable', 'boolean'],
            'is_online' => ['nullable', 'boolean'],
            'is_place' => ['nullable', 'boolean'],
            'certificate_status' => ['nullable', 'boolean'],
            'certificate_sent' => ['nullable', 'boolean'],
            'confirmation_sent' => ['nullable', 'boolean'],
            'student_consent' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Prepare manually submitted fields.
     */
    private function prepareManualData(array $validated): array
    {
        if (isset($validated['technology']) && is_array($validated['technology'])) {
            $validated['technology'] = implode(',', $validated['technology']);
        }

        unset(
            $validated['source_student_id'],
            $validated['total_fees'],
            $validated['reg_fees'],
            $validated['paid_fees'],
            $validated['pending_fees'],
            $validated['next_due_date'],
            $validated['reg_due_amount'],
            $validated['due_date']
        );

        $columns = Schema::getColumnListing('intern_students');

        return array_intersect_key($validated, array_flip($columns));
    }

    public function downloadLetters(Request $request)
    {
        // Accept IDs as either an array or JSON string.
        if (is_string($request->ids)) {
            $request->merge([
                'ids' => json_decode($request->ids, true),
            ]);
        }

        $validated = $request->validate([
            'letter_type' => [
                'required',
                Rule::in([
                    'confirmation_letter',
                    'internship_confirmation_letter',
                    'offer',
                    'strict_offer_letter',
                ]),
            ],
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['required', 'integer', 'distinct', 'min:1'],
            'is_monthly' => ['nullable', 'boolean'],
            'is_logo_show' => ['nullable', 'boolean'],
        ]);

        $letterType = $validated['letter_type'];
        $ids = array_map('intval', $validated['ids']);

        $students = InternStudent::with([
            'collegeData',
            'batchData',
        ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if ($students->count() !== count($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'One or more selected intern students were not found.',
            ], 422);
        }

        // Keep the selected IDs in the same order.
        $students = collect($ids)->map(function ($id) use ($students) {
            return $students->get($id);
        });

        /*
        |--------------------------------------------------------------------------
        | Prepare data and validate all students before generating PDFs
        |--------------------------------------------------------------------------
        */

        $validationErrors = [];

        foreach ($students as $student) {

            $missing = [];

            if (blank($student->student_name)) {
                $missing[] = 'Student Name';
            }

            if (blank($student->f_name)) {
                $missing[] = 'Father Name';
            }

            if (blank($student->gender)) {
                $missing[] = 'Gender';
            }

            if (in_array($letterType, [
                'confirmation_letter',
                'internship_confirmation_letter',
            ])) {
                if (blank($student->start_date)) {
                    $missing[] = 'Start Date';
                }

                if (blank($student->end_date)) {
                    $missing[] = 'End Date';
                }

                if (!$student->is_place && !$student->collegeData) {
                    $missing[] = 'College';
                }

                if ($student->is_place && blank($student->place)) {
                    $missing[] = 'Place';
                }
            }

            if ($letterType === 'offer') {
                $normalOfferFields = [
                    'offer_training_start_date' => 'Internship Start Date',
                    'offer_training_duration_months' => 'Internship Duration',
                    'internship_type' => 'Internship Type',
                    'offer_ppo_evaluation_months' => 'PPO Evaluation Period (Months)',
                ];

                foreach ($normalOfferFields as $field => $label) {
                    if (blank($student->{$field})) {
                        $missing[] = $label;
                    }
                }
            }

            if ($letterType === 'strict_offer_letter') {
                $strictFields = [
                    'offer_training_start_date' => 'Training Start Date',
                    'offer_training_duration_months' => 'Training Duration',
                    'offer_probation_duration_months' => 'Probation Duration',
                    'offer_working_hours' => 'Working Hours',
                    'offer_service_agreement_duration' => 'Service Agreement Duration',
                    'offer_annual_salary' => 'Annual Salary / Package',
                ];

                foreach ($strictFields as $field => $label) {
                    if (blank($student->{$field})) {
                        $missing[] = $label;
                    }
                }
            }

            if (!empty($missing)) {
                $validationErrors[] =
                    ($student->student_name ?: 'Student ID: ' . $student->id)
                    . ': ' . implode(', ', $missing);
            }
        }

        if (!empty($validationErrors)) {
                return redirect()
                    ->route('intern-students.index')
                    ->with('letter_validation_error', [
                        'message' => 'Letters cannot be generated. Please update the following information:',
                        'errors' => $validationErrors,
                    ]);
            }

        /*
        |--------------------------------------------------------------------------
        | Generate PDFs
        |--------------------------------------------------------------------------
        */

        $pdfFiles = [];

        foreach ($students as $student) {

            $pdfContent = $this->generateInternStudentLetterPdf(
                $student,
                $letterType,
                $request->boolean('is_monthly'),
                $request->boolean('is_logo_show')
            );

            $studentName = Str::upper(
                Str::slug($student->student_name, '_')
            );

            $fatherName = Str::upper(
                Str::slug($student->f_name, '_')
            );

            $letterName = Str::upper(
                Str::slug($letterType, '_')
            );

            $fileName = "{$studentName}_{$fatherName}_{$letterName}.pdf";

            $pdfFiles[] = [
                'name' => $fileName,
                'content' => $pdfContent,
            ];

            // Update existing confirmation download counter.
            if (in_array($letterType, [
                'confirmation_letter',
                'internship_confirmation_letter',
            ])) {
                $student->increment('download_count_confirmation');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Single student: return normal PDF
        |--------------------------------------------------------------------------
        */

        if (count($pdfFiles) === 1) {

            return response($pdfFiles[0]['content'])
                ->header('Content-Type', 'application/pdf')
                ->header(
                    'Content-Disposition',
                    'attachment; filename="' . $pdfFiles[0]['name'] . '"'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Multiple students: create ZIP
        |--------------------------------------------------------------------------
        */

        $zipFileName = Str::slug($letterType, '_')
            . '_' . now()->format('d_F_Y_His')
            . '.zip';

        $zipPath = storage_path('app/' . Str::uuid() . '.zip');

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create ZIP file.',
            ], 500);
        }

        foreach ($pdfFiles as $file) {
            $zip->addFromString($file['name'], $file['content']);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function generateInternStudentLetterPdf(
        InternStudent $student,
        string $letterType,
        bool $isMonthly = false,
        bool $isLogoShow = false
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Prepare compatibility properties for existing Student letter templates
        |--------------------------------------------------------------------------
        */

        $student->setAttribute(
            'father_name_with_title',
            $student->f_name
        );

        // Existing confirmation templates may use CollegeData (uppercase).
        $student->setRelation(
            'CollegeData',
            $student->collegeData
        );

        // Existing confirmation templates use sessionData.
        $sessionData = StudentSession::find($student->session);

        $student->setRelation('sessionData', $sessionData);

        /*
        |--------------------------------------------------------------------------
        | Generate Confirmation / Internship Confirmation Letter
        |--------------------------------------------------------------------------
        */

        if (in_array($letterType, [
            'confirmation_letter',
            'internship_confirmation_letter',
        ])) {

            $isInternship = $letterType === 'internship_confirmation_letter';

            $view = $isInternship
                ? 'pdf.internship_detail'
                : 'pdf.confirmation_detail';

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'tempDir' => storage_path('app/mpdf'),
            ]);

            $mpdf->SetHTMLHeader(
                '<div style="position: fixed; top: -35px;" class="head-shape">
                    <img src="' . public_path('images/confirmation_images/head-shape.png') . '"/>
                </div>'
            );

            $mpdf->SetHTMLFooter(
                '<div style="position: fixed; bottom: -35px;" class="ct-footer-shape">
                    <img src="' . public_path('images/confirmation_images/footer-shape-11.png') . '"/>
                </div>'
            );

            $html = View::make($view, [
                'student' => $student,
                'isInternship' => $isInternship,
                'ismonthly' => $isMonthly,
                'is_logo_show' => $isLogoShow,
            ])->render();

            $mpdf->WriteHTML($html);

            return $mpdf->Output('', 'S');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Offer / Strict Offer Letter
        |--------------------------------------------------------------------------
        */

        $view = match ($letterType) {
            'offer' => 'student_additional_letters.intern_offer_letter_fixed_pdf_dynamic',
            'strict_offer_letter' => 'student_additional_letters.strict_offer_letter_pdf',
            default => abort(422, 'Invalid letter type.'),
        };

        /*
         * The existing Offer Letter templates expect:
         * $letter->student
         *
         * Build an unsaved StudentAdditionalLetter object using
         * the fields already stored in intern_students.
         */

        $letter = new StudentAdditionalLetter();

        $letter->forceFill([
            'student_id' => $student->id,
            'internship_type' => $letterType,
            'issue_date' => now()->toDateString(),

            'offer_training_start_date' => $student->offer_training_start_date,
            'offer_training_duration_months' => $student->offer_training_duration_months,
            'offer_ppo_evaluation_months' => $student->offer_ppo_evaluation_months,
            'offer_probation_duration_months' => $student->offer_probation_duration_months,
            'offer_working_hours' => $student->offer_working_hours,
            'offer_service_agreement_duration' => $student->offer_service_agreement_duration,
            'offer_annual_salary' => $student->offer_annual_salary,
        ]);

        $letter->setRelation('student', $student);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $html = View::make($view, [
            'letter' => $letter,
        ])->render();

        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName(
            'lastPageFooter',
            $this->getPDFFooter()
        );

        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');

        return $mpdf->Output('', 'S');
    }
}
