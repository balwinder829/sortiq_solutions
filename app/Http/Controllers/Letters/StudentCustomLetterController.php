<?php

namespace App\Http\Controllers\Letters;

use App\Http\Controllers\Controller;

use App\Models\StudentCustomLetter;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class StudentCustomLetterController extends Controller
{
    use PdfLayoutTrait;

    protected string $permissionPrefix = 'letters';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'download'         => 'view',
        'sendEmail'         => 'view',
         

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

        $query = StudentCustomLetter::
                where('session_id', $activeSessionNo);

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        return view('student_custom_letters.index', [
            // 'letters' => $query->latest()->get()
            'letters' => $query->orderBy('updated_at', 'desc')->get()
            // 'letters' => $query->orderBy('updated_at', 'desc')->get()

        ]);
    }

    public function create()
    {   
        $activeSessionNo = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionNo);
        return view('student_custom_letters.create', compact('activeSession'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'letter_type' => [
                'required',
                Rule::in([
                    'part_time_job_opportunity',
                    'strict_offer_letter',
                    'offer_letter',
                    'strict_consent_letter',
                    'internship_consent',
                    'stipend_policy'
                ]),
            ],

            'student_name' => 'required|string',
            'college'      => 'nullable|string',
            // 'father_name' => 'nullable|string',
            // 'course_branch' => 'nullable|string',
            // 'contact_no' => 'nullable|string',
            // 'email_id' => 'nullable|email',
            // 'training_domain' => 'nullable|string',
            // 'batch_mode' => 'nullable|string',
            // 'joining_date' => 'nullable|date',
            // 'completion_date' => 'nullable|date',
            // 'reporting_mentor' => 'nullable|string',
            // 'internship_mode' => 'nullable|string',

            'issue_date'   => 'nullable|date',

            // Required only for strict_offer_letter
            'training_start_date' => [
                'nullable',
                'date',
                 Rule::requiredIf(in_array(
                    $request->letter_type,
                    ['strict_offer_letter', 'offer_letter']
                )),
            ],

            'training_duration' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_offer_letter',
                        'offer_letter',
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],

            'father_name' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],

            'course_branch' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'contact_no' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'email_id' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'training_domain' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],
            'batch_mode' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],

            'joining_date' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],

            'completion_date' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'internship_consent',
                    ]
                )),
            ],
            'reporting_mentor' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'stipend_policy'
                    ]
                )),
            ],
            'internship_mode' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'stipend_policy'
                    ]
                )),
            ],

            'probation_period' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            'working_hours' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            'bond_duration' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            // Required for both strict_offer_letter and offer_letter
            'position' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    ['strict_offer_letter', 'offer_letter']
                )),
            ],

        ]);

        $data['session_id'] = session('admin_session_id');

        $data['issue_date'] = $data['issue_date']
            ?? Carbon::today()->toDateString();

        StudentCustomLetter::create($data);

        return redirect()
            ->route('student-custom-letters.index')
            ->with('success', 'Student Letter created successfully.');
    }
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'letter_type' => [
    //             'required',
    //             Rule::in([
    //                 'part_time_job_opportunity','strict_offer_letter','offer_letter'
    //             ]),
    //         ],

    //         // 👇 changed to array (multi student)
    //         'student_name'      => 'required|string',
    //         'training_duration'      => 'nullable|string',
    //         'probation_period'      => 'nullable|string',
    //         'working_hours'      => 'nullable|string',
    //         'bond_duration'      => 'nullable|string',
    //         'position'      => 'nullable|string',
    //         'college'      => 'nullable|string',
    //         // 'student_id.*'    => 'exists:students_detail,id',

    //         // 'subject'         => 'required_if:internship_type,custom|max:255',
    //         // 'letter_content'  => 'required_if:internship_type,custom,stipend',
    //         'issue_date'      => 'nullable|date',
    //         'training_start_date'      => 'nullable|date',
    //     ], [
    //         // custom message kept
    //         // (unique removed because now handled manually)
    //     ]);

    //     $activeSessionNo = session('admin_session_id');
    //     StudentCustomLetter::create([
    //         'session_id'      => $activeSessionNo,
    //         'letter_type' => $request->letter_type,
    //         'student_name'         => $request->student_name,
    //         'college'         => $request->college,
    //         'issue_date'      => $request->issue_date 
    //                             ? $request->issue_date 
    //                             : Carbon::today()->toDateString(),
    //     ]);

            

    //     return redirect()
    //         ->route('student-custom-letters.index')
    //         ->with('success', "Student Letter created successfully.");
    // }
        
    public function edit($id)
    {
        $activeSessionNo = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionNo);

        $letter = StudentCustomLetter::findOrFail($id);

        return view(
            'student_custom_letters.edit',
            compact('letter', 'activeSession')
        );
    }

    public function update(Request $request, $id)
    {
        $letter = StudentCustomLetter::findOrFail($id);

        $data = $request->validate([

            'letter_type' => [
                'required',
                Rule::in([
                    'part_time_job_opportunity',
                    'strict_offer_letter',
                    'offer_letter',
                    'strict_consent_letter',
                    'internship_consent',
                    'stipend_policy'
                ]),
            ],

            'student_name' => 'required|string',

            'college' => 'required|string',
            // 'father_name' => 'nullable|string',
            // 'course_branch' => 'nullable|string',
            // 'contact_no' => 'nullable|string',
            // 'email_id' => 'nullable|email',
            // 'training_domain' => 'nullable|string',
            // 'batch_mode' => 'nullable|string',
            // 'joining_date' => 'nullable|date',
            // 'completion_date' => 'nullable|date',
            // 'reporting_mentor' => 'nullable|string',
            // 'internship_mode' => 'nullable|string',

            'issue_date' => 'nullable|date',

            
            'father_name' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],

            'course_branch' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'contact_no' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'email_id' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],
            'training_domain' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],
            'batch_mode' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                    ]
                )),
            ],

            'joining_date' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],

            'completion_date' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'internship_consent',
                    ]
                )),
            ],
            'reporting_mentor' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'stipend_policy'
                    ]
                )),
            ],
            'internship_mode' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'stipend_policy'
                    ]
                )),
            ],

            // Required for offer_letter & strict_offer_letter
            'training_start_date' => [
                'nullable',
                'date',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    ['offer_letter', 'strict_offer_letter']
                )),
            ],

            // Required only for strict_offer_letter
            'training_duration' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    [
                        'strict_offer_letter',
                        'offer_letter',
                        'strict_consent_letter',
                        'internship_consent',
                        'stipend_policy'
                    ]
                )),
            ],

            'probation_period' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            'working_hours' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            'bond_duration' => [
                'nullable',
                'string',
                'required_if:letter_type,strict_offer_letter',
            ],

            // Required for offer_letter & strict_offer_letter
            'position' => [
                'nullable',
                'string',
                Rule::requiredIf(in_array(
                    $request->letter_type,
                    ['offer_letter', 'strict_offer_letter']
                )),
            ],

        ]);

        // default issue date
        $data['issue_date'] = $data['issue_date']
            ?? Carbon::today()->toDateString();

        // keep session updated
        $data['session_id'] = session('admin_session_id');

        $letter->update($data);

        return redirect()
            ->route('student-custom-letters.index')
            ->with('success', 'Student Letter updated successfully.');
    }

    public function update20may(Request $request, $id)
    {
        $letter = StudentCustomLetter::findOrFail($id);

        $data = $request->validate([
            'letter_type' => [
                'required',
                Rule::in([
                    'part_time_job_opportunity'
                ]),
            ],

            'student_name' => 'required|string',
            'issue_date'   => 'nullable|date',
        ]);

        $letter->update([
            'letter_type' => $request->letter_type,
            'student_name' => $request->student_name,
            'college' => $request->college,
            'issue_date' => $request->issue_date
                ? $request->issue_date
                : Carbon::today()->toDateString(),
        ]);

        return redirect()
            ->route('student-custom-letters.index')
            ->with('success', 'Student Letter updated successfully.');
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

         
        $letter->load('sessionData');
        
         $view = match ($letter->letter_type) {

            'part_time_job_opportunity'
                => 'student_custom_letters.part_time_job_opportunity_letter_pdf',

            'offer_letter'
                => 'student_custom_letters.offer_letter_fixed_pdf',

            'strict_offer_letter'
                => 'student_custom_letters.strict_offer_letter_pdf',

            'strict_consent_letter'
                => 'student_custom_letters.strict_consent_letter_pdf',

            'internship_consent'
                => 'student_custom_letters.internship_consent_pdf',

            'stipend_policy'
                => 'student_custom_letters.stipend_policy_pdf',

            default => 'letters.pdf',
        };

         $html = View::make($view, compact('letter'))->render();
       
        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
              
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');
        // RETURN STRING ONLY
        return $mpdf->Output('', 'S');
    }

    public function download(StudentCustomLetter $studentCustomLetter)
    {
        // dd($studentCustomLetter);
        $pdfContent = $this->pdf($studentCustomLetter);

        // Clean filename (VALID FIELDS ONLY)

        $internshipTypeMap = [

            'part_time_job_opportunity'
                => 'PART TIME JOB OPPORTUNITY LETTER',

            'offer_letter'
                => 'OFFER LETTER',

            'strict_offer_letter'
                => 'STRICT OFFER LETTER',

            'strict_consent_letter'
                => 'STRICT CONSENT LETTER',

            'internship_consent'
                => 'INTERNSHIP CONSENT LETTER',

            'stipend_policy'
                => 'STIPEND POLICY LETTER',
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
        // dd($studentCustomLetter);
        $studentName =  $sanitize(($studentCustomLetter->student_name));
        // $fatherName =  $sanitize(($studentCustomLetter->student->f_name));

         $internshipKey = $studentCustomLetter->letter_type;
        $internshipFullName = $sanitize(
            $internshipTypeMap[$internshipKey] ?? 'INTERNSHIP LETTER'
        );

        $fileName = "{$studentName}_{$internshipFullName}.pdf";

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
    
    
    public function destroy(StudentCustomLetter $student_custom_letter)
    {
        $student_custom_letter->delete();

        return redirect()
            ->route('student-custom-letters.index')
            ->with('success', 'Letter deleted successfully.');
    }

}
