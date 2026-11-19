<?php

namespace App\Http\Controllers\Letters;
use App\Http\Controllers\Controller;

use App\Models\StudentAdditionalLetter;
use App\Models\College;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use Illuminate\Validation\Rule;
use App\Models\LetterTemplate;
use ZipArchive;
use App\Models\InternStudent;

class StudentAdditionalLetterController extends Controller
{	
	use PdfLayoutTrait;
    protected string $permissionPrefix = 'student_letters';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'bulkDownload' => 'view',

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

    public function index(Request $request)
{
    $activeSessionNo = session('admin_session_id');

    // Default tab is Student.
    $letterFor = $request->input('letter_for', 'student');

    if (!in_array($letterFor, ['student', 'intern'], true)) {
        $letterFor = 'student';
    }

    if ($letterFor === 'intern') {

        $query = StudentAdditionalLetter::forInterns()
            ->with('intern')
            ->whereHas('intern', function ($q) use ($activeSessionNo) {
                $q->where('session', $activeSessionNo);
            });

    } else {

        // Existing Student global scope automatically applies.
        $query = StudentAdditionalLetter::with('student')
            ->whereHas('student', function ($q) use ($activeSessionNo) {
                $q->where('session', $activeSessionNo);
            });
    }

    if ($request->filled('internship_type')) {
        $query->where('internship_type', $request->internship_type);
    }

    $letters = $query->orderBy('updated_at', 'desc')->get();

    return view('student_additional_letters.index', compact(
        'letters',
        'letterFor'
    ));
}
    public function index_16nov(Request $request)
    {
        $activeSessionNo = session('admin_session_id');

        $query = StudentAdditionalLetter::with('student')
            ->whereHas('student', function ($q) use ($activeSessionNo) {
                $q->where('session', $activeSessionNo);
            });

        if ($request->filled('internship_type')) {
            $query->where('internship_type', $request->internship_type);
        }

        return view('student_additional_letters.index', [
            // 'letters' => $query->latest()->get()
            'letters' => $query->orderBy('updated_at', 'desc')->get()
            // 'letters' => $query->orderBy('updated_at', 'desc')->get()

        ]);
    }

    public function index1(Request $request)
	{

        $query = StudentAdditionalLetter::with('student');

	    if ($request->filled('internship_type')) {
	        $query->where('internship_type', $request->internship_type);
	    }

	    return view('student_additional_letters.index', [
	        'letters' => $query->latest()->get()
	    ]);
	}

