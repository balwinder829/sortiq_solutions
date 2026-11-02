<?php

namespace App\Exports;

use App\Models\StudentTest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SelectedTestStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $studentTestIds;
    protected int $testId;

    public function __construct(int $testId, array $studentTestIds)
    {
        $this->testId = $testId;
        $this->studentTestIds = $studentTestIds;
    }

    public function collection()
    {
        return StudentTest::with('college')
            ->where('test_id', $this->testId)
            ->whereIn('id', $this->studentTestIds)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('student_tests')
                    ->where('test_id', $this->testId)
                    ->whereNotNull('student_mobile')
                    ->where('student_mobile', '!=', '')
                    ->groupBy('student_mobile', 'college_id');
            })
            ->orderBy('student_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Gender',
            'Mobile',
            'Email',
            'Class',
            'Semester',
            'College',
            
        ];
    }

    public function map($student): array
    {
        return [
            ucwords($student->student_name) ?? '-',
            $student->gender
                ? ucfirst(strtolower($student->gender))
                : '-',
            $student->student_mobile ?? '-',
            $student->student_email ?? '-',
            $student->class ?? '-',
            $student->semester ?? '-',
            $student->college->full_name ?? '-',
            
        ];
    }
}
