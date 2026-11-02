<?php

namespace App\Exports;

use App\Models\StudentTestMachineRound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MachineRoundExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query(): Builder
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

        // Join the main table only when Class is selected.
        if (($this->filters['class'] ?? '') !== '') {
            $query->join(
                'student_tests as st',
                'st.id',
                '=',
                'student_test_machine_round.source_student_test_id'
            )->where('st.class', $this->filters['class']);
        }

        $query->orderByDesc('student_test_machine_round.id');

        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('student_test_machine_round.student_name', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.student_email', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.student_mobile', 'like', "%{$search}%")
                    ->orWhere('student_test_machine_round.college_name', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['college_id'])) {
            $query->where(
                'student_test_machine_round.college_id',
                $this->filters['college_id']
            );
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Mobile',
            'College',
            // 'Source Test ID',
            // 'Score',
            'Moved To',
            'Moved On',
            'Destination ID',
            'Notes',
            'Added On',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->student_name,
            $student->student_email,
            $student->student_mobile,
            $student->college_name,
            // $student->source_student_test_id,
            // $student->score,
            $student->final_record_id ? 'Final Students' : 'Not Moved',
            $student->final_moved_at
                ? Carbon::parse($student->final_moved_at)->format('d M Y')
                : '-',
            $student->final_record_id ? '#'.$student->final_record_id : '-',
            $student->notes,
            $student->created_at ? $student->created_at->format('d M Y') : '-',
        ];
    }
}
