<?php

namespace App\Exports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;


class CollegesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return College::with(['state', 'district'])->get();
    }

    public function headings(): array
    {
        return [
            // 'ID',
            'College / Place Name',
            'State',
            'District',
            'Display Name',
        ];
    }

    public function map($college): array
    {
        return [
            // $college->id,
            $college->college_name,
            $college->state->name ?? '-',
            $college->district->name ?? '-',
            $college->college_display_name,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 80,   // College / Place Name ✅
            'B' => 25,   // State
            'C' => 25,   // District
            'D' => 80,   // Clean Name
            
        ];
    }
}
