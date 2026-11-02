<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InternStudentListExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = clone $query;
    }

    public function query(): Builder
    {
        return $this->query->with([
            'collegeData',
            'batchData',
        ]);
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Father Name',
            'Gender',
            'Contact',
            'Email',
            'Alternative Phone',
            'Address',
            'College Name',
            'Technology',
            'Created At',
            'Updated At',
        ];
    }

    public function map($student): array
    {
        $data = $student->getAttributes();

        $college = $student->collegeData;

        $collegeName = $college->college_display_name
            ?? $college->college_name
            ?? $data['college_name']
            ?? '';

        $technology = $student->course_name;

        // Fall back to the stored value if course names are unavailable.
        if (empty($technology)) {
            $technology = $student->technology;
            $technology = is_array($technology)
                ? implode(', ', $technology)
                : (string) $technology;
        }

        $batchName = $student->batchData->batch_name
            ?? $data['batch_assign']
            ?? '';

        return [
            $data['student_name'] ?? '',
            $data['f_name'] ?? '',
            $data['gender'] ?? '',
            $data['contact'] ?? '',
            $data['email_id'] ?? '',
            $data['alternative_phone'] ?? '',
            $data['address'] ?? '',
            $collegeName,
            
            $technology,
             
            $this->formatDate($data['created_at'] ?? null),
            $this->formatDate($data['updated_at'] ?? null),
        ];
    }

    private function formatDate($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->format('d M Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
