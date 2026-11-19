<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeminarRegistration;
use App\Models\StudentSession;
use App\Rules\NotBlockedNumber;
use App\Imports\CrStudentsImport;
use App\Exports\SeminarRegistrationListExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class AdminSeminarRegistrationController extends Controller
{

    protected string $permissionPrefix = 'seminar_registration';
    protected array $permissionMap = [
        'index'         => 'view',
        'exportExcel'   => 'view',
        'destroy'       => 'delete',
        'bulkDestroy'    => 'delete',
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

    /**
     * list filters. Keep the same query rules in SeminarRegistrationListExport.
     * Older filters are intentionally retained below as comments for future use.
     */
    protected function filteredQuery(Request $request)
    {
        $query = SeminarRegistration::query();

        // College
        if ($request->filled('college_name_input')) {
            $query->where('college_name_input', $request->college_name_input);
        }

        if ($request->filled('course_name_input')) {
            $query->where('course_name_input', $request->course_name_input);
        }

        if ($request->filled('student_name')) {
            $query->where('student_name', $request->student_name);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Mobile: partial match, so last 4-5 digits can also be searched
        if ($request->filled('contact')) {
            $query->where('contact', 'like', '%' . trim($request->contact) . '%');
        }

        // Default to the currently selected admin session, as in the Student module.
        if (session('admin_session_id')) {
            // $query->where('session', session('admin_session_id'));
        }

        return $query->orderByDesc('id');
    }

    public function index(Request $request)
    {
        $students = $this->filteredQuery($request)->get();

        // Unique colleges from seminar_registrations table
        $colleges = SeminarRegistration::query()
            ->whereNotNull('college_name_input')
            ->where('college_name_input', '!=', '')
            ->distinct()
            ->orderBy('college_name_input')
            ->pluck('college_name_input');

        // Unique technologies from seminar_registrations table
        $technologies = SeminarRegistration::query()
            ->whereNotNull('course_name_input')
            ->where('course_name_input', '!=', '')
            ->distinct()
            ->orderBy('course_name_input')
            ->pluck('course_name_input');

        return view('seminar_registration.index', compact(
            'students',
            'colleges',
            'technologies'
        ));
    }
     

    public function destroy(SeminarRegistration $seminar_registration)
    {
        $seminar_registration->delete();
        return redirect()->route('admin.seminar-registration.index')->with('success', 'Record deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        $validated = validator(['ids' => $ids], [
            'ids' => 'required|array|min:1|max:1000',
            'ids.*' => 'required|integer|distinct',
        ])->validate();

        $deleted = SeminarRegistration::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.seminar-registration.index')
            ->with('success', $deleted . ' Record(s) deleted successfully.');
    }

     
    
public function exportExcel(Request $request)
{
    $request->validate([
        'ids' => 'nullable|array',
        'ids.*' => 'required|integer|distinct',
        'college_name_input' => 'nullable|string',
        'course_name_input' => 'nullable|string',
        'student_name' => 'nullable|string',
        'semester' => 'nullable|string',
        'gender' => 'nullable|in:male,female,other',
        'contact' => 'nullable|string',
    ]);

    $date = strtolower(now()->format('d_F'));

    $parts = ['seminar_registration'];

    foreach ([
        'college_name_input',
        'course_name_input',
        'gender',
        'contact'
    ] as $field) {

        if ($request->filled($field)) {
            $parts[] = Str::slug(
                $field . '_' . $request->input($field),
                '_'
            );
        }
    }

    if (!empty($request->input('ids'))) {
        $parts[] = 'selected_records';
    }

    $fileName = substr(
        implode('_', $parts) . '_' . $date . '.xlsx',
        0,
        150
    );

    return Excel::download(
        new SeminarRegistrationListExport($request),
        $fileName
    );
}

}
