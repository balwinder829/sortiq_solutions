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
use App\Models\BlockedNumber;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Str;
use App\Services\CollegeResolver;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);
        dd($row);
        $duplicateEMail = [];
        $duplicateContacts = [];
         /* ================= BLOCKED NUMBER CHECK ================= */
            if (!empty($row['contact'])) {
                $isBlocked = BlockedNumber::where('number', $row['contact'])->exists();

                if ($isBlocked) {     
                    $this->duplicateContacts[] = "Blocked contact skipped: {$row['contact']}";
                    return null;
                }
            }
        // Skip duplicate emails
        if (Student::where('email_id', $row['email_id'])->exists()) {
            $this->duplicateEMail[] = "Duplicate email skipped: {$row['email_id']}";
            return null;
        }
        

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
        // $technologyId = null;
        // if (!empty($row['technology'])) {
        //     $technologyId = Course::where('course_name', $row['technology'])->value('id');
        // }

        $technologyId = null;

        if (empty(trim($row['technology'] ?? ''))) {
            $courseName = 'Not Selected';
        } else {
            $courseName = trim($row['technology']);
        }

        $course = Course::firstOrCreate([
            'course_name' => $courseName
        ]);

        $technologyId = $course->id;

        // Batch
        $batchId = null;
        if (!empty($row['batch_assign'])) {
            $batchId = Batch::where('batch_name', $row['batch_assign'])->value('id');
        }

        $collegeId = null;


        if (!empty($row['college_name'])) {

            $college = app(CollegeResolver::class)
            ->resolve($row['college_name']);

        $collegeId = $college->id;

        }





        // $collegeId = null;

        // if (!empty($row['college_name'])) {

        //     $excelCollege = strtolower(trim($row['college_name']));
        //     $excelCollege = str_replace(',', '', $excelCollege);
        //     $excelCollege = preg_replace('/\s+/', ' ', $excelCollege);

        //     $college = College::whereRaw("
        //         LOWER(REPLACE(college_name, ',', '')) LIKE ?
        //     ", ["%{$excelCollege}%"])->first();

        //     // OPTIONAL: auto-create if not found
        //     if (!$college) {
        //         $college = College::create([
        //             'college_name' => trim($row['college_name'])
        //         ]);
        //     }

        //     $collegeId = $college->id;
        // }

        // $collegeId = null;

// if (!empty($row['college_name'])) {

//     // Normalize Excel value
//     $excelCollege = strtolower(trim($row['college_name']));
//     $excelCollege = str_replace(
//         [',', '.', '(', ')'],
//         '',
//         $excelCollege
//     );
//     $excelCollege = preg_replace('/\s+/', ' ', $excelCollege);

//     $college = College::where(function ($q) use ($excelCollege) {

//         $q->whereRaw("
//             LOWER(
//                 REPLACE(
//                     REPLACE(
//                         REPLACE(
//                             REPLACE(college_display_name, ',', ''),
//                         '.', ''),
//                     '(', ''),
//                 ')', '')
//             ) LIKE ?
//         ", ["%{$excelCollege}%"])

//         ->orWhereRaw("
//             LOWER(
//                 REPLACE(
//                     REPLACE(
//                         REPLACE(
//                             REPLACE(clean_name, ',', ''),
//                         '.', ''),
//                     '(', ''),
//                 ')', '')
//             ) LIKE ?
//         ", ["%{$excelCollege}%"])

//         ->orWhereRaw("
//             LOWER(
//                 REPLACE(
//                     REPLACE(
//                         REPLACE(
//                             REPLACE(college_name, ',', ''),
//                         '.', ''),
//                     '(', ''),
//                 ')', '')
//             ) LIKE ?
//         ", ["%{$excelCollege}%"]);

//     })->first();

//      // 2️⃣ If NOT found → create new college
//     if (!$college) {
//         // echo "-1";
//         $college = College::create([
//             'college_name'         => trim($row['college_name']),
//             'college_display_name' => trim($row['college_name']),
//             'clean_name'           => $excelCollege, // normalized
//             'slug'                 => \Str::slug($excelCollege),
//             'state_id'             => null,
//             'district_id'          => null,
//         ]);
//     }

