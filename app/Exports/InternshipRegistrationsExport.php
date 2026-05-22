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
            ->when($this->request->ids, fn ($q) =>
                $q->whereIn('id', $this->request->ids)
            )
            ->when($this->request->college, fn ($q) =>
                $q->where('college_name', $this->request->college)
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

            /*
            |--------------------------------------------------------------------------
            | Date Filters
            |--------------------------------------------------------------------------
            */

            ->when($this->request->date_filter == 'today', fn ($q) =>
                $q->whereDate('created_at', today())
            )

            ->when($this->request->date_filter == 'yesterday', fn ($q) =>
                $q->whereDate('created_at', today()->subDay())
            )

            ->when($this->request->date_filter == 'this_week', fn ($q) =>
                $q->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
            )

            ->when($this->request->date_filter == 'last_week', fn ($q) =>
                $q->whereBetween('created_at', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ])
            )

            ->when($this->request->date_filter == 'this_month', fn ($q) =>
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year)
            )

            ->when(
                $this->request->date_filter == 'custom'
                && $this->request->from_date
                && $this->request->to_date,

                fn ($q) => $q->whereBetween('created_at', [
                    \Carbon\Carbon::parse($this->request->from_date)->startOfDay(),
                    \Carbon\Carbon::parse($this->request->to_date)->endOfDay()
                ])
            )

            ->when($this->request->limit, fn ($q) =>
                $q->limit(min((int)$this->request->limit, 100))
            )

            ->latest()

            ->get()

            ->map(function ($row) {

                return [
                    'Full Name'   => $row->full_name,
                    'Email'       => $row->email,
                    'Phone'       => $row->phone,

                    'College'     => optional($row->collegeData)->college_display_name
                                        ?? $row->college_name
                                        ?? '-',

                    'Technology'  => optional($row->courseData)->course_name
                                        ?? $row->technology
                                        ?? '-',

                    'Status'      => $row->status,
                    'Message'     => $row->message,
                    'Created At'  => optional($row->created_at)->format('d-m-Y'),
                ];
            });
    }
    public function collection_old(): Collection
    {
        return InternshipRegistration::with(['collegeData', 'courseData'])
            ->when($this->request->page_type, fn ($q) =>
                $q->where('page_type', $this->request->page_type)
            )
            ->when($this->request->college, fn ($q) =>
                $q->where('college_name', $this->request->college)
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
                    'College'     => optional($row->collegeData)->college_display_name 
                        ?? $row->college_name 
                        ?? '-',

                    'Technology'  => optional($row->courseData)->course_name 
                                        ?? $row->technology 
                                        ?? '-',
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
            'Submit Date',
        ];
    }
}
