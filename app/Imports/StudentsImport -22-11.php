<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Student([
            'student_name' => $row['student_name'] ?? null,
            'f_name'       => $row['f_name'] ?? null,
            'gender'       => $row['gender'] ?? null,
            'session'      => $row['session'] ?? null,
            'college_name' => $row['college'] ?? null,
            'contact'      => $row['contact'] ?? null,
            'email_id'     => $row['email'] ?? null,
            'status'       => $row['status'] ?? null,
            'technology'   => $row['technology'] ?? null,
            'total_fees'   => $row['total_fees'] ?? null,
            'reg_fees'     => $row['reg_fees'] ?? null,
            'pending_fees' => $row['pending_fees'] ?? null,
            'department'   => $row['department'] ?? null,
            'join_date'    => $row['join_date'] ?? null,
            'duration'     => $row['duration'] ?? null,
            'start_date'   => $row['start_date'] ?? null,
            'end_date'     => $row['end_date'] ?? null,
        ]);
    }
}
