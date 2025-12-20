<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Student::all();
    }

    public function headings(): array
    {
        return [
            'ID', 'Student Name', 'Father Name', 'Gender', 'Session',
            'College', 'Contact', 'Email', 'Status', 'Technology', 
            'Total Fees', 'Reg Fees', 'Pending Fees', 'Department', 
            'Join Date', 'Duration', 'Start Date', 'End Date'
        ];
    }
}
