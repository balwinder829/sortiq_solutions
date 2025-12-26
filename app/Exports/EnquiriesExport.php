<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnquiriesExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Enquiry::query();

        // 🔐 Role-based
        if (!auth()->user()->isAdmin()) {
            $query->where('assigned_to', auth()->id());
        }

        if (!empty($this->filters['salesperson_id'])) {
            $query->where('assigned_to', $this->filters['salesperson_id']);
        }

        // 🔍 Same filters as index
        if (!empty($this->filters['college'])) {
            $query->where('college', $this->filters['college']);
        }

        if (!empty($this->filters['study'])) {
            $query->where('study', 'like', '%' . $this->filters['study'] . '%');
        }

        if (!empty($this->filters['semester'])) {
            $query->where('semester', $this->filters['semester']);
        }

        if (!empty($this->filters['lead_status'])) {
            $query->where('lead_status', $this->filters['lead_status']);
        }

        if (!empty($this->filters['source_type'])) {
            $query->where('source', $this->filters['source_type']);
        }

        if (!empty($this->filters['registered'])) {
            if ($this->filters['registered'] === 'yes') {
                $query->whereNotNull('registered_at');
            } else {
                $query->whereNull('registered_at');
            }
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $query->whereBetween('created_at', [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59'
            ]);
        }

        return $query->with('collegeData')->get()->map(function ($e) {
            return [
                'Student Name'   => $e->name,
                'Contact Number' => $e->mobile,
                'College'        => $e->collegeData->FullName ?? '',
                'Course'         => $e->study,
                'Semester'       => $e->semester,
                'Branch'         => $e->branch ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Contact Number',
            'College',
            'Course',
            'Semester',
            'Branch',
        ];
    }
}
