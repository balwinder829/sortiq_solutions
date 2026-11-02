<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\College;
use App\Models\Course;
use App\Models\Semester;
use App\Models\TestCategory;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\StudentTest;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TestAllStudentsExport;
use App\Exports\TestCollegeLinksExport;
use App\Exports\TestSelectedStudentsExport;
use App\Exports\FinalizedStudentsExport;
use App\Models\Enquiry;
use App\Exports\SingleTestStudentsExport;
use App\Exports\OverallStudentsExport;
use App\Exports\SelectedTestStudentsExport;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use App\Models\TestLink;
use App\Models\StudentSession;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{

    use PdfLayoutTrait;
    
    protected string $permissionPrefix = 'tests';

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
        'bulkDelete' => 'delete',
        'updateLinkAvailability' => 'edit',
        'toggleLinkAvailability' => 'edit',

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
    // List all tests
    // public function index(Request $request)
    // {
    //     $query = Test::with('studentCourse');

    //     // Apply filters
    //     if ($request->filled('title')) {
    //         $query->where('title', 'like', '%' . $request->title . '%');
    //     }

    //     if ($request->filled('course')) {
    //         $query->whereHas('studentCourse', function($q) use ($request) {
    //             $q->where('course_name', 'like', '%' . $request->course . '%');
    //         });
    //     }
    //     $tests = $query->paginate(10); // Paginate results

    //     return view('admin.tests.index', compact('tests'));
    // }

     /* ================= LIST WITH FILTERS ================= */
     public function index(Request $request)
{
    $gender = $request->filled('gender')
        ? strtolower($request->gender)
        : null;

    $tests = Test::where('test_mode', 'online')
        ->with(['category','college','course','semester','links.college'])
        ->withCount([

            'questions',
            // TOTAL REGISTERED (gender-aware)
            'studentTests as total_registered' => function ($q) use ($gender) {
                $q->where('source', 'online');

                if ($gender) {
                    $q->whereRaw('LOWER(gender) = ?', [$gender]);
                }
            },

            // FINALIZED COUNT (gender-aware)
            'studentTests as selected_count' => function ($q) use ($gender) {
                $q->where('source', 'online')
                  ->where('is_finalized', 1);

                if ($gender) {
                    $q->whereRaw('LOWER(gender) = ?', [$gender]);
                }
            },
        ]);

    /* ===== EXISTING FILTERS ===== */

    // if ($request->college_id)
    //     $tests->where('college_id', $request->college_id);

    if ($request->college_id) {
        $tests->whereHas('links', function ($q) use ($request) {
            $q->where('college_id', $request->college_id);
        });
    }

    // if ($request->student_course_id)
    //     $tests->where('student_course_id', $request->student_course_id);

    // if ($request->semester_id)
    //     $tests->where('semester_id', $request->semester_id);

    if ($request->test_category_id)
        $tests->where('test_category_id', $request->test_category_id);

    if ($request->status)
        $tests->where('status', $request->status);

    if ($request->filled('is_active'))
        $tests->where('is_active', $request->is_active);

    // if ($request->from_date)
    //     $tests->whereDate('exam_start_at', '>=', $request->from_date);

    // if ($request->to_date)
    //     $tests->whereDate('exam_end_at', '<=', $request->to_date);

    if ($request->from_date) {
        $tests->whereDate('exam_end_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $tests->whereDate('exam_start_at', '<=', $request->to_date);
    }

    // ✅ FILTER TESTS BY GENDER (EXISTS)
    if ($gender) {
        $tests->whereHas('studentTests', function ($q) use ($gender) {
            $q->where('source', 'online')
              ->whereRaw('LOWER(gender) = ?', [$gender]);
        });
    }

    return view('admin.tests.index', [
        'tests'     => $tests->latest('updated_at')->get(),
        'colleges'  => College::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}

    public function create()
    {
        return view('admin.tests.create', [
            'colleges'  => College::all(),
            // 'courses'   => Course::all(),
            'semesters' => Semester::all(),
            'branches'  => array(),
            'categories' => TestCategory::all(),
        ]);
    }


     /* ================= STORE TEST ================= */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required',
            // 'slug'              => 'required|unique:tests',
            // 'access_key'        => 'required|unique:tests',
            // 'college_id'        => 'required',
            'college_ids' => 'required|array',
            'college_ids.*' => 'exists:colleges,id',
            // 'student_course_id' => 'required',
            // 'semester_id'       => 'required',
            // 'branch_id'         => 'required',
            'test_category_id'  => 'required',
            'status'            => 'required|in:draft,published,unpublished',
            'is_active'         => 'nullable|boolean',
            'exam_start_at' => 'nullable|date',
            'exam_end_at'   => 'nullable|date|after_or_equal:exam_start_at',
            // 'timer_type'    => 'required|in:fixed,individual',
        ]);
        // dd(Str::random(30));

         $test = Test::create([
            'title'             => $request->title,
            'slug'              => Str::random(30), // ✅ RANDOM URL NAME
            'access_key'        => Str::random(10),
            'college_id'        => $request->college_id,
            // 'student_course_id' => $request->student_course_id,
            // 'semester_id'       => $request->semester_id,
            'test_category_id'  => $request->test_category_id,
            'status'            => $request->status,
            'is_active'            => $request->is_active,
            // 'exam_start_at' => Carbon::parse($request->exam_start_at)->startOfDay(),
            // 'exam_end_at'   => Carbon::parse($request->exam_end_at)->endOfDay(),
            'exam_start_at' => NULL,
            'exam_end_at'   => NULL,
            'timer_type' => 'fixed',
        ]);

        // Test::create($request->all());
        foreach ($request->college_ids as $collegeId) {

            TestLink::create([
                'test_id' => $test->id,
                'college_id' => $collegeId,
                'slug' => Str::random(40)
            ]);

        }

        return redirect()->route('admin.tests.index')
                         ->with('success', "Test '{$test->title}' created successfully.");
    }

    

    public function regenerateCollegeLink(TestLink $link)
    {
        $link->update([
            'slug' => Str::random(40)
        ]);

        return back()->with(
            'success',
            'Link regenerated for ' . ($link->college->full_name ?? 'college')
        );
    }

        
    /**
     * Save college-specific test availability settings.
     * A NULL valid_until means the test has not been started.
     */
    public function updateLinkAvailability(Request $request, TestLink $link)
    {
        $validated = $request->validate([
            'valid_from'  => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
        ]);

        if (!empty($validated['valid_from']) && !empty($validated['valid_until'])) {
            if (
                Carbon::parse($validated['valid_until'])
                    ->lt(Carbon::parse($validated['valid_from']))
            ) {
                return back()
                    ->withErrors([
                        'valid_until' => 'Valid Until must be after or equal to Valid From.'
                    ])
                    ->withInput();
            }
        }

        $link->valid_from = !empty($validated['valid_from'])
            ? Carbon::parse($validated['valid_from'])
            : null;

        $link->valid_until = !empty($validated['valid_until'])
            ? Carbon::parse($validated['valid_until'])
            : null;

        $link->save();

        return back()->with(
            'success',
            'Availability settings saved successfully.'
        );
    }

    /**
     * Manually enable or disable a college-specific test link.
     */
    public function toggleLinkAvailability(TestLink $link)
    {
        $link->is_enabled = !$link->is_enabled;
        $link->save();

        return back()->with(
            'success',
            'Test link ' . ($link->is_enabled ? 'enabled' : 'disabled') . ' successfully.'
        );
    }


    public function regenerateLink(Test $test)
    {
        $links = TestLink::where('test_id', $test->id)->get();

        // NEW SYSTEM (links already exist)
        if ($links->count()) {

            foreach ($links as $link) {
                $link->update([
                    'slug' => Str::random(40)
                ]);
            }

            return back()->with(
                'success',
                'All college links regenerated successfully.'
            );
        }

        // LEGACY TEST → migrate to new system
        if ($test->college_id) {

            TestLink::create([
                'test_id' => $test->id,
                'college_id' => $test->college_id,
                'slug' => Str::random(40)
            ]);

            return back()->with(
                'success',
                'Legacy test migrated to new college link system.'
            );
        }

        // NO COLLEGE ASSIGNED
        return redirect()
            ->route('admin.tests.edit', $test->id)
            ->with(
                'error',
                'Please assign a college first to generate student links.'
            );
    }
    /* ================= EDIT FORM ================= */
    public function edit(Test $test)
    {
        return view('admin.tests.edit', [
            'test'      => $test,
            'colleges'  => College::all(),
            // 'courses'   => Course::all(),
            'semesters' => Semester::all(),
            // 'branches'  => Branch::all(),
            'categories' => TestCategory::all(),
        ]);
    }

    /* ================= UPDATE TEST ================= */
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'title'             => 'required',
            // 'slug'              => 'required|unique:tests,slug,' . $test->id,
            // 'access_key'        => 'required|unique:tests,access_key,' . $test->id,
            'status'            => 'required|in:draft,published,unpublished',
            'exam_start_at' => 'nullable|date',
            'exam_end_at'   => 'nullable|date|after_or_equal:exam_start_at',
            // 'timer_type'    => 'required|in:fixed,individual',
            'is_active'         => 'nullable|boolean',
            'college_ids' => 'required|array',
            'college_ids.*' => 'exists:colleges,id',
        ]);

        // $test->update($request->all());
        $test->update([
            'title'             => $request->title,
            // 'student_course_id' => $request->student_course_id,
            // 'semester_id'       => $request->semester_id,
            'test_category_id'  => $request->test_category_id,
            'status'            => $request->status,
            'is_active'         => $request->is_active,
            // 'exam_start_at' => Carbon::parse($request->exam_start_at)->startOfDay(),
            // 'exam_end_at'   => Carbon::parse($request->exam_end_at)->endOfDay(),
            'exam_start_at' => NULL,
            'exam_end_at'   => NULL,
            'timer_type' => 'fixed',
        ]);

        $existingCollegeIds = TestLink::where('test_id', $test->id)
        ->pluck('college_id')
        ->toArray();


        $newCollegeIds = $request->college_ids;

        $toAdd = array_diff($newCollegeIds, $existingCollegeIds);

        $toRemove = array_diff($existingCollegeIds, $newCollegeIds);


        foreach ($toAdd as $collegeId) {

            TestLink::create([
                'test_id' => $test->id,
                'college_id' => $collegeId,
                'slug' => Str::random(40)
            ]);

        }

        foreach ($toRemove as $collegeId) {

            $hasStudents = StudentTest::where('test_id', $test->id)
                ->where('college_id', $collegeId)
                ->exists();

            if (!$hasStudents) {

                TestLink::where('test_id', $test->id)
                    ->where('college_id', $collegeId)
                    ->delete();

            }

        }
        return redirect()->route('admin.tests.index')
            ->with('success', "Test '{$test->title}' updated successfully.");

    }

    public function destroy(Test $test)
    {
         if (StudentTest::where('test_id', $test->id)->exists()) {
            return redirect()->route('admin.tests.index')
                ->with('success', 'This test cannot be deleted because student test records already exist.');
        }

        $test->delete();
        return redirect()->route('admin.tests.index')
                         ->with('error', 'Test deleted successfully.');
    }


    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'string'],
        ]);

        $ids = array_filter(
            array_map('intval', explode(',', $request->ids))
        );

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No tests selected.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Tests having even 1 student record CANNOT be deleted
        |--------------------------------------------------------------------------
        */

        $blockedIds = StudentTest::whereIn('test_id', $ids)
            ->pluck('test_id')
            ->unique()
            ->toArray();

        $deletableIds = array_values(
            array_diff($ids, $blockedIds)
        );

        /*
        |--------------------------------------------------------------------------
        | Delete only tests having NO students
        |--------------------------------------------------------------------------
        */

        $deletedCount = 0;

        if (!empty($deletableIds)) {
            $deletedCount = Test::whereIn('id', $deletableIds)->delete();
        }

        $blockedCount = count($blockedIds);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        if ($deletedCount > 0 && $blockedCount > 0) {

            return response()->json([
                'success' => true,
                'message' =>
                    $deletedCount . ' test' .
                    ($deletedCount == 1 ? '' : 's') .
                    ' deleted successfully. ' .
                    $blockedCount . ' test' .
                    ($blockedCount == 1 ? '' : 's') .
                    ' could not be deleted because student records exist.'
            ]);
        }

        if ($deletedCount > 0) {

            return response()->json([
                'success' => true,
                'message' =>
                    $deletedCount . ' test' .
                    ($deletedCount == 1 ? '' : 's') .
                    ' deleted successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' =>
                'Selected test' .
                ($blockedCount == 1 ? '' : 's') .
                ' cannot be deleted because student records exist.'
        ], 422);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required',
    //         'student_course_id' => 'required',
    //     ]);

    //     Test::create([
    //         'title' => $request->title,
    //         'student_course_id' => $request->student_course_id,
    //         'description' => $request->description,
    //         'access_key' => strtoupper(uniqid()),
    //         'slug' => Str::random(12), // ensures slug is always set
    //     ]);

    //     return redirect()->route('admin.tests.index')->with('success','Test Created Successfully');
    // }


    // Show single test (optional)
    public function show(Test $test)
    {
        $test->load('questions.options'); // eager load

        $backRoute = $test->test_mode === 'offline'
        ? route('admin.offline-tests.index')
        : route('admin.tests.index');
        // dd($test);
        return view('admin.tests.show', compact('test','backRoute'));
    }
   
    public function selectedStudents(Test $test)
    {
        $students = $test->studentTests()
            ->where('is_finalized', 1)
            ->orderBy('score', 'desc') // highest score first
            ->get();

        return view(
            'admin.tests.partials.selected_students_modal',
            compact('students')
        );
    }

    public function results(Request $request, $test_id)
    {
        $movedStudentTestIds = Enquiry::where('test_id', $test_id)
            ->pluck('student_test_id')
            ->toArray();

        $test = Test::withCount('questions')->findOrFail($test_id);

        // Fetch students for S.No
        $students = Student::select('email_id','sno')->get()->keyBy('email_id');

        // $studentTestsQuery = $test->studentTests();

        $studentTestsQuery = $test->studentTests()
        ->whereIn('id', function ($query) use ($test_id) {
            $query->selectRaw('MAX(id)')
                ->from('student_tests')
                ->where('test_id', $test_id)
                ->whereNotNull('student_mobile')
                ->where('student_mobile', '!=', '')
                ->groupBy('student_mobile', 'college_id');
        });

        /* ===== COLLEGE DROPDOWN DATA ===== */

        $collegeIds = StudentTest::where('test_id', $test_id)
            ->whereNotNull('college_id') // prevent NULL values
            ->orderByDesc('created_at')
            ->pluck('college_id')
            ->unique()
            ->values();

        $colleges = College::whereIn('id', $collegeIds)
            ->get()
            ->sortBy(function ($college) use ($collegeIds) {
                return $collegeIds->search($college->id);
            })
            ->values();

        /* ===== LATEST COLLEGE ===== */

        $latestCollegeId = StudentTest::where('test_id', $test_id)
            ->whereNotNull('college_id')
            ->latest('created_at')
            ->value('college_id');

        /* ===== COLLEGE FILTER ===== */

        if ($request->has('college_id')) {

            if ($request->college_id != '') {
                $studentTestsQuery->where('college_id', $request->college_id);
            }

        } else {

            if ($latestCollegeId) {
                $studentTestsQuery->where('college_id', $latestCollegeId);
            } elseif ($test->college_id) {
                // legacy fallback
                $studentTestsQuery->where('college_id', $test->college_id);
            }
        }

        /* ===== EXISTING FILTERS ===== */

        if ($request->filled('sno')) {

            $studentEmails = $students->filter(function ($student) use ($request) {
                return str_contains($student->sno, $request->sno);
            })->keys();

            $studentTestsQuery->whereIn('student_email', $studentEmails);
        }

        if ($request->filled('name')) {
            $studentTestsQuery->where('student_name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $studentTestsQuery->where('student_email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('student_mobile')) {
            $studentTestsQuery->where('student_mobile', 'like', '%' . $request->student_mobile . '%');
        }

        /* ===== SORTING ===== */

        if ($request->has('college_id') && $request->college_id != '') {

            // Specific college → highest score first
            $studentTestsQuery->orderByDesc('score');

        } else {

            // All colleges → latest college first
            if ($collegeIds->isNotEmpty()) {

                $ids = $collegeIds->implode(',');

                $studentTestsQuery
                    ->orderByRaw("FIELD(college_id,$ids)")
                    ->orderByDesc('score');

            } else {

                $studentTestsQuery->orderByDesc('score');

            }
        }

        /* ===== TOP N ===== */

        if ($request->filled('top_n') && is_numeric($request->top_n)) {
            $studentTestsQuery->limit((int)$request->top_n);
        }

        /* ===== STATUS FILTER ===== */

        if ($request->filled('finalized')) {
            $studentTestsQuery->where('is_finalized', $request->finalized);
        }

        if ($request->filled('gender')) {
            $studentTestsQuery->where('gender', $request->gender);
        }

        if ($request->filled('moved')) {

            if ($request->moved === '1') {
                $studentTestsQuery->whereIn('id', $movedStudentTestIds);
            }

            elseif ($request->moved === '0' && !empty($movedStudentTestIds)) {
                $studentTestsQuery->whereNotIn('id', $movedStudentTestIds);
            }
        }

        if ($request->filled('course_type')) {
            $studentTestsQuery->where('course_type', $request->course_type);
        }

        // if ($request->filled('branch')) {
        //     $studentTestsQuery->where('branch', $request->branch);
        // }

        if ($request->filled('semester')) {
            $studentTestsQuery->where('semester', $request->semester);
        }

        if ($request->filled('branch')) {
            $studentTestsQuery->where('branch', 'like', '' . $request->branch . '%');
        }
        /* ===== CLONE FILTERED QUERY ===== */
        $filteredQuery = clone $studentTestsQuery;

        /* ===== TOTAL STUDENTS (FILTERED) ===== */
        $totalFilteredStudents = (clone $filteredQuery)->count();

        /* ===== COLLEGE-WISE COUNT (FILTERED) ===== */
         
        $collegeStudentCounts = (clone $studentTestsQuery)
        ->reorder() // ✅ removes ORDER BY
        ->selectRaw('college_id, COUNT(*) as total')
        ->groupBy('college_id')
        ->pluck('total', 'college_id');

        $studentTests = $studentTestsQuery->get();

        /* ===== ATTACH S.NO ===== */

        $studentTests->each(function ($st) use ($students) {
            $st->sno = $students[$st->student_email]->sno ?? '-';
        });

        $defaultCollegeId = $request->has('college_id')
            ? $request->college_id
            : ($latestCollegeId ?? $test->college_id);


            $sessionsList = StudentSession::where('status', 'active') // ✅ string status
                ->orderBy('start_date', 'desc')
                ->get()
                ->pluck('display_name', 'id');


        return view('admin.tests.results', compact(
            'test',
            'studentTests',
            'movedStudentTestIds',
            'colleges',
            'totalFilteredStudents',
            'collegeStudentCounts',
            'sessionsList',
            'defaultCollegeId'
        ));
    }

     
public function updateStudentScore(Request $request, $test_id, $student_test_id)
{
    $test = Test::withCount('questions')->findOrFail($test_id);

    $studentTest = StudentTest::where('test_id', $test->id)
        ->where('id', $student_test_id)
        ->firstOrFail();

    $request->validate([
        'score' => ['required', 'integer', 'min:0'],
    ]);

    $maxScore = (int) $test->questions_count;

    // Cap scores at the maximum question count.
    $score = min((int) $request->input('score'), $maxScore);

    $studentTest->score = $score;
    $studentTest->save();

    return redirect()->back()->with(
        'success',
        "Score updated successfully: {$score}/{$maxScore} for {$studentTest->student_name}."
    );
}


public function updateSelectedScores(Request $request, $test_id)
{
    $test = Test::withCount('questions')->findOrFail($test_id);

    $validated = $request->validate([
        'student_test_ids'   => ['required', 'array', 'min:1'],
        'student_test_ids.*' => ['required', 'integer', 'distinct'],
        'score'              => ['required', 'integer', 'min:0'],
    ]);

    $maxScore = (int) $test->questions_count;

    // Cap the submitted score at the test's maximum.
    $score = min((int) $validated['score'], $maxScore);

    // Only update selected students belonging to this test.
    $updatedCount = StudentTest::where('test_id', $test->id)
        ->whereIn('id', $validated['student_test_ids'])
        ->update(['score' => $score]);

    if ($updatedCount === 0) {
        return redirect()->back()->withErrors([
            'student_test_ids' => 'No matching students were found for this test.',
        ]);
    }

    return redirect()->back()->with(
        'success',
        "Score updated to {$score}/{$maxScore} for {$updatedCount} student(s)."
    );
}


    public function bulkFinalize(Request $request)
    {
        $request->validate([
            'student_test_ids' => 'required|array'
        ]);

        // dd($request->student_test_ids);
        \App\Models\StudentTest::whereIn('id', $request->student_test_ids)
            ->where('is_finalized', 0)
            ->update(['is_finalized' => 1]);

        return back()->with('success', 'Selected students finalized successfully.');
    }

    public function exportAllStudents(Test $test)
    {
        return Excel::download(
            new TestAllStudentsExport($test->id),
            'test_'.$test->id.'_all_students.xlsx'
        );
    }

    public function exportSelectedStudents(Test $test)
    {
        return Excel::download(
            new TestSelectedStudentsExport($test->id),
            'test_'.$test->id.'_selected_students.xlsx'
        );
    }

    public function exportFinalized(Test $test)
    {
        return Excel::download(
            new FinalizedStudentsExport($test->id),
            'test_'.$test->id.'_finalized_students.xlsx'
        );
    }

     public function moveFinalizedToEnquiries(Request $request)
{
    $ids = $request->student_test_ids;
    $sessionId = $request->session_id;
// dd($ids,$sessionId);
    // ✅ Validation
    if (empty($ids)) {
        // return response()->json(['message' => 'No students selected'], 422);
         return back()->with('success', 'No students selected.');
    }

    if (!$sessionId) {
        // return response()->json(['message' => 'Session required'], 422);
         return back()->with('success', 'Session required.');
    }

    // ✅ Ignore already moved (BEST PRACTICE)
    $students = StudentTest::whereIn('id', $ids)
        ->where('is_moved_to_enquiry', 0)
        ->get();

    if ($students->isEmpty()) {
        // return response()->json(['message' => 'No valid students found'], 422);
         return back()->with('error', 'No valid students found.');
    }

    $count = 0;

foreach ($students as $st) {

    // Same mobile + session ki Enquiry pehle se hai ya nahi
    $enquiry = Enquiry::where('mobile', $st->student_mobile)
        ->where('session_id', $sessionId)
        ->first();

    // Enquiry nahi mili to create karein
    if (!$enquiry) {
        $enquiry = Enquiry::create([
            'name'        => $st->student_name,
            'email'       => $st->student_email,
            'mobile'      => $st->student_mobile ?? null,
            'college'     => $st->college_id ?? null,
            'study'       => $st->class ?? '',
            'semester'    => $st->semester ?? null,
            'session_id'  => $sessionId,
            'source'      => 'online',
            'source_type' => 'online',
            'source_id'   => $st->id,
            'status'      => 'new',
            'created_by'  => auth()->id(),
        ]);

        // Sirf new Enquiry count hogi
        $count++;
    }

    // Enquiry pehle se ho ya abhi create hui ho — flag update karein
    $st->update([
        'is_moved_to_enquiry' => 1,
    ]);
}

return back()->with('success', "$count student(s) moved successfully");
     
}

    public function mo21veFinalizedToEnquiries(Test $test)
    {
        dd('here');
        $studentTests = $test->studentTests()
            ->where('is_finalized', 1)
            ->get();

        if ($studentTests->isEmpty()) {
            return back()->with('success', 'No finalized students found.');
        }

        foreach ($studentTests as $st) {

            Enquiry::firstOrCreate(
                [
                    // UNIQUE KEY → prevents duplicates
                    'student_test_id' => $st->id,
                ],
                [
                    'name'       => $st->student_name,
                    'email'      => $st->student_email,
                    'mobile'     => $st->student_mobile ?? null,

                    'college'    => $test->college_id,
                    'study'      => '',
                    'semester'   => $test->semester_id ?? null,

                    'test_id'    => $test->id,
                    'student_id' => null,
                    'source'     => 'online',

                    'status'     => 'followup',
                    'created_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Finalized students moved to Enquiries successfully.');
    }

   
    public function studentView($slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        // Display the test to the student
       return redirect()->route('student.enter.key', ['test_id' => $test->id]);

    }

        /* ================= OVERALL FINALIZED ================= */
    public function exportOverallFinalized()
    {
        return Excel::download(
            new OverallStudentsExport([
                'finalized' => true
            ]),
            'overall_finalized_students.xlsx'
        );
    }

    /* ================= OVERALL ATTEMPTED ================= */
    public function exportOverallAttempted()
    {
        return Excel::download(
            new OverallStudentsExport([
                'attempted' => true
            ]),
            'overall_attempted_students.xlsx'
        );
    }

    /* ================= ONLINE FINALIZED ================= */
    public function exportOnlineFinalized()
    {
        return Excel::download(
            new OverallStudentsExport([
                'finalized' => true,
                'mode' => 'online'
            ]),
            'online_finalized_students.xlsx'
        );
    }

    /* ================= OFFLINE FINALIZED ================= */
    public function exportOfflineFinalized()
    {
        return Excel::download(
            new OverallStudentsExport([
                'finalized' => true,
                'mode' => 'offline'
            ]),
            'offline_finalized_students.xlsx'
        );
    }

    /* ================= CATEGORY FINALIZED ================= */
    public function exportCategoryFinalized(TestCategory $category)
    {
        return Excel::download(
            new OverallStudentsExport([
                'finalized' => true,
                'category_id' => $category->id
            ]),
            'category_'.$category->id.'_finalized_students.xlsx'
        );
    }

    /* ================= ALL STUDENTS (SINGLE TEST) ================= */
    public function exportTestAll(Request $request, Test $test)
    {   
        $collegeId = request()->get('college_id');
        return Excel::download(
            new SingleTestStudentsExport($test, false, $request->all()),
            'test_'.$test->id.'_all_students.xlsx'
        );
    }

    /* ================= FINALIZED STUDENTS (SINGLE TEST) ================= */
    public function exportTestFinalized(Request $request, Test $test)
    {
        $collegeId = request()->get('college_id');
        return Excel::download(
            new SingleTestStudentsExport($test, true, $request->all()),
            'test_'.$test->id.'_finalized_students.xlsx'
        );
    }

    public function exportOnlineAttempted()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OverallStudentsExport([
                'attempted' => true,
                'mode'      => 'online',
            ]),
            'online_all_students.xlsx'
        );
    }

    public function exportOfflineAttempted()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OverallStudentsExport([
                'attempted' => true,
                'mode'      => 'offline',
            ]),
            'offline_all_students.xlsx'
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
        // $mpdf->SetHTMLFooter('');

        // Write ALL content in one go
        $mpdf->WriteHTML($html);

        // Footer only on last page
        // $mpdf->SetHTMLFooter($this->getStudentTestPDFFooter());
        $mpdf->WriteHTML('');

        $safeTitle = preg_replace('/[\/\\\\]/', '-', $test->title);
        $safeTitle = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $safeTitle);
         return response()->streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            $safeTitle . '-question-paper.pdf'
        );
    
    }

    /**
     * Export all college links for a test.
     */
    
