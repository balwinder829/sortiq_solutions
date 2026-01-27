<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class FeeStatusExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $collegeId;
    protected $courseId;

    public function __construct($collegeId, $courseId)
    {
        $this->collegeId = $collegeId;
        $this->courseId = $courseId;
    }

    public function collection()
    {
        $activeSessionNo = session('admin_session_id');

        $query = Student::with(['collegeData', 'courseData'])
            ->where('session', $activeSessionNo);

        if ($this->collegeId) {
            $query->where('college_name', $this->collegeId);
        }

        if ($this->courseId) {
            $query->where('technology', $this->courseId);
        }

        return $query->get()->map(function ($student) {
            $total = $student->total_fees ?? 0;
            $paid  = $student->reg_fees ?? 0;

            $paidPercent = $total > 0 ? round(($paid / $total) * 100, 2) : 0;

            return [
                'Student Name'   => $student->student_name,
                'College'        => $student->collegeData->college_name ?? '',
                'Technology'     => $student->courseData->course_name ?? '',
                'Total Fees'     => $student->total_fees,
                'Paid Fees'      => $student->reg_fees,
                'Pending Fees'   => $student->pending_fees,
                'Paid %'         => $paidPercent,
                'Status'         => $student->pending_fees == 0
                    ? 'Fully Paid'
                    : ($paid > 0 ? 'Partially Paid' : 'Not Paid'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'College',
            'Technology',
            'Total Fees',
            'Paid Fees',
            'Pending Fees',
            'Paid %',
            'Status',
        ];
    }
}
