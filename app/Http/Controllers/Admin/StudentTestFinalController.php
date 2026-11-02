<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\StudentTestFinal;
use App\Models\StudentTest;
use App\Exports\FinalStudentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\File;
use App\Traits\PdfLayoutTrait;

class StudentTestFinalController extends Controller
{

    use PdfLayoutTrait;

    protected string $permissionPrefix = 'student_final_students';

    protected array $permissionMap = [
        'index'        => 'view',
        
        'downloadFinalStudentLetters'         => 'download_letters',
        
        'export'       => 'export',
        'bulkUpdateCategory'        => 'edit',

        'update'       => 'edit',
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
    
    /**
     * Benefit tabs are based on the existing benefit_type column.
     * Add future benefit types here when needed.
     */
    private array $benefitTypes = [
        'free' => 'Free Internship',
        'discount' => 'Discounted Internship',
        'stipend' => 'Stipend-Based Internship',
        '3_month_ppo' => '3-Month Internship with PPO',
    ];

    /**
     * Certificate types are separate from benefit_type so certificate options
     * can expand without changing the student table.
     */
    private array $certificateTypes = [
        'placement' => 'Placement Certificate',
        'offer' => 'Offer Letter',
        'free_internship' => 'Free Internship Certificate',
        'internship' => 'Internship Certificate',
        'stipend_internship' => 'Stipend-Based Internship Certificate',
        'ppo' => 'Pre-Placement Offer (PPO) Letter',
    ];

    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'all');

