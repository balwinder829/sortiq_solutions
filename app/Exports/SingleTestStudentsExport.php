<?php

namespace App\Exports;

use App\Models\Test;
use App\Models\StudentTest;
use App\Models\OfflineTestStudent;
use Maatwebsite\Excel\Concerns\{
    FromCollection, WithHeadings, WithMapping , ShouldAutoSize
};

class SingleTestStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Test $test;
    protected bool $finalizedOnly;
    protected array $filters;

    public function __construct(Test $test, bool $finalizedOnly = false,  array $filters = [])
    {
        $this->test = $test;
        $this->finalizedOnly = $finalizedOnly;
        $this->filters = $filters;
    }

    public function collection()
{
    if ($this->test->test_mode === 'online') {

        $q = StudentTest::with('college')
            ->where('test_id', $this->test->id);

        /* ===== COLLEGE FILTER ===== */

        if (!empty($this->filters['college_id'])) {
            $q->where('college_id', $this->filters['college_id']);
        }

        /* ===== NAME FILTER ===== */

        if (!empty($this->filters['name'])) {
            $q->where('student_name', 'like', '%' . $this->filters['name'] . '%');
        }

        /* ===== EMAIL FILTER ===== */

        if (!empty($this->filters['email'])) {
            $q->where('student_email', 'like', '%' . $this->filters['email'] . '%');
        }

        if (!empty($this->filters['student_mobile'])) {
            $q->where('student_mobile', 'like', '%' . $this->filters['student_mobile'] . '%');
        }

         if (!empty($this->filters['gender'])) {
            $q->where('gender', $this->filters['gender']);
        }

        /* ===== FINALIZED FILTER ===== */

        if (isset($this->filters['finalized']) && $this->filters['finalized'] !== '') {
            $q->where('is_finalized', $this->filters['finalized']);
        }

        if ($this->finalizedOnly) {
            $q->where('is_finalized', 1);
        }

        /* ===== SORTING (same as results page) ===== */

        if (!empty($this->filters['college_id'])) {

            // specific college → score high to low
            $q->orderByDesc('score');

        } else {

            // all colleges → latest college first
            // $collegeIds = StudentTest::where('test_id', $this->test->id)
            //     ->orderByDesc('created_at')
            //     ->pluck('college_id')
            //     ->unique()
            //     ->values();

            $collegeIds = StudentTest::where('test_id', $this->test->id)
                ->whereNotNull('college_id') // ✅ ADD THIS
                ->orderByDesc('created_at')
                ->pluck('college_id')
                ->unique()
                ->values();

            if ($collegeIds->isNotEmpty()) {

                $ids = $collegeIds->implode(',');

                $q->orderByRaw("FIELD(college_id,$ids)")
                  ->orderByDesc('score');

            } else {

                $q->orderByDesc('score');

            }
            // if ($collegeIds->count()) {

            //     $ids = $collegeIds->implode(',');

            //     $q->orderByRaw("FIELD(college_id,$ids)")
            //       ->orderByDesc('score');

            // } else {

            //     $q->orderByDesc('score');

            // }
        }

        /* ===== TOP N ===== */

        if (!empty($this->filters['top_n']) && is_numeric($this->filters['top_n'])) {
            $q->limit((int)$this->filters['top_n']);
        }

        return $q->get();
    }

    // OFFLINE
    $q = OfflineTestStudent::where('test_id', $this->test->id);

    if ($this->finalizedOnly) {
        $q->where('is_finalized', 1);
    }

    return $q->orderByDesc('score')->get();
}

    public function oldccollection()
    {
        if ($this->test->test_mode === 'online') {
            // $q = StudentTest::where('test_id', $this->test->id);

             $q = StudentTest::with('college')
                ->where('test_id', $this->test->id);

            /* ===== COLLEGE FILTER ===== */

        if (!empty($this->filters['college_id'])) {
            $q->where('college_id', $this->filters['college_id']);
        }

        /* ===== NAME FILTER ===== */

        if (!empty($this->filters['name'])) {
            $q->where('student_name', 'like', '%' . $this->filters['name'] . '%');
        }

        /* ===== EMAIL FILTER ===== */

        if (!empty($this->filters['email'])) {
            $q->where('student_email', 'like', '%' . $this->filters['email'] . '%');
        }

        /* ===== FINALIZED FILTER ===== */

        if (isset($this->filters['finalized']) && $this->filters['finalized'] !== '') {
            $q->where('is_finalized', $this->filters['finalized']);
        }

        /* ===== TOP N ===== */

        if (!empty($this->filters['top_n']) && is_numeric($this->filters['top_n'])) {
            $q->orderByDesc('score')->limit((int)$this->filters['top_n']);
        } else {
            $q->orderByDesc('score');
        }

        if ($this->finalizedOnly) {
            $q->where('is_finalized', 1);
        }

            return $q->orderBy('score', 'desc')->get();
            // return $q->get();
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
            'Finalized',
            'College',
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
            $s->college->full_name ?? '-',
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
