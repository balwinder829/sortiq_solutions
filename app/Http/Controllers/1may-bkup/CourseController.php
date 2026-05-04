<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;
use App\Exports\CourseStudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Validation\Rule;


class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:technologies.view')->only(['index']);
        $this->middleware('permission:technologies.create')->only(['create','store']);
        $this->middleware('permission:technologies.edit')->only(['edit','update']);
        $this->middleware('permission:technologies.delete')->only('destroy');
    }

    // public function index()
    // {
    //     // $courses = Course::latest()->get();
    //      $courses = Course::withCount('students')
    //         ->orderBy('course_name', 'asc') // A → Z
    //         ->get();
    //     return view('courses.index', compact('courses'));
    // }

    // public function index()
    // {
    //     $activeSessionId = session('admin_session_id');

    //     $courses = Course::withCount([
    //             'students as students_count' => function ($query) use ($activeSessionId) {
    //                 $query->where('session', $activeSessionId);
    //             }
    //         ])
    //         ->orderBy('course_name', 'asc') // A → Z
    //         ->get();

    //     return view('courses.index', compact('courses'));
    // }

    public function index()
    {
        return view('courses.index');
    }

    public function data(Request $request)
    {
        $activeSessionId = session('admin_session_id');
        $query = Course::withCount([
            'students as students_count' => function ($query) use ($activeSessionId) {
                $query->where('session', $activeSessionId);
            }
        ]);

        // 👉 Add filter here
        if ($request->filled('student_filter')) {

            if ($request->student_filter == 'asc') {
                $query->orderBy('students_count', 'asc');
            }

            if ($request->student_filter == 'desc') {
                $query->orderBy('students_count', 'desc');
            }
        }
        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id', 'course_name'],
            'searchable' => ['course_name'],
        ], function ($course, $index, $start) {
            $studentLink = '<a href="' . route('common_filtered_student', ['technology' => $course->id]) . '" class="text-decoration-none"><span class="badge bg-success">' . (int) $course->students_count . '</span></a>';
            $actions = '<a href="' . route('courses.edit', $course->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('courses.destroy', $course->id) . '" method="POST" style="display:inline-block;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?"><i class="fa fa-trash"></i></button></form>';
            return [
                $course->id,
                e($course->course_name),
                $studentLink,
                $actions,
            ];
        });
    }


    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_name' => 'required|string|max:255',
        ]);

         // $exists = Course::withTrashed()
         //    ->whereRaw('LOWER(course_name) = ?', [strtolower($data['course_name'])])
         //    ->exists();

         $exists = Course::whereRaw('LOWER(course_name) = ?', [strtolower($data['course_name'])])
        ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'course_name' => 'Course already exists.'
                ])
                ->withInput();
        }
        Course::create([
            'course_name' => $request->course_name,
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'course_name' => 'required|string|max:255',
        ]);


        // $exists = Course::withTrashed()
        //     ->whereRaw('LOWER(course_name) = ?', [strtolower($data['course_name'])])
        //     ->where('id', '!=', $course->id)
        //     ->exists();

        $courseName = trim($data['course_name']);
        $exists = Course::whereRaw('LOWER(course_name) = ?', [strtolower($courseName)])
        ->where('id', '!=', $course->id)
        ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'course_name' => 'Course already exists.'
                ])
                ->withInput();
        }

        $course->update([
            'course_name' => $request->course_name,
        ]);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    public function students(Course $course)
    {
         $students = Student::with('sessionData')
            ->where('technology', $course->id)
            ->orderBy('student_name', 'asc')
            ->get()
            ->map(function ($student) {
                return [
                    'student_name' => $student->student_name,
                    'sno'          => $student->sno,
                    'session_id'   => $student->session,
                    'session_name' => optional($student->sessionData)->session_name,
                ];
            });

        return response()->json($students);
        // $students = Student::where('technology', $course->id)
        //     ->select('student_name', 'sno', 'session')
        //     ->orderBy('student_name', 'asc')
        //     ->get();

        // return response()->json($students);
    }

    public function exportStudentsExcel(Course $course)
    {
        $fileName = $course->course_name . '_students.xlsx';

        return Excel::download(
            new CourseStudentsExport($course->id),
            $fileName
        );
    }
}
