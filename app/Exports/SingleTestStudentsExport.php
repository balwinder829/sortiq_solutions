<?php

namespace App\Exports;

use App\Models\Test;
use App\Models\StudentTest;
use App\Models\OfflineTestStudent;
use Maatwebsite\Excel\Concerns\{
    FromCollection, WithHeadings, WithMapping
};

class SingleTestStudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Test $test;
    protected bool $finalizedOnly;

    public function __construct(Test $test, bool $finalizedOnly = false)
    {
        $this->test = $test;
        $this->finalizedOnly = $finalizedOnly;
    }

    public function collection()
    {
        if ($this->test->test_mode === 'online') {
            $q = StudentTest::where('test_id', $this->test->id);

            if ($this->finalizedOnly) {
                $q->where('is_finalized', 1);
            }

            return $q->get();
        }

        // OFFLINE
        $q = OfflineTestStudent::where('test_id', $this->test->id);

        if ($this->finalizedOnly) {
            $q->where('is_finalized', 1);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Email',
            'Mobile',
            'Gender',
            'Score',
            'Finalized'
        ];
    }

    public function map($s): array
    {
        return [
            $s->student_name,
            $s->student_email,
            $s->student_mobile,
            $this->formatGender($s),
            $s->score,
            $s->is_finalized ? 'Yes' : 'No',
        ];
    }

    protected function formatGender($student): string
	{
	    if (!isset($student->gender) || empty($student->gender)) {
	        return '-';
	    }

	    return ucfirst(strtolower($student->gender));
	}

}
