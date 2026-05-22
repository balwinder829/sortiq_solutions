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
        return view('student_additional_letters.create', compact('colleges','students'));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'internship_type' => [
            'required',
            Rule::in([
                'free','stipend','offer','custom',
                'mutual_consent','noc','training_consent',
                'placement','internship','internship_with_package','strict_offer_letter', 'strict_consent_letter', 'stipend_policy', 'internship_consent','part_time_job_opportunity'
            ]),
        ],

        // 👇 changed to array (multi student)
        'student_id'      => 'required|array',
        'student_id.*'    => 'exists:students_detail,id',

        'subject'         => 'required_if:internship_type,custom|max:255',
        'letter_content'  => 'required_if:internship_type,custom,stipend',
        'issue_date'      => 'nullable|date',
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
            'letter_content'  => $request->letter_content,
            'issue_date'      => $request->issue_date 
                                ? $request->issue_date 
                                : Carbon::today()->toDateString(),
        ]);

        $created++;
    }

    return redirect()
        ->route('student-additional-letters.index')
        ->with('success', "$created letter(s) created successfully. $skipped skipped (already existed).");
}
    public function store13ap(Request $request)
    {
        $data = $request->validate([
            // 'internship_type' => 'required|in:free,stipend,offer,custom,mutual_consent,noc,training_consent,placement',
             'internship_type' => [
                'required',
                Rule::in(['free','stipend','offer','custom','mutual_consent','noc','training_consent','placement','internship','internship_with_package','strict_offer_letter', 'strict_consent_letter', 'stipend_policy', 'internship_consent','part_time_job_opportunity']),
                Rule::unique('student_additional_letters')
                    ->where('student_id', $request->student_id),
            ],
            'subject'         => 'required_if:internship_type,custom|max:255',
            'student_id'      => 'required|exists:students_detail,id',
            'letter_content'  => 'required_if:internship_type,custom,stipend',
            'issue_date'      => 'nullable|date',
        ], [
            'internship_type.unique' => 'This letter type already exists for this student.',
        ]);

         // 👉 Set default date if not provided
    $data['issue_date'] = $request->issue_date 
        ? $request->issue_date 
        : Carbon::today()->toDateString();

        StudentAdditionalLetter::create($data);

        return redirect()
            ->route('student-additional-letters.index')
            ->with('success', 'Letter created successfully.');
    }

    public function edit(StudentAdditionalLetter $studentAdditionalLetter)
    {
        $activeSessionNo = session('admin_session_id');
        $students = Student::where('session', $activeSessionNo)->orderBy('student_name')->get();
        return view('student_additional_letters.edit', [
            'letter' => $studentAdditionalLetter,
            'students' => $students
        ]);
    }

    public function update(Request $request, StudentAdditionalLetter $studentAdditionalLetter)
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
                    'part_time_job_opportunity'
                ]),
                Rule::unique('student_additional_letters')
                    ->where('student_id', $request->student_id)
                    ->ignore($studentAdditionalLetter->id),
            ],
            'subject'         => 'required_if:internship_type,custom|max:255',
            'student_id'      => 'required|exists:students_detail,id',
            'letter_content'  => 'required_if:internship_type,custom,stipend',
            'issue_date'      => 'nullable|date',
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

    /**
     * Download PDF
     */
    // public function pdf1($id)
    // {
    //     $letter = StudentAdditionalLetter::findOrFail($id);

    //     $content = $this->prepareContent($letter);

    //     $mpdf = new Mpdf();
    //     $mpdf->WriteHTML(
    //         view('student_additional_letters.pdf', compact('content'))->render()
    //     );

    //     return $mpdf->Output('student-letter.pdf', 'I');
    // }

    private function pdf12(StudentAdditionalLetter $letter): string
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

 		$letter = StudentAdditionalLetter::findOrFail($letter);
 		$content = $this->prepareContent($letter);
        
        $html = View::make('student_additional_letters.pdf', compact('content'))->render();

        $mpdf->SetHTMLHeader('');
            // $mpdf->SetHTMLFooter('');
            $mpdf->DefHTMLFooterByName('emptyFooter', '');

            // 2️⃣ Define last page footer
            $mpdf->DefHTMLFooterByName('bondFooter', $this->getPDFFooter());

            // 3️⃣ Apply empty footer initially
            // 

         
            $mpdf->SetHTMLFooterByName('bondFooter');
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('emptyFooter');
       	return $mpdf->Output('student-letter.pdf', 'I');
        return $mpdf->Output('', 'S');
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
            
            default => 'letters.pdf',
        };

     $html = View::make($view, compact('letter'))->render();
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

public function download(StudentAdditionalLetter $StudentAdditionalLetter)
{
    $pdfContent = $this->pdf($StudentAdditionalLetter);

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

    public function download12(StudentAdditionalLetter $letter)
    {
        $pdfContent = $this->pdf($letter);
       

          // Clean & format values
       $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->letter_type))
        );

        $employeeName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->emp_name))
        );

        $profileName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->position))
        );

        // Remove double underscores just in case
        $fileName = trim(
            preg_replace('/_+/', '_', "{$employeeName}_{$profileName}_{$letterType}"),
            '_'
        ) . '.pdf';
        // return response($pdfContent)
        //     ->header('Content-Type', 'application/pdf')
        //     ->header(
        //         'Content-Disposition',
        //         'attachment; filename="'.strtoupper($letter->letter_type).'_LETTER.pdf"'
        //     );
         return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );
    }

    /**
     * Send Email with PDF Attachment
     */
    public function sendEmai12l($id)
    {
        $letter = StudentAdditionalLetter::findOrFail($id);

        $content = $this->prepareContent($letter);

        // Generate PDF as string
        $mpdf = new Mpdf();
        $mpdf->WriteHTML(
            view('student_additional_letters.pdf', compact('content'))->render()
        );
        $pdf = $mpdf->Output('', 'S');

        Mail::send([], [], function ($message) use ($letter, $pdf) {
            $message->to($letter->email)
                ->subject('Internship Letter')
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
}
