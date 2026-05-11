<?php

namespace App\Imports;

use App\Models\ManualData;
use App\Models\StudentSession;
use App\Services\CollegeResolver;
use App\Rules\NotBlockedNumber;

use Illuminate\Support\Facades\Session;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;

use Throwable;

class ManualDataImport implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError
{
    use SkipsFailures;

    // Counters
    public $totalRows    = 0;
    public $insertedRows = 0;
    public $skippedRows  = 0;

    // Warnings
    public $duplicateContacts = [];

    /* ================= SKIP EMPTY ================= */
    public function isEmptyRow(array $row): bool
    {
        $filtered = collect($row)->filter(function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        return $filtered->isEmpty();
    }

    /* ================= VALIDATION ================= */
    public function rules(): array
    {
        return [
            '*.college_name' => function ($attribute, $value, $fail) {
                if (!$value) {
                    $fail('College name is required.');
                    return;
                }

                if (count(explode(',', $value)) < 3) {
                    $fail('Use format: College Name, District, State');
                }
            },

            '*.student_name'   => 'required|string|max:255',
            '*.student_email'  => 'nullable|email|max:255',

            '*.student_mobile' => [
                'required',
                'digits:10',
                new NotBlockedNumber
            ],

            '*.gender'       => 'nullable|in:male,female',
            '*.course_type'  => 'nullable|in:Degree,Diploma',
            '*.class'        => 'nullable|in:BCA,MCA,BTech,BSc,BSc IT,BSc CS,Polytechnic',
            '*.semester'     => 'nullable|integer|min:1|max:8',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.student_name.required' => 'Student name is required.',
            '*.student_email.required' => 'Email is required.',
            '*.student_mobile.required' => 'Mobile is required.',
        ];
    }

    /* ================= MAIN LOGIC ================= */
    public function model(array $row)
    {
        $this->totalRows++;

        // Clean values
        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

        $sessionId = session('admin_session_id');

        /* -------- COLLEGE RESOLVE -------- */
        try {
            $college = app(CollegeResolver::class)->resolve($row['college_name']);
            $collegeId = $college->id;
        } catch (\Exception $e) {
            $this->skippedRows++;
            $this->duplicateContacts[] =
                "Invalid college: {$row['college_name']}";
            return null;
        }

        /* -------- DUPLICATE CHECK -------- */
        if (
            !empty($row['student_mobile']) &&
            ManualData::where('student_mobile', $row['student_mobile'])
                ->where('session_id', $sessionId)
                ->exists()
        ) {
            $this->skippedRows++;
            $this->duplicateContacts[] =
                "Duplicate skipped: {$row['student_mobile']}";
            return null;
        }

        /* -------- INSERT -------- */
        $this->insertedRows++;

        return new ManualData([
            'session_id'     => $sessionId,
            'college_id'     => $collegeId,
            'student_name'   => $row['student_name'],
            'student_email'  => $row['student_email'],
            'student_mobile' => $row['student_mobile'],
            'gender'         => strtolower($row['gender']),
            'course_type'    => $row['course_type'],
            'class'          => $row['class'],
            'semester'       => $row['semester'],
            'source'         => 'manual',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors
    }
}