    public function create(Request $request)
{
    $activeSessionNo = session('admin_session_id');

    $letterFor = $request->input('letter_for', 'student');

    if (!in_array($letterFor, ['student', 'intern'], true)) {
        $letterFor = 'student';
    }

    $colleges = College::orderBy('college_display_name')->get();

    $students = collect();
    $internStudents = collect();

    if ($letterFor === 'student') {
        $students = Student::where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $letterTypes = $this->studentLetterTypes();
    } else {
        $internStudents = InternStudent::with('collegeData')
            ->where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $letterTypes = $this->internLetterTypes();
    }

    $templates = LetterTemplate::where('status', 1)
        ->whereIn('letter_type', [
            'student_responsibility',
            'placed_student_responsibility'
        ])
        ->pluck('content', 'letter_type');

    return view('student_additional_letters.create', compact(
        'colleges',
        'students',
        'internStudents',
        'templates',
        'letterFor',
        'letterTypes'
    ));
}
    public function create_16nov()
    {   
        $activeSessionNo = session('admin_session_id');
        $colleges = College::orderBy('college_display_name')->get();
        $students = Student::where('session', $activeSessionNo)->orderBy('student_name')->get();

        $templates = LetterTemplate::where('status',1)
            ->whereIn('letter_type',[
                'student_responsibility',
                'placed_student_responsibility'
            ])
            ->pluck('content','letter_type');

        return view('student_additional_letters.create', compact('colleges','students','templates'));
    }

public function store(Request $request)
{
    $letterFor = $request->input('letter_for', 'student');

    // Invalid type ko validation reject karegi.
    $allowedLetterFor = ['student', 'intern'];

    $letterTypes = $letterFor === 'intern'
        ? $this->internLetterTypes()
        : $this->studentLetterTypes();

    $data = $request->validate([

        'letter_for' => [
            'required',
            Rule::in($allowedLetterFor),
        ],

        'internship_type' => [
            'required',
            Rule::in(array_keys($letterTypes)),
        ],

        // Student selection
        'student_id' => 'required_if:letter_for,student|array|min:1',
        'student_id.*' => 'required|distinct|exists:students_detail,id',

        // Intern selection
        'intern_student_id' => 'required_if:letter_for,intern|array|min:1',
        'intern_student_id.*' => 'required|distinct|exists:intern_students,id',

        // Existing Student fields
        'subject' => 'required_if:internship_type,custom|max:255',

        'roll_number' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',

        'semester' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',

        'letter_content' => 'required_if:internship_type,custom,stipend',

        'issue_date' => 'nullable|date',

        // Normal and Strict Offer shared fields
        'offer_training_start_date' => 'required_if:internship_type,offer,strict_offer_letter|nullable|date',

        'offer_training_duration_months' => 'required_if:internship_type,offer,strict_offer_letter|nullable|integer|min:1',

        // Normal Offer only
        'offer_internship_type' => 'required_if:internship_type,offer|nullable|string|max:50',

        'offer_ppo_evaluation_months' => 'required_if:internship_type,offer|nullable|integer|min:1',

        // Strict Offer only
        'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

        'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',

    ]);

    $activeSessionNo = session('admin_session_id');

    $type = $data['internship_type'];

    /*
    |--------------------------------------------------------------------------
    | Verify selected records belong to active session
    |--------------------------------------------------------------------------
    */

    if ($letterFor === 'student') {

        $selectedIds = $data['student_id'];

        $validCount = Student::where('session', $activeSessionNo)
            ->whereIn('id', $selectedIds)
            ->count();

        if ($validCount !== count($selectedIds)) {
            return back()
                ->withErrors([
                    'student_id' => 'Some selected students do not belong to the active session.'
                ])
                ->withInput();
        }

    } else {

        $selectedIds = $data['intern_student_id'];

        $validCount = InternStudent::where('session', $activeSessionNo)
            ->whereIn('id', $selectedIds)
            ->count();

        if ($validCount !== count($selectedIds)) {
            return back()
                ->withErrors([
                    'intern_student_id' => 'Some selected interns do not belong to the active session.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Validate individual Intern dates
        |--------------------------------------------------------------------------
        | Only these two letter types require start_date and end_date.
        */

        if (in_array($type, [
            'intern_confirmation_letter',
            'intern_internship_letter',
        ], true)) {

            $interns = InternStudent::where('session', $activeSessionNo)
                ->whereIn('id', $selectedIds)
                ->get([
                    'id',
                    'student_name',
                    'start_date',
                    'end_date',
                ]);

            $dateErrors = [];

            foreach ($interns as $intern) {

                $missing = [];

                if (empty($intern->start_date)) {
                    $missing[] = 'Start Date';
                }

                if (empty($intern->end_date)) {
                    $missing[] = 'End Date';
                }

                if (!empty($missing)) {
                    $dateErrors[] = ($intern->student_name ?? 'Unknown Intern')
                        . ' — '
                        . implode(' and ', $missing)
                        . ' missing';
                }
            }

            if (!empty($dateErrors)) {

                return back()
                    ->withErrors([
                        'intern_student_id' =>
                            'Please update internship dates in the following Intern records: '
                            . implode('; ', $dateErrors)
                    ])
                    ->withInput();
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare common letter data
    |--------------------------------------------------------------------------
    */

    $issueDate = $data['issue_date'] ?? now()->toDateString();

    $commonData = [

        'internship_type' => $type,

        'subject' => $data['subject'] ?? null,

        'roll_number' => $data['roll_number'] ?? null,

        'semester' => $data['semester'] ?? null,

        'letter_content' => $data['letter_content'] ?? null,

        'issue_date' => $issueDate,

        // Shared by Normal and Strict Offer
        'offer_training_start_date' => in_array(
            $type,
            ['offer', 'strict_offer_letter'],
            true
        ) ? ($data['offer_training_start_date'] ?? null) : null,

        'offer_training_duration_months' => in_array(
            $type,
            ['offer', 'strict_offer_letter'],
            true
        ) ? ($data['offer_training_duration_months'] ?? null) : null,

        // Normal Offer only
        'offer_internship_type' => $type === 'offer'
            ? ($data['offer_internship_type'] ?? null)
            : null,

        'offer_ppo_evaluation_months' => $type === 'offer'
            ? ($data['offer_ppo_evaluation_months'] ?? null)
            : null,

        // Strict Offer only
        'offer_probation_duration_months' => $type === 'strict_offer_letter'
            ? ($data['offer_probation_duration_months'] ?? null)
            : null,

        'offer_working_hours' => $type === 'strict_offer_letter'
            ? ($data['offer_working_hours'] ?? null)
            : null,

        'offer_service_agreement_duration' => $type === 'strict_offer_letter'
            ? ($data['offer_service_agreement_duration'] ?? null)
            : null,

        'offer_annual_salary' => $type === 'strict_offer_letter'
            ? ($data['offer_annual_salary'] ?? null)
            : null,
    ];

    /*
    |--------------------------------------------------------------------------
    | Create Letters
    |--------------------------------------------------------------------------
    */

    $created = 0;
    $skipped = 0;

    foreach ($selectedIds as $recordId) {

        if ($letterFor === 'student') {

            // Existing Student duplicate check
            $exists = StudentAdditionalLetter::where('student_id', $recordId)
                ->where('internship_type', $type)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            StudentAdditionalLetter::create(array_merge(
                $commonData,
                [
                    'letter_for' => 'student',
                    'student_id' => $recordId,
                    'intern_student_id' => null,
                ]
            ));

        } else {

            // Intern duplicate check
            $exists = StudentAdditionalLetter::forInterns()
                ->where('intern_student_id', $recordId)
                ->where('internship_type', $type)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            StudentAdditionalLetter::withoutGlobalScope('student_default')
                ->create(array_merge(
                    $commonData,
                    [
                        'letter_for' => 'intern',
                        'student_id' => null,
                        'intern_student_id' => $recordId,
                    ]
                ));
        }

        $created++;
    }

    return redirect()
        ->route('student-additional-letters.index', [
            'letter_for' => $letterFor
        ])
        ->with(
            'success',
            "$created letter(s) created successfully. $skipped skipped (already existed)."
        );
}
public function store_16nov(Request $request)
{
    $data = $request->validate([
        'internship_type' => [
            'required',
            Rule::in([
                'free', 'stipend', 'offer', 'custom',
                'mutual_consent', 'noc', 'training_consent',
                'placement', 'internship', 'internship_with_package',
                'strict_offer_letter', 'strict_consent_letter',
                'stipend_policy', 'internship_consent',
                'part_time_job_opportunity', 'with_roll_number',
                'with_roll_number_internship', 'confirmation_letter',
                'student_responsibility', 'placed_student_responsibility'
            ]),
        ],

        'student_id' => 'required|array',
        'student_id.*' => 'exists:students_detail,id',

        'subject' => 'required_if:internship_type,custom|max:255',
        'roll_number' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
        'semester' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
        'letter_content' => 'required_if:internship_type,custom,stipend',
        'issue_date' => 'nullable|date',

        // Normal Offer Letter fields
        'offer_training_start_date' => 'required_if:internship_type,offer,strict_offer_letter|nullable|date',
        'offer_training_duration_months' => 'required_if:internship_type,offer,strict_offer_letter|nullable|integer|min:1',
        'offer_internship_type' => 'required_if:internship_type,offer|nullable|string|max:50',
        'offer_ppo_evaluation_months' => 'required_if:internship_type,offer|nullable|integer|min:1',

        // Strict Offer Letter fields
        'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',
        'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',
        'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',
        'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',
    ]);

    $created = 0;
    $skipped = 0;

    foreach ($request->student_id as $studentId) {

        $exists = StudentAdditionalLetter::where('student_id', $studentId)
            ->where('internship_type', $request->internship_type)
            ->exists();

        if ($exists) {
            $skipped++;
            continue;
        }

        $type = $request->internship_type;

        StudentAdditionalLetter::create([
            'student_id' => $studentId,
            'internship_type' => $type,
            'subject' => $request->subject,
            'roll_number' => $request->roll_number,
            'semester' => $request->semester,
            'letter_content' => $request->letter_content,
            'issue_date' => $request->issue_date ?: now()->toDateString(),

            // Shared by Normal and Strict Offer Letter
            'offer_training_start_date' => in_array($type, ['offer', 'strict_offer_letter'])
                ? $request->offer_training_start_date : null,

            'offer_training_duration_months' => in_array($type, ['offer', 'strict_offer_letter'])
                ? $request->offer_training_duration_months : null,

            // Normal Offer Letter only
            'offer_internship_type' => $type === 'offer'
                ? $request->offer_internship_type : null,

            'offer_ppo_evaluation_months' => $type === 'offer'
                ? $request->offer_ppo_evaluation_months : null,

            // Strict Offer Letter only
            'offer_probation_duration_months' => $type === 'strict_offer_letter'
                ? $request->offer_probation_duration_months : null,

            'offer_working_hours' => $type === 'strict_offer_letter'
                ? $request->offer_working_hours : null,

            'offer_service_agreement_duration' => $type === 'strict_offer_letter'
                ? $request->offer_service_agreement_duration : null,

            'offer_annual_salary' => $type === 'strict_offer_letter'
                ? $request->offer_annual_salary : null,
        ]);

        $created++;
    }

    return redirect()
        ->route('student-additional-letters.index')
        ->with('success', "$created letter(s) created successfully. $skipped skipped (already existed).");
}

public function store_10nov(Request $request)
{
    $data = $request->validate([
        'internship_type' => [
            'required',
            Rule::in([
                'free','stipend','offer','custom',
                'mutual_consent','noc','training_consent',
                'placement','internship','internship_with_package',
                'strict_offer_letter', 'strict_consent_letter', 'stipend_policy', 
                'internship_consent','part_time_job_opportunity', 
                'with_roll_number', 'with_roll_number_internship', 'confirmation_letter','student_responsibility',
                    'placed_student_responsibility'
            ]),
        ],

        // 👇 changed to array (multi student)
        'student_id'      => 'required|array',
        'student_id.*'    => 'exists:students_detail,id',

        'subject'         => 'required_if:internship_type,custom|max:255',
        'roll_number'     => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
        'semester'        => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
        'letter_content'  => 'required_if:internship_type,custom,stipend',
        'issue_date'      => 'nullable|date',
        // Strict Offer Letter fields
        'offer_training_start_date' => 'required_if:internship_type,strict_offer_letter|nullable|date',

        'offer_training_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

        'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

        'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',
    ], [
        // custom message kept
        // (unique removed because now handled manually)
    ]);

    $created = 0;
    $skipped = 0;

    foreach ($request->student_id as $studentId) {

        // ✅ keep your uniqueness logic (moved here)
        $exists = StudentAdditionalLetter::where('student_id', $studentId)
            ->where('internship_type', $request->internship_type)
            ->exists();

        if ($exists) {
            $skipped++;
            continue;
        }

        StudentAdditionalLetter::create([
            'student_id'      => $studentId,
            'internship_type' => $request->internship_type,
            'subject'         => $request->subject,
            'roll_number'     => $request->roll_number,
            'semester'        => $request->semester,
            'letter_content'  => $request->letter_content,
            'issue_date'      => $request->issue_date 
                                ? $request->issue_date 
                                : Carbon::today()->toDateString(),

            'offer_training_start_date' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_training_start_date : null,

            'offer_training_duration_months' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_training_duration_months : null,

            'offer_probation_duration_months' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_probation_duration_months : null,

            'offer_working_hours' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_working_hours : null,

            'offer_service_agreement_duration' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_service_agreement_duration : null,

            'offer_annual_salary' => $request->internship_type === 'strict_offer_letter'
                ? $request->offer_annual_salary : null,
            
        ]);

        $created++;
    }

    return redirect()
        ->route('student-additional-letters.index')
        ->with('success', "$created letter(s) created successfully. $skipped skipped (already existed).");
}
    
    public function edit(Request $request, $id)
{
    $studentAdditionalLetter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->findOrFail($id);

    $letterFor = $studentAdditionalLetter->letter_for ?? 'student';

    $activeSessionNo = session('admin_session_id');

    $students = collect();
    $internStudents = collect();

    if ($letterFor === 'student') {

        $students = Student::where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $letterTypes = $this->studentLetterTypes();

    } else {

        $internStudents = InternStudent::with('collegeData')
            ->where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $letterTypes = $this->internLetterTypes();
    }

    $templates = LetterTemplate::where('status', 1)
        ->whereIn('letter_type', [
            'student_responsibility',
            'placed_student_responsibility'
        ])
        ->pluck('content', 'letter_type');

    return view('student_additional_letters.edit', compact(
        'studentAdditionalLetter',
        'letterFor',
        'students',
        'internStudents',
        'letterTypes',
        'templates'
    ));
}
    public function edit_166n($id)
{
    $activeSessionNo = session('admin_session_id');

    // Find letter according to its type
    $letter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->where('id', $id)
        ->firstOrFail();

    $letterFor = $letter->letter_for ?? 'student';

    if (!in_array($letterFor, ['student', 'intern'], true)) {
        abort(404);
    }

    if ($letterFor === 'intern') {

        $letter = StudentAdditionalLetter::forInterns()
            ->with('intern')
            ->where('id', $id)
            ->whereHas('intern', function ($query) use ($activeSessionNo) {
                $query->where('session', $activeSessionNo);
            })
            ->firstOrFail();

        $students = collect();

        $internStudents = InternStudent::where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $letterTypes = $this->internLetterTypes();

    } else {

        $letter = StudentAdditionalLetter::with('student')
            ->where('id', $id)
            ->whereHas('student', function ($query) use ($activeSessionNo) {
                $query->where('session', $activeSessionNo);
            })
            ->firstOrFail();

        $students = Student::where('session', $activeSessionNo)
            ->orderBy('student_name')
            ->get();

        $internStudents = collect();

        $letterTypes = $this->studentLetterTypes();
    }

    $templates = LetterTemplate::where('status', 1)
        ->whereIn('letter_type', [
            'student_responsibility',
            'placed_student_responsibility'
        ])
        ->pluck('content', 'letter_type');

    return view('student_additional_letters.edit', compact(
        'letter',
        'students',
        'internStudents',
        'letterFor',
        'letterTypes',
        'templates'
    ));
}
    public function edit_16nov(StudentAdditionalLetter $studentAdditionalLetter)
    {
        $activeSessionNo = session('admin_session_id');
        $students = Student::where('session', $activeSessionNo)->orderBy('student_name')->get();

        $templates = LetterTemplate::where('status', 1)
            ->whereIn('letter_type', [
                'student_responsibility',
                'placed_student_responsibility'
            ])
            ->pluck('content', 'letter_type');



        return view('student_additional_letters.edit', [
            'letter' => $studentAdditionalLetter,
            'students' => $students,
            'templates'=> $templates
        ]);
    }


public function update(Request $request, $id)
{
    // Fetch without Student default global scope.
    $studentAdditionalLetter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->findOrFail($id);

    $letterFor = $studentAdditionalLetter->letter_for ?? 'student';

    $letterTypes = $letterFor === 'intern'
        ? $this->internLetterTypes()
        : $this->studentLetterTypes();

    $rules = [
        'internship_type' => [
            'required',
            Rule::in(array_keys($letterTypes)),
        ],

        'issue_date' => 'nullable|date',

        'subject' => 'required_if:internship_type,custom|max:255',

        'letter_content' => 'required_if:internship_type,custom,stipend',

        'offer_training_start_date' => 'required_if:internship_type,offer,strict_offer_letter|nullable|date',

        'offer_training_duration_months' => 'required_if:internship_type,offer,strict_offer_letter|nullable|integer|min:1',

        'offer_internship_type' => 'required_if:internship_type,offer|nullable|string|max:50',

        'offer_ppo_evaluation_months' => 'required_if:internship_type,offer|nullable|integer|min:1',

        'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

        'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

        'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',
    ];

    if ($letterFor === 'student') {
        $rules['student_id'] = 'required|exists:students_detail,id';

        $rules['roll_number'] = 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255';

        $rules['semester'] = 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255';
    } else {
        $rules['intern_student_id'] = 'required|exists:intern_students,id';
    }

    $data = $request->validate($rules);

    $type = $data['internship_type'];

    // Confirm selected record belongs to active session.
    $activeSessionNo = session('admin_session_id');

    if ($letterFor === 'student') {

        $person = Student::where('session', $activeSessionNo)
            ->findOrFail($data['student_id']);

        // Duplicate check for another Student letter.
        $duplicate = StudentAdditionalLetter::where('letter_for', 'student')
            ->where('student_id', $data['student_id'])
            ->where('internship_type', $type)
            ->where('id', '<>', $studentAdditionalLetter->id)
            ->exists();

    } else {

        $person = InternStudent::where('session', $activeSessionNo)
            ->findOrFail($data['intern_student_id']);

        // Duplicate check for another Intern letter.
        $duplicate = StudentAdditionalLetter::forInterns()
            ->where('intern_student_id', $data['intern_student_id'])
            ->where('internship_type', $type)
            ->where('id', '<>', $studentAdditionalLetter->id)
            ->exists();

        // Date validation only for the two new Intern letters.
        if (in_array($type, [
            'intern_confirmation_letter',
            'intern_internship_letter',
        ], true)) {

            $missing = [];

            if (empty($person->start_date)) {
                $missing[] = 'Start Date';
            }

            if (empty($person->end_date)) {
                $missing[] = 'End Date';
            }

            if (!empty($missing)) {
                return back()
                    ->withErrors([
                        'intern_student_id' =>
                            ($person->student_name ?? 'Intern')
                            . ' — '
                            . implode(' and ', $missing)
                            . ' missing. Please update the Intern record first.'
                    ])
                    ->withInput();
            }
        }
    }

    if ($duplicate) {
        return back()
            ->withErrors([
                'internship_type' => 'This letter type already exists for this ' . $letterFor . '.'
            ])
            ->withInput();
    }

    // Issue date
    $data['issue_date'] = $request->issue_date ?: now()->toDateString();

    // Keep the correct foreign key for the record type.
    if ($letterFor === 'student') {
        $data['student_id'] = $request->student_id;
        $data['intern_student_id'] = null;
    } else {
        $data['intern_student_id'] = $request->intern_student_id;
        $data['student_id'] = null;
    }

    // Shared Normal and Strict Offer fields.
    $data['offer_training_start_date'] = in_array($type, ['offer', 'strict_offer_letter'], true)
        ? $request->offer_training_start_date : null;

    $data['offer_training_duration_months'] = in_array($type, ['offer', 'strict_offer_letter'], true)
        ? $request->offer_training_duration_months : null;

    // Normal Offer only.
    $data['offer_internship_type'] = $type === 'offer'
        ? $request->offer_internship_type : null;

    $data['offer_ppo_evaluation_months'] = $type === 'offer'
        ? $request->offer_ppo_evaluation_months : null;

    // Strict Offer only.
    $data['offer_probation_duration_months'] = $type === 'strict_offer_letter'
        ? $request->offer_probation_duration_months : null;

    $data['offer_working_hours'] = $type === 'strict_offer_letter'
        ? $request->offer_working_hours : null;

    $data['offer_service_agreement_duration'] = $type === 'strict_offer_letter'
        ? $request->offer_service_agreement_duration : null;

    $data['offer_annual_salary'] = $type === 'strict_offer_letter'
        ? $request->offer_annual_salary : null;

    // Do not allow changing the record's category through form input.
    unset($data['letter_for']);

    $studentAdditionalLetter->update($data);

    return redirect()
        ->route('student-additional-letters.index', ['letter_for' => $letterFor])
        ->with('success', 'Letter updated successfully.');
}

public function update_16nov(Request $request, StudentAdditionalLetter $studentAdditionalLetter)
{
    $data = $request->validate([
        'internship_type' => [
            'required',
            Rule::in([
                'free', 'stipend', 'offer', 'custom',
                'mutual_consent', 'noc', 'training_consent',
                'placement', 'internship', 'internship_with_package',
                'strict_offer_letter', 'strict_consent_letter',
                'stipend_policy', 'internship_consent',
                'part_time_job_opportunity', 'with_roll_number',
                'with_roll_number_internship', 'confirmation_letter',
                'student_responsibility', 'placed_student_responsibility'
            ]),
            Rule::unique('student_additional_letters')
                ->where('student_id', $request->student_id)
                ->ignore($studentAdditionalLetter->id),
        ],

        'student_id' => 'required|exists:students_detail,id',
        'subject' => 'required_if:internship_type,custom|max:255',
        'letter_content' => 'required_if:internship_type,custom,stipend',
        'issue_date' => 'nullable|date',
        'roll_number' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
        'semester' => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',

        // Normal Offer Letter fields
        'offer_training_start_date' => 'required_if:internship_type,offer,strict_offer_letter|nullable|date',
        'offer_training_duration_months' => 'required_if:internship_type,offer,strict_offer_letter|nullable|integer|min:1',
        'offer_internship_type' => 'required_if:internship_type,offer|nullable|string|max:50',
        'offer_ppo_evaluation_months' => 'required_if:internship_type,offer|nullable|integer|min:1',

        // Strict Offer Letter fields
        'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',
        'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',
        'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',
        'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',
    ], [
        'internship_type.unique' => 'This letter type already exists for this student.',
    ]);

    $type = $request->internship_type;

    $data['issue_date'] = $request->issue_date ?: now()->toDateString();

    // Shared by Normal and Strict Offer Letter
    $data['offer_training_start_date'] = in_array($type, ['offer', 'strict_offer_letter'])
        ? $request->offer_training_start_date : null;

    $data['offer_training_duration_months'] = in_array($type, ['offer', 'strict_offer_letter'])
        ? $request->offer_training_duration_months : null;

    // Normal Offer Letter only
    $data['offer_internship_type'] = $type === 'offer'
        ? $request->offer_internship_type : null;

    $data['offer_ppo_evaluation_months'] = $type === 'offer'
        ? $request->offer_ppo_evaluation_months : null;

    // Strict Offer Letter only
    $data['offer_probation_duration_months'] = $type === 'strict_offer_letter'
        ? $request->offer_probation_duration_months : null;

    $data['offer_working_hours'] = $type === 'strict_offer_letter'
        ? $request->offer_working_hours : null;

    $data['offer_service_agreement_duration'] = $type === 'strict_offer_letter'
        ? $request->offer_service_agreement_duration : null;

    $data['offer_annual_salary'] = $type === 'strict_offer_letter'
        ? $request->offer_annual_salary : null;

    $studentAdditionalLetter->update($data);

    return redirect()
        ->route('student-additional-letters.index')
        ->with('success', 'Letter updated successfully.');
}


    public function update_10nov(Request $request, StudentAdditionalLetter $studentAdditionalLetter)
    {
        $data = $request->validate([
            // 'internship_type' => 'required|in:free,stipend,offer,custom,mutual_consent,noc,training_consent,placement',
             'internship_type' => [
                'required',
                Rule::in([
                    'free',
                    'stipend',
                    'offer',
                    'custom',
                    'mutual_consent',
                    'noc',
                    'training_consent',
                    'placement',
                    'internship',
                    'internship_with_package',
                    'strict_offer_letter', 
                    'strict_consent_letter', 
                    'stipend_policy', 
                    'internship_consent',
                    'part_time_job_opportunity',
                    'with_roll_number',
                    'with_roll_number_internship',
                    'confirmation_letter',
                    'student_responsibility',
                    'placed_student_responsibility'
                ]),
                Rule::unique('student_additional_letters')
                    ->where('student_id', $request->student_id)
                    ->ignore($studentAdditionalLetter->id),
            ],
            'subject'         => 'required_if:internship_type,custom|max:255',
            'student_id'      => 'required|exists:students_detail,id',
            'letter_content'  => 'required_if:internship_type,custom,stipend',
            'issue_date'      => 'nullable|date',
            'roll_number'     => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
            'semester'        => 'required_if:internship_type,with_roll_number,with_roll_number_internship,confirmation_letter|max:255',
            // Strict Offer Letter fields
            'offer_training_start_date' => 'required_if:internship_type,strict_offer_letter|nullable|date',

            'offer_training_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

            'offer_probation_duration_months' => 'required_if:internship_type,strict_offer_letter|nullable|integer|min:1',

            'offer_working_hours' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

            'offer_service_agreement_duration' => 'required_if:internship_type,strict_offer_letter|nullable|string|max:100',

            'offer_annual_salary' => 'required_if:internship_type,strict_offer_letter|nullable|numeric|min:0',
        ], [
            'internship_type.unique' => 'This letter type already exists for this student.',
        ]);

         // 👉 Set issue_date (same logic)
        $data['issue_date'] = $request->input('issue_date', now()->toDateString());
        
        $studentAdditionalLetter->update($data);

        return redirect()
            ->route('student-additional-letters.index')
            ->with('success', 'Letter updated successfully.');
    }

    
public function destroy($id)
{
    $studentAdditionalLetter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->findOrFail($id);

    $studentAdditionalLetter->delete();

    return back()->with('success', 'Letter deleted successfully.');
}

public function bulkDelete(Request $request)
{
    $request->validate([
        'ids'   => 'required|array|min:1',
        'ids.*' => 'required|integer|distinct|exists:student_additional_letters,id',
    ]);

    $activeSessionNo = session('admin_session_id');

    $letters = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->with(['student', 'intern'])
        ->whereIn('id', $request->ids)
        ->get();

    if ($letters->count() !== count($request->ids)) {
        return back()->with('error', 'Some selected letters could not be found.');
    }

    foreach ($letters as $letter) {
        if ($letter->letter_for === 'intern') {
            if (!$letter->intern || $letter->intern->session != $activeSessionNo) {
                return back()->with('error', 'Some selected letters do not belong to the active session.');
            }
        } elseif ($letter->letter_for === 'student') {
            if (!$letter->student || $letter->student->session != $activeSessionNo) {
                return back()->with('error', 'Some selected letters do not belong to the active session.');
            }
        } else {
            return back()->with('error', 'Invalid letter selected.');
        }
    }

    foreach ($letters as $letter) {
        $letter->delete();
    }

    return back()->with('success', $letters->count() . ' letters deleted successfully.');
}

    
private function pdf($letter): string
{
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

    $isIntern = $letter->letter_for === 'intern';

    /*
    |--------------------------------------------------------------------------
    | Load Student / Intern Relation
    |--------------------------------------------------------------------------
    */

    if ($isIntern) {

        $intern = InternStudent::with([
            'collegeData.district',
            'collegeData.state',
        ])->findOrFail($letter->intern_student_id);

        // Keep existing PDF views compatible with $letter->student.
        $letter->setRelation('student', $intern);

    } else {

        // Original Student relation loading - unchanged.
        $letter->load(
            'student.collegeData',
            'student.collegeData.district',
            'student.collegeData.state'
        );
    }

    $activeSessionNo = session('admin_session_id');

    /*
    |--------------------------------------------------------------------------
    | Student Free Letter Template
    |--------------------------------------------------------------------------
    */

    if (!$isIntern) {

        if (
            $letter->internship_type == 'free' &&
            $activeSessionNo == '3'
        ) {
            $free_letter_temp = 'student_additional_letters.free_letter_fixed_pdf';
        } else {
            $free_letter_temp = 'student_additional_letters.other_45_days_free_letter_fixed_pdf';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PDF Template Selection
    |--------------------------------------------------------------------------
    */

    if ($isIntern) {

        $view = match ($letter->internship_type) {

            'intern_confirmation_letter' =>
                'pdf.confirmation_detail',

            'intern_internship_letter' =>
                'pdf.internship_detail',

            'offer' =>
                'student_additional_letters.offer_letter_fixed_pdf',

            'strict_offer_letter' =>
                'student_additional_letters.strict_offer_letter_pdf',

            default => 'letters.pdf',
        };

    } else {

        // Original Student template mapping - unchanged.
        $view = match ($letter->internship_type) {

            'free' => $free_letter_temp,

            'stipend' => 'student_additional_letters.pdf',

            'noc' => 'student_additional_letters.no_consent_pdf',

            'mutual_consent' => 'student_additional_letters.mutual_consent_pdf',

            'training_consent' => 'student_additional_letters.training_consent_pdf',

            'placement' => 'student_additional_letters.placement_consent_pdf',

            'offer' => 'student_additional_letters.offer_letter_fixed_pdf',

            'custom' => 'student_additional_letters.custom_type_pdf',

            'internship' => 'student_additional_letters.pdf-offer_letter_fixed_internship',

            'internship_with_package' => 'student_additional_letters.pdf-offer_letter_fixed_internship_with_package',

            'strict_offer_letter' => 'student_additional_letters.strict_offer_letter_pdf',

            'strict_consent_letter' => 'student_additional_letters.strict_consent_letter_pdf',

            'stipend_policy' => 'student_additional_letters.stipend_policy_pdf',

            'internship_consent' => 'student_additional_letters.internship_consent_pdf',

            'part_time_job_opportunity' => 'student_additional_letters.part_time_job_opportunity_letter_pdf',

            'with_roll_number' => 'student_additional_letters.with_roll_number_letter_pdf',

            'with_roll_number_internship' => 'student_additional_letters.with_roll_number_internship_letter_pdf',

            'confirmation_letter' => 'student_additional_letters.confirmation_letter',

            'student_responsibility' => 'student_additional_letters.student_responsibility_pdf',
            'additional_job_opportunity' => 'student_additional_letters.additional_job_letter_pdf',

            'placed_student_responsibility' => 'student_additional_letters.placed_student_responsibility_pdf',

            default => 'letters.pdf',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Student Responsibility Letter Content
    |--------------------------------------------------------------------------
    | Existing Student-only logic - unchanged.
    */

    if (!$isIntern && in_array($letter->internship_type, [
        'student_responsibility',
        'placed_student_responsibility',
    ], true)) {

        $student = $letter->student;

        $gender = strtolower($student->gender ?? '');
        $relation = 'son';

        if ($gender === 'female') {
            $relation = $student->is_married ? 'wife' : 'daughter';
        }

        $content = $letter->letter_content;

        $content = str_replace(
            [
                '{{student_name}}',
                '{{relation}}',
                '{{father_name}}',
                '{{technology}}',
                '{{joining_date}}',
            ],
            [
                ucwords($student->student_name ?? ''),
                $relation,
                $student->father_name_with_title ?? '-',
                ucwords($student->course_name ?? 'N/A'),
                $student->start_date
                    ? \Carbon\Carbon::parse($student->start_date)->format('d M Y')
                    : '',
            ],
            $content
        );

        $html = View::make($view, [
            'letter' => $letter,
            'content' => $content,
        ])->render();

    } elseif (
        $isIntern &&
        in_array($letter->internship_type, [
            'intern_confirmation_letter',
            'intern_internship_letter',
        ], true)
    ) {

        /*
        |--------------------------------------------------------------------------
        | Intern Confirmation / Internship PDF
        |--------------------------------------------------------------------------
        | Same variables as original generateConfirmationPdf().
        */

        $student = $letter->student;

        $isInternship = $letter->internship_type === 'intern_internship_letter';

        $ismonthly = false;
        $is_logo_show = false;

        $html = View::make($view, compact(
            'student',
            'isInternship',
            'ismonthly',
            'is_logo_show'
        ))->render();

    } else {

        // Existing Student and other PDF rendering unchanged.
        $html = View::make($view, compact('letter'))->render();
    }

    /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

    $mpdf->SetHTMLHeader('');

    $mpdf->DefHTMLFooterByName(
        'lastPageFooter',
        $this->getPDFFooter()
    );

    $mpdf->WriteHTML($html);

    $mpdf->SetHTMLFooterByName('lastPageFooter');

    return $mpdf->Output('', 'S');
}


    
    private function pdf_16nov($letter): string
    {
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

        // $letter->load('student.collegeData');
        $letter->load('student.collegeData','student.collegeData.district', 'student.collegeData.state');
        $activeSessionNo = session('admin_session_id');
        // dd($activeSessionNo);

        if($letter->internship_type == 'free' && $activeSessionNo == '3'){
            $free_letter_temp = 'student_additional_letters.free_letter_fixed_pdf';
        }else{
            $free_letter_temp = 'student_additional_letters.other_45_days_free_letter_fixed_pdf';
        }
         $view = match ($letter->internship_type) {
                'free' => $free_letter_temp,
                'stipend' => 'student_additional_letters.pdf',
                'noc' => 'student_additional_letters.no_consent_pdf',
                'mutual_consent' => 'student_additional_letters.mutual_consent_pdf',
                'training_consent' => 'student_additional_letters.training_consent_pdf',
                'placement' => 'student_additional_letters.placement_consent_pdf',
                'offer' => 'student_additional_letters.offer_letter_fixed_pdf',
                'custom' => 'student_additional_letters.custom_type_pdf',
                'internship' => 'student_additional_letters.pdf-offer_letter_fixed_internship',
                'internship_with_package' => 'student_additional_letters.pdf-offer_letter_fixed_internship_with_package',
                'strict_offer_letter' => 'student_additional_letters.strict_offer_letter_pdf',
                'strict_consent_letter' => 'student_additional_letters.strict_consent_letter_pdf',
                'stipend_policy' => 'student_additional_letters.stipend_policy_pdf',
                'internship_consent' => 'student_additional_letters.internship_consent_pdf',
                'part_time_job_opportunity' => 'student_additional_letters.part_time_job_opportunity_letter_pdf',
                'with_roll_number' => 'student_additional_letters.with_roll_number_letter_pdf',
                'with_roll_number_internship' => 'student_additional_letters.with_roll_number_internship_letter_pdf',
                'confirmation_letter' => 'student_additional_letters.confirmation_letter',
                'student_responsibility' => 'student_additional_letters.student_responsibility_pdf',
                'additional_job_opportunity' => 'student_additional_letters.additional_job_letter_pdf',
                'placed_student_responsibility' => 'student_additional_letters.placed_student_responsibility_pdf',
                default => 'letters.pdf',
            };

         // $html = View::make($view, compact('letter'))->render();

            if (in_array($letter->internship_type, [
            'student_responsibility',
            'placed_student_responsibility'
        ])) {

            $student = $letter->student;

            $gender = strtolower($student->gender ?? '');
            $relation = 'son';

            if ($gender === 'female') {
                $relation = $student->is_married ? 'wife' : 'daughter';
            }

            $content = $letter->letter_content;

            $content = str_replace(
                [
                    '{{student_name}}',
                    '{{relation}}',
                    '{{father_name}}',
                    '{{technology}}',
                    '{{joining_date}}',
                ],
                [
                    ucwords($student->student_name ?? ''),
                    $relation,
                    $student->father_name_with_title ?? '-',
                    ucwords($student->course_name ?? 'N/A'),
                    $student->start_date
                        ? \Carbon\Carbon::parse($student->start_date)->format('d M Y')
                        : '',
                ],
                $content
            );

            $html = View::make($view, [
                'letter' => $letter,
                'content' => $content,
            ])->render();

        } else {

            $html = View::make($view, compact('letter'))->render();

        }
        // $html = View::make(
        //     'student_additional_letters.pdf',
        //     compact('letter')
        // )->render();
         
        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
              
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');
        // RETURN STRING ONLY
        return $mpdf->Output('', 'S');
    }


private function getMissingOfferFields($letter): array
{
    $requiredFields = [];

    if ($letter->internship_type === 'offer') {
        $requiredFields = [
            'offer_training_start_date' => 'Internship Start Date',
            'offer_training_duration_months' => 'Internship Duration',
            'offer_internship_type' => 'Internship Type',
            'offer_ppo_evaluation_months' => 'PPO Evaluation Period',
        ];
    }

    if ($letter->internship_type === 'strict_offer_letter') {
        $requiredFields = [
            'offer_training_start_date' => 'Training Start Date',
            'offer_training_duration_months' => 'Initial Training Duration',
            'offer_probation_duration_months' => 'Probation Duration',
            'offer_working_hours' => 'Working Hours',
            'offer_service_agreement_duration' => 'Service Agreement Duration',
            'offer_annual_salary' => 'Annual Salary / Package',
        ];
    }

    $missing = [];

    foreach ($requiredFields as $column => $label) {
        if (
            $letter->{$column} === null ||
            trim((string) $letter->{$column}) === ''
        ) {
            $missing[] = $label;
        }
    }

    return $missing;
}


    private function getMissingStrictOfferFields_10nov($letter): array
    {
        // dd($letter);
        if ($letter->internship_type !== 'strict_offer_letter') {
            return [];
        }

        $requiredFields = [
            'offer_training_start_date' => 'Training Start Date',
            'offer_training_duration_months' => 'Initial Training Duration',
            'offer_probation_duration_months' => 'Probation Duration',
            'offer_working_hours' => 'Working Hours',
            'offer_service_agreement_duration' => 'Service Agreement Duration',
            'offer_annual_salary' => 'Annual Salary / Package',
        ];

        $missing = [];

        foreach ($requiredFields as $column => $label) {
            if ($letter->{$column} === null || trim((string) $letter->{$column}) === '') {
                $missing[] = $label;
            }
        }

        return $missing;
    }

public function download($id)
{
    $StudentAdditionalLetter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->findOrFail($id);

    $isIntern = $StudentAdditionalLetter->letter_for === 'intern';

    $missingFields = $this->getMissingOfferFields($StudentAdditionalLetter);

    if (!empty($missingFields)) {

        if ($isIntern) {
            $person = InternStudent::find($StudentAdditionalLetter->intern_student_id);
        } else {
            $person = $StudentAdditionalLetter->student;
        }

        $studentName = $person->student_name ?? 'Unknown Student';

        $letterName = $StudentAdditionalLetter->internship_type === 'offer'
            ? 'Offer Letter'
            : 'Strict Offer Letter';

        return redirect()->back()->with(
            'error',
            "Cannot download {$letterName} for {$studentName}. Please add: "
            . implode(', ', $missingFields) . '.'
        );
    }

    $pdfContent = $this->pdf($StudentAdditionalLetter);

    $internshipTypeMap = [
        'free' => 'FREE INTERNSHIP LETTER',
        'stipend' => 'STIPEND INTERNSHIP LETTER',
        'offer' => 'OFFER LETTER',
        'custom' => 'CUSTOM LETTER',
        'mutual_consent' => 'MUTUAL CONSENT LETTER',
        'noc' => 'INTERNSHIP NO OBJECTION CERTIFICATE',
        'training_consent' => 'TRAINING CONSENT LETTER',
        'placement' => 'PLACEMENT CONSENT LETTER',
        'internship' => 'INTERNSHIP LETTER',
        'internship_with_package' => 'INTERNSHIP WITH PACKAGE LETTER',
        'strict_offer_letter' => 'STRICT OFFER LETTER',
        'strict_consent_letter' => 'STRICT CONSENT LETTER',
        'stipend_policy' => 'STIPEND POLICY LETTER',
        'internship_consent' => 'INTERNSHIP CONSENT LETTER',
        'part_time_job_opportunity' => 'PART TIME JOB OPPORTUNITY LETTER',
        'with_roll_number' => 'WITH ROLL NUMBER LETTER',
        'with_roll_number_internship' => 'WITH ROLL NUMBER INTERNSHIP LETTER',
        'confirmation_letter' => 'CONFIRMATION LETTER',
        'student_responsibility' => 'STUDENT RESPONSIBILITY LETTER',
        'placed_student_responsibility' => 'PLACED STUDENT RESPONSIBILITY LETTER',

        // Intern-specific letters
        'intern_confirmation_letter' => 'INTERN CONFIRMATION LETTER',
        'intern_internship_letter' => 'INTERN INTERNSHIP LETTER',
    ];

    $sanitize = function ($value) {
        return strtoupper(
            trim(
                preg_replace(
                    '/_+/',
                    '_',
                    preg_replace('/[^A-Za-z0-9]+/', '_', (string) $value)
                ),
                '_'
            )
        );
    };

    if ($isIntern) {
        $person = InternStudent::find($StudentAdditionalLetter->intern_student_id);
    } else {
        $person = $StudentAdditionalLetter->student;
    }

    $studentName = $sanitize(
        $person->student_name ?? ($isIntern ? 'INTERN' : 'STUDENT')
    );

    $fatherName = $sanitize(
        $person->f_name ?? ''
    );

    $internshipKey = $StudentAdditionalLetter->internship_type;

    $internshipFullName = $sanitize(
        $internshipTypeMap[$internshipKey] ?? 'INTERNSHIP LETTER'
    );

    $fileName = "{$studentName}_{$fatherName}_{$internshipFullName}.pdf";

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'attachment; filename="' . $fileName . '"'
        );
}
public function download_16nov(StudentAdditionalLetter $StudentAdditionalLetter)
{
    $missingFields = $this->getMissingOfferFields($StudentAdditionalLetter);

    if (!empty($missingFields)) {
        $studentName = $StudentAdditionalLetter->student->student_name
            ?? 'Unknown Student';

        $letterName = $StudentAdditionalLetter->internship_type === 'offer'
            ? 'Offer Letter'
            : 'Strict Offer Letter';

        return redirect()->back()->with(
            'error',
            "Cannot download {$letterName} for {$studentName}. Please add: "
            . implode(', ', $missingFields) . '.'
        );
    }

    $pdfContent = $this->pdf($StudentAdditionalLetter);

    $internshipTypeMap = [
        'free' => 'FREE INTERNSHIP LETTER',
        'stipend' => 'STIPEND INTERNSHIP LETTER',
        'offer' => 'OFFER LETTER',
        'custom' => 'CUSTOM LETTER',
        'mutual_consent' => 'MUTUAL CONSENT LETTER',
        'noc' => 'INTERNSHIP NO OBJECTION CERTIFICATE',
        'training_consent' => 'TRAINING CONSENT LETTER',
        'placement' => 'PLACEMENT CONSENT LETTER',
        'internship' => 'INTERNSHIP LETTER',
        'internship_with_package' => 'INTERNSHIP WITH PACKAGE LETTER',
        'strict_offer_letter' => 'STRICT OFFER LETTER',
        'strict_consent_letter' => 'STRICT CONSENT LETTER',
        'stipend_policy' => 'STIPEND POLICY LETTER',
        'internship_consent' => 'INTERNSHIP CONSENT LETTER',
        'part_time_job_opportunity' => 'PART TIME JOB OPPORTUNITY LETTER',
        'with_roll_number' => 'WITH ROLL NUMBER LETTER',
        'with_roll_number_internship' => 'WITH ROLL NUMBER INTERNSHIP LETTER',
        'confirmation_letter' => 'CONFIRMATION LETTER',
        'student_responsibility' => 'STUDENT RESPONSIBILITY LETTER',
        'placed_student_responsibility' => 'PLACED STUDENT RESPONSIBILITY LETTER',
    ];

    $sanitize = function ($value) {
        return strtoupper(
            trim(
                preg_replace(
                    '/_+/',
                    '_',
                    preg_replace('/[^A-Za-z0-9]+/', '_', (string) $value)
                ),
                '_'
            )
        );
    };

    $studentName = $sanitize(
        $StudentAdditionalLetter->student->student_name ?? 'STUDENT'
    );

    $fatherName = $sanitize(
        $StudentAdditionalLetter->student->f_name ?? ''
    );

    $internshipKey = $StudentAdditionalLetter->internship_type;

    $internshipFullName = $sanitize(
        $internshipTypeMap[$internshipKey] ?? 'INTERNSHIP LETTER'
    );

    $fileName = "{$studentName}_{$fatherName}_{$internshipFullName}.pdf";

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'attachment; filename="' . $fileName . '"'
        );
}

    public function download_10nov(StudentAdditionalLetter $StudentAdditionalLetter)
    {
        // dd($StudentAdditionalLetter);
        $missingFields = $this->getMissingStrictOfferFields($StudentAdditionalLetter);

        if (!empty($missingFields)) {
            $studentName = $StudentAdditionalLetter->student->student_name ?? 'Unknown Student';

            return redirect()->back()->with(
                'warning',
                "Cannot preview Strict Offer Letter for {$studentName}. Please add: "
                . implode(', ', $missingFields) . '.'
            );
        }

        $pdfContent = $this->pdf($StudentAdditionalLetter);
        // $pdfContent = $this->pdf($StudentAdditionalLetter);

        // Clean filename (VALID FIELDS ONLY)

        $internshipTypeMap = [
            'free' => 'FREE INTERNSHIP LETTER',
            'stipend' => 'STIPEND INTERNSHIP LETTER',
            'offer' => 'OFFER LETTER',
            'custom' => 'CUSTOM LETTER',
            'mutual_consent' => 'MUTUAL CONSENT LETTER',
            'noc' => 'INTERNSHIP NO OBJECTION CERTIFICATE',
            'training_consent' => 'TRAINING CONSENT LETTER',
            'placement' => 'PLACEMENT CONSENT LETTER',
            'internship' => 'INTERNSHIP LETTER',
            'internship_with_package' => 'INTERNSHIP WITH PACKAGE LETTER',
            'part_time_job_opportunity' => 'PART TIME JOB OPPORTUNITY LETTER',
            'with_roll_number' => 'WITH ROLL NUMBER LETTER',
            'confirmation_letter' => 'Confirmation Letter',
            'student_responsibility' => 'Student Responsibility Letter',
            'placed_student_responsibility' => 'Placed Student Responsibility Letter',
        ];

        $sanitize = function ($value) {
            return strtoupper(
                trim(
                    preg_replace('/_+/', '_',
                        preg_replace('/[^A-Za-z0-9]+/', '_', $value)
                    ),
                    '_'
                )
            );
        };
        // dd($StudentAdditionalLetter);
        $studentName =  $sanitize(($StudentAdditionalLetter->student->student_name));
        $fatherName =  $sanitize(($StudentAdditionalLetter->student->f_name));

        //  $studentName = strtoupper(
        //     preg_replace('/[^A-Za-z0-9]+/', '_', trim($StudentAdditionalLetter->F_name))
        // );

        // $internshipType = strtoupper(
        //     preg_replace('/[^A-Za-z0-9]+/', '_', trim($StudentAdditionalLetter->internship_type))
        // );

         $internshipKey = $StudentAdditionalLetter->internship_type;
        $internshipFullName = $sanitize(
            $internshipTypeMap[$internshipKey] ?? 'INTERNSHIP LETTER'
        );

        $fileName = "{$studentName}_{$fatherName}_{$internshipFullName}.pdf";

        // $fileName = trim(
        //     preg_replace('/_+/', '_', "{$studentName}_{$internshipType}_LETTER"),
        //     '_'
        // ) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

public function preview($id)
{
    $studentAdditionalLetter = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->findOrFail($id);

    $isIntern = $studentAdditionalLetter->letter_for === 'intern';

    $missingFields = $this->getMissingOfferFields($studentAdditionalLetter);

    if (!empty($missingFields)) {

        if ($isIntern) {
            $person = InternStudent::find($studentAdditionalLetter->intern_student_id);
        } else {
            $person = $studentAdditionalLetter->student;
        }

        $studentName = $person->student_name ?? 'Unknown Student';

        $letterName = $studentAdditionalLetter->internship_type === 'offer'
            ? 'Offer Letter'
            : 'Strict Offer Letter';

        return redirect()->back()->with(
            'error',
            "Cannot preview {$letterName} for {$studentName}. Please add: "
            . implode(', ', $missingFields) . '.'
        );
    }

    $pdfContent = $this->pdf($studentAdditionalLetter);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'inline; filename="student-letter-preview.pdf"'
        );
}
public function preview_16nov(StudentAdditionalLetter $studentAdditionalLetter)
{
    $missingFields = $this->getMissingOfferFields($studentAdditionalLetter);

    if (!empty($missingFields)) {
        $studentName = $studentAdditionalLetter->student->student_name
            ?? 'Unknown Student';

        $letterName = $studentAdditionalLetter->internship_type === 'offer'
            ? 'Offer Letter'
            : 'Strict Offer Letter';

        return redirect()->back()->with(
            'error',
            "Cannot preview {$letterName} for {$studentName}. Please add: "
            . implode(', ', $missingFields) . '.'
        );
    }

    $pdfContent = $this->pdf($studentAdditionalLetter);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'inline; filename="student-letter-preview.pdf"'
        );
}

    public function preview_10nov(StudentAdditionalLetter $studentAdditionalLetter)
    {
        $pdfContent = $this->pdf($studentAdditionalLetter);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="student-letter-preview.pdf"');
    }

    public function sendEmail($id)
    {
        // $letter = StudentAdditionalLetter::findOrFail($id);
         $letter = StudentAdditionalLetter::with('student')->findOrFail($id);
        
         if (!$letter->student || empty($letter->student->email_id)) {
            return back()->with('error', 'No email found for this user.');
        }

        // dd($letter);
        $pdf = $this->pdf($letter);

        Mail::send([], [], function ($message) use ($letter, $pdf) {
            $message->to($letter->student->email_id)
                ->subject('Student Letter')
                ->html('Please find your letter attached.')
                ->attachData($pdf, 'student-letter.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return back()->with('success', 'Letter emailed successfully.');
    }

    /**
     * Replace dynamic placeholders
     */
    private function prepareContent(StudentAdditionalLetter $letter): string
    {
        return str_replace(
            ['{{student_name}}', '{{internship_type}}', '{{email}}'],
            [
                $letter->student_name,
                ucfirst($letter->internship_type),
                $letter->email
            ],
            $letter->letter_content
        );
    }

     public function uploadPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Move file to public/uploads directory
            $file->move(public_path('uploads'), $fileName);
            
            $url = asset('uploads/' . $fileName);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'fileName' => $file->getClientOriginalName()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File upload failed.'
        ], 400);
    }



public function bulkDownload(Request $request)
{
    $request->validate([
        'ids'   => 'required|array|min:1',
        'ids.*' => 'required|integer|distinct|exists:student_additional_letters,id',
    ]);

    $activeSessionNo = session('admin_session_id');

    // Fetch selected letters with both Student and Intern relationships.
    $fetchedLetters = StudentAdditionalLetter::withoutGlobalScope('student_default')
        ->with([
            'student',
            'intern.collegeData',
            'intern.batchData',
        ])
        ->whereIn('id', $request->ids)
        ->get()
        ->keyBy('id');

    // Preserve selected IDs order.
    $letters = collect($request->ids)->map(function ($id) use ($fetchedLetters) {
        return $fetchedLetters->get($id);
    });

    // Ensure all requested records exist.
    if ($letters->contains(null) || $letters->count() !== count($request->ids)) {
        return back()->with(
            'error',
            'Some selected letters are invalid.'
        );
    }

    // Validate active session exactly as in index().
    foreach ($letters as $letter) {

        if ($letter->letter_for === 'intern') {

            // Same relationship and session condition as Intern index().
            $intern = $letter->intern;

            if (!$intern || $intern->session != $activeSessionNo) {
                return back()->with(
                    'error',
                    'Some selected letters do not belong to the active session.'
                );
            }

            // Keep compatibility with the existing PDF method.
            $letter->setRelation('student', $intern);

        } elseif ($letter->letter_for === 'student') {

            // Same relationship and session condition as Student index().
            if (
                !$letter->student ||
                $letter->student->session != $activeSessionNo
            ) {
                return back()->with(
                    'error',
                    'Some selected letters do not belong to the active session.'
                );
            }

        } else {

            return back()->with(
                'error',
                'Invalid letter type selected.'
            );
        }
    }

    // Validate Offer and Strict Offer Letter fields.
    foreach ($letters as $letter) {

        $missingFields = $this->getMissingOfferFields($letter);

        if (!empty($missingFields)) {

            $studentName = $letter->student->student_name
                ?? 'Unknown Student';

            $letterName = $letter->internship_type === 'offer'
                ? 'Offer Letter'
                : 'Strict Offer Letter';

            return back()->with(
                'error',
                "Cannot download {$letterName} for {$studentName}. Please add: "
                . implode(', ', $missingFields) . '.'
            );
        }
    }

    $internshipTypeMap = [
        'free' => 'FREE INTERNSHIP LETTER',
        'stipend' => 'STIPEND INTERNSHIP LETTER',
        'offer' => 'OFFER LETTER',
        'custom' => 'CUSTOM LETTER',
        'mutual_consent' => 'MUTUAL CONSENT LETTER',
        'noc' => 'INTERNSHIP NO OBJECTION CERTIFICATE',
        'training_consent' => 'TRAINING CONSENT LETTER',
        'placement' => 'PLACEMENT CONSENT LETTER',
        'internship' => 'INTERNSHIP LETTER',
        'internship_with_package' => 'INTERNSHIP WITH PACKAGE LETTER',
        'strict_offer_letter' => 'STRICT OFFER LETTER',
        'strict_consent_letter' => 'STRICT CONSENT LETTER',
        'stipend_policy' => 'STIPEND POLICY LETTER',
        'internship_consent' => 'INTERNSHIP CONSENT LETTER',
        'part_time_job_opportunity' => 'PART TIME JOB OPPORTUNITY LETTER',
        'with_roll_number' => 'WITH ROLL NUMBER LETTER',
        'with_roll_number_internship' => 'WITH ROLL NUMBER INTERNSHIP LETTER',
        'confirmation_letter' => 'CONFIRMATION LETTER',
        'student_responsibility' => 'STUDENT RESPONSIBILITY LETTER',
        'placed_student_responsibility' => 'PLACED STUDENT RESPONSIBILITY LETTER',

        // Intern-specific letters.
        'intern_confirmation_letter' => 'INTERN CONFIRMATION LETTER',
        'intern_internship_letter' => 'INTERN INTERNSHIP LETTER',
    ];

    $sanitize = function ($value) {
        return strtoupper(
            trim(
                preg_replace(
                    '/_+/',
                    '_',
                    preg_replace('/[^A-Za-z0-9]+/', '_', (string) $value)
                ),
                '_'
            )
        );
    };

    $zipPath = null;
    $zip = null;

    try {

        // Generate unique filename for every letter.
        $generateFileName = function ($letter) use ($sanitize, $internshipTypeMap) {

            $studentName = $sanitize(
                $letter->student->student_name ?? 'STUDENT'
            );

            $fatherName = $sanitize(
                $letter->student->f_name ?? ''
            );

            $letterName = $sanitize(
                $internshipTypeMap[$letter->internship_type]
                    ?? 'INTERNSHIP LETTER'
            );

            // Unique database ID prevents duplicate filenames.
            return "{$studentName}_{$fatherName}_{$letterName}_{$letter->id}.pdf";
        };

        // Single letter: Direct PDF download.
        if ($letters->count() === 1) {

            $letter = $letters->first();

            $pdfContent = $this->pdf($letter);

            $fileName = $generateFileName($letter);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header(
                    'Content-Disposition',
                    'attachment; filename="' . $fileName . '"'
                );
        }

        // Multiple letters: ZIP download.
        $zipPath = tempnam(sys_get_temp_dir(), 'student_letters_');

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::OVERWRITE) !== true) {

            @unlink($zipPath);

            return back()->with(
                'error',
                'Unable to create ZIP file. Please try again.'
            );
        }

        foreach ($letters as $letter) {

            $pdfContent = $this->pdf($letter);

            $fileName = $generateFileName($letter);

            if (!$zip->addFromString($fileName, $pdfContent)) {
                throw new \RuntimeException(
                    "Unable to add letter ID {$letter->id} to ZIP."
                );
            }
        }

        $zip->close();
        $zip = null;

        return response()
            ->download($zipPath, 'Additional_Letters.zip')
            ->deleteFileAfterSend(true);

    } catch (\Throwable $e) {

        if ($zip instanceof \ZipArchive) {
            $zip->close();
        }

        if ($zipPath && file_exists($zipPath)) {
            @unlink($zipPath);
        }

        report($e);

        return back()->with(
            'error',
            'Something went wrong while generating selected letters. Please try again.'
        );
    }
}


    public function bulkDownload_16nov(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|distinct|exists:student_additional_letters,id',
        ]);

        $activeSessionNo = session('admin_session_id');

        // Fetch only selected letters belonging to the active session
        $letters = StudentAdditionalLetter::with('student')
            ->whereIn('id', $request->ids)
            ->whereHas('student', function ($q) use ($activeSessionNo) {
                $q->where('session', $activeSessionNo);
            })
            ->get();

        // Ensure all requested records belong to the active session
        if ($letters->count() !== count($request->ids)) {
            return back()->with(
                'error',
                'Some selected letters are invalid or do not belong to the active session.'
            );
        }

        // Validate Offer and Strict Offer Letter fields
        foreach ($letters as $letter) {

            $missingFields = $this->getMissingOfferFields($letter);

            if (!empty($missingFields)) {

                $studentName = $letter->student->student_name
                    ?? 'Unknown Student';

                $letterName = $letter->internship_type === 'offer'
                    ? 'Offer Letter'
                    : 'Strict Offer Letter';

                return back()->with(
                    'error',
                    "Cannot download {$letterName} for {$studentName}. Please add: "
                    . implode(', ', $missingFields) . '.'
                );
            }
        }

        // Create temporary ZIP file
        $zipPath = tempnam(sys_get_temp_dir(), 'student_letters_');

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);

            return back()->with(
                'error',
                'Unable to create ZIP file. Please try again.'
            );
        }

        $internshipTypeMap = [
            'free' => 'FREE INTERNSHIP LETTER',
            'stipend' => 'STIPEND INTERNSHIP LETTER',
            'offer' => 'OFFER LETTER',
            'custom' => 'CUSTOM LETTER',
            'mutual_consent' => 'MUTUAL CONSENT LETTER',
            'noc' => 'INTERNSHIP NO OBJECTION CERTIFICATE',
            'training_consent' => 'TRAINING CONSENT LETTER',
            'placement' => 'PLACEMENT CONSENT LETTER',
            'internship' => 'INTERNSHIP LETTER',
            'internship_with_package' => 'INTERNSHIP WITH PACKAGE LETTER',
            'strict_offer_letter' => 'STRICT OFFER LETTER',
            'strict_consent_letter' => 'STRICT CONSENT LETTER',
            'stipend_policy' => 'STIPEND POLICY LETTER',
            'internship_consent' => 'INTERNSHIP CONSENT LETTER',
            'part_time_job_opportunity' => 'PART TIME JOB OPPORTUNITY LETTER',
            'with_roll_number' => 'WITH ROLL NUMBER LETTER',
            'with_roll_number_internship' => 'WITH ROLL NUMBER INTERNSHIP LETTER',
            'confirmation_letter' => 'CONFIRMATION LETTER',
            'student_responsibility' => 'STUDENT RESPONSIBILITY LETTER',
            'placed_student_responsibility' => 'PLACED STUDENT RESPONSIBILITY LETTER',
        ];

        $sanitize = function ($value) {
            return strtoupper(
                trim(
                    preg_replace(
                        '/_+/',
                        '_',
                        preg_replace('/[^A-Za-z0-9]+/', '_', (string) $value)
                    ),
                    '_'
                )
            );
        };

        try {

            foreach ($letters as $letter) {

                // Generate PDF using your existing PDF method
                $pdfContent = $this->pdf($letter);

                $studentName = $sanitize(
                    $letter->student->student_name ?? 'STUDENT'
                );

                $fatherName = $sanitize(
                    $letter->student->f_name ?? ''
                );

                $internshipFullName = $sanitize(
                    $internshipTypeMap[$letter->internship_type]
                        ?? 'INTERNSHIP LETTER'
                );

                // Include record ID to prevent duplicate ZIP filenames
                $fileName = "{$studentName}_{$fatherName}_{$internshipFullName}_{$letter->id}.pdf";

                $zip->addFromString($fileName, $pdfContent);
            }

            $zip->close();

            return response()
                ->download($zipPath, 'Student_Additional_Letters.zip')
                ->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

            $zip->close();

            if (file_exists($zipPath)) {
                @unlink($zipPath);
            }

            report($e);

            return back()->with(
                'error',
                'Something went wrong while generating selected letters. Please try again.'
            );
        }
    }

    private function studentLetterTypes(): array
{
    return [
        'free' => 'Free Internship Letter',
        'stipend' => 'Stipend Letter',
        'offer' => 'Offer Letter',
        'custom' => 'Custom Letter',
        'mutual_consent' => 'Mutual Consent Letter',
        'noc' => 'No Objection Certificate',
        'training_consent' => 'Training Consent Letter',
        'placement' => 'Placement Consent Letter',
        'internship' => 'Internship Letter',
        'internship_with_package' => 'Internship With Package Letter',
        'strict_offer_letter' => 'Strict Offer Letter',
        'strict_consent_letter' => 'Strict Consent Letter',
        'stipend_policy' => 'Stipend Policy Letter',
        'internship_consent' => 'Internship Consent Letter',
        'part_time_job_opportunity' => 'Part Time Job Opportunity Letter',
        'with_roll_number' => 'With Roll Number Letter',
        'with_roll_number_internship' => 'With Roll Number Internship Letter',
        'confirmation_letter' => 'Confirmation Letter',
        'student_responsibility' => 'Student Responsibility Letter',
        'placed_student_responsibility' => 'Placed Student Responsibility Letter',
        'additional_job_opportunity' => 'Additional Job Opportunity Letter',
    ];
}

private function internLetterTypes(): array
{
    return [
        'intern_confirmation_letter' => 'Intern Confirmation Letter',
        'intern_internship_letter' => 'Intern Internship Letter',
        'offer' => 'Offer Letter',
        'strict_offer_letter' => 'Strict Offer Letter',
    ];
}

}
