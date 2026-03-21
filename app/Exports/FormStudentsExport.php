<?php

namespace App\Exports;

use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceSubmission;
use Maatwebsite\Excel\Concerns\{
    FromCollection, WithHeadings, WithMapping, ShouldAutoSize
};

class FormStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ExternalAttendanceTest $test;
    protected bool $finalizedOnly;
    protected array $filters;

    public function __construct(ExternalAttendanceTest $test, bool $finalizedOnly = false, array $filters = [])
    {
        $this->test = $test;
        $this->finalizedOnly = $finalizedOnly;
        $this->filters = $filters;
    }

    public function collection()
    {
        $q = ExternalAttendanceSubmission::with('college')
            ->where('external_attendance_test_id', $this->test->id);

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

        /* ===== MOBILE FILTER ===== */

        if (!empty($this->filters['mobile'])) {
            $q->where('student_mobile', 'like', '%' . $this->filters['mobile'] . '%');
        }

        /* ===== GENDER FILTER ===== */

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

          // COURSE
        if (!empty($this->filters['course_id'])) {
            $q->where('course_id', $this->filters['course_id']);
        }

        // CLASS
        if (!empty($this->filters['class'])) {
            $q->where('class', $this->filters['class']);
        }

        // SEMESTER
        if (!empty($this->filters['semester'])) {
            $q->where('semester', $this->filters['semester']);
        }
        /* ===== SORTING ===== */

        $q->latest(); // latest submissions first

        return $q->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Email',
            'Mobile',
            'Gender',
            'Class',
            'Semester',
            'College',
            'Course',
            'Submitted At',
        ];
    }

    public function map($s): array
    {
        return [
            ucwords($s->student_name),
            $s->student_email,
            $s->student_mobile,
            $this->formatGender($s),
            $s->class ?? '-',
            $s->semester ?? '-',
            $s->college->full_name ?? '-',
            $s->course->course_name ?? '-',
            $s->exam_submitted_at
                ? \Carbon\Carbon::parse($s->exam_submitted_at)->format('d M Y h:i A')
                : '-',
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