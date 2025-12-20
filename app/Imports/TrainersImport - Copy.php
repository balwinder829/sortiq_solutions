<?php

namespace App\Imports;

use App\Models\Trainer;
use App\Models\StudentCourse; // course table
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;

class TrainersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    use Importable;

    public $duplicatePhones = [];

    public function model(array $row)
    {
        // DUPLICATE PHONE CHECK
        if (!empty($row['phone']) && Trainer::where('phone', $row['phone'])->exists()) {
            $this->duplicatePhones[] = "Duplicate phone skipped: {$row['phone']}";
            return null;
        }

        // COURSE LOOKUP
        $courseId = StudentCourse::whereRaw("LOWER(course_name) = ?", strtolower(trim($row['technology'])))
                    ->value('id');

        if (!$courseId) {
            $this->duplicatePhones[] = "Invalid technology: {$row['technology']}";
            return null;
        }

        return new Trainer([
            'trainer_name' => $row['trainer_name'],
            'gender'       => strtolower(trim($row['gender'])),
            'phone'        => $row['phone'],
            'email'        => $row['email'],
            'technology'   => $courseId,
            'department'   => $row['department'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.trainer_name' => 'required',
            '*.gender'       => 'required|in:male,female',
            '*.phone'        => 'required',
            '*.email'        => 'nullable|email',
            '*.technology'   => 'required',
            '*.department'   => 'nullable',
        ];
    }
}
