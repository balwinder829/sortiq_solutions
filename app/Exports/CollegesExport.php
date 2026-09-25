<?php

namespace App\Exports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CollegesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;
    protected $activeSessionId;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->activeSessionId = session('admin_session_id');
    }

    public function collection()
    {
        $filters = $this->filters;
        // dd($filters);
        $query = College::query()
            ->with(['state', 'district'])
            ->withCount([
                'students as students_count' => function ($q) {
                    $q->where('session', $this->activeSessionId);
                }
            ]);

        // Status
        if (($filters['status'] ?? '') !== '') {
            $query->where('status', $filters['status']);
        }
        // State
        if (!empty($filters['state_name'])) {
            $query->whereHas('state', function ($q) use ($filters) {
                $q->where('name', $filters['state_name']);
            });
        }

        // District
        if (!empty($filters['district_name'])) {
            $query->whereHas('district', function ($q) use ($filters) {
                $q->where('name', $filters['district_name']);
            });
        }

        // College Type
        if (($filters['college_type'] ?? '') !== '') {
            $query->where('college_type', $filters['college_type']);
        }

        // Training
        if (($filters['offer_training'] ?? '') !== '') {
            $query->where('offer_training', $filters['offer_training']);
        }

        // Call Status
        if (($filters['call_status'] ?? '') !== '') {
            $query->where('call_status', $filters['call_status']);
        }

        // Important
        if (($filters['is_important'] ?? '') !== '') {
            $query->where('is_important', $filters['is_important']);
        }

        // Ownership
        if (($filters['ownership_type'] ?? '') !== '') {
            $query->where('ownership_type', $filters['ownership_type']);
        }

        // Connection
        if (($filters['connection_type'] ?? '') !== '') {
            $query->where('connection_type', $filters['connection_type']);
        }


        // Training Schedule - 21 Days
        if (($this->filters['training_21_days'] ?? '') !== '') {
            $query->where(
                'training_schedule->21_days',
                $this->filters['training_21_days']
            );
        }

        // Training Schedule - 45 Days
        if (($this->filters['training_45_days'] ?? '') !== '') {
            $query->where(
                'training_schedule->45_days',
                $this->filters['training_45_days']
            );
        }

        // Training Schedule - 6 Months
        if (($this->filters['training_6_months'] ?? '') !== '') {
            $query->where(
                'training_schedule->6_months',
                $this->filters['training_6_months']
            );
        }

         /*
        |--------------------------------------------------------------------------
        | Department - Multiple
        |--------------------------------------------------------------------------
        |
        | Match colleges having ANY selected department.
        |
        */

        // Department - Multiple
if (!empty($filters['departments']) && is_array($filters['departments'])) {

    $departments = array_values(array_filter(
        $filters['departments'],
        fn ($department) => $department !== null && $department !== ''
    ));

    if (!empty($departments)) {
        $query->where(function ($q) use ($departments) {
            foreach ($departments as $department) {
                $q->orWhereJsonContains('colleges.departments', $department);
            }
        });
    }
}

        // Training In
        if (($filters['training_in'] ?? '') !== '') {
            $query->where(
                'training_in',
                $filters['training_in']
            );
        }

        // Training Times in Year
        if (($filters['training_in_year'] ?? '') !== '') {
            $query->where(
                'training_in_year',
                $filters['training_in_year']
            );
        }

        // Whom to Connect
        if (($filters['connected_to'] ?? '') !== '') {
            $query->where(
                'connected_to',
                $filters['connected_to']
            );
        }

        // Reference By
        if (($filters['reference_by'] ?? '') !== '') {
            $query->where(
                'reference_by',
                'like',
                '%' . $filters['reference_by'] . '%'
            );
        }

        // Contact Person
        if (($filters['contact_person'] ?? '') !== '') {
            $query->where(
                'contact_person',
                'like',
                '%' . $filters['contact_person'] . '%'
            );
        }

        // Student Filter
        if (!empty($filters['student_filter'])) {

            switch ($filters['student_filter']) {

                case 'zero':
                    $query->having('students_count', '=', 0);
                    break;

                case 'more':
                    $query->having('students_count', '>', 0);
                    break;

                case 'asc':
                    $query->orderBy('students_count', 'asc');
                    break;

                case 'desc':
                    $query->orderBy('students_count', 'desc');
                    break;
            }
        }

        return $query->get();
    }

     public function headings(): array
    {
        return [
            'College / Place Name',
            'State',
            'District',
            'Display Name',
            'College Type',
            'Providing Training',
            'Training In',
            'Training Times in Year',
            'Whom to Connect',
            'Reference By',
            'Contact Person',
            'Important',
            'Ownership',
            'Connection',
            'Departments',
            'Students Count',
        ];
    }

    public function map($college): array
    {
        if ($college->college_type == 2) {
            $collegeType = 'Degree, Diploma';
        } else {
            $collegeType = College::TYPES[$college->college_type] ?? 'N/A';
        }

        return [
            $college->college_name,
            $college->state->name ?? '-',
            $college->district->name ?? '-',
            $college->college_display_name,

            $collegeType,

            $college->offer_training
                ? 'Yes'
                : 'No',

            $college->training_in ?? '',

            $college->training_in_year ?? '',

            $college->connected_to ?? '',

            $college->reference_by ?? '',

            $college->contact_person ?? '',

            $college->is_important
                ? 'Yes'
                : 'No',

            $college->ownership_type
                ? 'Government'
                : 'Private',

            $college->connection_type
                ? 'Old Connection'
                : 'New Connection',

            !empty($college->departments)
                ? implode(', ', $college->departments)
                : '',

            $college->students_count ?? 0,
        ];
    }
}