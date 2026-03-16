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

    public function __construct($collegeId, $courseId, $percent_range)
    {
        $this->collegeId = $collegeId;
        $this->courseId = $courseId;
        $this->percent_range = $percent_range;
    }

    public function collection()
    {
        $activeSessionNo = session('admin_session_id');

        $query = Student::with(['collegeData'])
            ->where('session', $activeSessionNo);

        if ($this->collegeId) {
            $query->where('college_name', $this->collegeId);
        }

        if ($this->courseId) {
            // $query->where('technology', $this->courseId);
            $query->whereRaw(
                "FIND_IN_SET(?, technology)",
                [$this->courseId]
            );
        }

        if ($this->percent_range) {

            $expr = '(COALESCE((reg_fees + paid_fees) / NULLIF(total_fees,0),0) * 100)';

            switch ($this->percent_range) {

                case 'upto50':
                    $query->whereRaw("$expr <= 50");
                    break;

                case '50to80':
                    $query->whereRaw("$expr > 50 AND $expr <= 80");
                    break;

                case '80to99':
                    $query->whereRaw("$expr > 80 AND $expr < 100");
                    break;

                case '100':
                    $query->whereRaw("$expr = 100");
                    break;
            }
        }

        return $query->orderBy('student_name', 'asc')->get()->map(function ($student) {
            $total = $student->total_fees ?? 0;
            // $paid  = $student->reg_fees ?? 0;
            $paid  = ($student->reg_fees ?? 0) + ($student->paid_fees ?? 0);

            $paidPercent = $total > 0 ? round(($paid / $total) * 100, 2) : 0;

            return [
                'Student Name'   => ucwords($student->student_name),
                'SNo'   => $student->sno,
                'College'        => $student->collegeData->college_name ?? '',
                'Contact No'        => $student->contact ?? '',
                'Technology'     => $student->course_name ?? '',
                'Total Fees'     => $student->total_fees,
                'Paid Fees'      => $paid,
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
            'SNo',
            'College',
            'Contact No',
            'Technology',
            'Total Fees',
            'Paid Fees',
            'Pending Fees',
            'Paid %',
            'Status',
        ];
    }
}
