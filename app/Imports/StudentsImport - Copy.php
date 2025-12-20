<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\College;
use App\Models\Course;
use App\Models\Batch;
use App\Models\StudentSession;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        $duplicateEMail = [];
        // Skip duplicate emails
        if (Student::where('email_id', $row['email_id'])->exists()) {
            $this->duplicateEMail[] = "Duplicate email skipped: {$row['email_id']}";
            return null;
        }
        $duplicateContacts = [];

        // Skip duplicate contact numbers
        // if (!empty($row['contact']) && Student::where('contact', $row['contact'])->exists()) {
        //     return null;
        // }

        // Duplicate contact check
        if (!empty($row['contact']) && Student::where('contact', $row['contact'])->exists()) {

            // Add message to duplicate list
            $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";

            // Skip row (do not insert)
            return null;
        }


        /** -----------------------------
         * MAP TEXT TO IDS (FK MAPPINGS)
         * ----------------------------- */

        // Technology (Course)
        $technologyId = null;
        if (!empty($row['technology'])) {
            $technologyId = Course::where('course_name', $row['technology'])->value('id');
        }

        // Batch
        $batchId = null;
        if (!empty($row['batch_assign'])) {
            $batchId = Batch::where('batch_name', $row['batch_assign'])->value('id');
        }

        // College
        // $collegeId = null;
        // if (!empty($row['college_name'])) {
        //     $collegeId = College::where('college_name', $row['college_name'])->value('id');
        // }

        // ---- COLLEGE LOOKUP (Flexible Matching) ----

        $collegeId = null;

        if (!empty($row['college_name'])) {

            // Normalize Excel value
            $excelCollege = strtolower(trim($row['college_name']));

            // Remove commas and extra spaces
            $excelCollege = str_replace(',', '', $excelCollege);
            $excelCollege = preg_replace('/\s+/', ' ', $excelCollege);

            // Flexible matching in DB
            $collegeId = \App\Models\College::whereRaw("
                LOWER(REPLACE(college_name, ',', '')) LIKE ?
            ", ["%{$excelCollege}%"])
            ->value('id');
        }


        // Session
        $sessionId = null;
        if (!empty($row['session'])) {
            $sessionId = StudentSession::where('session_name', $row['session'])->value('id');
        }

        /** -------------------
         * STATUS NORMALIZATION
         * ------------------- */

        $status = strtolower(trim($row['status'] ?? ''));
        $allowedStatuses = [
            'joined',
            'dropout',
            'certificate_only',
            'shift_patiala',
        ];
        if (!in_array($status, $allowedStatuses)) {
            $status = null; // OR throw error in validation
        }

        return new Student([
            'student_name'   => $row['student_name'] ?? null,
            'f_name'         => $row['f_name'] ?? null,
            'sno'            => $row['sno'] ?? null,
            'email_id'       => $row['email_id'] ?? null,
            'contact'        => $row['contact'] ?? null,
            'gender'         => $row['gender'] ?? null,

            // Store IDs instead of text
            'college_name'   => $collegeId,
            'session'        => $sessionId,
            'technology'     => $technologyId,
            'batch_assign'   => $batchId,
            'status'         => $status,

            'total_fees'     => $row['total_fees'] ?? null,
            'reg_fees'       => $row['reg_fees'] ?? null,
            'pending_fees'   => $row['pending_fees'] ?? null,
            'next_due_date'  => !empty($row['next_due_date']) ? Carbon::parse($row['next_due_date'])->format('Y-m-d') : null,
            'join_date'      => !empty($row['join_date']) ? Carbon::parse($row['join_date'])->format('Y-m-d') : null,
            'duration'       => $row['duration'] ?? null,
            'reg_due_amount' => $row['reg_due_amount'] ?? null,
            'start_date'     => !empty($row['start_date']) ? Carbon::parse($row['start_date'])->format('Y-m-d') : null,
            'end_date'       => !empty($row['end_date']) ? Carbon::parse($row['end_date'])->format('Y-m-d') : null,
        ]);
    }

    /** -------------------------------------
     * VALIDATION RULES FOR MAPPED FIELDS
     * ------------------------------------- */

    public function rules(): array
    {
        return [
            '*.student_name' => 'required',
            '*.contact'      => 'required',
            '*.email_id'     => 'nullable|email',
            '*.sno'          => 'nullable|max:255',

            // Technology validation
            '*.technology' => function ($attribute, $value, $fail) {
                if ($value && !Course::where('course_name', $value)->exists()) {
                    $fail("Invalid technology: '{$value}'.");
                }
            },

            // Batch validation
            '*.batch_assign' => function ($attribute, $value, $fail) {
                if ($value && !Batch::where('batch_name', $value)->exists()) {
                    $fail("Invalid batch: '{$value}'.");
                }
            },

            // // College validation
            // '*.college_name' => function ($attribute, $value, $fail) {
            //     if ($value && !College::where('college_name', $value)->exists()) {
            //         $fail("Invalid college: '{$value}'.");
            //     }
            // },

            '*.college_name' => function ($attribute, $value, $fail) {

                if (!$value) return;

                // Normalize Excel value
                $excelCollege = strtolower(trim($value));
                $excelCollege = str_replace(',', '', $excelCollege);
                $excelCollege = preg_replace('/\s+/', ' ', $excelCollege);

                // Try flexible match in DB
                $exists = \App\Models\College::whereRaw("
                    LOWER(REPLACE(college_name, ',', '')) LIKE ?
                ", ["%{$excelCollege}%"])->exists();

                if (!$exists) {
                    $fail("Invalid college name: '{$value}'");
                }
            },


            // Session validation
            '*.session' => function ($attribute, $value, $fail) {
                if ($value && !StudentSession::where('session_name', $value)->exists()) {
                    $fail("Invalid session: '{$value}'.");
                }
            },

            // Status validation
            '*.status' => function ($attribute, $value, $fail) {
                $allowed = ['joined','dropout','certificate_only','shift_patiala'];
                if ($value && !in_array(strtolower(trim($value)), $allowed)) {
                    $fail("Invalid status: '{$value}'. Allowed: ".implode(', ', $allowed));
                }
            },

            '*.duration' => function ($attribute, $value, $fail) {

            if (!$value) return;

            $allowed = [
                '20','13','29','44','59',
                '89','119','179','269','364'
            ];

            if (!in_array(trim($value), $allowed)) {
                $fail("Invalid duration: '{$value}'. Allowed: ".implode(', ', $allowed));
            }
        },

        ];
    }
}