public function exportCollegeLinks(Test $test)
{
    $fileName = preg_replace('/[^A-Za-z0-9_-]/', '_', $test->title);

    $fileName = $fileName . '_College_Links_' . now()->format('d_M_Y') . '.xlsx';

    return Excel::download(
        new \App\Exports\TestCollegeLinksExport($test),
        $fileName
    );
}

    
    public function links(Test $test)
    {
        // $links = $test->links()->with('college')->get();
        $links = $test->links()
        ->with('college')
        ->orderByDesc('updated_at')
        ->get();

         // Get last attempt date per college
        // $lastAttempts = StudentTest::where('test_id', $test->id)
        //     // ->selectRaw('college_id, MAX(created_at) as last_attempt')
        //     ->selectRaw('college_id, COUNT(*) as total_students, MAX(created_at) as last_attempt')
        //     ->groupBy('college_id')
        //     ->pluck('last_attempt', 'college_id');

        $collegeStats = StudentTest::where('test_id', $test->id)
        ->selectRaw('college_id, COUNT(*) as total_students, MAX(created_at) as last_attempt')
        ->groupBy('college_id')
        ->get()
        ->keyBy('college_id');

        return view('admin.tests.links', compact('test','links','collegeStats'));
    }

    // public function downloadCertificates(Request $request, $testId)
    // {
    //     dd('here');
    // }

    public function downloadCertificates(Request $request)
    {

        $mode = $request->query('mode');

        $rules = [
            'student_test_ids' => 'required|array|min:1'
        ];

        // ✅ Require course ONLY for letters
        // if (in_array($mode, ['internship', 'offer'])) {
        //     $rules['course_name'] = 'required|string|max:255';
        // }

        $rules['course_name'] = 'required|string|max:255';
        
        $messages = [
            'student_test_ids.required' => 'Please select at least one student.',
            'course_name.required' => 'Course name is required for this letter.'
        ];

         $request->validate($rules, $messages);
        // ✅ VALIDATION
        // $request->validate([
        //     // 'course_name' => 'required|string|max:255',
        //     'student_test_ids' => 'required|array|min:1'
        // ], [
        //     // 'course_name.required' => 'Course name is required.',
        //     'student_test_ids.required' => 'Please select at least one student.'
        // ]);

        // $courseName = "training";
        // $courseName = $mode 
        // ? $request->course_name   // letters
        // : 'training';   

        $courseName = $request->course_name ?? 'training';
        // $courseName = $request->course_name;
        $ids = $request->student_test_ids;
        
         

        $students = StudentTest::whereIn('id', $ids)->get();
        if ($students->isEmpty()) {
            return back()->with('error', 'Selected students not found.');
        }

        $pdfPaths = [];

        foreach ($students as $student) {

            // ✅ PASS COURSE NAME
            $pdfPath = $this->generatePdf($student, $courseName, $mode);

            if (file_exists($pdfPath)) {
                $pdfPaths[] = $pdfPath;
            }
        }

        // ✅ SINGLE PDF
        if (count($pdfPaths) === 1) {
            return response()->download($pdfPaths[0], basename($pdfPaths[0]), [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // ✅ ZIP
        $zipFileName = 'certificate_letters_' . time() . '.zip';
        $zipFullPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();

        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            foreach ($pdfPaths as $path) {
                $zip->addFile($path, basename($path));
            }

            $zip->close();

            return response()->download($zipFullPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    private function generatePdf($student, $courseName, $mode = null)
    {
        // dd($mode);
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("student_certificate/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        // $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $studentName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->student_name));
        $mob = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->student_mobile));
        $id = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->id));
        // $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->CollegeData->FullName));
        $collegeOrPlace = optional($student->college)->FullName ?: $student->place;
       
        // sanitize
        $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($collegeOrPlace));

        $dateFormatted = Carbon::now()->format('d_F_Y'); // 26_March_2015

        $fileName = "{$studentName}_{$mob}{$id}_{$collegeName}_{$dateFormatted}_certificate.pdf";
         
        $filePath = $folderPath . '/' . $fileName;
        // dd($filePath);
        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }
        // echo $filePath;
        // Generate or overwrite PDF if needed
        if ($regenerate) {
            
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
        
            $view = match ($mode) {
                'free' => 'pdf.free_letter_fixed_test_pdf',
                'offer' => 'pdf.offer_letter_fixed_test_pdf',
                default => 'pdf.one_day_student_certificate',
            };

            

            if($mode  == null){
                $html = View::make($view, compact('student', 'courseName'))->render();
                $mpdf->SetHTMLHeader($this->getPDFHeader());
                $mpdf->WriteHTML($html);
                $mpdf->SetHTMLFooter($this->getPDFFooter());
            }else{
                $letter = $student;
                $html = View::make($view, compact('student' , 'courseName'))->render();
                $mpdf->SetHTMLHeader('');
                $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
                      
                $mpdf->WriteHTML($html);
                $mpdf->SetHTMLFooterByName('lastPageFooter');
            }
            

            // $html = view('pdf.one_day_student_certificate', compact('student'))->render();
        
            
            $mpdf->Output($filePath, 'F');
            //return response()->download($filePath);
        }

        return $filePath;
    }

    public function destroyLink($id)
    {
        $link = TestLink::findOrFail($id);

        // Count students for this test + college
        $count = StudentTest::where('test_id', $link->test_id)
            ->where('college_id', $link->college_id)
            ->count();

        // ❌ Prevent delete if students exist
        if ($count > 0) {
            return back()->with('error', 'Cannot delete! Students have already attempted this test.');
        }

        // ✅ Safe to delete
        $link->delete();

        return back()->with('success', 'Link deleted successfully.');
    }


