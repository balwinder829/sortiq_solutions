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

class StudentAdditionalLetterController extends Controller
{	
	use PdfLayoutTrait;
    protected string $permissionPrefix = 'student_letters';

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
    public function index(Request $request)
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


    public function create()
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

    public function edit(StudentAdditionalLetter $studentAdditionalLetter)
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


public function update(Request $request, StudentAdditionalLetter $studentAdditionalLetter)
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

    public function destroy(StudentAdditionalLetter $studentAdditionalLetter)
    {
        $studentAdditionalLetter->delete();

        return back()->with('success', 'Letter deleted successfully.');
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


public function download(StudentAdditionalLetter $StudentAdditionalLetter)
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


public function preview(StudentAdditionalLetter $studentAdditionalLetter)
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
}
