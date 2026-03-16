<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EnquiriesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $filters;
    protected $isPassout;

    public function __construct($filters, $isPassout = 0)
    {
        $this->filters = $filters;
        $this->isPassout = $isPassout;

    }

    public function collection()
    {
        $query = Enquiry::where('is_passout', $this->isPassout);

        // 🔐 Role-based
        if (!auth()->user()->isAdmin()) {
            // $query->where('assigned_to', auth()->id());
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

        // =========================
        // ASSIGNED STATUS FILTER
        // =========================
        if (!empty($this->filters['assigned_status'])) {

            if ($this->filters['assigned_status'] === 'assigned') {
                $query->whereNotNull('assigned_to');
            }

            if ($this->filters['assigned_status'] === 'unassigned') {
                $query->where(function ($q) {
                    $q->whereNull('assigned_to')
                      ->orWhere('assigned_to', '');
                });
            }
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $query->whereBetween('created_at', [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59'
            ]);
        }

        return $query->with('collegeData','assignedTo')->get()->map(function ($e) {
            return [
                'Student Name'   => ucwords($e->name),
                'Contact Number' => $e->mobile,
                'Email'          => ucwords($e->email ?? ''),
                'College'        => $e->collegeData->FullName ?? '',
                'Course'         => ucwords($e->study),
                'Semester'       => ucwords($e->semester),
                'Branch'         => ucwords($e->branch ?? ''),
                'Assigned To'    => ucwords($e->assignedTo->name ?? '-'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Contact Number',
            'Email',
            'College',
            'Course',
            'Semester',
            'Branch',
            'Assigned To',
        ];
    }
}
