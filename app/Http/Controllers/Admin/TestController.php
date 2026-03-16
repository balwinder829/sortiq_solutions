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
use App\Exports\TestSelectedStudentsExport;
use App\Exports\FinalizedStudentsExport;
use App\Models\Enquiry;
use App\Exports\SingleTestStudentsExport;
use App\Exports\OverallStudentsExport;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use App\Models\TestLink;

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

    // ✅ FILTER TESTS BY GENDER (EXISTS)
    if ($gender) {
        $tests->whereHas('studentTests', function ($q) use ($gender) {
            $q->where('source', 'online')
              ->whereRaw('LOWER(gender) = ?', [$gender]);
        });
    }

    return view('admin.tests.index', [
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
    $tests = Test::where('test_mode', 'online')
        ->with(['category','college','course','semester'])
        ->withCount([
            // total registrations (online only)
            'studentTests as total_registered' => function ($q) {
                $q->where('source', 'online');
            },
            // finalized selections
            'studentTests as selected_count' => function ($q) {
                $q->where('source', 'online')
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

    return view('admin.tests.index', [
        'tests'     => $tests->latest()->get(),
        'colleges'  => College::all(),
        'courses'   => Course::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}

    public function index16dec(Request $request)
    {
        $tests = Test::query();

        if ($request->college_id)
            $tests->where('college_id', $request->college_id);

        if ($request->student_course_id)
            $tests->where('student_course_id', $request->student_course_id);

        if ($request->semester_id)
            $tests->where('semester_id', $request->semester_id);

        // if ($request->branch_id)
        //     $tests->where('branch_id', $request->branch_id);

        if ($request->test_category_id)
            $tests->where('test_category_id', $request->test_category_id);

        if ($request->test_id)
            $tests->where('id', $request->test_id);

        if ($request->from_date)
            $tests->whereDate('test_date', '>=', $request->from_date);

        if ($request->to_date)
            $tests->whereDate('test_date', '<=', $request->to_date);

         // ✅ ADD: Status filter
        if ($request->status)
            $tests->where('status', $request->status);

        // ✅ ADD: Active / Inactive filter
        if ($request->filled('is_active'))
            $tests->where('is_active', $request->is_active);


            $tests->where('test_mode', 'online');

        return view('admin.tests.index', [
            'tests'     => $tests->latest()->get(),
            'colleges'  => College::all(),
            'courses'   => Course::all(),
            'semesters' => Semester::all(),
             'branches'  => array(),
             // 'branches'  => Branch::all(),
            'categories' => TestCategory::all(),
            'titles'    => Test::select('id','title')->get(),
        ]);
    }



    // Show form to create test
    public function create1()
    {
        $courses = StudentCourse::all();
        return view('admin.tests.create', compact('courses'));
    }

    public function create()
    {
        return view('admin.tests.create', [
            'colleges'  => College::all(),
            'courses'   => Course::all(),
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
            'student_course_id' => 'required',
            'semester_id'       => 'required',
            // 'branch_id'         => 'required',
            'test_category_id'  => 'required',
            'status'            => 'required|in:draft,published,unpublished',
            'is_active'         => 'nullable|boolean',
            'exam_start_at' => 'required|date',
            'exam_end_at'   => 'required|date|after:exam_start_at',
            'timer_type'    => 'required|in:fixed,individual',
        ]);
        // dd(Str::random(30));

         $test = Test::create([
            'title'             => $request->title,
            'slug'              => Str::random(30), // ✅ RANDOM URL NAME
            'access_key'        => Str::random(10),
            'college_id'        => $request->college_id,
            'student_course_id' => $request->student_course_id,
            'semester_id'       => $request->semester_id,
            'test_category_id'  => $request->test_category_id,
            'status'            => $request->status,
            'is_active'            => $request->is_active,
            'exam_start_at'     => $request->exam_start_at,
            'exam_end_at'       => $request->exam_end_at,
            'timer_type'        => $request->timer_type,
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

    // public function regenerateLink(Test $test)
    // {
    //     $test->update([
    //         'slug' => Str::random(30),
    //         'access_key' => Str::random(10)
    //     ]);

    //     return back()->with('success', 'Test link regenerated successfully.');
    // }

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
            'courses'   => Course::all(),
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
            'exam_start_at' => 'required|date',
            'exam_end_at'   => 'required|date|after:exam_start_at',
            'timer_type'    => 'required|in:fixed,individual',
            'is_active'         => 'nullable|boolean',
            'college_ids' => 'required|array',
            'college_ids.*' => 'exists:colleges,id',
        ]);

        // $test->update($request->all());
        $test->update([
            'title'             => $request->title,
            'student_course_id' => $request->student_course_id,
            'semester_id'       => $request->semester_id,
            'test_category_id'  => $request->test_category_id,
            'status'            => $request->status,
            'is_active'         => $request->is_active,
            'exam_start_at'     => $request->exam_start_at,
            'exam_end_at'       => $request->exam_end_at,
            'timer_type'        => $request->timer_type,
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
        $test->delete();
        return redirect()->route('admin.tests.index')
                         ->with('success', 'Test deleted successfully.');
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
    // Show form to edit test
    // public function edit(Test $test)
    // {
    //     $courses = StudentCourse::all();
    //     return view('admin.tests.edit', compact('test','courses'));
    // }

    // Update test
    // public function update(Request $request, Test $test)
    // {
    //     $request->validate([
    //         'title'=>'required',
    //         'student_course_id'=>'required',
    //     ]);

    //     $test->update([
    //         'title'=>$request->title,
    //         'student_course_id'=>$request->student_course_id,
    //         'description'=>$request->description,
    //     ]);

    //     return redirect()->route('admin.tests.index')->with('success','Test Updated Successfully');
    // }

    // Delete test
    // public function destroy(Test $test)
    // {
    //     $test->delete();
    //     return redirect()->route('admin.tests.index')->with('success','Test Deleted Successfully');
    // }
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

        $studentTestsQuery = $test->studentTests();

        /* ===== COLLEGE DROPDOWN DATA ===== */

        $collegeIds = StudentTest::where('test_id', $test_id)
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

        /* ===== COLLEGE FILTER ===== */

        if ($request->has('college_id')) {

            if ($request->college_id != '') {
                $studentTestsQuery->where('college_id', $request->college_id);
            }

        } else {

            $latestCollegeId = StudentTest::where('test_id', $test_id)
                ->latest('created_at')
                ->value('college_id');

            if ($latestCollegeId) {
                $studentTestsQuery->where('college_id', $latestCollegeId);
            } elseif ($test->college_id) {
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

        /* ===== SORTING ===== */

        if ($request->has('college_id') && $request->college_id != '') {

            // Specific college → score high to low
            $studentTestsQuery->orderByDesc('score');

        } else {

            // All colleges → latest college first
            if ($collegeIds->count()) {

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

        if ($request->filled('moved')) {

            if ($request->moved === '1') {
                $studentTestsQuery->whereIn('id', $movedStudentTestIds);
            }

            elseif ($request->moved === '0' && !empty($movedStudentTestIds)) {
                $studentTestsQuery->whereNotIn('id', $movedStudentTestIds);
            }
        }

        $studentTests = $studentTestsQuery->get();

        /* ===== ATTACH S.NO ===== */

        $studentTests->each(function ($st) use ($students) {
            $st->sno = $students[$st->student_email]->sno ?? '-';
        });

        $defaultCollegeId = $request->has('college_id')
            ? $request->college_id
            : ($latestCollegeId ?? $test->college_id);

        return view('admin.tests.results', compact(
            'test',
            'studentTests',
            'movedStudentTestIds',
            'colleges',
            'defaultCollegeId'
        ));
    }
    
    public function resultsold(Request $request, $test_id)
    {   


        $movedStudentTestIds = Enquiry::where('test_id', $test_id)
            ->pluck('student_test_id')
            ->toArray();
        $test = Test::withCount('questions')->findOrFail($test_id);

        // Fetch students for S.No
        $students = Student::select('email_id','sno')->get()->keyBy('email_id');

        $studentTestsQuery = $test->studentTests();

         /* ===== COLLEGE DROPDOWN DATA ===== */

        // $collegeIds = StudentTest::where('test_id', $test_id)
        //     ->distinct()
        //     ->pluck('college_id');

        // $colleges = College::whereIn('id', $collegeIds)->get();

         $collegeIds = StudentTest::where('test_id', $test_id)
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


        /* ===== COLLEGE FILTER ===== */

        // if ($request->filled('college_id')) {
        //     $studentTestsQuery->where('college_id', $request->college_id);
        // }

        /* ===== COLLEGE FILTER ===== */

        // if ($request->has('college_id')) {

        //     // If specific college selected
        //     if ($request->college_id != '') {
        //         $studentTestsQuery->where('college_id', $request->college_id);
        //     }

        //     // If "All Colleges" selected → do nothing (show all)

        // } else {

        //     // No filter applied → default to test college
        //     $studentTestsQuery->where('college_id', $test->college_id);
        // }

        if ($request->has('college_id')) {

            if ($request->college_id != '') {
                $studentTestsQuery->where('college_id', $request->college_id);
            }

        } else {

            // latest college attempt for this test
            $latestCollegeId = StudentTest::where('test_id', $test_id)
                ->latest('created_at')
                ->value('college_id');

            if ($latestCollegeId) {
                $studentTestsQuery->where('college_id', $latestCollegeId);
            } elseif ($test->college_id) {
                // fallback for legacy tests
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

        // Top scorer (respects other filters)
       

        // Top N filter (NEW)
        // if ($request->filled('top_n') && is_numeric($request->top_n)) {
        //     $studentTestsQuery->orderByDesc('score')->limit((int)$request->top_n);
        // } else {
        //     $studentTestsQuery->orderByDesc('score');
        // }

        if ($request->filled('top_n') && is_numeric($request->top_n)) {

                $studentTestsQuery
                    ->orderByDesc('created_at')
                    ->orderByDesc('score')
                    ->limit((int)$request->top_n);

            } else {

                $studentTestsQuery
                    ->orderByDesc('created_at')
                    ->orderByDesc('score');

            }

        // Selected / Unselected filter (NEW)
        if ($request->filled('finalized')) {
            $studentTestsQuery->where('is_finalized', $request->finalized);
        }

        if ($request->filled('moved')) {
            if ($request->moved === '1') {
                // Only moved
                $studentTestsQuery->whereIn('id', $movedStudentTestIds);
            } elseif ($request->moved === '0') {
                // Not moved
                if (!empty($movedStudentTestIds)) {
                    $studentTestsQuery->whereNotIn('id', $movedStudentTestIds);
                }
            }
        }

        $studentTests = $studentTestsQuery->get();

        // Attach S.No
        $studentTests->each(function ($st) use ($students) {
            $st->sno = $students[$st->student_email]->sno ?? '-';
        });

        // $defaultCollegeId = $request->college_id ?? $latestCollegeId ?? $test->college_id;
        $defaultCollegeId = $request->has('college_id')
        ? $request->college_id
        : ($latestCollegeId ?? $test->college_id);

        // Filter: moved / not moved
    
        return view('admin.tests.results', compact('test', 'studentTests','movedStudentTestIds','colleges','defaultCollegeId'));
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


    public function moveFinalizedToEnquiries(Test $test)
    {
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

    public function results16dec(Request $request, $test_id)
    {
        // Load test with studentTests
        $test = Test::with('studentTests')->findOrFail($test_id);

        // Fetch all students for S.No
        $students = Student::all()->keyBy('email_id');

        // Start query for student tests
        $studentTestsQuery = $test->studentTests();

        // Apply filters from request
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
         // Top scorer filter
        if ($request->filled('top_scorer') && $request->top_scorer == '1') {
            $maxScore = $test->studentTests()->max('score');
            $studentTestsQuery->where('score', $maxScore);
        }

        if ($request->filled('test')) {
            // Optional if you want to filter by test title
            $testTitle = $request->test;
            if ($test->title !== $testTitle) {
                $studentTestsQuery->where('id', 0); // no match
            }
        }

        $studentTests = $studentTestsQuery->get();

        // Attach S.No to each studentTest
        $studentTests->each(function ($st) use ($students) {
            $st->sno = $students[$st->student_email]->sno ?? '-';
        });

        return view('admin.tests.results', compact('test', 'studentTests'));
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
        $mpdf->SetHTMLFooter('');

        // Write ALL content in one go
        $mpdf->WriteHTML($html);

        // Footer only on last page
        $mpdf->SetHTMLFooter($this->getStudentTestPDFFooter());
        $mpdf->WriteHTML('');

        $safeTitle = preg_replace('/[\/\\\\]/', '-', $test->title);
        $safeTitle = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $safeTitle);
         return response()->streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            $safeTitle . '-question-paper.pdf'
        );
    
    }


    public function links(Test $test)
    {
        $links = $test->links()->with('college')->get();

        return view('admin.tests.links', compact('test','links'));
    }


}
