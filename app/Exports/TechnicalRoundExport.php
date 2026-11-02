<?php

namespace App\Exports;

use App\Models\StudentTestTechnical;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TechnicalRoundExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $machineRound = DB::table('student_test_machine_round')
            ->select('source_student_test_id')
            ->selectRaw('MAX(id) as machine_round_record_id')
            ->selectRaw('MAX(created_at) as machine_round_moved_at')
            ->whereNull('deleted_at')
            ->groupBy('source_student_test_id');

        $finalRound = DB::table('student_test_final')
            ->select('source_student_test_id')
            ->selectRaw('MAX(id) as final_record_id')
            ->selectRaw('MAX(created_at) as final_moved_at')
            ->whereNull('deleted_at')
            ->groupBy('source_student_test_id');

        $query = StudentTestTechnical::query()
            ->leftJoinSub(
                $machineRound,
                'machine_round',
                function ($join) {
                    $join->on(
                        'student_test_technical.source_student_test_id',
                        '=',
                        'machine_round.source_student_test_id'
                    );
                }
            )
            ->leftJoinSub(
                $finalRound,
                'final_round',
                function ($join) {
                    $join->on(
                        'student_test_technical.source_student_test_id',
                        '=',
                        'final_round.source_student_test_id'
                    );
                }
            );

        // Join the main student_tests table only when Class is selected.
        if (($this->filters['class'] ?? '') !== '') {
            $query->join(
                'student_tests as st',
                'st.id',
                '=',
                'student_test_technical.source_student_test_id'
            )->where('st.class', $this->filters['class']);
        }

        $query->select(
            'student_test_technical.*',
            'machine_round.machine_round_record_id',
            'machine_round.machine_round_moved_at',
            'final_round.final_record_id',
            'final_round.final_moved_at'
        )->orderByDesc('student_test_technical.id');

        // Search filter
        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);

            $query->where(function ($q) use ($search) {
                $q->where('student_test_technical.student_name', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.student_email', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.student_mobile', 'like', "%{$search}%")
                    ->orWhere('student_test_technical.college_name', 'like', "%{$search}%");
            });
        }

        // College filter
        if (!empty($this->filters['college_id'])) {
            $query->where(
                'student_test_technical.college_id',
                $this->filters['college_id']
            );
        }

        // Status filter
        if (($this->filters['status'] ?? '') !== '') {
            $query->where(
                'student_test_technical.status',
                $this->filters['status']
            );
        }

        // Return every matching record; do not paginate the export.
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Mobile',
            'College',
            'Source Student Test ID',
            'Score',
            'Moved To',
            'Moved On',
            'Notes',
            'Added On',
        ];
    }

    public function map($student): array
    {
        $movedTo = '';
        $movedOn = null;

        // Final takes precedence if a final-round record exists.
        if ($student->final_record_id) {
            $movedTo = 'Final Students';
            $movedOn = $student->final_moved_at;
        } elseif ($student->machine_round_record_id) {
            $movedTo = 'Machine Round';
            $movedOn = $student->machine_round_moved_at;
        }

        return [
            $student->id,
            $student->student_name,
            $student->student_email,
            $student->student_mobile ?? '',
            $student->college_name ?? '',
            $student->source_student_test_id,
            $student->score,
            $movedTo,
            $movedOn
                ? Carbon::parse($movedOn)->format('d M Y')
                : '',
            $student->notes ?? '',
            $student->created_at
                ? $student->created_at->format('d M Y')
                : '',
        ];
    }
}
