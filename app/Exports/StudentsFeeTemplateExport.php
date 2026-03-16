<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class StudentsFeeTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected int $sessionId;

    public function __construct(int $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /* ---------- FETCH STUDENTS ---------- */
    public function collection()
    {
        return Student::where('session', $this->sessionId)
            ->orderBy('sno')
            ->get();
    }

    /* ---------- HEADERS (MUST MATCH VALIDATION) ---------- */
    public function headings(): array
    {
        return [
            'sno',
            'student_name',
            'f_name',
            'contact',
            'total_fees',
            'reg_fees',
            'paid_fees',
            'session_id',
        ];
    }

    /* ---------- MAP STUDENT → EXCEL ROW ---------- */
    public function map($student): array
    {
        return [
            $student->sno,
            $student->student_name,
            $student->f_name,
            $student->contact,
            $student->total_fees,
            $student->reg_fees,
            $student->paid_fees,
            $student->session, // numeric session_id
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getProtection()->setSheet(true);
        $event->sheet->getProtection()->setPassword('readonly');
    }

}
