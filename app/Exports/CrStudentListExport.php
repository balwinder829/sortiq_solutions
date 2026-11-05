<?php

namespace App\Exports;

use App\Models\CrStudent;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CrStudentListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /** Same four filters and active-session restriction as the CR index. */
    public function collection()
    {
        $request = $this->request;
        $query = CrStudent::query();

        if ($request->filled('college_name')) {
            $query->where('college_name', $request->college_name);
        }

        if ($request->filled('technology')) {
            $query->whereRaw('FIND_IN_SET(?, technology)', [$request->technology]);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('mobile')) {
            $query->where('contact', 'like', '%' . trim($request->mobile) . '%');
        }

        if (session('admin_session_id')) {
            $query->where('session', session('admin_session_id'));
        }

        return $query->with(['sessionData', 'collegeData'])->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Serial No.', 'Student Name', 'Father Name', 'Gender',
            'Session', 'College', 'Mobile', 'Email', 'Technology', 'Status',
            'Total Fees', 'Registration Fees', 'Paid Fees', 'Pending Fees',
            'Entry Source', 'Source Student ID', 'Created At',
        ];
    }

    public function map($student): array
    {
        $entrySource = match ((int) $student->entry_type) {
            0 => 'Copied from Student',
            2 => 'Imported',
            default => 'Manual Entry',
        };

        return [
            $student->id,
            $student->sno,
            $student->student_name,
            $student->f_name,
            $student->gender,
            $student->sessionData->session_display_name ?? $student->sessionData->session_name ?? $student->session,
            $student->collegeData->FullName ?? $student->collegeData->college_display_name ?? $student->college_name,
            $student->contact,
            $student->email_id,
            $student->course_name,
            $student->status,
            $student->total_fees,
            $student->reg_fees,
            $student->paid_fees,
            $student->pending_fees,
            $entrySource,
            $student->source_student_id,
            $student->created_at,
        ];
    }
}
