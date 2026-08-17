<?php

namespace App\Imports;

use App\Models\HardData;
use App\Models\StudentSession;
use App\Services\CollegeResolver;
// use App\Rules\NotBlockedNumber;
use App\Models\BlockedNumber;

use Illuminate\Support\Facades\Session;

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

    // Counters
    public $totalRows    = 0;
    public $insertedRows = 0;
    public $skippedRows  = 0;

    // Warnings
    // public $duplicateContacts = [];
    // Warnings
    public $duplicateContacts = [];
    public $blockedNumbers = [];
    public $collegeNotFound = [];

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
            // '*.college_name' => function ($attribute, $value, $fail) {
            //     if (!$value) {
            //         $fail('College name is required.');
            //         return;
            //     }

            //     if (count(explode(',', $value)) < 3) {
            //         $fail('Use format: College Name, District, State');
            //     }
            // },

            '*.student_name'   => 'required|string|max:255',
            '*.student_email'  => 'nullable|email|max:255',

            // '*.student_mobile' => [
            //     'required',
            //     'digits:10',
            //     new NotBlockedNumber
            // ],

            '*.student_mobile' => [
                'required',
                'digits:10'
            ],

           // REMOVE strict validation
            '*.gender'       => 'nullable|string',
            '*.course_type'  => 'nullable|string',
            '*.class'        => 'nullable|string',
            '*.semester'     => 'nullable',
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

        // $sessionId = session('admin_session_id');
        $sessionId = session(
            'admin_header_session_id',
            session('admin_session_id')
        );

        /* -------- COLLEGE RESOLVE -------- */
        // try {
        //     $college = app(CollegeResolver::class)->resolve($row['college_name']);
        //     $collegeId = $college->id;
        // } catch (\Exception $e) {
        //     $this->skippedRows++;
        //     $this->duplicateContacts[] =
        //         "Invalid college: {$row['college_name']}";
        //     return null;
        // }

        $collegeId = null;
        $collegeName = $row['college_name'] ?? null;

        if (!empty($row['college_name'])) {

            try {

                $college = app(CollegeResolver::class)
                    ->resolve($row['college_name']);

                $collegeId = $college->id;

                // Save standardized college name
                $collegeName = $college->name;

            } catch (\Exception $e) {

                // College not found
                $collegeId = null;

                // Save original excel text
                $collegeName = $row['college_name'];

                // Save only once
                if (!in_array($collegeName, $this->collegeNotFound)) {
                    $this->collegeNotFound[] = $collegeName;
                }

            }

        }

        /* -------- BLOCKED NUMBER CHECK -------- */

        if (
            !empty($row['student_mobile']) &&
            BlockedNumber::where('number', $row['student_mobile'])->exists()
        ) {

            $this->skippedRows++;

            // $this->duplicateContacts[] =
            //     "Blocked number skipped: {$row['student_mobile']}";


            if (!in_array($row['student_mobile'], $this->blockedNumbers)) {
                $this->blockedNumbers[] = $row['student_mobile'];
            }

            return null;
        }
        /* -------- DUPLICATE CHECK -------- */
        if (
            !empty($row['student_mobile']) &&
            HardData::where('student_mobile', $row['student_mobile'])
                ->where('session_id', $sessionId)
                ->exists()
        ) {
            $this->skippedRows++;
            // $this->duplicateContacts[] =
            //     "Duplicate skipped: {$row['student_mobile']}";

            if (!in_array($row['student_mobile'], $this->duplicateContacts)) {
                $this->duplicateContacts[] = $row['student_mobile'];
            }
            return null;
        }

         /* -------- CLEAN OPTIONAL VALUES -------- */

        $gender = strtolower(trim($row['gender'] ?? ''));

        if (!in_array($gender, ['male', 'female'])) {
            $gender = null;
        }

        $courseType = trim($row['course_type'] ?? '');

        if (!in_array($courseType, ['Degree', 'Diploma'])) {
            $courseType = null;
        }

        $class = trim($row['class'] ?? '');

        if (!in_array($class, [
            'BCA',
            'MCA',
            'BTech',
            'BSc',
            'BSc IT',
            'BSc CS',
            'Polytechnic'
        ])) {
            $class = null;
        }

        $semester = $row['semester'] ?? null;

        if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
            $semester = null;
        }


        /* -------- INSERT -------- */
        $this->insertedRows++;
        // $collegeId = null;
        return new HardData([
            'session_id'     => $sessionId,
            'college_id'     => $collegeId,
            'college_name'   => $collegeName,
            'student_name'   => $row['student_name'],
            'student_email'  => $row['student_email'],
            'student_mobile' => $row['student_mobile'],
            'gender'         => $gender,
            'course_type'    => $courseType,
            'class'          => $class,
            'semester'       => $semester,
            'source'         => 'manual',
        ]);
    }

    public function onError(Throwable $e)
    {
        dd($e->getMessage());
        // Hide system errors
    }
}