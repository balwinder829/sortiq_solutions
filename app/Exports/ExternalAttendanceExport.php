<?php

namespace App\Exports;

use App\Models\ExternalAttendanceSubmission;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExternalAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $test;
    protected $request;

    public function __construct($test, Request $request)
    {
        $this->test = $test;
        $this->request = $request;
    }

    public function collection()
    {
        $query = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $this->test->id
        );

        // ✅ SAME FILTERS AS results()

        if ($this->request->filled('college_id')) {
            $query->where('college_id', $this->request->college_id);
        }

        if ($this->request->filled('name')) {
            $query->where('student_name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->filled('email')) {
            $query->where('student_email', 'like', '%' . $this->request->email . '%');
        }

        if ($this->request->filled('mobile')) {
            $query->where('student_mobile', 'like', '%' . $this->request->mobile . '%');
        }

        if ($this->request->filled('gender')) {
            $query->where('gender', $this->request->gender);
        }

        if ($this->request->filled('finalized')) {
            $query->where('is_finalized', $this->request->finalized);
        }

        if ($this->request->filled('status')) {
            $query->where('is_moved_to_enquiry', $this->request->status);
        }

        if ($this->request->filled('course_id')) {
            $query->where('course_id', $this->request->course_id);
        }

        if ($this->request->filled('class')) {
            $query->where('class', $this->request->class);
        }

        if ($this->request->filled('semester')) {
            $query->where('semester', $this->request->semester);
        }

        return $query->with('college')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Mobile',
            'College',
            'Course',
            'Class',
            'Semester',
            'Gender',
            'Finalized',
            'Status',
            'Date'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->student_name,
            $row->student_email,
            $row->student_mobile,
            $row->college->FullName ?? '',
            $row->course_id,
            $row->class,
            $row->semester,
            ucfirst($row->gender),
            $row->is_finalized ? 'Yes' : 'No',
            $row->is_moved_to_enquiry ? 'Move' : 'Not Moved',
            optional($row->created_at)->format('d-m-Y H:i'),
        ];
    }
}