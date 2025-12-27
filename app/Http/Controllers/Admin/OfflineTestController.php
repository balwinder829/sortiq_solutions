<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\College;
use App\Models\Course;
use App\Models\Semester;
use App\Models\TestCategory;
use App\Models\OfflineTestStudent;
use App\Models\Enquiry;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OfflineStudentsImport;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\StudentCourse;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;



class OfflineTestController extends Controller
{   

    use PdfLayoutTrait;
    public function index(Request $request)
{
    $gender = $request->filled('gender')
        ? strtolower($request->gender)
        : null;

    $tests = Test::where('test_mode', 'offline')
        ->with(['category','college','course','semester'])
        ->withCount([

            // TOTAL REGISTERED (OFFLINE + GENDER)
            'offlineTests as total_registered' => function ($q) use ($gender) {
                $q->where('source', 'offline');

                if ($gender) {
                    $q->whereRaw('LOWER(COALESCE(gender, "")) = ?', [$gender]);
                }
            },

            // FINALIZED COUNT (OFFLINE + GENDER)
            'offlineTests as selected_count' => function ($q) use ($gender) {
                $q->where('source', 'offline')
                  ->where('is_finalized', 1);

                if ($gender) {
                    $q->whereRaw('LOWER(COALESCE(gender, "")) = ?', [$gender]);
                }
            }
        ]);

    /* ===== EXISTING FILTERS ===== */

    if ($request->college_id)
        $tests->where('college_id', $request->college_id);

    if ($request->student_course_id)
        $tests->where('student_course_id', $request->student_course_id);

    if ($request->semester_id)
        $tests->where('semester_id', $request->semester_id);

    if ($request->test_category_id)
        $tests->where('test_category_id', $request->test_category_id);

    if ($request->status)
        $tests->where('status', $request->status);

    if ($request->filled('is_active'))
        $tests->where('is_active', $request->is_active);

    if ($request->from_date)
        $tests->whereDate('test_date', '>=', $request->from_date);

    if ($request->to_date)
        $tests->whereDate('test_date', '<=', $request->to_date);

    // ✅ FILTER TESTS BY GENDER (OFFLINE STUDENTS)
    if ($gender) {
        $tests->whereHas('offlineTests', function ($q) use ($gender) {
            $q->where('source', 'offline')
              ->whereRaw('LOWER(COALESCE(gender, "")) = ?', [$gender]);
        });
    }

    return view('admin.tests.offline.index', [
        'tests'     => $tests->latest()->get(),
        'colleges'  => College::all(),
        'courses'   => Course::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}

    public function index20dec(Request $request)
{
    $tests = Test::where('test_mode', 'offline')
        ->with(['category','college','course','semester'])
        ->withCount([
            // total registrations (offline only)
            'offlineTests as total_registered' => function ($q) {
                $q->where('source', 'offline');
            },
            // finalized selections
            'offlineTests as selected_count' => function ($q) {
                $q->where('source', 'offline')
                  ->where('is_finalized', 1);
            }
        ]);

    /* ===== EXISTING FILTERS (UNCHANGED) ===== */

    if ($request->college_id)
        $tests->where('college_id', $request->college_id);

    if ($request->student_course_id)
        $tests->where('student_course_id', $request->student_course_id);

    if ($request->semester_id)
        $tests->where('semester_id', $request->semester_id);

    if ($request->test_category_id)
        $tests->where('test_category_id', $request->test_category_id);

    if ($request->status)
        $tests->where('status', $request->status);

    if ($request->filled('is_active'))
        $tests->where('is_active', $request->is_active);

    if ($request->from_date)
        $tests->whereDate('test_date', '>=', $request->from_date);

    if ($request->to_date)
        $tests->whereDate('test_date', '<=', $request->to_date);

    return view('admin.tests.offline.index', [
        'tests'     => $tests->latest()->get(),
        'colleges'  => College::all(),
        'courses'   => Course::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}
    public function index2(Request $request)
    {
        $tests = Test::where('test_mode', 'offline');

        // Same filters (safe to duplicate)
        if ($request->college_id)
            $tests->where('college_id', $request->college_id);

        if ($request->student_course_id)
            $tests->where('student_course_id', $request->student_course_id);

        if ($request->semester_id)
            $tests->where('semester_id', $request->semester_id);

        if ($request->test_category_id)
            $tests->where('test_category_id', $request->test_category_id);

        if ($request->status)
            $tests->where('status', $request->status);

        if ($request->filled('is_active'))
            $tests->where('is_active', $request->is_active);

        if ($request->from_date)
            $tests->whereDate('test_date', '>=', $request->from_date);

        if ($request->to_date)
            $tests->whereDate('test_date', '<=', $request->to_date);

        return view('admin.tests.offline.index', [
            'tests'     => $tests->latest()->get(),
            'colleges'  => College::all(),
            'courses'   => Course::all(),
            'semesters' => Semester::all(),
            'categories'=> TestCategory::all(),
        ]);
    }

     /* RESULTS PAGE */

      public function results(Request $request, $test_id)
{
    /* ===== MOVED STUDENTS (OFFLINE) ===== */
    $movedStudentTestIds = Enquiry::where('test_id', $test_id)
        ->pluck('student_test_id') // 🔴 change if column name differs
        ->toArray();

    $test = Test::withCount('questions')->findOrFail($test_id);

    /* ===== STUDENT S.NO MAP ===== */
    // $students = Student::select('email_id', 'sno')
    //     ->get()
    //     ->keyBy('email_id');

    /* ===== BASE QUERY (OFFLINE STUDENTS) ===== */
    $studentTestsQuery = OfflineTestStudent::where('test_id', $test_id);

    /* ===== FILTER : S.NO ===== */
    // if ($request->filled('sno')) {
    //     $studentEmails = $students->filter(function ($student) use ($request) {
    //         return str_contains($student->sno, $request->sno);
    //     })->keys();

    //     $studentTestsQuery->whereIn('student_email', $studentEmails);
    // }

    /* ===== FILTER : NAME ===== */
    if ($request->filled('name')) {
        $studentTestsQuery->where('student_name', 'like', '%' . $request->name . '%');
    }

    /* ===== FILTER : EMAIL ===== */
    if ($request->filled('email')) {
        $studentTestsQuery->where('student_email', 'like', '%' . $request->email . '%');
    }

    /* ===== FILTER : FINALIZED ===== */
    if ($request->filled('finalized')) {
        $studentTestsQuery->where('is_finalized', $request->finalized);
    }

    /* ===== FILTER : MOVED ===== */
    if ($request->filled('moved')) {
        if ($request->moved === '1') {
            $studentTestsQuery->whereIn('id', $movedStudentTestIds);
        } elseif ($request->moved === '0' && !empty($movedStudentTestIds)) {
            $studentTestsQuery->whereNotIn('id', $movedStudentTestIds);
        }
    }

    /* ===== TOP N / SCORE ORDER ===== */
    if ($request->filled('top_n') && is_numeric($request->top_n)) {
        $studentTestsQuery
            ->orderByDesc('score')
            ->limit((int) $request->top_n);
    } else {
        $studentTestsQuery->orderByDesc('score');
    }

    $studentTests = $studentTestsQuery->get();

    /* ===== ATTACH S.NO ===== */
    // $studentTests->each(function ($st) use ($students) {
    //     $st->sno = $students[$st->student_email]->sno ?? '-';
    // });

    // dd($studentTests);

    return view(
        'admin.tests.offline.results',
        compact('test', 'studentTests', 'movedStudentTestIds')
    );
}

     public function results12(Request $request, Test $test)
{
    $students = OfflineTestStudent::where('test_id', $test->id)

        // 🔍 Filter by student name
        ->when($request->filled('name'), function ($q) use ($request) {
            $q->where('student_name', 'like', '%' . $request->name . '%');
        })

        // 🔍 Filter by email
        ->when($request->filled('email'), function ($q) use ($request) {
            $q->where('student_email', 'like', '%' . $request->email . '%');
        })

        // 🔍 Filter by finalized status
        ->when($request->filled('finalized'), function ($q) use ($request) {
            $q->where('is_finalized', $request->finalized);
        })

        // 🏆 Order by score
        ->orderByDesc('score');

    // 🔢 Top N filter (optional)
    if ($request->filled('top_n')) {
        $students->limit((int) $request->top_n);
    }

    $students = $students->get();

    return view('admin.tests.offline.results', compact('test', 'students'));
}

    public function resultsold(Test $test)
    {
        $students = OfflineTestStudent::where('test_id', $test->id)
            ->orderByDesc('score')
            ->get();

        return view('admin.tests.offline.results', compact('test', 'students'));
    }

    /* EXCEL UPLOAD */
    public function uploadExcel(Request $request, Test $test)
    {
         $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new OfflineStudentsImport($test->id);

        Excel::import($import, $request->file('file'));

        // ❌ If there are validation failures
        if (!empty($import->failures)) {

            $errors = collect($import->failures)->map(function ($failure) {
                return "Row {$failure->row()}: " . implode(', ', $failure->errors());
            })->toArray();

            return back()->withErrors($errors);
        }

        // ✅ All good
        return back()->with('success', 'Excel uploaded successfully');
    }

    /* MANUAL ADD */
    public function storeStudent(Request $request, Test $test)
    {

        $request->validate([
            'student_name' => 'required',
            'student_email'=> 'nullable|email',
            'student_mobile'=> 'nullable',
            'score'        => 'required|numeric',
        ]);

        OfflineTestStudent::create([
            'test_id' => $test->id,
            'student_name' => $request->student_name,
            'student_email'=> $request->student_email,
            'student_mobile'=> $request->student_mobile,
            'score' => $request->score,
        ]);

        return redirect()
    ->route('admin.offline-tests.results', $test->id) 
    ->with('success', 'Student added successfully');
      

        // return back()->with('success', 'Student added');
    }

    /* FINALIZE */
    public function bulkFinalize(Request $request)
    {
        OfflineTestStudent::whereIn('id', $request->student_ids ?? [])
            ->update(['is_finalized' => 1]);

        return back()->with('success', 'Students finalized');
    }

    /* MOVE TO ENQUIRIES */
    public function moveToEnquiries(Request $request, Test $test)
    {
        $students = OfflineTestStudent::where('test_id', $test->id)
            ->where('is_finalized', 1)
            ->get();

        foreach ($students as $st) {
            Enquiry::updateOrCreate(
                ['student_test_id' => $st->id,
                 'test_id'         => $test->id,
                ],
                [
                    'name' => $st->student_name,
                    'email'=> $st->student_email,
                    'mobile'=> $st->student_mobile,
                    'college'=> $test->college_id,
                    'student_test_id'=> $st->id,
                    'study'=> '',
                    'semester'=> $test->semester_id ?? '',
                    'source'=> 'offline',
                    'test_id'    => $test->id,
                    'status'     => 'followup',
                    'created_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Moved to enquiries');
    }


    public function create()
    {
        return view('admin.tests.offline.create', [
            'colleges'  => College::all(),
            'courses'   => Course::all(),
            'semesters' => Semester::all(),
            'branches'  => array(),
            'categories' => TestCategory::all(),
        ]);
    }

    public function createStudent(Test $test)
{
    return view('admin.tests.offline.create_student', compact('test'));
}


public function store(Request $request)
{
    $data = $request->validate([
        'title'             => 'required',
        'college_id'        => 'required',
        'student_course_id' => 'required',
        'semester_id'       => 'required',
        'test_category_id'  => 'required',
        'status'            => 'required|in:draft,published,unpublished',
        'is_active'         => 'nullable|boolean',
        'test_date'         => 'nullable|date',
        'description'       => 'nullable|string',
    ]);

    $data['slug'] = Str::random(15); // 👈 ALPHANUMERIC SLUG
    $data['access_key'] = Str::random(15); // 👈 ALPHANUMERIC SLUG
    $data['test_mode'] = 'offline'; // 👈 ALPHANUMERIC SLUG

    Test::create($data);

    return redirect()->route('admin.offline-tests.index')
        ->with('success', 'Test created successfully.');
}
     public function store16dec(Request $request)
    {
        $request->validate([
            'title'             => 'required',
            // 'slug'              => 'required|unique:tests',
            // 'access_key'        => 'required|unique:tests',
            'college_id'        => 'required',
            'student_course_id' => 'required',
            'semester_id'       => 'required',
            // 'branch_id'         => 'required',
            'test_category_id'  => 'required',
            'status'            => 'required|in:draft,published,unpublished'
        ]);
        $slug = Str::random(15);
        $request->slug = $slug;
        Test::create($request->all());

        return redirect()->route('admin.offline-tests.index')
                         ->with('success', 'Test created successfully.');
    }

    public function edit(Test $offline_test)
{
    return view('admin.tests.offline.edit', [
        'test'       => $offline_test,
        'colleges'   => College::all(),
        'courses'    => Course::all(),
        'semesters'  => Semester::all(),
        'categories' => TestCategory::all(),
    ]);
}
    public function edit17dec(Test $test)
    {
        return view('admin.tests.offline.edit', [
            'test'      => $test,
            'colleges'  => College::all(),
            'courses'   => Course::all(),
            'semesters' => Semester::all(),
            // 'branches'  => Branch::all(),
            'categories' => TestCategory::all(),
        ]);
    }

    /* ================= UPDATE TEST ================= */
    public function update111(Request $request, Test $offline_test)
    {
        $request->validate([
            'title'  => 'required',
            'status' => 'required|in:draft,published,unpublished',
        ]);

        // ✅ use the bound model
        $offline_test->update($request->all());

        return redirect()
            ->route('admin.offline-tests.index')
            ->with('success', 'Test updated successfully.');
    }

    public function update(Request $request, Test $offline_test)
    {
        $request->validate([
            'title'             => 'required',
            // 'slug'              => 'required|unique:tests,slug,' . $test->id,
            // 'access_key'        => 'required|unique:tests,access_key,' . $test->id,
            'status'            => 'required|in:draft,published,unpublished'
        ]);

        $offline_test->update($request->all());

        return redirect()->route('admin.offline-tests.index')
                         ->with('success', 'Test updated successfully.');
    }

    public function selectedStudents(Test $test)
    {
        $students = $test->offlineTests()
            ->where('is_finalized', 1)
            ->get();

        return view(
            'admin.tests.partials.selected_students_modal',
            compact('students')
        );
    }


    public function downloadMcqPaper($testId)
    {
        $test = Test::with(['questions.options'])->findOrFail($testId);

        // Keep question order as stored
        $questions = $test->questions->map(function ($question) {
            return (object) [
                'id' => $question->id,
                'question' => $question->question,
                'marks' => $question->marks ?? null,

                // Options in original order
                'options' => $question->options->values(),

                // Correct option index (a,b,c,d)
                'correct_index' => $question->options
                    ->values()
                    ->search(fn ($opt) => $opt->is_correct === 1),
            ];
        });

         $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'tempDir' => storage_path('app/mpdf'), // IMPORTANT
        ]);

        $html = View::make('pdf.aptitude-test-pdf', compact('questions','test'))->render();
        // $mpdf->SetHTMLHeaderByName('firstHeader');
        $mpdf->SetHTMLFooter('');

        // Write ALL content in one go
        $mpdf->WriteHTML($html);

        // Footer only on last page
        $mpdf->SetHTMLFooter($this->getStudentTestPDFFooter());
        $mpdf->WriteHTML('');

         return response()->streamDownload(
            fn () => $mpdf->Output('', 'D'),
            $test->title . '-question-paper.pdf'
        );
    
    }

    /* FUTURE METHODS (placeholders) */
    // public function upload()
    // public function storeUpload()
    // public function finalize()
}
