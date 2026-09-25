<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\College;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SessionStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $sessionId;
    protected $collegeData = [];

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function collection()
    {
        $students = Student::where('session', $this->sessionId)
            ->orderBy('student_name')
            ->get();

        // college_name contains College ID
        $collegeIds = $students
            ->pluck('college_name')
            ->filter()
            ->unique()
            ->values();

        // Get college + state + district in one query
        $colleges = College::with(['state', 'district'])
            ->whereIn('id', $collegeIds)
            ->get();

        foreach ($colleges as $college) {
            $this->collegeData[$college->id] = [
                'name' => $college->college_name,
                'state' => $college->state->name ?? '-',
                'district' => $college->district->name ?? '-',
            ];
        }

        return $students;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Mobile',
            'College Name',
            'State',
            'District',
        ];
    }

    public function map($student): array
    {
        $college = $this->collegeData[$student->college_name] ?? null;

        return [
            $student->student_name,
            $student->contact,
            $college['name'] ?? '-',
            $college['state'] ?? '-',
            $college['district'] ?? '-',
        ];
    }
}