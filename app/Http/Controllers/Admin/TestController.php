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
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TestAllStudentsExport;
use App\Exports\TestSelectedStudentsExport;
use App\Exports\FinalizedStudentsExport;
use App\Models\Enquiry;
use App\Exports\SingleTestStudentsExport;
use App\Exports\OverallStudentsExport;


class TestController extends Controller
{
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
        ->with(['category','college','course','semester'])
        ->withCount([

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
            'college_id'        => 'required',
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

         Test::create([
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

        return redirect()->route('admin.tests.index')
                         ->with('success', 'Test created successfully.');
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
        ]);

        $test->update($request->all());

        return redirect()->route('admin.tests.index')
                         ->with('success', 'Test updated successfully.');
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
        if ($request->filled('top_n') && is_numeric($request->top_n)) {
            $studentTestsQuery->orderByDesc('score')->limit((int)$request->top_n);
        } else {
            $studentTestsQuery->orderByDesc('score');
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

        

        // Filter: moved / not moved
    
        return view('admin.tests.results', compact('test', 'studentTests','movedStudentTestIds'));
    }

    public function bulkFinalize(Request $request)
    {
        $request->validate([
            'student_test_ids' => 'required|array'
        ]);

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
    public function exportTestAll(Test $test)
    {
        return Excel::download(
            new SingleTestStudentsExport($test, false),
            'test_'.$test->id.'_all_students.xlsx'
        );
    }

    /* ================= FINALIZED STUDENTS (SINGLE TEST) ================= */
    public function exportTestFinalized(Test $test)
    {
        return Excel::download(
            new SingleTestStudentsExport($test, true),
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


}
