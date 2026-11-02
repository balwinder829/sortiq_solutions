<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\StudentTestMachineRound;
use App\Models\StudentTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MachineRoundExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentTestMachineRoundController extends Controller
{

    protected string $permissionPrefix = 'student_test_machine_round';

    protected array $permissionMap = [
        'index'        => 'view',
        'export'       => 'export',
        'update'       => 'edit',
        'moveSelectedToFinal'       => 'edit',
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
    public function index(Request $request)
    {
        $query = StudentTestMachineRound::query()
            ->select('student_test_machine_round.*')
            ->addSelect([
                'final_record_id' => DB::table('student_test_final')
                    ->select('id')
                    ->whereColumn(
                        'student_test_final.source_student_test_id',
                        'student_test_machine_round.source_student_test_id'
                    )
                    ->whereNull('student_test_final.deleted_at')
                    ->latest('id')
                    ->limit(1),
                'final_moved_at' => DB::table('student_test_final')
                    ->select('created_at')
                    ->whereColumn(
                        'student_test_final.source_student_test_id',
                        'student_test_machine_round.source_student_test_id'
                    )
                    ->whereNull('student_test_final.deleted_at')
                    ->latest('id')
                    ->limit(1),
            ]);

        // Join student_tests only when Class is selected.
        if ($request->filled('class')) {
            $query->join(
                'student_tests as st',
                'st.id',
                '=',
                'student_test_machine_round.source_student_test_id'
            )->where('st.class', $request->input('class'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('student_test_machine_round.student_name', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.student_email', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.student_mobile', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.college_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('college_id')) {
            $query->where(
                'student_test_machine_round.college_id',
                $request->input('college_id')
            );
        }

        $machineStudents = $query
            ->orderByDesc('student_test_machine_round.id')
            ->get();

        $collegeIds = StudentTestMachineRound::query()
            ->whereNotNull('college_id')
            ->distinct()
            ->pluck('college_id');

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        // All unique Classes from the main student_tests table.
        $classes = StudentTest::query()
            ->whereNotNull('class')
            ->where('class', '<>', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return view('admin.machine-round.index', compact(
            'machineStudents',
            'colleges',
            'classes'
        ));
    }
    public function index_19oct(Request $request)
    {
        $query = StudentTestMachineRound::query()
            ->select('student_test_machine_round.*')
            ->addSelect([
                'final_record_id' => DB::table('student_test_final')
                    ->select('id')
                    ->whereColumn(
                        'student_test_final.source_student_test_id',
                        'student_test_machine_round.source_student_test_id'
                    )
                    ->whereNull('student_test_final.deleted_at')
                    ->latest('id')
                    ->limit(1),
                'final_moved_at' => DB::table('student_test_final')
                    ->select('created_at')
                    ->whereColumn(
                        'student_test_final.source_student_test_id',
                        'student_test_machine_round.source_student_test_id'
                    )
                    ->whereNull('student_test_final.deleted_at')
                    ->latest('id')
                    ->limit(1),
            ])
            ->orderByDesc('student_test_machine_round.id');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('student_mobile', 'like', "%{$search}%")
                    ->orWhere('college_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->input('college_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $machineStudents = $query->get();

        $statuses = StudentTestMachineRound::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $collegeIds = StudentTestMachineRound::query()
            ->whereNotNull('college_id')
            ->distinct()
            ->pluck('college_id');

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        return view('admin.machine-round.index', compact(
            'machineStudents',
            'statuses',
            'colleges'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MachineRoundExport(
                $request->only(['search', 'college_id', 'status','class'])
            ),
            'machine-round-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function update(Request $request, StudentTestMachineRound $machineRound)
    {
        $validated = $request->validate([
            'score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $machineRound->score = $validated['score'] ?? 0;
        $machineRound->status = $validated['status'];
        $machineRound->notes = $validated['notes'] ?? null;
        $machineRound->updated_by = auth()->id();
        $machineRound->save();

        return back()->with('success', 'Machine Round record updated successfully.');
    }

    public function moveSelectedToFinal(Request $request)
    {
        $validated = $request->validate([
            'machine_round_ids' => ['required', 'array', 'min:1'],
            'machine_round_ids.*' => [
                'required', 'integer', 'distinct',
                'exists:student_test_machine_round,id',
            ],
            'benefit_type' => [
                'required',
                'in:free,discount,stipend,3_month_ppo',
            ],
        ]);

        $movedCount = 0;
        $skippedCount = 0;
        $userId = auth()->id();

        DB::transaction(function () use (
            $validated, $userId, &$movedCount, &$skippedCount
        ) {
            $machineStudents = StudentTestMachineRound::query()
                ->whereIn('id', $validated['machine_round_ids'])
                ->lockForUpdate()
                ->get();

            if ($machineStudents->count() !== count(array_unique(array_map(
                'intval',
                $validated['machine_round_ids']
            )))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'machine_round_ids' => 'Some selected Machine Round records were not found.',
                ]);
            }

            foreach ($machineStudents as $machineStudent) {
                $source = StudentTest::query()
                    ->whereKey($machineStudent->source_student_test_id)
                    ->lockForUpdate()
                    ->first();

                if (!$source) {
                    $skippedCount++;
                    continue;
                }

                $alreadyExists = DB::table('student_test_final')
                    ->where('source_student_test_id', $machineStudent->source_student_test_id)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($alreadyExists) {
                    $skippedCount++;
                    continue;
                }

                $now = now();

                $destinationId = DB::table('student_test_final')->insertGetId([
                    'source_student_test_id' => $machineStudent->source_student_test_id,
                    'previous_round_type' => 'machine_round',
                    'previous_round_record_id' => $machineStudent->id,
                    'student_name' => $machineStudent->student_name,
                    'student_email' => $machineStudent->student_email,
                    'student_mobile' => $machineStudent->student_mobile,
                    'college_id' => $machineStudent->college_id,
                    'college_name' => $machineStudent->college_name,
                    'final_status' => 'selected',
                    'benefit_type' => $validated['benefit_type'],
                    'benefit_amount' => null,
                    'benefit_details' => null,
                    'offer_date' => null,
                    'notes' => null,
                    'created_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $oldIsMoved = (bool) $source->is_moved_to_round;
                $oldIsFinalized = (bool) $source->is_finalized;

                $source->is_moved_to_round = 1;
                $source->moved_to_round_type = 'final';
                $source->moved_to_round_at = $now;
                $source->moved_to_round_record_id = $destinationId;
                $source->is_finalized = 1;
                $source->save();

                DB::table('student_test_progression_logs')->insert([
                    'source_student_test_id' => $source->id,
                    'record_type' => 'final',
                    'record_id' => $destinationId,
                    'event_type' => 'moved',
                    'from_round' => 'machine_round',
                    'to_round' => 'final',
                    'old_values' => json_encode([
                        'is_moved_to_round' => $oldIsMoved,
                        'is_finalized' => $oldIsFinalized,
                    ]),
                    'new_values' => json_encode([
                        'is_moved_to_round' => 1,
                        'destination_round' => 'final',
                        'destination_record_id' => $destinationId,
                        'previous_round_type' => 'machine_round',
                        'previous_round_record_id' => $machineStudent->id,
                        'benefit_type' => $validated['benefit_type'],
                    ]),
                    'remarks' => 'Moved from Machine Round',
                    'performed_by' => $userId,
                    'created_at' => $now,
                ]);

                $movedCount++;
            }
        });

        $message = "{$movedCount} student(s) moved to Final Students.";

        if ($skippedCount > 0) {
            $message .= " {$skippedCount} student(s) skipped because the source result was missing or an active Final Students record already existed.";
        }

        return back()->with($movedCount > 0 ? 'success' : 'error', $message);
    }
}
