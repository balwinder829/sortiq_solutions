<?php

namespace App\Exports;

use App\Models\InternshipRegistration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InternshipRegistrationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return InternshipRegistration::with(['collegeData', 'courseData'])
            ->when($this->request->page_type, fn ($q) =>
                $q->where('page_type', $this->request->page_type)
            )
            ->when($this->request->college, fn ($q) =>
                $q->where('college', $this->request->college)
            )
            ->when($this->request->technology, fn ($q) =>
                $q->where('technology', $this->request->technology)
            )
            ->when($this->request->slug, fn ($q) =>
                $q->where('slug', $this->request->slug)
            )
            ->when($this->request->status, fn ($q) =>
                $q->where('status', $this->request->status)
            )
            ->when($this->request->limit, fn ($q) =>
                $q->limit(min((int)$this->request->limit, 100))
            )
            ->latest()
            ->get()
            ->map(function ($row) {
                return [
                    'Full Name'   => $row->full_name,
                    'Email'      => $row->email,
                    'Phone'      => $row->phone,
                    'College'    => $row->collegeData->college_display_name ?? '-',
                    'Technology' => $row->courseData->course_name ?? '-',
                    // 'Page Type'  => $row->page_type,
                    'Status'     => $row->status,
                    'Message'    => $row->message,
                    'Created At' => optional($row->created_at)->format('d-m-Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Phone',
            'College',
            'Technology',
            // 'Page Type',
            'Status',
            'Message',
            'Date',
        ];
    }
}
