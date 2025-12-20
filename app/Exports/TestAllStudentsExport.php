<?php

namespace App\Exports;

use App\Models\StudentTest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TestAllStudentsExport implements FromCollection, WithHeadings
{
    protected $testId;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    public function collection()
    {
        return StudentTest::where('test_id', $this->testId)
            ->select('student_name','student_email','score','is_finalized')
            ->get();
    }

    public function headings(): array
    {
        return ['Name','Email','Score','Selected'];
    }
}