//     if ($college) {
//         // echo "-2";
//         $collegeId = $college->id;
//     }
// }
// dd($collegeId,$college);
        // use Illuminate\Support\Str;

       // $collegeId = null;

// if (!empty($row['college_name'])) {

//     $rawName = trim($row['college_name']);

//     // ✅ HARD normalization (no helpers, no magic)
//     $cleanName = strtolower($rawName);
//     $cleanName = str_replace([',', '.', '(', ')'], '', $cleanName);
//     $cleanName = preg_replace('/\s+/', ' ', $cleanName);

//     // ✅ ALWAYS lookup by clean_name
//     $college = College::where('clean_name', $cleanName)->first();

//     if (!$college) {
//         $college = College::create([
//             'clean_name'           => $cleanName,
//             'college_name'         => $rawName,
//             'college_display_name' => $rawName,
//             'slug'                 => Str::slug($cleanName),
//             'state_id'             => null,
//             'district_id'          => null,
//         ]);
//     }

//     $collegeId = $college->id;
// }


        // Session
        $sessionId = null;
        if (!empty($row['session'])) {
            $sessionId = StudentSession::where('session_name', $row['session'])->value('id');
        }

        // dd($sessionId);
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
            $status = 'joined'; // OR throw error in validation
        }


        // ================== FEES CALCULATION ==================
        $totalFees = isset($row['total_fees']) && is_numeric($row['total_fees'])
            ? (float) $row['total_fees']
            : null;

        $regFees = isset($row['reg_fees']) && is_numeric($row['reg_fees'])
            ? (float) $row['reg_fees']
            : 0;

        $paid_fees = isset($row['paid_fees']) && is_numeric($row['paid_fees'])
            ? (float) $row['paid_fees']
            : 0;

        // Auto-calculate pending fees
        $pendingFees = null;

        if ($totalFees !== null) {
            $pendingFees = max($totalFees - $regFees, 0); // never negative
        }

        $startDate = null;
        $register_date = null;

        if (!empty($row['start_date'])) {
            if (is_numeric($row['start_date'])) {
                // Excel date number (most reliable)
                $startDate = Carbon::instance(
                    ExcelDate::excelToDateTimeObject($row['start_date'])
                );
            } else {
                // Works for 2025-02-12 and 02/12/2025
                $startDate = Carbon::parse($row['start_date']);
            }
        }
         if (!empty($row['register_date'])) {
            if (is_numeric($row['register_date'])) {
                // Excel date number (most reliable)
                $register_date = Carbon::instance(
                    ExcelDate::excelToDateTimeObject($row['register_date'])
                );
            } else {
                // Works for 2025-02-12 and 02/12/2025
                $register_date = Carbon::parse($row['register_date']);
            }
        }
       

        $allowedDays = [179, 119, 59, 29, 41, 27, 20, 269,364];
        $durationDays = 179;
        if (!empty($row['duration']) && is_numeric($row['duration'])) {
            $duration = (int) $row['duration'];

            if (in_array($duration, $allowedDays, true)) {
                $durationDays = $duration;
            }
        }
        // $durationDays = !empty($row['duration']) && is_numeric($row['duration'])
        //     ? (int) $row['duration']
        //     : 179;

        $endDate = ($startDate && $durationDays)
            ? $startDate->copy()->addDays($durationDays)
            : null;


        $lastSno = Student::orderBy('id', 'desc')->value('sno');

        $newSno = is_numeric($lastSno) ? ((int)$lastSno + 1) : 1;
        //  dd([
        //     'start date' => $row['start_date'],
        //     'joindate' => $row['register_date'],
        //     'register_date' => $register_date,
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        //     'newSno' => $newSno,
        //     'duration date' => $row['duration'],
        // ]);
        // $validate['sno'] = $newSno;

            // dd($durationDays, $row['duration']);
        return new Student([
            'student_name'   => $row['student_name'] ?? null,
            'f_name'         => $row['f_name'] ?? null,
            'sno'            => $newSno ?? null,
            'email_id'       => $row['email_id'] ?? null,
            'contact'        => $row['contact'] ?? null,
            'gender'         => $row['gender'] ?? 'Male',

            // Store IDs instead of text
            'college_name'   => $collegeId,
            'session'        => $sessionId,
            'technology'     => $technologyId,
            'batch_assign'   => $batchId,
            'status'         => $status,

            'total_fees'     => $totalFees ?? null,
            'reg_fees'       => $regFees ?? null,
            'paid_fees'       => $regFees ?? null,
            'pending_fees'   => $pendingFees ?? null,
            'next_due_date'  => !empty($row['next_due_date']) ? Carbon::parse($row['next_due_date'])->format('Y-m-d') : null,
            'join_date'      => $register_date?->format('Y-m-d'),
            'duration'       => $durationDays ?? null,
            'start_date'     => $startDate?->format('Y-m-d'),
            'end_date'       => $endDate?->format('Y-m-d'),

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
            // '*.sno'          => 'nullable|max:255',

            // Technology validation
            // '*.technology' => function ($attribute, $value, $fail) {
            //     if ($value && !Course::where('course_name', $value)->exists()) {
            //         $fail("Invalid technology: '{$value}'.");
            //     }
            // },

            // Batch validation
            // '*.batch_assign' => function ($attribute, $value, $fail) {
            //     if ($value && !Batch::where('batch_name', $value)->exists()) {
            //         $fail("Invalid batch: '{$value}'.");
            //     }
            // },

            // // College validation
            // '*.college_name' => function ($attribute, $value, $fail) {
            //     if ($value && !College::where('college_name', $value)->exists()) {
            //         $fail("Invalid college: '{$value}'.");
            //     }
            // },

            // '*.college_name' => function ($attribute, $value, $fail) {

            //     if (!$value) return;

            //     // Normalize Excel value
            //     $excelCollege = strtolower(trim($value));
            //     $excelCollege = str_replace(',', '', $excelCollege);
            //     $excelCollege = preg_replace('/\s+/', ' ', $excelCollege);

            //     // Try flexible match in DB
            //     $exists = \App\Models\College::whereRaw("
            //         LOWER(REPLACE(college_name, ',', '')) LIKE ?
            //     ", ["%{$excelCollege}%"])->exists();

            //     if (!$exists) {
            //         $fail("Invalid college name: '{$value}'");
            //     }
            // },


            // Session validation
            // '*.session' => function ($attribute, $value, $fail) {
            //     if ($value && !StudentSession::where('session_name', $value)->exists()) {
            //         $fail("Invalid session: '{$value}'.");
            //     }
            // },

            // Status validation
            // '*.status' => function ($attribute, $value, $fail) {
            //     $allowed = ['joined','dropout','certificate_only','shift_patiala'];
            //     if ($value && !in_array(strtolower(trim($value)), $allowed)) {
            //         $fail("Invalid status: '{$value}'. Allowed: ".implode(', ', $allowed));
            //     }
            // },

            '*.status' => function ($attribute, $value, $fail) {
                $allowed = ['joined','dropout','certificate_only','shift_patiala'];

                // If status is empty → default will be applied later
                if (!$value) {
                    return;
                }

                if (!in_array(strtolower(trim($value)), $allowed)) {
                    $fail("Invalid status: '{$value}'. Allowed: ".implode(', ', $allowed));
                }
            },


        //     '*.duration' => function ($attribute, $value, $fail) {

        //     if (!$value) return;

        //     $allowed = [
        //         '20','13','29','44','59',
        //         '89','119','179','269','364'
        //     ];

        //     if (!in_array(trim($value), $allowed)) {
        //         $fail("Invalid duration: '{$value}'. Allowed: ".implode(', ', $allowed));
        //     }
        // },

        ];
    }
}