public function moveSelectedToRound(Request $request, $test_id)
{
    $validated = $request->validate([
        'student_test_ids' => ['required', 'array', 'min:1'],
        'student_test_ids.*' => ['required', 'integer', 'distinct'],

        'destination_round' => [
            'required',
            'in:technical,machine_round,final',
        ],

        // Required only when moving to Final Students.
        'benefit_type' => [
            'required_if:destination_round,final',
            'nullable',
            'in:free,discount,stipend,3_month_ppo',
        ],
    ], [
        'student_test_ids.required' =>
            'Please select at least one student.',
        'destination_round.required' =>
            'Please select a destination.',
        'benefit_type.required_if' =>
            'Please select a category for Final Students.',
        'benefit_type.in' =>
            'Please select a valid Final Student category.',
    ]);

    $test = Test::findOrFail($test_id);

    $destination = $validated['destination_round'];
    $benefitType = $destination === 'final'
        ? $validated['benefit_type']
        : null;

    $tables = [
        'technical' => 'student_test_technical',
        'machine_round' => 'student_test_machine_round',
        'final' => 'student_test_final',
    ];

    $destinationTable = $tables[$destination];
    $userId = auth()->id();

    $movedCount = 0;
    $skippedCount = 0;

    DB::transaction(function () use (
        $validated,
        $test,
        $destination,
        $destinationTable,
        $benefitType,
        $userId,
        &$movedCount,
        &$skippedCount
    ) {
        $selectedIds = array_values(array_unique(array_map(
            'intval',
            $validated['student_test_ids']
        )));

        $students = StudentTest::with('college')
            ->where('test_id', $test->id)
            ->whereIn('id', $selectedIds)
            ->lockForUpdate()
            ->get();

        if ($students->count() !== count($selectedIds)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'student_test_ids' =>
                    'Some selected students do not belong to this test.',
            ]);
        }

        foreach ($students as $student) {

            // Do not move the same main result twice.
            if ($student->is_moved_to_round) {
                $skippedCount++;
                continue;
            }

            // Prevent duplicate active destination records.
            $alreadyExists = DB::table($destinationTable)
                ->where('source_student_test_id', $student->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($alreadyExists) {
                $skippedCount++;
                continue;
            }

            $now = now();

            $common = [
                'source_student_test_id' => $student->id,
                'previous_round_type' => 'main',
                'previous_round_record_id' => null,
                'student_name' => $student->student_name,
                'student_email' => $student->student_email,
                'student_mobile' => $student->student_mobile,
                'college_id' => $student->college_id,
                'college_name' => $student->college->full_name ?? null,
                'created_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($destination === 'final') {

                $destinationId = DB::table($destinationTable)
                    ->insertGetId($common + [
                        'final_status' => 'selected',
                        'benefit_type' => $benefitType,
                        'benefit_amount' => null,
                        'benefit_details' => null,
                        'offer_date' => null,
                        'notes' => null,
                    ]);

            } else {

                $destinationId = DB::table($destinationTable)
                    ->insertGetId($common + [
                        'score' => 0,
                        'status' => 'pending',
                        'notes' => null,
                    ]);
            }

            // Preserve the original result and record its destination.
            $student->is_moved_to_round = 1;
            $student->moved_to_round_type = $destination;
            $student->moved_to_round_at = $now;
            $student->moved_to_round_record_id = $destinationId;

            if ($destination === 'final') {
                $student->is_finalized = 1;
            }

            $student->save();

            // Record the student's progression.
            DB::table('student_test_progression_logs')->insert([
                'source_student_test_id' => $student->id,
                'record_type' => $destination,
                'record_id' => $destinationId,
                'event_type' => 'moved',
                'from_round' => 'main',
                'to_round' => $destination,
                'old_values' => json_encode([
                    'is_moved_to_round' => 0,
                    'is_finalized' => (bool) $student->getOriginal(
                        'is_finalized'
                    ),
                ]),
                'new_values' => json_encode([
                    'is_moved_to_round' => 1,
                    'destination_round' => $destination,
                    'destination_record_id' => $destinationId,
                    'benefit_type' => $benefitType,
                ]),
                'remarks' => 'Moved from main test results',
                'performed_by' => $userId,
                'created_at' => $now,
            ]);

            $movedCount++;
        }
    });

    $destinationLabel = [
        'technical' => 'Technical Round',
        'machine_round' => 'Machine Round',
        'final' => 'Final Students',
    ][$destination];

    $message = "{$movedCount} student(s) moved to {$destinationLabel}.";

    if ($skippedCount > 0) {
        $message .= " {$skippedCount} student(s) skipped because they were already moved or already existed in the destination.";
    }

    return back()->with(
        $movedCount > 0 ? 'success' : 'error',
        $message
    );
}
    public function moveSelectedToRound_12oct(Request $request, $test_id)
{
    $request->validate([
        'student_test_ids'   => ['required', 'array', 'min:1'],
        'student_test_ids.*' => ['required', 'integer', 'distinct'],
        'destination_round'  => [
            'required',
            'in:technical,machine_round,final',
        ],
    ]);

    $test = Test::findOrFail($test_id);

    $destination = $request->destination_round;

    // Only allow these known table names.
    $tables = [
        'technical'    => 'student_test_technical',
        'machine_round' => 'student_test_machine_round',
        'final'        => 'student_test_final',
    ];

    $destinationTable = $tables[$destination];
    $userId = auth()->id();

    $movedCount = 0;
    $skippedCount = 0;

    DB::transaction(function () use (
        $request,
        $test,
        $destination,
        $destinationTable,
        $userId,
        &$movedCount,
        &$skippedCount
    ) {
        $students = StudentTest::with('college')
            ->where('test_id', $test->id)
            ->whereIn('id', $request->student_test_ids)
            ->lockForUpdate()
            ->get();

        if ($students->count() !== count(
            array_unique(array_map(
                'intval',
                $request->student_test_ids
            ))
        )) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'student_test_ids' =>
                    'Some selected students do not belong to this test.',
            ]);
        }

        foreach ($students as $student) {

            // Do not move the same main result twice.
            if ($student->is_moved_to_round) {
                $skippedCount++;
                continue;
            }

            // Prevent a duplicate active entry in the destination.
            $alreadyExists = DB::table($destinationTable)
                ->where('source_student_test_id', $student->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($alreadyExists) {
                $skippedCount++;
                continue;
            }

            $now = now();

            $common = [
                'source_student_test_id' => $student->id,
                'previous_round_type'    => 'main',
                'previous_round_record_id' => null,
                'student_name'           => $student->student_name,
                'student_email'          => $student->student_email,
                'student_mobile'         => $student->student_mobile,
                'college_id'             => $student->college_id,
                'college_name'           => $student->college->full_name ?? null,
                'created_by'             => $userId,
                'created_at'             => $now,
                'updated_at'             => $now,
            ];

            if ($destination === 'final') {

                $destinationId = DB::table($destinationTable)
                    ->insertGetId($common + [
                        'final_status' => 'selected',
                        'benefit_type' => null,
                        'benefit_amount' => null,
                        'benefit_details' => null,
                        'offer_date' => null,
                        'notes' => null,
                    ]);

            } else {

                $destinationId = DB::table($destinationTable)
                    ->insertGetId($common + [
                        'score' => 0,
                        'status' => 'pending',
                        'notes' => null,
                    ]);
            }

            // Preserve the original result and record its destination.
            $student->is_moved_to_round = 1;
            $student->moved_to_round_type = $destination;
            $student->moved_to_round_at = $now;
            $student->moved_to_round_record_id = $destinationId;

            if ($destination === 'final') {
                $student->is_finalized = 1;
            }

            $student->save();

            // Add an audit event for the student's journey.
            DB::table('student_test_progression_logs')->insert([
                'source_student_test_id' => $student->id,
                'record_type' => $destination,
                'record_id' => $destinationId,
                'event_type' => 'moved',
                'from_round' => 'main',
                'to_round' => $destination,
                'old_values' => json_encode([
                    'is_moved_to_round' => 0,
                    'is_finalized' => (bool) $student->getOriginal('is_finalized'),
                ]),
                'new_values' => json_encode([
                    'is_moved_to_round' => 1,
                    'destination_round' => $destination,
                    'destination_record_id' => $destinationId,
                ]),
                'remarks' => 'Moved from main test results',
                'performed_by' => $userId,
                'created_at' => $now,
            ]);

            $movedCount++;
        }
    });

    $message = "{$movedCount} student(s) moved to "
        . str_replace('_', ' ', ucfirst($destination)) . '.';

    if ($skippedCount > 0) {
        $message .= " {$skippedCount} student(s) skipped because they were already moved or already existed in the destination.";
    }

    return back()->with(
        $movedCount > 0 ? 'success' : 'error',
        $message
    );
}

