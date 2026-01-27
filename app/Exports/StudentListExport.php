<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class StudentListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;
        $query = Student::query();

        // Same filters as index()
        if ($request->notification === 'registered_today') {
            $query->whereDate('created_at', today());
        } else {

            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', "%{$request->student_name}%");
            }

            if ($request->filled('f_name')) {
                $query->where('f_name', 'like', "%{$request->f_name}%");
            }

            if ($request->filled('sno')) {
                $query->where('sno', $request->sno);
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('session')) {
                $query->where('session', $request->session);
            }

            if ($request->filled('college_name')) {
                $query->where('college_name', $request->college_name);
            }

            if ($request->filled('email_id')) {
                $query->where('email_id', 'like', "%{$request->email_id}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('technology')) {
                $query->where('technology', $request->technology);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }

            if ($request->filled('pending_fees') && $request->pending_fees == 1) {
                $query->where('pending_fees', '>', 0);
            }

            if ($request->filled('part_time_offer')) {
                $query->where('part_time_offer', $request->part_time_offer);
            }

            if ($request->filled('placement_offer')) {
                $query->where('placement_offer', $request->placement_offer);
            }

            if ($request->filled('pg_offer')) {
                $query->where('pg_offer', $request->pg_offer);
            }

            if (auth()->user()->role == 1) {
                $query->where('session', session('admin_session_id'));
            }
        }

        $query->where('certificate_status', 0);
        $limit = $request->filled('limit') ? (int) $request->limit : null;

        $query = $query
            ->with([
                'sessionData',
                'collegeData',
                'courseData',
                'batchData',
                'durationData',
            ])
            ->orderBy('id', 'desc');

        // ✅ Apply limit only if provided
        if ($limit && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get();

        
        // return $query->orderBy('id', 'desc')->get();
        // return $query
        //     ->with([
        //         'sessionData',
        //         'collegeData',
        //         'courseData',
        //         'batchData',
        //         'durationData',
        //     ])
        //     ->orderBy('id', 'desc')
        //     ->get();

    }

    public function headings(): array
    {
        return [
            'S.No',
            'Student Name',
            'Father Name',
            'Contact',
            'Email',
            'Gender',
            'Session',
            'College',
            'Technology',
            'Status',
            // 'Pending Fees',
        ];
    }

    public function map($student): array
    {
        return [
            $student->sno,
            $student->student_name,
            $student->f_name,
            $student->contact,
            $student->email_id,
            $student->gender,
            $student->sessionData->session_name ?? '-',
            $student->collegeData->college_display_name ?? '-',
            $student->courseData->course_name ?? '-',
            $student->status,
            // $student->pending_fees,
        ];
    }

}
