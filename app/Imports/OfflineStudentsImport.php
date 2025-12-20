<?php

namespace App\Imports;

use App\Models\OfflineTestStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

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

    public function model(array $row)
    {
        $email  = $row['student_email'] ?? null;
        $mobile = $row['student_mobile'] ?? null;

        // ✅ DUPLICATE CHECK
        $exists = OfflineTestStudent::where('test_id', $this->testId)
            ->when($email, fn ($q) => $q->where('student_email', $email))
            ->when(!$email && $mobile, fn ($q) => $q->where('student_mobile', $mobile))
            ->exists();

        if ($exists) {
            // ❌ Skip duplicate row
            return null;
        }

        return new OfflineTestStudent([
            'test_id'        => $this->testId,
            'student_name'   => $row['student_name'],
            'student_email'  => $email,
            'student_mobile' => $mobile,
            'gender'         => $this->normalizeGender($row['gender'] ?? null),
            'score'          => $row['score'],
        ]);
    }

    public function rules(): array
    {
        return [
            'student_name'   => 'required|string',
            'student_email'  => 'nullable|email',
            'student_mobile' => 'nullable',
            'gender'         => 'nullable|string',
            'score'          => 'required|numeric',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'student_name.required' => 'Student name is missing',
            'score.required'        => 'Score is missing',
            'score.numeric'         => 'Score must be numeric',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }

    protected function normalizeGender($gender): ?string
    {
        if (!$gender) return null;

        $g = strtolower(trim($gender));

        return match ($g) {
            'male', 'm' => 'male',
            'female', 'f' => 'female',
            default => null,
        };
    }
}
