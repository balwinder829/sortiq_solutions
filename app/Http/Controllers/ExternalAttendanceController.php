<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\College;
use App\Models\TestCategory;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Enquiry;
use App\Exports\SingleTestStudentsExport;
use App\Exports\FormStudentsExport;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use App\Models\Test;
use App\Models\Course;
use App\Models\Semester;
use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceLink;
use App\Models\ExternalAttendanceSubmission;
use App\Models\StudentSession;
use App\Exports\ExternalAttendanceExport;
use Illuminate\Support\Facades\DB;
use App\Models\TestLink;
use App\Models\StudentTest;
use Illuminate\Validation\ValidationException;

// use App\Models\ExternalAttendance;


class ExternalAttendanceController extends Controller
{

    use PdfLayoutTrait;
    
    protected string $permissionPrefix = 'external_attendance';

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
    
     /* ================= LIST WITH FILTERS ================= */

    public function index(Request $request)
{
    $gender = $request->filled('gender')
        ? strtolower($request->gender)
        : null;

    $tests = ExternalAttendanceTest::query()
        ->with(['college','links.college'])
        ->withCount([

            // ✅ TOTAL SUBMISSIONS
            'submissions as total_submissions' => function ($q) use ($gender) {

                if ($gender) {
                    $q->whereRaw('LOWER(gender) = ?', [$gender]);
                }
            },

            // ✅ FINALIZED COUNT
            'submissions as finalized_count' => function ($q) use ($gender) {
                $q->where('is_finalized', 1);

                if ($gender) {
                    $q->whereRaw('LOWER(gender) = ?', [$gender]);
                }
            },
        ]);

    /* ===== FILTERS ===== */

    if ($request->college_id) {
        $tests->whereHas('links', function ($q) use ($request) {
            $q->where('college_id', $request->college_id);
        });
    }

    if ($request->status) {
        $tests->where('status', $request->status);
    }

    if ($request->filled('is_active')) {
        $tests->where('is_active', $request->is_active);
    }

    if ($request->from_date) {
        $tests->whereDate('test_date', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $tests->whereDate('test_date', '<=', $request->to_date);
    }

    // ✅ FILTER BY GENDER (EXISTS)
    if ($gender) {
        $tests->whereHas('submissions', function ($q) use ($gender) {
            $q->whereRaw('LOWER(gender) = ?', [$gender]);
        });
    }

    return view('admin.external-attendance.index', [
        'tests'     => $tests->latest('updated_at')->get(),
        'colleges'  => College::all(),
        'courses'   => Course::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}

     public function index11(Request $request)
{
    $gender = $request->filled('gender')
        ? strtolower($request->gender)
        : null;

    $tests = ExternalAttendanceTest::where('test_mode', 'online')
        ->with(['college','links.college'])
        ->withCount([

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

    return view('admin.external-attendance.index', [
        'tests'     => $tests->latest()->get(),
        'colleges'  => College::all(),
        'courses'   => Course::all(),
        'semesters' => Semester::all(),
        'branches'  => [],
        'categories'=> TestCategory::all(),
    ]);
}

 
    public function create()
    {
        return view('admin.external-attendance.create', [
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
            'college_ids' => 'required|array',
            'college_ids.*' => 'exists:colleges,id',
            // 'test_category_id'  => 'required',
            'status'            => 'required|in:draft,published,unpublished',
            'is_active'         => 'nullable|boolean'
        ]);
        // dd(Str::random(30));

        $test = ExternalAttendanceTest::create([
            'title'      => $request->title,
            'college_id' => null,
            'description'=> $request->description ?? null,
            'test_date'  => $request->exam_start_at ?? null,
            'status'     => $request->status,
            'is_active'  => $request->is_active ?? 1,
        ]);

        // Test::create($request->all());
        foreach ($request->college_ids as $collegeId) {

            ExternalAttendanceLink::create([
                'external_attendance_test_id' => $test->id,
                'college_id' => $collegeId,
                'slug' => Str::random(40)
            ]);

        }

        $this->createMissingTestLinks(
            $test->id,
            $request->college_ids
        );

        return redirect()->route('admin.external-attendance.index')
                         ->with('success', "Test '{$test->title}' created successfully.");
    }

    public function regenerateLink(ExternalAttendanceTest $test)
    {
        $links = ExternalAttendanceLink::where(
                'external_attendance_test_id',
                $test->id
            )->get();

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

            ExternalAttendanceLink::create([
                'external_attendance_test_id' => $test->id,
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
            ->route('admin.external-attendance.edit', $test->id)
            ->with(
                'error',
                'Please assign a college first to generate student links.'
            );
    }
    /* ================= EDIT FORM ================= */
    public function edit(ExternalAttendanceTest $test)
    {
        return view('admin.external-attendance.edit', [
            'test'      => $test,
            'colleges'  => College::all(),
        ]);
    }

    /* ================= UPDATE TEST ================= */
   public function update(Request $request, ExternalAttendanceTest $test)
{
    $request->validate([
        'title' => 'required',
        'college_ids' => 'required|array',
        'college_ids.*' => 'exists:colleges,id',
        // 'test_category_id' => 'required',
        'status' => 'required|in:draft,published,unpublished',
        'is_active' => 'nullable|boolean'
    ]);

    $test->update([
        'title' => $request->title,
        // 'student_course_id' => $request->student_course_id,
        'semester_id' => $request->semester_id,
        // 'test_category_id' => $request->test_category_id,
        'status' => $request->status,
        'is_active' => $request->is_active ?? 1,
        'test_date' => $request->test_date,
        'description' => $request->description,
    ]);

    $existingCollegeIds = ExternalAttendanceLink::where(
        'external_attendance_test_id',
        $test->id
    )->pluck('college_id')->toArray();

    $newCollegeIds = $request->college_ids;

    $toAdd = array_diff($newCollegeIds, $existingCollegeIds);
    $toRemove = array_diff($existingCollegeIds, $newCollegeIds);

    /* ===== ADD NEW LINKS ===== */
    foreach ($toAdd as $collegeId) {
        ExternalAttendanceLink::create([
            'external_attendance_test_id' => $test->id,
            'college_id' => $collegeId,
            'slug' => Str::random(40)
        ]);
    }

    /* ===== REMOVE LINKS (SAFE DELETE) ===== */
    foreach ($toRemove as $collegeId) {

        $hasStudents = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
        ->where('college_id', $collegeId)
        ->exists();

        if (!$hasStudents) {
            ExternalAttendanceLink::where(
                'external_attendance_test_id',
                $test->id
            )
            ->where('college_id', $collegeId)
            ->delete();
        }
    }

    $this->createMissingTestLinks(
        $test->id,
        $request->college_ids
    );

    return redirect()
        ->route('admin.external-attendance.index')
        ->with('success', "Attendance '{$test->title}' updated successfully.");
}


    public function destroy(ExternalAttendanceTest $test)
    {
        // Check if this test has any student submissions
        $hasStudents = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )->exists();

        if ($hasStudents) {
            return redirect()
                ->route('admin.external-attendance.index')
                ->with(
                    'error',
                    'This test cannot be deleted because student records already exist.'
                );
        }

        $test->delete();

        return redirect()
            ->route('admin.external-attendance.index')
            ->with('success', 'Test deleted successfully.');
    }

    

   
    public function selectedStudents(ExternalAttendanceTest $test)
    {
        $students = $test->studentTests()
            ->where('is_finalized', 1)
            ->orderBy('score', 'desc') // highest score first
            ->get();

        return view(
            'admin.external-attendance.partials.selected_students_modal',
            compact('students')
        );
    }

    public function results(Request $request, ExternalAttendanceTest $test)
{
    /*
    |--------------------------------------------------------------------------
    | Linked categories and tests for this attendance form
    |--------------------------------------------------------------------------
    */

    $linkedOptions = DB::table('external_attendance_test_options as o')
        ->join('attendance_test_categories as c', 'c.id', '=', 'o.category_id')
        ->join('tests as t', 't.id', '=', 'o.test_id')
        ->where('o.external_attendance_test_id', $test->id)
        ->when($request->filled('college_id'), function ($query) use ($request) {
            $query->whereExists(function ($subQuery) use ($request) {
                $subQuery->selectRaw('1')
                    ->from('test_links as tl')
                    ->whereColumn('tl.test_id', 'o.test_id')
                    ->where('tl.college_id', $request->college_id);
            });
        })
        ->select(
            'o.id as option_id',
            'o.category_id',
            'o.test_id',
            'o.display_name',
            'o.test_type',
            'c.name as category_name',
            't.title as test_title'
        )
        ->selectSub(function ($query) use ($request) {
            $query->from('student_tests as st')
                ->whereColumn('st.test_id', 'o.test_id');

            if ($request->filled('college_id')) {
                $query->where('st.college_id', $request->college_id);
            }

            $query->selectRaw('COUNT(*)');
        }, 'student_count')
        ->orderBy('c.sort_order')
        ->orderBy('c.name')
        ->orderBy('o.sort_order')
        ->get();

    $categories = $linkedOptions
        ->unique('category_id')
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Base attendance submissions query
    |--------------------------------------------------------------------------
    */

    $query = ExternalAttendanceSubmission::query()
        ->where(
            'external_attendance_submissions.external_attendance_test_id',
            $test->id
        );

    /*
    |--------------------------------------------------------------------------
    | Existing filters
    |--------------------------------------------------------------------------
    */

    if ($request->filled('college_id')) {
        $query->where(
            'external_attendance_submissions.college_id',
            $request->college_id
        );
    }

    if ($request->filled('name')) {
        $query->where(
            'external_attendance_submissions.student_name',
            'like',
            '%' . $request->name . '%'
        );
    }

    if ($request->filled('email')) {
        $query->where(
            'external_attendance_submissions.student_email',
            'like',
            '%' . $request->email . '%'
        );
    }

    if ($request->filled('mobile')) {
        $query->where(
            'external_attendance_submissions.student_mobile',
            'like',
            '%' . $request->mobile . '%'
        );
    }

    if ($request->filled('gender')) {
        $query->where(
            'external_attendance_submissions.gender',
            $request->gender
        );
    }

    if ($request->filled('finalized')) {
        $query->where(
            'external_attendance_submissions.is_finalized',
            $request->finalized
        );
    }

    if ($request->filled('status')) {
        $query->where(
            'external_attendance_submissions.is_moved_to_enquiry',
            $request->status
        );
    }

    // Course Type filter: Degree / Diploma
    if ($request->filled('course_type')) {
        $query->where('course_type', $request->course_type);
    }

    // Branch filter
    if ($request->filled('student_branch')) {
        $query->where('student_branch', $request->student_branch);
    }

    if ($request->filled('semester')) {
        $query->where(
            'external_attendance_submissions.semester',
            $request->semester
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Category filter
    |--------------------------------------------------------------------------
    | Only submissions whose linked StudentTest belongs to a test mapped
    | under the selected category are returned.
    */

    if ($request->filled('category_id')) {
        $query->whereExists(function ($subQuery) use ($request, $test) {
            $subQuery->selectRaw('1')
                ->from('student_tests as st')
                ->join(
                    'external_attendance_test_options as o',
                    'o.test_id',
                    '=',
                    'st.test_id'
                )
                ->whereColumn(
                    'st.external_attendance_submission_id',
                    'external_attendance_submissions.id'
                )
                ->where(
                    'o.external_attendance_test_id',
                    $test->id
                )
                ->where(
                    'o.category_id',
                    $request->category_id
                );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Linked test filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('linked_test_id')) {
        $query->whereExists(function ($subQuery) use ($request, $test) {
            $subQuery->selectRaw('1')
                ->from('student_tests as st')
                ->join(
                    'external_attendance_test_options as o',
                    'o.test_id',
                    '=',
                    'st.test_id'
                )
                ->whereColumn(
                    'st.external_attendance_submission_id',
                    'external_attendance_submissions.id'
                )
                ->where(
                    'o.external_attendance_test_id',
                    $test->id
                )
                ->where(
                    'o.test_id',
                    $request->linked_test_id
                );

            if ($request->filled('category_id')) {
                $subQuery->where(
                    'o.category_id',
                    $request->category_id
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown data
    |--------------------------------------------------------------------------
    */

    $collegeIds = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
        ->whereNotNull('college_id')
        ->orderByDesc('created_at')
        ->pluck('college_id')
        ->unique()
        ->values();

    $colleges = College::whereIn('id', $collegeIds)
        ->orderBy('college_name')
        ->get();

    $courses = StudentCourse::whereNotIn('course_name', [
            'Not decided',
            'n/a',
            'na',
        ])
        ->orderBy('course_name')
        ->get();

    $sessionsList = StudentSession::where('status', 'active')
        ->orderBy('start_date', 'desc')
        ->get()
        ->pluck('display_name', 'id');

    /*
    |--------------------------------------------------------------------------
    | Attendance submissions
    |--------------------------------------------------------------------------
    */

    $students = $query
        ->with(['college', 'course'])
        ->latest('external_attendance_submissions.created_at')
        ->get();

    return view('admin.external-attendance.results', compact(
        'test',
        'students',
        'courses',
        'sessionsList',
        'colleges',
        'categories',
        'linkedOptions'
    ));
}


    public function results_29sep(Request $request,ExternalAttendanceTest $test)
{


    // $test = ExternalAttendanceTest::findOrFail($test);
    //      dd($test);
    $query = ExternalAttendanceSubmission::where(
        'external_attendance_test_id',
        $test->id
    );


    /* ===== COLLEGE FILTER DATA ===== */

    $collegeIds = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
        ->whereNotNull('college_id')
        ->orderByDesc('created_at')
        ->pluck('college_id')
        ->unique()
        ->values();

    $colleges = College::whereIn('id', $collegeIds)->get();

    /* ===== FILTERS ===== */

    if ($request->filled('college_id')) {
        $query->where('college_id', $request->college_id);
    }

    if ($request->filled('name')) {
        $query->where('student_name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('student_email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('mobile')) {
        $query->where('student_mobile', 'like', '%' . $request->mobile . '%');
    }

    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    if ($request->filled('finalized')) {
        $query->where('is_finalized', $request->finalized);
    }

    if ($request->filled('status')) {
        $query->where('is_moved_to_enquiry', $request->status);
    }

      // COURSE
    if ($request->filled('course_id')) {
        $query->where('course_id', $request->course_id);
    }

    // CLASS
    if ($request->filled('class')) {
        $query->where('class', $request->class);
    }

    // SEMESTER
    if ($request->filled('semester')) {
        $query->where('semester', $request->semester);
    }

    /* ===== SORTING ===== */

    $query->latest();


     $courses = StudentCourse::whereNotIn('course_name', [
            'Not decided', 'n/a', 'na'
        ])->orderBy('course_name')->get();
        
    /* ===== DATA ===== */

    $students = $query->with('college')->get();
    $sessionsList = StudentSession::where('status', 'active') // ✅ string status
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

    return view('admin.external-attendance.results', compact(
        'test',
        'students',
        'courses',
        'sessionsList',
        'colleges'
    ));
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



    public function moveFinalizedToEnquiries(ExternalAttendanceTest $test)
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
                    'source_type'     => 'online',
                    'source_id'     => $st->id,

                    'status'     => 'new',
                    'created_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Finalized students moved to Enquiries successfully.');
    }

    
 

   
    /* ================= ALL STUDENTS (SINGLE TEST) ================= */
    public function exportTestAll(Request $request, ExternalAttendanceTest $test)
    {   
        return Excel::download(
            new FormStudentsExport($test, false, $request->all()),
            'form_'.$test->id.'_all_students.xlsx'
        );
    }


    public function links(ExternalAttendanceTest $test)
    {
        $links = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $test->id
        )  ->with('college')
        ->orderByDesc('updated_at')
        ->orderByDesc('id')
        ->get();

        $lastAttempts = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
            ->selectRaw('college_id, MAX(created_at) as last_attempt')
            ->groupBy('college_id')
            ->pluck('last_attempt', 'college_id');

        $testIds = DB::table('external_attendance_test_options')
            ->where('external_attendance_test_id', $test->id)
            ->whereNotNull('test_id')
            ->distinct()
            ->pluck('test_id');

        $mappedTests = Test::whereIn('id', $testIds)
            ->orderBy('id')
            ->get(['id', 'title']);

        return view(
            'admin.external-attendance.links',
            compact('test', 'links', 'lastAttempts', 'mappedTests')
        );
    }


    public function links_21oct(ExternalAttendanceTest $test)
    {
        $links = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $test->id
        )->with('college')->get();

         // Get last attempt date per college
        $lastAttempts = ExternalAttendanceSubmission::where('external_attendance_test_id', $test->id)
            ->selectRaw('college_id, MAX(created_at) as last_attempt')
            ->groupBy('college_id')
            ->pluck('last_attempt', 'college_id');

        return view('admin.external-attendance.links', compact('test','links','lastAttempts'));
    }


    public function qmoveAttendanceToEnquiries(Request $request, Test $test)
    {
        $ids = $request->attendance_ids;

        // ✅ Validation
        if (empty($ids)) {
            return back()->with('error', 'Please select at least one student.');
        }

        // ✅ Get selected attendance records
        $students = ExternalAttendanceSubmission::whereIn('id', $ids)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No valid students selected.');
        }

        $count = 0;

        foreach ($students as $st) {

            // ✅ Prevent duplicate (based on email + test)
            $enquiry = Enquiry::firstOrCreate(
                [
                    'email'   => $st->student_email,
                    'test_id' => $test->id,
                ],
                [
                    'name'       => $st->student_name,
                    'email'      => $st->student_email,
                    'mobile'     => $st->student_mobile ?? null,

                    'college'    => $st->college_id ?? null,
                    'study'      => $st->course->course_name ?? '',
                    'semester'   => $st->semester ?? null,

                    'test_id'    => $test->id,
                    'student_id' => null,
                    'source'     => 'attendance',

                    'status'     => 'followup',
                    'created_by' => auth()->id(),
                ]
            );

            // count only newly created
            if ($enquiry->wasRecentlyCreated) {
                $count++;
            }
        }

        return back()->with(
            'success',
            "$count student(s) moved to Enquiries successfully."
        );
    }

    public function moveAttendanceToEnquiries(Request $request, Test $test)
    {
        $ids = $request->attendance_ids;
        $sessionId = $request->session_id;

        // ✅ Validation
        if (empty($ids)) {
            return back()->with('error', 'Please select at least one student.');
        }

        if (!$sessionId) {
            return back()->with('error', 'Session is required.');
        }

        // ✅ Get selected attendance records
        $students = ExternalAttendanceSubmission::with(['course', 'college'])
            ->whereIn('id', $ids)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No valid students selected.');
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
                    'test_id'     => $test->id,
                    'session_id'  => $sessionId,
                    'student_id'  => null,
                    'source'      => 'attendance',
                    'source_type' => 'attendance',
                    'source_id'   => $st->id,
                    'status'      => 'new',
                    'created_by'  => auth()->id(),
                ]);

                // Count only newly created Enquiry
                $count++;
            }

            // Existing ya newly created Enquiry — dono cases mein flag update
            $st->update([
                'is_moved_to_enquiry' => 1,
            ]);

            // Mark related StudentTest record(s) as moved
            StudentTest::where('external_attendance_submission_id', $st->id)
                ->update([
                    'is_moved_to_enquiry' => 1,
                ]);
        }


        return back()->with(
            'success',
            "$count student(s) moved to Enquiries successfully."
        );
    }

    

public function exportResults(Request $request, ExternalAttendanceTest $test)
{
    return Excel::download(
        new ExternalAttendanceExport($test, $request),
        'test-'.$test->id.'-results-' . now()->format('Ymd_His') . '.xlsx'
    );
}

/**
 * Create missing TestLink records for tests mapped to this
 * external attendance form and its selected colleges.
 */
private function createMissingTestLinks(int $attendanceTestId, array $collegeIds): void
{
    $testIds = DB::table('external_attendance_test_options')
        ->where('external_attendance_test_id', $attendanceTestId)
        ->whereNotNull('test_id')
        ->pluck('test_id')
        ->unique();

    foreach ($testIds as $testId) {
        foreach ($collegeIds as $collegeId) {
            TestLink::firstOrCreate(
                [
                    'test_id'    => $testId,
                    'college_id' => $collegeId,
                ],
                [
                    'slug' => Str::random(40),
                ]
            );
        }
    }
}

public function bulkLinkActions(Request $request, ExternalAttendanceTest $external_attendance)
{
    $request->validate([
        'scope'       => 'required|in:selected,all',
        'action'      => 'required|in:enable,disable,start,end,both',
        'link_ids'    => 'nullable|array',
        'link_ids.*'  => 'integer',
        'valid_from'  => 'nullable|date',
        'valid_until' => 'nullable|date',
    ]);

    $action = $request->action;

    if ($action === 'start' || $action === 'both') {
        $request->validate([
            'valid_from' => 'required|date',
        ]);
    }

    if ($action === 'end' || $action === 'both') {
        $request->validate([
            'valid_until' => 'required|date',
        ]);
    }

    if ($action === 'both' &&
        strtotime($request->valid_until) <= strtotime($request->valid_from)) {
        throw ValidationException::withMessages([
            'valid_until' => 'The end date and time must be after the start date and time.',
        ]);
    }

    if ($action === 'enable') {
        // Enabling requires an end date that has not expired.
        // A future start date is allowed.
        $linksToCheck = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $external_attendance->id
        );

        if ($request->scope === 'selected') {
            $ids = $request->input('link_ids', []);

            if (empty($ids)) {
                return back()->with('error', 'Please select at least one college.');
            }

            $linksToCheck->whereIn('id', $ids);
        }

        $linksToCheck = $linksToCheck->get();

        if ($linksToCheck->isEmpty()) {
            return back()->with('error', 'No college links were found.');
        }

        foreach ($linksToCheck as $link) {
            if (
                !$link->valid_until ||
                now()->greaterThanOrEqualTo($link->valid_until)
            ) {
                return back()->with(
                    'error',
                    'Every selected college must have a future end date before it can be enabled.'
                );
            }
        }
    }

    $updatedCount = 0;

    \Illuminate\Support\Facades\DB::transaction(function () use (
        $request,
        $external_attendance,
        $action,
        &$updatedCount
    ) {
        $query = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $external_attendance->id
        );

        if ($request->scope === 'selected') {
            $ids = $request->input('link_ids', []);

            if (empty($ids)) {
                throw ValidationException::withMessages([
                    'link_ids' => 'Please select at least one college.',
                ]);
            }

            $query->whereIn('id', $ids);
        }

        $links = $query->get();

        if ($links->isEmpty()) {
            throw ValidationException::withMessages([
                'link_ids' => 'No matching college links were found.',
            ]);
        }

        // Exams linked to this attendance form.
        $testIds = \Illuminate\Support\Facades\DB::table(
            'external_attendance_test_options'
        )
            ->where('external_attendance_test_id', $external_attendance->id)
            ->whereNotNull('test_id')
            ->distinct()
            ->pluck('test_id');

        foreach ($links as $link) {
            $changes = [];

            if ($action === 'enable') {
                $changes['is_enabled'] = 1;
            } elseif ($action === 'disable') {
                $changes['is_enabled'] = 0;
            } elseif ($action === 'start') {
                $changes['valid_from'] = $request->valid_from;
            } elseif ($action === 'end') {
                $changes['valid_until'] = $request->valid_until;
            } elseif ($action === 'both') {
                $changes['valid_from'] = $request->valid_from;
                $changes['valid_until'] = $request->valid_until;
            }

            $link->update($changes);

            // Apply the same settings to exams for this college only.
            $examQuery = TestLink::where('college_id', $link->college_id)
                ->whereIn('test_id', $testIds);

            if ($action === 'enable') {
                $examQuery->update(['is_enabled' => 1]);
            } elseif ($action === 'disable') {
                $examQuery->update(['is_enabled' => 0]);
            } elseif ($action === 'start') {
                $examQuery->update(['valid_from' => $request->valid_from]);
            } elseif ($action === 'end') {
                $examQuery->update(['valid_until' => $request->valid_until]);
            } elseif ($action === 'both') {
                $examQuery->update([
                    'valid_from'  => $request->valid_from,
                    'valid_until' => $request->valid_until,
                ]);
            }

            $updatedCount++;
        }
    });

    return back()->with(
        'success',
        "{$action} action completed for {$updatedCount} college link(s)."
    );
}

public function regenerateCollegeLink(
    Request $request,
    ExternalAttendanceLink $link
) {
    $request->validate([
        'regenerate_scope' => 'required|in:attendance,both',
    ]);

    $attendanceTest = ExternalAttendanceTest::findOrFail(
        $link->external_attendance_test_id
    );

    \Illuminate\Support\Facades\DB::transaction(function () use (
        $link,
        $attendanceTest,
        $request
    ) {
        // Always regenerate the selected attendance form link.
        $link->update([
            'slug' => \Illuminate\Support\Str::random(40),
        ]);

        if ($request->regenerate_scope === 'both') {
            $testIds = \Illuminate\Support\Facades\DB::table(
                'external_attendance_test_options'
            )
                ->where('external_attendance_test_id', $attendanceTest->id)
                ->whereNotNull('test_id')
                ->distinct()
                ->pluck('test_id');

            // Only the selected college's exam links are regenerated.
            TestLink::where('college_id', $link->college_id)
                ->whereIn('test_id', $testIds)
                ->each(function ($testLink) {
                    $testLink->update([
                        'slug' => \Illuminate\Support\Str::random(40),
                    ]);
                });
        }
    });

    $message = $request->regenerate_scope === 'both'
        ? 'Attendance link and linked exam links regenerated for this college.'
        : 'Attendance form link regenerated successfully.';

    return back()->with('success', $message);
}
    

    public function syncMissingLinks(ExternalAttendanceTest $external_attendance)
    {
        $collegeIds = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $external_attendance->id
        )
            ->pluck('college_id')
            ->unique()
            ->values()
            ->all();

        if (empty($collegeIds)) {
            return back()->with(
                'error',
                'No colleges are assigned to this attendance form.'
            );
        }

        DB::transaction(function () use ($external_attendance, $collegeIds) {
            $this->createMissingTestLinks(
                $external_attendance->id,
                $collegeIds
            );
        });

        return back()->with(
            'success',
            'Sync completed. Missing exam links have been created; existing links were preserved.'
        );
    }


    public function updateExamTiming(
        Request $request,
        ExternalAttendanceTest $external_attendance
    ) {
        $validated = $request->validate([
            'college_id' => ['required', 'integer'],
            'test_id' => ['required', 'integer'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
        ]);

        if (!$request->filled('valid_from') &&
            !$request->filled('valid_until')) {
            return back()->with(
                'error',
                'Enter a start date/time or end date/time.'
            );
        }

        $collegeExists = ExternalAttendanceLink::where(
            'external_attendance_test_id',
            $external_attendance->id
        )
            ->where('college_id', $validated['college_id'])
            ->exists();

        if (!$collegeExists) {
            return back()->with(
                'error',
                'The selected college is not assigned to this attendance form.'
            );
        }

        $mapped = DB::table('external_attendance_test_options')
            ->where('external_attendance_test_id', $external_attendance->id)
            ->where('test_id', $validated['test_id'])
            ->exists();

        if (!$mapped) {
            return back()->with(
                'error',
                'The selected exam is not mapped to this attendance form.'
            );
        }

        $link = TestLink::where('test_id', $validated['test_id'])
            ->where('college_id', $validated['college_id'])
            ->first();

        if (!$link) {
            return back()->with(
                'error',
                'Exam link is missing. Click Sync Missing Links first.'
            );
        }

        $start = $request->filled('valid_from')
            ? $validated['valid_from']
            : $link->valid_from;

        $end = $request->filled('valid_until')
            ? $validated['valid_until']
            : $link->valid_until;

        if ($start && $end && strtotime($end) <= strtotime($start)) {
            return back()->with(
                'error',
                'End date/time must be later than start date/time.'
            );
        }

        $changes = [];

        if ($request->filled('valid_from')) {
            $changes['valid_from'] = $validated['valid_from'];
        }

        if ($request->filled('valid_until')) {
            $changes['valid_until'] = $validated['valid_until'];
        }

        $link->update($changes);

        return back()->with(
            'success',
            'Timing updated for the selected college and exam only.'
        );
    }


    public function updateCollegeTiming(
    Request $request,
    ExternalAttendanceTest $external_attendance
) {
    $data = $request->validate([
        'college_id'  => ['required', 'integer'],
        'valid_from'  => ['nullable', 'date'],
        'valid_until' => ['required', 'date', 'after:now'],
    ]);

    if (
        !empty($data['valid_from']) &&
        strtotime($data['valid_until']) <= strtotime($data['valid_from'])
    ) {
        return back()->with(
            'error',
            'End date/time must be later than start date/time.'
        );
    }

    $attendanceLink = ExternalAttendanceLink::where(
        'external_attendance_test_id',
        $external_attendance->id
    )
        ->where('college_id', $data['college_id'])
        ->firstOrFail();

    $testIds = DB::table('external_attendance_test_options')
        ->where('external_attendance_test_id', $external_attendance->id)
        ->whereNotNull('test_id')
        ->distinct()
        ->pluck('test_id');

    DB::transaction(function () use (
        $attendanceLink,
        $data,
        $testIds
    ) {
        $timing = [
            'valid_from'  => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'],
        ];

        // Update only this college's attendance link.
        $attendanceLink->update($timing);

        // Update all existing mapped exam links for this college.
        TestLink::where('college_id', $data['college_id'])
            ->whereIn('test_id', $testIds)
            ->update($timing);
    });

    return back()->with(
        'success',
        'Timing updated for this college and all its mapped exam links.'
    );
}

public function toggleCollegeLinks(
    Request $request,
    ExternalAttendanceTest $external_attendance
) {
    $data = $request->validate([
        'college_id' => ['required', 'integer'],
        'is_enabled' => ['required', 'boolean'],
    ]);

    $attendanceLink = ExternalAttendanceLink::where(
        'external_attendance_test_id',
        $external_attendance->id
    )
        ->where('college_id', $data['college_id'])
        ->firstOrFail();

    $testIds = DB::table('external_attendance_test_options')
        ->where('external_attendance_test_id', $external_attendance->id)
        ->whereNotNull('test_id')
        ->distinct()
        ->pluck('test_id');

    DB::transaction(function () use (
        $attendanceLink,
        $data,
        $testIds
    ) {
        // Change this college's attendance link.
        $attendanceLink->update([
            'is_enabled' => $data['is_enabled'],
        ]);

        // Change all existing mapped exam links for this college.
        TestLink::where('college_id', $data['college_id'])
            ->whereIn('test_id', $testIds)
            ->update([
                'is_enabled' => $data['is_enabled'],
            ]);
    });

    return back()->with(
        'success',
        $data['is_enabled']
            ? 'Attendance and mapped exam links enabled for this college.'
            : 'Attendance and mapped exam links disabled for this college.'
    );
}

public function syncCollegeLinks(
    ExternalAttendanceTest $external_attendance,
    $collegeId
) {
    $assigned = ExternalAttendanceLink::where(
        'external_attendance_test_id',
        $external_attendance->id
    )
        ->where('college_id', $collegeId)
        ->exists();

    if (!$assigned) {
        return back()->with(
            'error',
            'This college is not assigned to the attendance form.'
        );
    }

    DB::transaction(function () use ($external_attendance, $collegeId) {
        $this->createMissingTestLinks(
            $external_attendance->id,
            [(int) $collegeId]
        );
    });

    return back()->with(
        'success',
        'Missing exam links synced for this college. Existing links and settings were preserved.'
    );
}


}
