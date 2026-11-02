<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\StudentTest;
use App\Models\StudentTestTechnical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TechnicalRoundExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentTestTechnicalController extends Controller
{

    protected string $permissionPrefix = 'student_test_technical';

    protected array $permissionMap = [
        'index'        => 'view',

        'export'       => 'export',
        'bulkUpdateCategory'        => 'edit',
        'update'       => 'edit',
        'moveSelected'       => 'edit',
        'moveSelectedToRound'       => 'edit',
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
    $query = StudentTestTechnical::query()
        ->select('student_test_technical.*')

        // Machine Round destination record ID.
        ->selectSub(
            DB::table('student_test_machine_round')
                ->select('id')
                ->whereColumn(
                    'source_student_test_id',
                    'student_test_technical.source_student_test_id'
                )
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->limit(1),
            'machine_round_record_id'
        )

        // Machine Round movement date.
        ->selectSub(
            DB::table('student_test_machine_round')
                ->select('created_at')
                ->whereColumn(
                    'source_student_test_id',
                    'student_test_technical.source_student_test_id'
                )
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->limit(1),
            'machine_round_moved_at'
        )

        // Final Students destination record ID.
        ->selectSub(
            DB::table('student_test_final')
                ->select('id')
                ->whereColumn(
                    'source_student_test_id',
                    'student_test_technical.source_student_test_id'
                )
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->limit(1),
            'final_record_id'
        )

        // Final Students movement date.
        ->selectSub(
            DB::table('student_test_final')
                ->select('created_at')
                ->whereColumn(
                    'source_student_test_id',
                    'student_test_technical.source_student_test_id'
                )
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->limit(1),
            'final_moved_at'
        );

    // Join the main table only when Class is selected.
    if ($request->filled('class')) {
        $query->join(
            'student_tests as st',
            'st.id',
            '=',
            'student_test_technical.source_student_test_id'
        )->where('st.class', $request->input('class'));
    }

    // Search filter.
    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where(
                'student_test_technical.student_name',
                'like',
                "%{$search}%"
            )
                ->orWhere(
                    'student_test_technical.student_email',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'student_test_technical.student_mobile',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'student_test_technical.college_name',
                    'like',
                    "%{$search}%"
                );
        });
    }

    // College filter.
    if ($request->filled('college_id')) {
        $query->where(
            'student_test_technical.college_id',
            $request->input('college_id')
        );
    }

    // Status filter.
    if ($request->filled('status')) {
        $query->where(
            'student_test_technical.status',
            $request->input('status')
        );
    }

    // DataTables handles pagination in the browser.
    $technicalStudents = $query
        ->orderByDesc('student_test_technical.id')
        ->get();

    $statuses = StudentTestTechnical::query()
        ->whereNotNull('status')
        ->distinct()
        ->orderBy('status')
        ->pluck('status');

    $collegeIds = StudentTestTechnical::query()
        ->whereNotNull('college_id')
        ->distinct()
        ->pluck('college_id');

    $colleges = College::query()
        ->whereIn('id', $collegeIds)
        ->orderBy('college_name')
        ->get(['id', 'college_name']);

    // All unique, non-empty Classes from the main table.
    $classes = StudentTest::query()
        ->whereNotNull('class')
        ->where('class', '<>', '')
        ->distinct()
        ->orderBy('class')
        ->pluck('class');

    return view('admin.technical-round.index', compact(
        'technicalStudents',
        'statuses',
        'colleges',
        'classes'
    ));
}

    public function index_19oct(Request $request)
    {
        $query = StudentTestTechnical::query()
            ->select('student_test_technical.*')

            // Machine Round destination record ID.
            ->selectSub(
                DB::table('student_test_machine_round')
                    ->select('id')
                    ->whereColumn(
                        'source_student_test_id',
                        'student_test_technical.source_student_test_id'
                    )
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->limit(1),
                'machine_round_record_id'
            )

            // Machine Round movement date.
            ->selectSub(
                DB::table('student_test_machine_round')
                    ->select('created_at')
                    ->whereColumn(
                        'source_student_test_id',
                        'student_test_technical.source_student_test_id'
                    )
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->limit(1),
                'machine_round_moved_at'
            )

            // Final Students destination record ID.
            ->selectSub(
                DB::table('student_test_final')
                    ->select('id')
                    ->whereColumn(
                        'source_student_test_id',
                        'student_test_technical.source_student_test_id'
                    )
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->limit(1),
                'final_record_id'
            )

            // Final Students movement date.
            ->selectSub(
                DB::table('student_test_final')
                    ->select('created_at')
                    ->whereColumn(
                        'source_student_test_id',
                        'student_test_technical.source_student_test_id'
                    )
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->limit(1),
                'final_moved_at'
            );

        // Search filter.
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('student_test_technical.student_name', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.student_email', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.student_mobile', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.college_name', 'like', "%{$search}%");
            });
        }

        // College filter.
        if ($request->filled('college_id')) {
            $query->where(
                'student_test_technical.college_id',
                $request->input('college_id')
            );
        }

        // Status filter.
        if ($request->filled('status')) {
            $query->where(
                'student_test_technical.status',
                $request->input('status')
            );
        }

        // Return all filtered records; DataTables handles pagination.
        $technicalStudents = $query
            ->orderByDesc('student_test_technical.id')
            ->get();

        $statuses = StudentTestTechnical::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $collegeIds = StudentTestTechnical::query()
            ->whereNotNull('college_id')
            ->distinct()
            ->pluck('college_id');

        $colleges = College::query()
            ->whereIn('id', $collegeIds)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        return view('admin.technical-round.index', compact(
            'technicalStudents',
            'statuses',
            'colleges'
        ));
    }




    /**
     * Export the currently filtered Technical Round list as an Excel-compatible CSV.
     */
    
    public function export(Request $request)
    {
        return Excel::download(
            new TechnicalRoundExport(
                $request->only([
                    'search',
                    'college_id',
                    'status',
                    'class',
                ])
            ),
            'technical-round-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }


    /**
     * Move selected original test records into Technical Round.
     * Source student_tests records remain unchanged.
     */
    public function moveSelected(Request $request)
    {
        $validated = $request->validate([
            'student_test_ids' => ['required', 'array', 'min:1'],
            'student_test_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:student_tests,id',
            ],
        ]);

        $created = 0;
        $alreadyPresent = 0;

        DB::transaction(function () use ($validated, &$created, &$alreadyPresent) {
            $students = StudentTest::query()
                ->whereIn('id', $validated['student_test_ids'])
                ->lockForUpdate()
                ->get();

            foreach ($students as $source) {
                $exists = StudentTestTechnical::query()
                    ->where('source_student_test_id', $source->id)
                    ->exists();

                if ($exists) {
                    $alreadyPresent++;
                    continue;
                }

                StudentTestTechnical::create([
                    'source_student_test_id' => $source->id,
                    'previous_round_type' => 'test',
                    'previous_round_record_id' => $source->id,
                    'student_name' => $source->student_name,
                    'student_email' => $source->student_email,
                    'student_mobile' => $source->student_mobile,
                    'college_id' => $source->college_id,
                    'college_name' => $this->collegeName($source),
                    'score' => (int) $source->score,
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $created++;
            }
        });

        $message = "{$created} student(s) added to Technical Round.";
        if ($alreadyPresent > 0) {
            $message .= " {$alreadyPresent} student(s) skipped because they already exist.";
        }

        return back()->with($created > 0 ? 'success' : 'error', $message);
    }

    private function collegeName(StudentTest $source): ?string
    {
        // Works when StudentTest defines a college() relationship.
        if (method_exists($source, 'college')) {
            return $source->college?->college_name
                ?? $source->college?->name
                ?? null;
        }

        // Fallback for schemas where college relationship is not defined.
        return College::query()
            ->whereKey($source->college_id)
            ->value('college_name');
    }


    public function moveSelectedToRound(Request $request)
    {
        $validated = $request->validate([
            'technical_round_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'technical_round_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:student_test_technical,id',
            ],
            'destination_round' => [
                'required',
                'in:machine_round,final',
            ],
            'benefit_type' => [
                'required_if:destination_round,final',
                'nullable',
                'in:free,discount,stipend,3_month_ppo',
            ],
        ]);

        $destination = $validated['destination_round'];

        $tables = [
            'machine_round' => 'student_test_machine_round',
            'final'         => 'student_test_final',
        ];

        $destinationTable = $tables[$destination];
        $userId = auth()->id();

        $movedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use (
            $validated,
            $destination,
            $destinationTable,
            $userId,
            &$movedCount,
            &$skippedCount
        ) {
            $technicalStudents = StudentTestTechnical::query()
                ->whereIn('id', $validated['technical_round_ids'])
                ->lockForUpdate()
                ->get();

            if (
                $technicalStudents->count()
                !== count(array_unique(array_map(
                    'intval',
                    $validated['technical_round_ids']
                )))
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'technical_round_ids' =>
                        'Some selected Technical Round records were not found.',
                ]);
            }

            foreach ($technicalStudents as $technicalStudent) {
                // Preserve the original main test result.
                $source = StudentTest::query()
                    ->whereKey($technicalStudent->source_student_test_id)
                    ->lockForUpdate()
                    ->first();

                if (!$source) {
                    $skippedCount++;
                    continue;
                }

                // Prevent duplicate active destination records.
                $alreadyExists = DB::table($destinationTable)
                    ->where(
                        'source_student_test_id',
                        $technicalStudent->source_student_test_id
                    )
                    ->whereNull('deleted_at')
                    ->exists();

                if ($alreadyExists) {
                    $skippedCount++;
                    continue;
                }

                $now = now();

                $common = [
                    'source_student_test_id' =>
                        $technicalStudent->source_student_test_id,
                    'previous_round_type' =>
                        'technical',
                    'previous_round_record_id' =>
                        $technicalStudent->id,
                    'student_name' =>
                        $technicalStudent->student_name,
                    'student_email' =>
                        $technicalStudent->student_email,
                    'student_mobile' =>
                        $technicalStudent->student_mobile,
                    'college_id' =>
                        $technicalStudent->college_id,
                    'college_name' =>
                        $technicalStudent->college_name,
                    'created_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($destination === 'final') {
                    $destinationId = DB::table($destinationTable)
                        ->insertGetId($common + [
                            'final_status' => 'selected',
                            'benefit_type' => $validated['benefit_type'],
                            'benefit_amount' => null,
                            'benefit_details' => null,
                            'offer_date' => null,
                            'notes' => null,
                        ]);
                } else {
                    $destinationId = DB::table($destinationTable)
                        ->insertGetId($common + [
                            'score' => 0,
                            'status' => 'pending',
                            'notes' => null,
                        ]);
                }

                // Preserve existing source-round tracking behavior.
                $oldIsMoved = (bool) $source->is_moved_to_round;
                $oldIsFinalized = (bool) $source->is_finalized;

                $source->is_moved_to_round = 1;
                $source->moved_to_round_type = $destination;
                $source->moved_to_round_at = $now;
                $source->moved_to_round_record_id = $destinationId;

                if ($destination === 'final') {
                    $source->is_finalized = 1;
                }

                $source->save();

                // Log progression.
                DB::table('student_test_progression_logs')->insert([
                    'source_student_test_id' => $source->id,
                    'record_type' => $destination,
                    'record_id' => $destinationId,
                    'event_type' => 'moved',
                    'from_round' => 'technical',
                    'to_round' => $destination,
                    'old_values' => json_encode([
                        'is_moved_to_round' => $oldIsMoved,
                        'is_finalized' => $oldIsFinalized,
                    ]),
                    'new_values' => json_encode([
                        'is_moved_to_round' => 1,
                        'destination_round' => $destination,
                        'destination_record_id' => $destinationId,
                        'previous_round_type' => 'technical',
                        'previous_round_record_id' => $technicalStudent->id,
                    ]),
                    'remarks' => 'Moved from Technical Round',
                    'performed_by' => $userId,
                    'created_at' => $now,
                ]);

                $movedCount++;
            }
        });

        $roundName = $destination === 'final'
            ? 'Final Students'
            : 'Machine Round';

        $message = "{$movedCount} student(s) moved to {$roundName}.";

        if ($skippedCount > 0) {
            $message .= " {$skippedCount} student(s) skipped because "
                . 'the source result was missing or an active destination '
                . 'record already existed.';
        }

        return back()->with(
            $movedCount > 0 ? 'success' : 'error',
            $message
        );
    }

}
