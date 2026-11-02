<?php

namespace App\Exports;

use App\Models\StudentTestFinal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinalStudentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    private array $filters;
    private array $benefitTypes;

    public function __construct(array $filters = [], array $benefitTypes = [])
    {
        $this->filters = $filters;
        $this->benefitTypes = $benefitTypes;
    }

    public function query(): Builder
    {
        $query = StudentTestFinal::query();

        // Join the main table only when Class is selected.
        if (($this->filters['class'] ?? '') !== '') {
            $query->join(
                'student_tests as st',
                'st.id',
                '=',
                'student_test_final.source_student_test_id'
            )->where('st.class', $this->filters['class'])
             ->select('student_test_final.*');
        }

        $tab = $this->filters['tab'] ?? 'all';
        if ($tab !== 'all' && isset($this->benefitTypes[$tab])) {
            $query->where('student_test_final.benefit_type', $tab);
        }

        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('student_test_final.student_name', 'like', "%{$search}%")
                    ->orWhere('student_test_final.student_email', 'like', "%{$search}%")
                    ->orWhere('student_test_final.student_mobile', 'like', "%{$search}%")
                    ->orWhere('student_test_final.college_name', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['college_id'])) {
            $query->where('student_test_final.college_id', $this->filters['college_id']);
        }

        if (!empty($this->filters['final_status'])) {
            $query->where('student_test_final.final_status', $this->filters['final_status']);
        }

        return $query->orderByDesc('student_test_final.id');
    }

    public function headings(): array
    {
        return [
            'ID', 'Student Name', 'Email', 'Mobile', 'College',
            'Source Test ID', 'Previous Round', 'Final Status',
            'Internship Category', 'Benefit Amount', 'Benefit Details',
            'Offer Date', 'Notes', 'Added On',
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
            $student->source_student_test_id,
            $student->previous_round_type,
            $student->final_status,
            $this->benefitTypes[$student->benefit_type] ?? $student->benefit_type,
            $student->benefit_amount,
            $student->benefit_details,
            optional($student->offer_date)->format('d M Y'),
            $student->notes,
            optional($student->created_at)->format('d M Y'),
        ];
    }
}
