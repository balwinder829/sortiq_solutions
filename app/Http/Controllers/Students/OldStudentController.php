<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

use App\Models\StudentSession;
use App\Models\College;
use App\Models\Course;

use App\Exports\OldStudentListExport;
use Maatwebsite\Excel\Facades\Excel;

class OldStudentController extends Controller
{
    protected string $permissionPrefix = 'old_students';

    protected array $permissionMap = [
        'index'       => 'view',
        'edit'        => 'edit',
        'update'      => 'edit',
        'destroy'     => 'delete',
        'bulkDelete'  => 'bulk_deletes',
        'exportExcel' => 'downloads',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Shared filtered query
    |--------------------------------------------------------------------------
    */
   private function filteredQuery(Request $request)
{
    $activeSessionId = session('admin_session_id');

    $query = DB::table('old_students')
        ->where('session', $activeSessionId)
        ->whereNull('deleted_at');

    // College ID
    if ($request->filled('college_name')) {
        $query->where('college_name', $request->input('college_name'));
    }

    // Technology Course ID
    if ($request->filled('technology')) {
        $query->whereRaw(
            "FIND_IN_SET(?, REPLACE(technology, ' ', ''))",
            [(string) $request->input('technology')]
        );
    }

    // Gender
    if ($request->filled('gender')) {
        $query->where('gender', $request->input('gender'));
    }

    // Study Mode: only filter when explicitly selected
    $isOnline = $request->input('is_online');

    if ($isOnline === '0' || $isOnline === '1') {
        $query->where('is_online', (int) $isOnline);
    }

    // From Session
    if ($request->filled('from_session')) {
        $query->where('from_session', $request->input('from_session'));
    }

    return $query;
}
private function filteredQuery9(Request $request)
{
    $activeSessionId = session('admin_session_id');

    $query = DB::table('old_students')
        ->where('session', $activeSessionId)
        ->whereNull('deleted_at');

    /*
    |--------------------------------------------------------------------------
    | College ID filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('college_name')) {
        $query->where('college_name', $request->college_name);
    }

    /*
    |--------------------------------------------------------------------------
    | Technology Course ID filter
    | Supports comma-separated IDs, including spaces after commas.
    |--------------------------------------------------------------------------
    */

    if ($request->filled('technology')) {
        $query->whereRaw(
            "FIND_IN_SET(?, REPLACE(technology, ' ', ''))",
            [(string) $request->technology]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gender
    |--------------------------------------------------------------------------
    */

    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    /*
    |--------------------------------------------------------------------------
    | Study Mode
    |--------------------------------------------------------------------------
    */

    if ($request->has('is_online') && $request->is_online !== '') {
        $query->where('is_online', $request->is_online);
    }

    /*
    |--------------------------------------------------------------------------
    | From Session
    |--------------------------------------------------------------------------
    */

    if ($request->filled('from_session')) {
        $query->where('from_session', $request->from_session);
    }

    return $query;
}
   

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        // dd($request);
        $students = $this->filteredQuery($request)
            ->orderByDesc('id')
            ->get();

        $sessions = StudentSession::orderByDesc('id')->get();

        $activeSessionId = session('admin_session_id');

        /*
        |--------------------------------------------------------------------------
        | Colleges used in active session
        |--------------------------------------------------------------------------
        */

        $collegeIds = DB::table('old_students')
            ->where('session', $activeSessionId)
            ->whereNull('deleted_at')
            ->whereNotNull('college_name')
            ->where('college_name', '!=', '')
            ->distinct()
            ->pluck('college_name')
            ->filter()
            ->unique()
            ->values();

        $colleges = College::whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Technologies used in active session
        | Technology stores comma-separated Course IDs
        |--------------------------------------------------------------------------
        */

        $technologyValues = DB::table('old_students')
            ->where('session', $activeSessionId)
            ->whereNull('deleted_at')
            ->whereNotNull('technology')
            ->where('technology', '!=', '')
            ->pluck('technology');

        $technologyIds = $technologyValues
            ->flatMap(function ($value) {
                return array_map('trim', explode(',', $value));
            })
            ->filter(function ($id) {
                return $id !== '' && is_numeric($id);
            })
            ->unique()
            ->values();

        // Fetch only existing courses used in this active session.
        $courses = Course::whereIn('id', $technologyIds)
            ->orderBy('course_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Maps
        |--------------------------------------------------------------------------
        */

        $collegeMap = $colleges->keyBy('id');

        $courseMap = $courses->keyBy('id');

        $sessionMap = $sessions->keyBy('id');

        return view('old_students.index', compact(
            'students',
            'sessions',
            'colleges',
            'courses',
            'collegeMap',
            'courseMap',
            'sessionMap'
        ));
    }

    public function index2(Request $request)
    {   
        
        $students = $this->filteredQuery($request)
            ->orderByDesc('id')
            ->get();

        $sessions = StudentSession::orderByDesc('id')->get();

$activeSessionId = session('admin_session_id');

// ========================================
// COLLEGES: Only colleges having old students
// in the active session
// ========================================

$collegeIds = DB::table('old_students')
    ->where('session', $activeSessionId)
    ->whereNull('deleted_at')
    ->whereNotNull('college_name')
    ->where('college_name', '!=', '')
    ->distinct()
    ->pluck('college_name')
    ->filter()
    ->unique()
    ->values();

$colleges = College::whereIn('id', $collegeIds)
    ->orderBy('college_name')
    ->get();


// ========================================
// COURSES: Only technologies assigned to
// old students in the active session
// ========================================

$technologyIds = DB::table('old_students')
    ->where('session', $activeSessionId)
    ->whereNull('deleted_at')
    ->whereNotNull('technology')
    ->where('technology', '!=', '')
    ->pluck('technology')
    ->flatMap(function ($value) {
        return array_map('trim', explode(',', $value));
    })
    ->filter(function ($id) {
        return $id !== '' && is_numeric($id);
    })
    ->unique()
    ->values();

$courses = Course::whereIn('id', $technologyIds)
    ->orderBy('course_name')
    ->get();


// Keep existing mapping variables unchanged
$collegeMap = $colleges->keyBy('id');
$courseMap = $courses->keyBy('id');
$sessionMap = $sessions->keyBy('id');

        return view('old_students.index', compact(
            'students',
            'sessions',
            'colleges',
            'courses',
            'collegeMap',
            'courseMap',
            'sessionMap'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

   public function edit($id)
{
    $student = DB::table('old_students')
        ->where('id', $id)
        ->whereNull('deleted_at')
        ->first();

    abort_if(!$student, 404);

    $sessions = StudentSession::orderByDesc('id')->get();

    // Session ID => Session details mapping
    $sessionMap = StudentSession::all()->keyBy('id');

    $colleges = College::orderBy('college_name')->get();

    $courses = Course::orderBy('course_name')->get();

    return view('old_students.edit', compact(
        'student',
        'sessions',
        'sessionMap',
        'colleges',
        'courses'
    ));
}


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
{
    $student = DB::table('old_students')
        ->where('id', $id)
        ->whereNull('deleted_at')
        ->first();

    abort_if(!$student, 404);

    $validated = $request->validate([
        'student_name' => 'required|string|max:255',
        'f_name' => 'nullable|string|max:255',
        'sno' => 'nullable|string|max:255',
        'gender' => 'nullable|in:male,female',
        'contact' => 'nullable|string|max:30',
        'alternative_phone' => 'nullable|string|max:30',
        'email_id' => 'nullable|email|max:255',

        'college_name' => 'nullable|string|max:255',

        'technology' => 'nullable|array',
        'technology.*' => 'string|max:255',

        'department' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:100',

        'is_online' => 'nullable|in:0,1',

        // Destination session is editable and required.
        'session' => 'required|string|max:255',

        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'join_date' => 'nullable|date',

        'address' => 'nullable|string',
    ]);

    // Store technology IDs as comma-separated values.
    $validated['technology'] = isset($validated['technology'])
        ? implode(',', $validated['technology'])
        : null;

    $allowedFields = [
        'student_name',
        'f_name',
        'sno',
        'gender',
        'contact',
        'alternative_phone',
        'email_id',
        'college_name',
        'technology',
        'department',
        'status',
        'is_online',

        // Only destination session can be updated.
        'session',

        'start_date',
        'end_date',
        'join_date',
        'address',
    ];

    $data = array_intersect_key(
        $validated,
        array_flip($allowedFields)
    );

    // Preserve original from_session from the database.
    // Never update it from submitted form data.

    $columns = Schema::getColumnListing('old_students');

    if (in_array('updated_at', $columns, true)) {
        $data['updated_at'] = now();
    }

    DB::table('old_students')
        ->where('id', $id)
        ->whereNull('deleted_at')
        ->update($data);

    return redirect()
        ->route('old-students.index', $request->query())
        ->with('success', 'Old student updated successfully.');
}

    /*
    |--------------------------------------------------------------------------
    | Single Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $student = DB::table('old_students')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        abort_if(!$student, 404);

        $columns = Schema::getColumnListing('old_students');

        if (in_array('deleted_at', $columns, true)) {
            $data = [
                'deleted_at' => now(),
            ];

            if (in_array('updated_at', $columns, true)) {
                $data['updated_at'] = now();
            }

            DB::table('old_students')
                ->where('id', $id)
                ->update($data);
        } else {
            DB::table('old_students')
                ->where('id', $id)
                ->delete();
        }

        return redirect()
            ->route('old-students.index')
            ->with('success', 'Old student deleted successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer|distinct',
        ]);

        $ids = $validated['ids'];

        $columns = Schema::getColumnListing('old_students');

        if (in_array('deleted_at', $columns, true)) {
            $data = [
                'deleted_at' => now(),
            ];

            if (in_array('updated_at', $columns, true)) {
                $data['updated_at'] = now();
            }

            DB::table('old_students')
                ->whereIn('id', $ids)
                ->whereNull('deleted_at')
                ->update($data);
        } else {
            DB::table('old_students')
                ->whereIn('id', $ids)
                ->delete();
        }

        return redirect()
            ->route('old-students.index')
            ->with('success', count($ids) . ' old student record(s) deleted successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */

    
public function exportExcel(Request $request)
{
    $fileName = 'old_students';

    if ($request->filled('college_name')) {
        $fileName .= '_college_' . $request->college_name;
    }

    if ($request->filled('technology')) {
        $fileName .= '_technology_' . $request->technology;
    }

    if ($request->filled('gender')) {
        $fileName .= '_' . $request->gender;
    }

    if ($request->filled('is_online')) {
        $fileName .= $request->is_online == 1
            ? '_online'
            : '_offline';
    }

    if ($request->filled('from_session')) {
        $fileName .= '_from_session_' . $request->from_session;
    }

    $fileName .= '_' . strtolower(now()->format('d_F')) . '.xlsx';

    return Excel::download(
        new OldStudentListExport($request),
        $fileName
    );
}

}
