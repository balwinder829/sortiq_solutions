<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CollegeStudentsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents
{
    protected $collegeId;

    public function __construct($collegeId)
    {
        $this->collegeId = $collegeId;
    }

    /**
     * Fetch students of selected college
     */
    public function collection()
    {
        return Student::with('sessionData')
            ->where('college_name', $this->collegeId)
            ->orderBy('student_name', 'asc')
            ->get();
    }

    /**
     * Excel headings
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'SNO',
            'Session ID',
            'Session Name',
        ];
    }

    /**
     * Map data to rows
     */
    public function map($student): array
    {
        return [
            $student->student_name,
            $student->sno,
            $student->session,
            optional($student->sessionData)->session_name,
        ];
    }

    /**
     * Styling & auto-width
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Auto size columns
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Bold header row
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);

                // Center align numeric columns
                $sheet->getStyle('B:D')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze header
                $sheet->freezePane('A2');
            }
        ];
    }
}
