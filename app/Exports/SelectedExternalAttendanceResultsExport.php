<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SelectedExternalAttendanceResultsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected int $attendanceTestId;
    protected array $attendanceIds;

    public function __construct(int $attendanceTestId, array $attendanceIds)
    {
        $this->attendanceTestId = $attendanceTestId;
        $this->attendanceIds = $attendanceIds;
    }

    public function collection(): Collection
    {
        return DB::table('external_attendance_submissions as s')
            ->leftJoin('colleges as c', 'c.id', '=', 's.college_id')
            ->where('s.external_attendance_test_id', $this->attendanceTestId)
            ->whereIn('s.id', $this->attendanceIds)
            ->select([
                's.student_name',
                's.gender',
                's.student_mobile',
                's.student_email',
                's.class',
                's.semester',
                'c.college_name',
            ])
            ->orderBy('s.student_name')
            ->get()
            ->map(function ($student) {
                return [
                    $student->student_name
                        ? ucwords($student->student_name)
                        : '-',

                    $student->gender
                        ? ucfirst(strtolower($student->gender))
                        : '-',

                    $student->student_mobile ?? '-',
                    $student->student_email ?? '-',
                    $student->class ?? '-',
                    $student->semester ?? '-',
                    $student->college_name ?? '-',
                ];
            });
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
}