<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CourseStudentsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents
{
    protected $courseId;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }

    public function collection()
    {
        return Student::with('sessionData')
            ->where('technology', $this->courseId)
            ->orderBy('student_name', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'SNO',
            'Session ID',
            'Session Name',
        ];
    }

    public function map($student): array
    {
        return [
            $student->student_name,
            $student->sno,
            $student->session,
            optional($student->sessionData)->session_name,
        ];
    }

    // ✅ Auto width + styling (works in all versions)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Auto-size columns
                foreach (range('A', 'D') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Bold header
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);

                // Center align numeric columns
                $sheet->getStyle('B:D')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
