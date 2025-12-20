<?php

namespace App\Exports;


use App\Models\StudentTest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TestSelectedStudentsExport implements FromCollection, WithHeadings
{
    protected $testId;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    public function collection()
    {
        return StudentTest::where('test_id', $this->testId)
            ->where('is_finalized', 1)
            ->select('student_name','student_email','score')
            ->get();
    }

    public function headings(): array
    {
        return ['Name','Email','Score'];
    }
}

