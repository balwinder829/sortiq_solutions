<?php

namespace App\Exports;

use App\Models\ManualData;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ManualDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    protected $sessionId;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sessionId = session('admin_session_id');
    }

    public function collection()
    {
        $query = ManualData::with('college')
            ->where('session_id', $this->sessionId);

        // SAME FILTERS AS CONTROLLER

        if ($this->request->college_id) {
            $query->where('college_id', $this->request->college_id);
        }

        if ($this->request->state_id) {
            $query->whereHas('college', function ($q) {
                $q->where('state_id', $this->request->state_id);
            });
        }

        if ($this->request->district_id) {
            $query->whereHas('college', function ($q) {
                $q->where('district_id', $this->request->district_id);
            });
        }

        if ($this->request->college_type !== null && $this->request->college_type !== '') {
            $query->whereHas('college', function ($q) {
                $q->where('college_type', $this->request->college_type);
            });
        }

        if ($this->request->email) {
            $query->where('student_email', 'like', '%' . $this->request->email . '%');
        }

        if ($this->request->mobile) {
            $query->where('student_mobile', 'like', '%' . $this->request->mobile . '%');
        }

        if ($this->request->gender) {
            $query->where('gender', $this->request->gender);
        }

        if ($this->request->course_type) {
            $query->where('course_type', $this->request->course_type);
        }

        if ($this->request->class) {
            $query->where('class', $this->request->class);
        }

        if ($this->request->semester) {
            $query->where('semester', $this->request->semester);
        }

        if ($this->request->source) {
            $query->where('source', $this->request->source);
        }

        if ($this->request->is_moved !== null && $this->request->is_moved !== '') {
            $query->where('is_moved_to_enquiry', (int) $this->request->is_moved);
        }

        // DATE FILTER
        if ($this->request->date && !$this->request->range) {
            $query->whereDate('created_at', $this->request->date);
        }

        if ($this->request->range) {

            switch ($this->request->range) {

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'last_7_days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;

                case 'last_30_days':
                    $query->whereBetween('created_at', [now()->subDays(30), now()]);
                    break;

                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        // ✅ SEQUENCE (LATEST FIRST SAME AS DATATABLE)
        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'College',
            'Email',
            'Mobile',
            'Class',
            'Semester',
            'Course Type',
            'Gender',
            'Date',
            'Status'
        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->student_name,
            $data->college->FullName ?? '',
            $data->student_email,
            $data->student_mobile,
            $data->class,
            $data->semester,
            $data->course_type,
            ucfirst($data->gender),
            optional($data->created_at)->format('d-m-Y'),
             $data->is_moved_to_enquiry == 1 ? 'Moved' : 'Not Moved',
        ];
    }
}