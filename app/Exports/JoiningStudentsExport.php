<?php

namespace App\Exports;

use App\Models\JoiningStudent;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JoiningStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData'
        ]);

        // ✅ FILTERS

        if ($this->request->student_name) {
            $query->where('student_name', 'like', '%' . $this->request->student_name . '%');
        }

        if ($this->request->college) {
            $query->where('college', $this->request->college);
        }

        if ($this->request->technology) {
            $query->where('technology', $this->request->technology);
        }

        if ($this->request->is_sent !== null && $this->request->is_sent !== '') {
            $query->where('is_sent_to_detail', (int)$this->request->is_sent);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Father Name',
            'College',
            'Duration',
            'Technology',
            'Date of Joining',
            'Created At',
            'Status'
        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->student_name,
            $data->father_name,
            $data->collegeData->FullName ?? '',
            $data->durationData->name ?? '',
            $data->courseData->course_name ?? '',
            optional($data->date_of_joining)->format('d-m-Y'),
            optional($data->created_at)->format('d-m-Y H:i'),
            $data->is_sent_to_detail ? 'Sent' : 'Not Sent',
        ];
    }
}