<?php

namespace App\Imports;

use App\Models\HardData;
use App\Models\BlockedNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class HardDataImport implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError
{
    use SkipsFailures;

    public $totalRows = 0;
    public $insertedRows = 0;
    public $skippedRows = 0;

    public $duplicateContacts = [];
    public $blockedNumbers = [];

    public function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(function ($value) {
            return !is_null($value) && trim((string) $value) !== '';
        })->isEmpty();
    }

    public function rules(): array
    {
        return [
            '*.student_name'   => 'required|string|max:255',
            '*.student_mobile' => ['required', 'digits:10'],
            '*.student_email'  => 'nullable|email|max:255',
            '*.college_name'   => 'required|string|max:512',
            '*.gender'         => 'nullable|string',
            '*.course_type'    => 'nullable|string',
            '*.class'          => 'nullable|string|max:500',
            '*.semester'       => 'nullable',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.student_name.required'   => 'Student name is required.',
            '*.student_mobile.required' => 'Mobile is required.',
            '*.student_mobile.digits'   => 'Mobile must contain exactly 10 digits.',
        ];
    }

    public function model(array $row)
    {
        $this->totalRows++;

        $row = array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $row
        );

        $sessionId = session(
            'admin_header_session_id',
            session('admin_session_id')
        );

        if (!$sessionId) {
            $this->skippedRows++;
            return null;
        }

        // College name is stored as free text; do not look it up in the colleges table.
        $collegeName = trim((string) ($row['college_name'] ?? ''));
        $collegeName = $collegeName !== '' ? $collegeName : null;

        $mobile = trim((string) ($row['student_mobile'] ?? ''));

        if ($mobile !== '' && BlockedNumber::where('number', $mobile)->exists()) {
            $this->skippedRows++;

            if (!in_array($mobile, $this->blockedNumbers, true)) {
                $this->blockedNumbers[] = $mobile;
            }

            return null;
        }

        if (
            $mobile !== '' &&
            HardData::where('student_mobile', $mobile)
                ->where('session_id', $sessionId)
                ->exists()
        ) {
            $this->skippedRows++;

            if (!in_array($mobile, $this->duplicateContacts, true)) {
                $this->duplicateContacts[] = $mobile;
            }

            return null;
        }

        $gender = strtolower(trim((string) ($row['gender'] ?? '')));
        if (!in_array($gender, ['male', 'female'], true)) {
            $gender = null;
        }

        $courseType = trim((string) ($row['course_type'] ?? ''));
        if (!in_array($courseType, ['Degree', 'Diploma'], true)) {
            $courseType = null;
        }

        $class = trim((string) ($row['class'] ?? ''));
        if (!in_array($class, [
            'BCA', 'MCA', 'BTech', 'BSc', 'BSc IT', 'BSc CS',
            'Polytechnic', 'CSE', 'IT', 'Civil',
        ], true)) {
            $class = null;
        }

        $semester = $row['semester'] ?? null;
        if (!is_numeric($semester) || (int) $semester < 1 || (int) $semester > 8) {
            $semester = null;
        } else {
            $semester = (int) $semester;
        }

        $this->insertedRows++;

        return new HardData([
            'session_id'     => $sessionId,
            'college_id'     => null,
            'college_name'   => $collegeName,
            'student_name'   => trim((string) $row['student_name']),
            'student_email'  => !empty($row['student_email']) ? trim((string) $row['student_email']) : null,
            'student_mobile' => $mobile,
            'gender'         => $gender,
            'course_type'    => $courseType,
            'class'          => $class !== '' ? $class : null,
            'semester'       => $semester,
            'source'         => 'manual',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Let the import continue; the controller reports the final import summary.
        $this->skippedRows++;
    }
}
