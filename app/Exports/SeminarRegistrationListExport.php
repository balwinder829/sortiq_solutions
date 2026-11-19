<?php

namespace App\Exports;

use App\Models\SeminarRegistration;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SeminarRegistrationListExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $request = $this->request;

        $query = SeminarRegistration::query();

        // Selected records take priority over filters.
        $ids = $request->input('ids', []);

        if (!empty($ids)) {

            $query->whereIn('id', $ids);

        } else {

            // College filter
            if ($request->filled('college_name_input')) {
                $query->where(
                    'college_name_input',
                    $request->college_name_input
                );
            }

            // Course filter
            if ($request->filled('course_name_input')) {
                $query->where(
                    'course_name_input',
                    $request->course_name_input
                );
            }

            // Student name
            if ($request->filled('student_name')) {
                $query->where(
                    'student_name',
                    $request->student_name
                );
            }

            // Semester
            if ($request->filled('semester')) {
                $query->where(
                    'semester',
                    $request->semester
                );
            }

            // Gender
            if ($request->filled('gender')) {
                $query->where(
                    'gender',
                    $request->gender
                );
            }

            // Contact partial match
            if ($request->filled('contact')) {
                $query->where(
                    'contact',
                    'like',
                    '%' . trim($request->contact) . '%'
                );
            }
        }

        return $query->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Father Name',
            'Gender',
            'College',
            'Contact',
            'Email',
            'Technology',
            'Semester',
            'Registered Date',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->student_name,
            $student->father_name,
            $student->gender,
            $student->college_name_input,
            $student->contact,
            $student->email_id,
            $student->course_name_input,
            $student->semester,
            $student->created_at
                ? \Carbon\Carbon::parse($student->created_at)->format('d M Y')
                : '-',
        ];
    }
}
