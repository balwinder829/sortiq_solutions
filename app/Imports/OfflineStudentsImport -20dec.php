<?php

namespace App\Imports;

use App\Models\OfflineTestStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;

class OfflineStudentsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    protected $testId;

    public $failures = [];

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    /**
     * Create model only if row is valid
     */
    public function model(array $row)
    {
        return new OfflineTestStudent([
            'test_id'         => $this->testId,
            'student_name'    => $row['student_name'],
            'student_email'   => $row['student_email'] ?? null,
            'student_mobile'  => $row['student_mobile'] ?? null,
            'score'           => $row['score'],
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'student_name'   => 'required|string',
            'student_email'  => 'nullable|email',
            'student_mobile' => 'nullable',
            'score'          => 'required|numeric',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'student_name.required' => 'Student name is missing',
            'score.required'        => 'Score is missing',
            'score.numeric'         => 'Score must be numeric',
        ];
    }

    /**
     * Capture failures (rows will be skipped automatically)
     */
    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }
}
