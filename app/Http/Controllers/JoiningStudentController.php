<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JoiningStudent;
use App\Mail\StudentJoinedMail;
use App\Models\College;
use App\Models\Course;
use App\Models\Duration;
use Mail;
use App\Models\Student;
use App\Models\StudentSession;
use App\Exports\JoiningStudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class JoiningStudentController extends Controller
{
    protected string $permissionPrefix = 'joined_students';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'adminUrl'         => 'view',
      
        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth')->except(['create', 'store']);

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

    // Frontend form
    public function create()
    {
        return view('joining-form', [
            'colleges'  => College::orderBy('college_name')->get(),
            'courses'   => Course::orderBy('course_name')->get(),
            'durations' => Duration::orderBy('name')->get(),
        ]);
    }

    // Save + Email
    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required',
            'father_name' => 'required',
            'college' => 'required',
            'duration' => 'required',
            'technology' => 'required',
            'date_of_joining' => 'required|date',
        ]);

        $student = JoiningStudent::create($request->all());
        $student->load(['collegeData', 'courseData', 'durationData']);

        $adminEmail = config('app.admin_email', 'admin@example.com');
        // Send email to admin
        Mail::to($adminEmail)
            ->send(new StudentJoinedMail($student));

        return redirect()->back()
            ->with('success', '🎉 Welcome to joining!');
    }

    // Admin list
    public function indexData(Request $request)
{
    if ($request->ajax()) {

        $query = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData'
        ]);

        // ✅ FILTERS

        if ($request->student_name) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->college) {
            $query->where('college', $request->college);
        }

        if ($request->technology) {
            $query->where('technology', $request->technology);
        }

        if ($request->is_sent !== null && $request->is_sent !== '') {
            $query->where('is_sent_to_detail', (int)$request->is_sent);
        }

        return datatables()->of($query->orderBy('id', 'desc'))->make(true);
    }

    $colleges = College::orderBy('college_name')->get();
    $courses  = Course::orderBy('course_name')->get();

    $sessionsList = StudentSession::where('status', 'active')
        ->pluck('display_name', 'id');

    return view('joining_students.index', compact('sessionsList', 'colleges', 'courses'));
}
    public function index(Request $request)
    { 
        $query = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData'
        ]);

        // ✅ FILTERS

        if ($request->student_name) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->college) {
            $query->where('college', $request->college);
        }

        if ($request->technology) {
            $query->where('technology', $request->technology);
        }

        if ($request->is_sent !== null && $request->is_sent !== '') {
            $query->where('is_sent_to_detail', (int)$request->is_sent);
        }
        
        $students = $query->latest()->get();
        //  $students = JoiningStudent::with([
        //     'collegeData',
        //     'courseData',
        //     'durationData'
        // ])->latest()->get();

          $sessionsList = StudentSession::where('status', 'active') // ✅ string status
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');
         // dd($students);

            $colleges = College::orderBy('college_name')->get();
            $courses = Course::orderBy('course_name')->get();

        return view('joining_students.index', compact('students', 'sessionsList','colleges','courses'));
        // return view('joining_students.index', compact('students', 'sessionsList','colleges'));
    }

    // Edit form
    public function edit($id)
    {
        $student = JoiningStudent::findOrFail($id);

        return view('joining_students.edit', [
            'student'   => $student,
            'colleges'  => College::orderBy('college_name')->get(),
            'courses'   => Course::orderBy('course_name')->get(),
            'durations' => Duration::orderBy('name')->get(),
        ]);
    }

    // Update student
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_name' => 'required',
            'father_name' => 'required',
            'college' => 'required',
            'duration' => 'required',
            'technology' => 'required',
            'date_of_joining' => 'required|date',
        ]);

        $student = JoiningStudent::findOrFail($id);
        $student->update($request->all());

        return redirect()
            ->route('joined_students.index')
            ->with('success', '✅ Student updated successfully');
    }

    // Soft delete
    public function destroy($id)
    {
        $student = JoiningStudent::findOrFail($id);
        $student->delete();

        return redirect()
            ->back()
            ->with('success', '🗑️ Student deleted successfully');
    }


    public function adminUrl()
    {
        return view('joining_students.link_index');
    }



    public function sendToSession(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:joining_students,id',
            'session_id' => 'required|exists:student_sessions,id',
        ]);

        $inserted = 0;
        $skipped = 0;

        foreach ($request->ids as $id) {

            $joining = JoiningStudent::find($id);

            $lastSno = Student::orderBy('id', 'desc')->value('sno');
            $newSno = is_numeric($lastSno) ? ((int)$lastSno + 1) : 1;

            if (!$joining) {
                $skipped++;
                continue;
            }

            // 🔒 Prevent duplicate
            $exists = Student::where('source_type', 'joining_student')
                ->where('source_id', $id)
                // ->where('session', $request->session_id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // ✅ Insert into students_detail
            Student::create([
                'sno'            => $newSno, // ✅ added
                'student_name'   => $joining->student_name,
                'f_name'         => $joining->father_name,
                'college_name'   => $joining->college,
                'duration'       => $joining->duration,
                'technology'     => $joining->technology,
                'join_date'      => $joining->date_of_joining,
                'start_date'     => $joining->date_of_joining,
                'session'        => $request->session_id,

                // 🔥 tracking
                'source_type'    => 'joining_student',
                'source_id'      => $joining->id,
            ]);

            // ✅ Update flag
            $joining->update([
                'is_sent_to_detail' => 1,
                'sent_to_detail_at' => now(),
            ]);

            $inserted++;
        }

        return response()->json([
            'status' => true,
            'message' => "$inserted students sent successfully" . ($skipped ? " ($skipped skipped)" : "")
        ]);
    }

    public function export(Request $request)
{
    $fileName = 'joining-students-' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new JoiningStudentsExport($request),
        $fileName
    );
}
}
