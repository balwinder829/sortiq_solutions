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

class ExternalAttendanceController extends Controller
{

    use PdfLayoutTrait;
    
    protected string $permissionPrefix = 'external';

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
        'tests'     => $tests->latest()->get(),
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

    return redirect()
        ->route('admin.external-attendance.index')
        ->with('success', "Attendance '{$test->title}' updated successfully.");
}

    public function destroy(ExternalAttendanceTest $test)
    {

        $test->delete();
        return redirect()->route('admin.external-attendance.index')
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

    public function results(Request $request,ExternalAttendanceTest $test)
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
    return view('admin.external-attendance.results', compact(
        'test',
        'students',
        'courses',
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

                    'status'     => 'followup',
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
        )->with('college')->get();

         // Get last attempt date per college
        $lastAttempts = ExternalAttendanceSubmission::where('external_attendance_test_id', $test->id)
            ->selectRaw('college_id, MAX(created_at) as last_attempt')
            ->groupBy('college_id')
            ->pluck('last_attempt', 'college_id');

        return view('admin.external-attendance.links', compact('test','links','lastAttempts'));
    }


}
