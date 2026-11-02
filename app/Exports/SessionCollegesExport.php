<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SessionCollegesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected $sessionId;
    protected $search;

    public function __construct($sessionId, $search = '')
    {
        $this->sessionId = $sessionId;
        $this->search = trim($search);
    }

    public function collection()
    {
        return Student::query()

            // Same session
            ->where('session', $this->sessionId)

            // Same deleted student check
            ->whereNull('deleted_at')

            // Same college check
            ->whereNotNull('college_name')

            // Load actual College information
            ->with([
                'collegeData' => function ($query) {
                    $query->withTrashed()
                        ->with(['state', 'district']);
                }
            ])

            // Same search as listing
            ->when($this->search !== '', function ($query) {

                $query->whereHas('collegeData', function ($q) {

                    $q->withTrashed()
                        ->where(
                            'college_name',
                            'like',
                            '%' . $this->search . '%'
                        );

                });

            })

            // Same grouping
            ->select('college_name')

            ->selectRaw(
                'COUNT(*) AS students_count'
            )

            ->groupBy('college_name')

            // Same ordering
            ->orderByDesc('students_count')

            ->get();
    }

    public function headings(): array
    {
        return [
            
            'College Name/Place',
            'State',
            'District',
            'Students',
        ];
    }

    public function map($college): array
    {
        $collegeMaster = $college->collegeData;

        return [
            

            // Actual College Name
            $collegeMaster->college_name ?? '-',

            // State
            $collegeMaster->state->name ?? '-',

            // District
            $collegeMaster->district->name ?? '-',

            // Session students
            $college->students_count,
        ];
    }
}