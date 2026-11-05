<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\CrStudent;
use App\Models\Student;
use App\Models\StudentSession;
use App\Models\College;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Reference;
use App\Models\Duration;
use App\Models\StudentStatus;
use App\Models\User;
use App\Rules\NotBlockedNumber;
use App\Imports\CrStudentsImport;
use App\Exports\CrStudentListExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CrStudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * CR list filters. Keep the same query rules in CrStudentListExport.
     * Older filters are intentionally retained below as comments for future use.
     */
    protected function filteredQuery(Request $request)
    {
        $query = CrStudent::query();

        // College
        if ($request->filled('college_name')) {
            $query->where('college_name', $request->college_name);
        }

        // Technology (technology is stored as comma-separated course IDs)
        if ($request->filled('technology')) {
            $query->whereRaw('FIND_IN_SET(?, technology)', [$request->technology]);
        }

        // Gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Mobile: partial match, so last 4-5 digits can also be searched
        if ($request->filled('mobile')) {
            $query->where('contact', 'like', '%' . trim($request->mobile) . '%');
        }

        // Default to the currently selected admin session, as in the Student module.
        if (session('admin_session_id')) {
            $query->where('session', session('admin_session_id'));
        }

        /* Future filters retained for later use:
        if ($request->filled('entry_type')) $query->where('entry_type', (int) $request->entry_type);
        if ($request->filled('student_name')) $query->where('student_name', 'like', '%' . $request->student_name . '%');
        if ($request->filled('f_name')) $query->where('f_name', 'like', '%' . $request->f_name . '%');
        if ($request->filled('sno')) $query->where('sno', $request->sno);
        if ($request->filled('session')) $query->where('session', $request->session);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('email_id')) $query->where('email_id', 'like', '%' . $request->email_id . '%');
        if ($request->filled('start_date')) $query->whereDate('start_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('end_date', '<=', $request->end_date);
        // if ($request->filled('fee_filter')) { restore fee filters here later; }
        */

        return $query->orderByDesc('id');
    }

    protected function formData(): array
    {
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        $colleges = College::where('status', 'active')
            ->orderBy('college_display_name', 'asc')->get();
        $courses = Course::orderBy('course_name', 'asc')->get();
        $batches = Batch::where('session_name', $activeSessionId)
            ->where('status', 'active')->whereNull('deleted_at')
            ->orderBy('batch_name', 'asc')->get();
        $references = Reference::orderBy('name', 'asc')->get();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();
        $lastStudent = CrStudent::orderBy('id', 'desc')->first();
        $lastParts = $lastStudent && $lastStudent->sno ? explode('-', $lastStudent->sno) : [];
        $lastSn = $lastParts ? ((int) end($lastParts) + 1) : 1;
        $nextSno = str_pad((string) $lastSn, 5, '0', STR_PAD_LEFT);
        $defaultTechnology = Course::where('course_name', 'Not Selected')->value('id');
        $salesStaff = DB::table('sales_staff')->where('status', 'active')
            ->whereNull('deleted_at')->orderBy('name')->get();
        $users = User::all();

        return compact(
            'activeSession','sessions','colleges','courses','batches','references',
            'course_duration','student_status','nextSno','defaultTechnology',
            'salesStaff','users'
        );
    }

    public function index(Request $request)
    {
        // Use get() like the Student reference page; DataTables handles paging/search UI.
        $students = $this->filteredQuery($request)
            ->with(['sessionData','collegeData','batchData','durationData'])
            ->get();

        $activeSessionId = session('admin_session_id');
        $collegeIds = CrStudent::query()
            ->when($activeSessionId, function ($q) use ($activeSessionId) {
                $q->where('session', $activeSessionId);
            })
            ->whereNotNull('college_name')->where('college_name', '!=', '')
            ->distinct()->pluck('college_name');

        $colleges = College::whereIn('id', $collegeIds)
            ->orderBy('college_display_name', 'asc')->get();
        $courses = Course::orderBy('course_name')->get();

        return view('cr_students.index', compact('students','colleges','courses'));
    }

    public function create()
    {
        return view('cr_students.create', $this->formData());
    }

    public function store(Request $request)
    {
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(trim(preg_replace('/^(mr\.?\s*)+/i', '', (string) $request->f_name)))
        ]);

        $data = $request->validate([
            'student_name' => 'required|string|max:255',
            'f_name' => 'required|string|max:255',
            'email_id' => 'nullable|email|max:255',
            'contact' => ['nullable','string',new NotBlockedNumber],
            'alternative_phone' => ['nullable','string',new NotBlockedNumber],
            'address' => 'nullable|string',
            'gender' => 'required|string',
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place' => 'required_if:is_place,1|nullable|string',
            'is_place' => 'nullable|boolean',
            'status' => 'nullable|string',
            'technology' => 'nullable|array',
            'technology.*' => 'string',
            'total_fees' => 'nullable|numeric',
            'reg_fees' => 'nullable|numeric',
            'paid_fees' => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            'join_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'duration' => 'nullable|string',
            'batch_assign' => 'nullable|string',
            'part_time_offer' => 'nullable|boolean',
            'placement_offer' => 'nullable|boolean',
            'pg_offer' => 'nullable|boolean',
            'is_intern' => 'nullable|boolean',
            'is_married' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'referred_by' => 'nullable|integer',
            'referred_by_type' => 'nullable|in:staff,reference',
        ]);

        // Keep the rest of the Student form fields too, provided they exist in cr_students.
        $extra = array_intersect_key(
            $request->except(['_token', '_method']),
            array_flip(Schema::getColumnListing('cr_students'))
        );
        unset($extra['id'], $extra['source_student_id'], $extra['entry_type'], $extra['created_at'], $extra['updated_at'], $extra['deleted_at']);
        $data = array_merge($extra, $data);

        if (!empty($data['referred_by'])) {
            if (($data['referred_by_type'] ?? null) === 'staff') {
                $request->validate(['referred_by' => 'exists:sales_staff,id']);
            } elseif (($data['referred_by_type'] ?? null) === 'reference') {
                $request->validate(['referred_by' => 'exists:references,id']);
            }
        } else {
            $data['referred_by'] = null;
            $data['referred_by_type'] = null;
        }

        $data['source_student_id'] = null;
        $data['entry_type'] = 1; // Manual CR entry
        $data['student_name'] = Str::lower(trim($data['student_name']));
        $data['f_name'] = Str::lower(trim($data['f_name']));
        $data['session'] = session('admin_session_id');
        $data['sno'] = $this->nextSerialNumber();
        $data['paid_fees'] = $data['paid_fees'] ?? 0;
        $data['reg_fees'] = $data['reg_fees'] ?? 0;
        $data['total_fees'] = $data['total_fees'] ?? 0;
        $data['pending_fees'] = max($data['total_fees'] - $data['reg_fees'] - $data['paid_fees'], 0);

        $plain = preg_replace('/\s+/', '_', trim($data['student_name'])) . $data['sno'];
        $data['plain_password'] = $plain;
        $data['password'] = $plain;

        $student = new CrStudent();
        $student->forceFill($data);
        $student->save();

        return redirect()->route('admin.cr-students.index')->with('success', 'CR student added successfully.');
    }

    protected function nextSerialNumber(): string
    {
        $lastSno = CrStudent::orderByDesc('id')->value('sno');
        $lastParts = $lastSno ? explode('-', $lastSno) : [];
        $last = $lastParts ? ((int) end($lastParts) + 1) : 2000;
        return 'SN-' . date('Ym') . '-' . str_pad((string) $last, 5, '0', STR_PAD_LEFT);
    }

    public function edit(CrStudent $cr_student)
    {
        $data = $this->formData();
        $data['student'] = $cr_student;
        return view('cr_students.edit', $data);
    }

    public function update(Request $request, CrStudent $cr_student)
    {
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(trim(preg_replace('/^(mr\.?\s*)+/i', '', (string) $request->f_name)))
        ]);

        $data = $request->validate([
            'student_name' => 'required|string|max:255',
            'f_name' => 'required|string|max:255',
            'sno' => 'nullable|string|max:255',
            'email_id' => 'nullable|email|max:255',
            'contact' => ['nullable','string',new NotBlockedNumber],
            'alternative_phone' => ['nullable','string',new NotBlockedNumber],
            'address' => 'nullable|string',
            'gender' => 'required|string',
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place' => 'required_if:is_place,1|nullable|string',
            'is_place' => 'nullable|boolean',
            'technology' => 'nullable|array',
            'technology.*' => 'string',
            'status' => 'nullable|string',
            'total_fees' => 'nullable|numeric',
            'reg_fees' => 'nullable|numeric',
            'paid_fees' => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            'join_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'duration' => 'nullable|string',
            'batch_assign' => 'nullable|string',
            'part_time_offer' => 'nullable|boolean',
            'placement_offer' => 'nullable|boolean',
            'pg_offer' => 'nullable|boolean',
            'is_intern' => 'nullable|boolean',
            'is_married' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'referred_by' => 'nullable|integer',
            'referred_by_type' => 'nullable|in:staff,reference',
            'password' => 'nullable|string|min:6',
        ]);

        // Keep all additional fields submitted by the shared Student edit form.
        $extra = array_intersect_key(
            $request->except(['_token', '_method']),
            array_flip(Schema::getColumnListing('cr_students'))
        );
        unset($extra['id'], $extra['source_student_id'], $extra['entry_type'], $extra['created_at'], $extra['updated_at'], $extra['deleted_at']);
        $data = array_merge($extra, $data);

        if (!empty($data['referred_by'])) {
            if (($data['referred_by_type'] ?? null) === 'staff') {
                $request->validate(['referred_by' => 'exists:sales_staff,id']);
            } elseif (($data['referred_by_type'] ?? null) === 'reference') {
                $request->validate(['referred_by' => 'exists:references,id']);
            }
        } else {
            $data['referred_by'] = null;
            $data['referred_by_type'] = null;
        }

        $data['student_name'] = Str::lower(trim($data['student_name']));
        $data['f_name'] = Str::lower(trim($data['f_name']));
        $data['paid_fees'] = $data['paid_fees'] ?? 0;
        $data['reg_fees'] = $data['reg_fees'] ?? 0;
        $data['total_fees'] = $data['total_fees'] ?? 0;
        $data['pending_fees'] = max($data['total_fees'] - $data['reg_fees'] - $data['paid_fees'], 0);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['plain_password'] = $data['password'];
        }

        $cr_student->forceFill($data);
        $cr_student->save();

        return redirect()->route('admin.cr-students.index')->with('success', 'CR student updated successfully.');
    }

    public function destroy(CrStudent $cr_student)
    {
        $cr_student->delete();
        return redirect()->route('admin.cr-students.index')->with('success', 'CR record deleted successfully.');
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

        $deleted = CrStudent::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.cr-students.index')
            ->with('success', $deleted . ' CR student record(s) deleted successfully.');
    }

    /**
     * Copy selected original students into CR table. Source rows are not changed.
     */
    public function markFromStudents(Request $request)
    {
        $ids = $request->input('student_ids');
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        $validated = validator(['student_ids' => $ids], [
            'student_ids' => 'required|array|min:1|max:500',
            'student_ids.*' => 'required|integer|distinct',
        ])->validate();

        $sourceColumns = Schema::getColumnListing('students_detail');
        $targetColumns = Schema::getColumnListing('cr_students');
        $copied = 0;
        $skipped = 0;

        DB::transaction(function () use ($validated, $sourceColumns, $targetColumns, &$copied, &$skipped) {
            $students = Student::whereIn('id', $validated['student_ids'])
                ->whereNull('deleted_at')->get();

            if ($students->count() !== count($validated['student_ids'])) {
                abort(422, 'One or more selected Student records are unavailable.');
            }

            foreach ($students as $student) {
                if (DB::table('cr_students')->where('source_student_id', $student->id)->exists()) {
                    $skipped++;
                    continue;
                }

                $data = array_intersect_key(
                    $student->getAttributes(),
                    array_flip(array_intersect($sourceColumns, $targetColumns))
                );
                unset($data['id'], $data['deleted_at']);
                $data['source_student_id'] = $student->id;
                $data['entry_type'] = 0; // Copied from Student module

                if (in_array('created_at', $targetColumns, true)) $data['created_at'] = now();
                if (in_array('updated_at', $targetColumns, true)) $data['updated_at'] = now();

                DB::table('cr_students')->insert($data);
                $copied++;
            }
        });

        return back()->with('success', "Marked {$copied} student(s) as CR. {$skipped} already existed and were skipped.");
    }

    public function importForm()
    {
        return view('cr_students.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt,xlsx,xls']);
        DB::beginTransaction();
        try {
            $importer = new CrStudentsImport();
            Excel::import($importer, $request->file('file'));

            $failures = method_exists($importer, 'failures') ? $importer->failures() : collect();
            if ($failures && $failures->isNotEmpty()) {
                DB::rollBack();
                $messages = [];
                foreach ($failures as $failure) {
                    $messages[] = "Row {$failure->row()} – {$failure->attribute()} – " . implode(', ', $failure->errors());
                }
                return back()->withErrors($messages);
            }

            DB::commit();
            $total = $importer->totalRows ?? 0;
            $inserted = $importer->insertedRows ?? 0;
            $skipped = $importer->skippedRows ?? 0;
            return back()->with('success', "From {$total} rows: {$inserted} imported successfully, {$skipped} skipped.");
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('CR student import failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['Import failed: ' . $e->getMessage()]);
        }
    }

    public function exportExcel(Request $request)
    {
        $date = strtolower(now()->format('d_F'));
        $parts = ['cr_students'];
        foreach (['college_name','technology','gender','mobile'] as $field) {
            if ($request->filled($field)) $parts[] = Str::slug($field . '_' . $request->input($field), '_');
        }
        return Excel::download(new CrStudentListExport($request), substr(implode('_', $parts) . '_' . $date . '.xlsx', 0, 150));
    }
}
