<?php

namespace App\Exports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SessionCollegesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $sessionId;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function collection()
    {
        return College::query()
            ->with(['state', 'district'])
            ->withCount([
                'students as students_count' => function ($query) {
                    $query->where('session', $this->sessionId);
                }
            ])
            ->having('students_count', '>', 0)
            ->orderByDesc('students_count')
            ->get();
    }

    public function headings(): array
    {
        return [
            'College Name',
            'State',
            'District',
            'Total Students',
        ];
    }

    public function map($college): array
    {
        return [
            $college->college_name,
            $college->state->name ?? '-',
            $college->district->name ?? '-',
            $college->students_count ?? 0,
        ];
    }
}