public function bulkUpdateLinkStatus(Request $request, Test $test)
{
    $validated = $request->validate([
        'status' => ['required', 'in:enable,disable'],
        'scope'  => ['required', 'in:selected,all'],
        'link_ids'   => ['required_if:scope,selected', 'array', 'min:1'],
        'link_ids.*' => ['integer', 'distinct'],
    ]);

    // Always restrict links to the currently opened test.
    $query = TestLink::where('test_id', $test->id);

    if ($validated['scope'] === 'selected') {
        $ids = array_map('intval', $validated['link_ids']);

        $query->whereIn('id', $ids);

        if ($query->count() !== count(array_unique($ids))) {
            return back()->with(
                'error',
                'Some selected links do not belong to this test.'
            );
        }
    }

    $updated = $query->update([
        'is_enabled' => $validated['status'] === 'enable' ? 1 : 0,
    ]);

    return back()->with(
        'success',
        "{$updated} college link(s) " .
        ($validated['status'] === 'enable' ? 'enabled' : 'disabled') .
        " successfully."
    );
}


public function bulkUpdateLinkSettings(Request $request, Test $test)
{
    $validated = $request->validate([
        'link_ids'   => ['required', 'array', 'min:1'],
        'link_ids.*' => ['required', 'integer', 'distinct'],
        'valid_from' => ['nullable', 'date'],
        'valid_until' => [
            'nullable',
            'date',
            'after_or_equal:valid_from',
        ],
    ]);

    $ids = array_map('intval', $validated['link_ids']);

    // Only selected college links belonging to this test.
    $query = TestLink::where('test_id', $test->id)
        ->whereIn('id', $ids);

    if ((clone $query)->count() !== count(array_unique($ids))) {
        return back()->with(
            'error',
            'Some selected links do not belong to this test.'
        );
    }

    $updated = $query->update([
        'valid_from' => !empty($validated['valid_from'])
            ? Carbon::parse($validated['valid_from'])
            : null,

        'valid_until' => !empty($validated['valid_until'])
            ? Carbon::parse($validated['valid_until'])
            : null,
    ]);

    return back()->with(
        'success',
        "Timing settings updated for {$updated} selected college link(s)."
    );
}


public function exportCheckedStudents(Request $request, $test_id)
{
    $request->validate([
        'student_test_ids' => ['required', 'array', 'min:1'],
        'student_test_ids.*' => ['required', 'integer', 'distinct'],
    ]);

    $test = Test::findOrFail($test_id);

    // Only allow student records belonging to this test.
    $ids = StudentTest::where('test_id', $test->id)
        ->whereIn('id', $request->input('student_test_ids', []))
        ->pluck('id')
        ->all();

    if (empty($ids)) {
        return back()->withErrors([
            'student_test_ids' => 'No valid students selected.',
        ]);
    }

    return Excel::download(
        new SelectedTestStudentsExport($test->id, $ids),
         'checked_students_' . now()->format('d_F_Y') . '.xlsx'
    );
}

}