        $allowedTabs = array_merge(['all'], array_keys($this->benefitTypes));
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'all';
        }

        $query = StudentTestFinal::query();

        // Join student_tests only when the Class filter is selected.
        if ($request->filled('class')) {
            $query->join(
                'student_tests as st',
                'st.id',
                '=',
                'student_test_final.source_student_test_id'
            )->where('st.class', $request->input('class'))
             ->select('student_test_final.*');
        }

        if ($activeTab !== 'all') {
            $query->where('benefit_type', $activeTab);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('student_mobile', 'like', "%{$search}%")
                    ->orWhere('college_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->input('college_id'));
        }

        if ($request->filled('final_status')) {
            $query->where('final_status', $request->input('final_status'));
        }

        // Return the filtered collection; DataTables handles pagination and horizontal scrolling.
        $students = $query->orderByDesc('id')->get();

        $collegeIds = StudentTestFinal::query()
            ->whereNotNull('college_id')
            ->distinct()
            ->pluck('college_id');

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        $statusOptions = StudentTestFinal::query()
            ->whereNotNull('final_status')
            ->distinct()
            ->orderBy('final_status')
            ->pluck('final_status');

        // All unique, non-empty Classes from the main student_tests table.
        $classes = StudentTest::query()
            ->whereNotNull('class')
            ->where('class', '<>', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        $tabCounts = ['all' => StudentTestFinal::count()];
        foreach ($this->benefitTypes as $key => $label) {
            $tabCounts[$key] = StudentTestFinal::where('benefit_type', $key)->count();
        }

        return view('admin.final-students.index', [
            'students' => $students,
            'colleges' => $colleges,
            'statusOptions' => $statusOptions,
            'classes' => $classes,
            'activeTab' => $activeTab,
            'benefitTypes' => $this->benefitTypes,
            'certificateTypes' => $this->certificateTypes,
            'tabCounts' => $tabCounts,
        ]);
    }
    
    public function index_19oct(Request $request)
    {
        $activeTab = $request->input('tab', 'all');

        $allowedTabs = array_merge(['all'], array_keys($this->benefitTypes));
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'all';
        }

        $query = StudentTestFinal::query();

        if ($activeTab !== 'all') {
            $query->where('benefit_type', $activeTab);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('student_mobile', 'like', "%{$search}%")
                    ->orWhere('college_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->input('college_id'));
        }

        if ($request->filled('final_status')) {
            $query->where('final_status', $request->input('final_status'));
        }

        // Return the filtered collection; DataTables handles pagination and horizontal scrolling.
        $students = $query->orderByDesc('id')->get();

        $collegeIds = StudentTestFinal::query()
            ->whereNotNull('college_id')
            ->distinct()
            ->pluck('college_id');

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        $statusOptions = StudentTestFinal::query()
            ->whereNotNull('final_status')
            ->distinct()
            ->orderBy('final_status')
            ->pluck('final_status');

        $tabCounts = ['all' => StudentTestFinal::count()];
        foreach ($this->benefitTypes as $key => $label) {
            $tabCounts[$key] = StudentTestFinal::where('benefit_type', $key)->count();
        }

        return view('admin.final-students.index', [
            'students' => $students,
            'colleges' => $colleges,
            'statusOptions' => $statusOptions,
            'activeTab' => $activeTab,
            'benefitTypes' => $this->benefitTypes,
            'certificateTypes' => $this->certificateTypes,
            'tabCounts' => $tabCounts,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'tab', 'search', 'college_id', 'final_status', 'class',
        ]);

        return Excel::download(
            new FinalStudentsExport($filters, $this->benefitTypes),
            'final-students-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Download certificates for the selected IDs only.
     * The certificate type is validated independently from internship category.
     */
    public function downloadCertificates(Request $request)
    {
        $validated = $request->validate([
            'student_final_ids' => ['required', 'array', 'min:1'],
            'student_final_ids.*' => [
                'required', 'integer', 'distinct',
                'exists:student_test_final,id',
            ],
            'certificate_type' => [
                'required',
                Rule::in(array_keys($this->certificateTypes)),
            ],
            'course_name' => ['nullable', 'string', 'max:255'],
            'certificate_date' => ['nullable', 'date'],
            'custom_details' => ['nullable', 'string', 'max:2000'],
        ]);

        $students = StudentTestFinal::query()
            ->whereIn('id', $validated['student_final_ids'])
            ->orderBy('student_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No active Final Students were found.');
        }

        $certificateType = $this->certificateTypes[$validated['certificate_type']];
        $certificateDate = $validated['certificate_date'] ?? now()->toDateString();
        $courseName = $validated['course_name'] ?? 'Internship / Professional Training';
        $customDetails = $validated['custom_details'] ?? null;

        // One selected student = one certificate page; multiple selected students
        // are combined into one downloadable PDF.
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        if (!is_dir(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0755, true);
        }

        foreach ($students as $index => $student) {
            $html = view('admin.final-students.certificate', [
                'student' => $student,
                'certificateType' => $certificateType,
                'certificateDate' => $certificateDate,
                'courseName' => $courseName,
                'customDetails' => $customDetails,
            ])->render();

            if ($index > 0) {
                $mpdf->AddPage();
            }
            $mpdf->WriteHTML($html);
        }

        $safeType = preg_replace('/[^A-Za-z0-9_-]/', '-', $validated['certificate_type']);
        $filename = $safeType . '-certificates-' . now()->format('Ymd-His') . '.pdf';

        return response()->streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Change internship category for selected final-student records.
     * The operation updates only benefit_type; it does not create/move records.
     */
    public function bulkUpdateCategory(Request $request)
    {
        $validated = $request->validate([
            'student_final_ids' => ['required', 'array', 'min:1'],
            'student_final_ids.*' => [
                'required', 'integer', 'distinct',
                'exists:student_test_final,id',
            ],
            'benefit_type' => [
                'required',
                Rule::in(array_keys($this->benefitTypes)),
            ],
        ]);

        $updated = StudentTestFinal::query()
            ->whereIn('id', $validated['student_final_ids'])
            ->update([
                'benefit_type' => $validated['benefit_type'],
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

        $label = $this->benefitTypes[$validated['benefit_type']];

        return redirect()
            ->route('admin.final-students.index', ['tab' => 'all'])
            ->with('success', "{$updated} student(s) updated to {$label}.");
    }

    public function update(Request $request, StudentTestFinal $finalStudent)
    {
        $validated = $request->validate([
            'final_status' => ['required', 'string', 'max:30'],
            'benefit_type' => ['nullable', Rule::in(array_keys($this->benefitTypes))],
            'benefit_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'benefit_details' => ['nullable', 'string', 'max:10000'],
            'offer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'course_name' => ['nullable', 'string', 'max:255'],
        ]);

        $finalStudent->fill($validated);
        $finalStudent->updated_by = auth()->id();
        $finalStudent->save();

        return back()->with('success', 'Final Student updated successfully.');
    }

    /**
 * Add more letter types here later.
 *
 * key => Blade template, filename label
 */
private array $finalStudentLetterTypes = [
    'offer' => [
        'view' => 'student_additional_letters.offer_letter_fixed_pdf',
        'label' => 'OFFER_LETTER',
        'internship_type' => 'offer',
    ],
    'placement' => [
        'view' => 'student_additional_letters.placement_consent_pdf',
        'label' => 'PLACEMENT_LETTER',
        'internship_type' => 'placement',
    ],
];

/**
 * Generate offer or placement letters for selected Final Students.
 */

public function downloadFinalStudentLetters(Request $request)
{
    $validated = $request->validate([
        'student_final_ids' => ['required', 'array', 'min:1'],
        'student_final_ids.*' => [
            'required',
            'integer',
            'distinct',
            'exists:student_test_final,id',
        ],
        'letter_type' => [
            'required',
            Rule::in(array_keys($this->finalStudentLetterTypes)),
        ],
        'course_name' => ['required', 'string', 'max:255'],
        'letter_date' => ['required', 'date'],
    ], [
        'student_final_ids.required' => 'Please select at least one student.',
        'letter_type.required' => 'Please select a letter type.',
        'course_name.required' => 'Please enter the course / technology.',
        'letter_date.required' => 'Please select the letter date.',
    ]);

    $students = StudentTestFinal::query()
        ->whereIn('id', $validated['student_final_ids'])
        ->with('college')
        ->orderBy('student_name')
        ->get();

    if ($students->isEmpty()) {
        return back()->with('error', 'No selected Final Students were found.');
    }

    // One course entered in the modal applies to every selected student.
    $courseName = trim($validated['course_name']);
    $letterConfig = $this->finalStudentLetterTypes[$validated['letter_type']];
    $letterDate = $validated['letter_date'];

    // Check only details that are genuinely required per student.
    $missing = [];

    foreach ($students as $student) {
        $fields = [];

        if (blank($student->student_name)) {
            $fields[] = 'Student name';
        }

        if (blank($student->college_id) || !$student->college) {
            $fields[] = 'College';
        }

        if ($fields) {
            $missing[] = trim($student->student_name ?: 'Final Student #' . $student->id)
                . ': ' . implode(', ', $fields);
        }
    }

    if ($missing) {
        return back()
            ->withInput()
            ->with('letter_missing_fields', $missing)
            ->with('error', 'Letters were not generated. Complete the missing student details and try again.');
    }

    $tempDirectory = storage_path('app/mpdf');
    $zipDirectory = storage_path('app/final-student-letters');

    foreach ([$tempDirectory, $zipDirectory] as $directory) {
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    $zipPath = $zipDirectory . DIRECTORY_SEPARATOR
        . 'final-student-letters-' . now()->format('Ymd-His') . '-' . uniqid() . '.zip';

    $zip = new \ZipArchive();

    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        return back()->with('error', 'Unable to create the letters ZIP file.');
    }

    try {
        foreach ($students as $student) {
            $college = $student->college;
            $college->loadMissing(['district', 'state']);

            // Adapt Final Student data to the existing letter templates.
            $studentData = (object) [
                'id' => $student->id,
                'student_name' => $student->student_name,
                'f_name' => '',
                'father_name' => '',
                'email_id' => $student->student_email,
                'contact' => $student->student_mobile,
                'college_name' => $student->college_name,
                'collegeData' => $college,
                'course_name' => $courseName,
                'start_date' => $student->offer_date
                    ? $student->offer_date->format('Y-m-d')
                    : $letterDate,
                'is_place' => false,
                'place' => '',
                'gender' => '',
                'is_married' => false,
                 'father_name_with_title' => '',
            ];

            $letter = (object) [
                'id' => $student->id,
                'student_id' => null,
                'student' => $studentData,
                'issue_date' => $letterDate,
                'internship_type' => $letterConfig['internship_type'],
                'letter_content' => '',
                'subject' => $letterConfig['label'],
            ];

            $html = view($letterConfig['view'], compact('letter'))->render();

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'tempDir' => $tempDirectory,
            ]);

            $mpdf->SetHTMLHeader('');
            $mpdf->DefHTMLFooterByName(
                'lastPageFooter',
                $this->getPDFFooter()
            );

            $mpdf->WriteHTML($html);
            $mpdf->SetHTMLFooterByName('lastPageFooter');

            $pdfContent = $mpdf->Output('', 'S');

            $safeName = strtoupper(
                trim(
                    preg_replace(
                        '/_+/',
                        '_',
                        preg_replace('/[^A-Za-z0-9]+/', '_', $student->student_name)
                    ),
                    '_'
                )
            );

            $pdfFilename = $safeName
                . '_' . $letterConfig['label']
                . '_' . $student->id
                . '.pdf';

            if (!$zip->addFromString($pdfFilename, $pdfContent)) {
                throw new \RuntimeException('Unable to add a generated letter to the ZIP.');
            }
        }

        if (!$zip->close()) {
            throw new \RuntimeException('Unable to finalize the letters ZIP.');
        }
    } catch (\Throwable $e) {

        dd($e);
    $zip->close();

    if (File::exists($zipPath)) {
        File::delete($zipPath);
    }

    \Illuminate\Support\Facades\Log::error(
        'Final student letter generation failed',
        [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]
    );

    return back()->with(
        'error',
        'Letter generation failed. Check storage/logs/laravel.log for the exact error.'
    );
}

    return response()->download(
        $zipPath,
        $letterConfig['label'] . '_letters_' . now()->format('Ymd-His') . '.zip'
    )->deleteFileAfterSend(true);
}

}
