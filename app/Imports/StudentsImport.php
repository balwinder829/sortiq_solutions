<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use App\Models\StudentSession;
use App\Models\BlockedNumber;
use App\Services\CollegeResolver;
use DateTimeInterface;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;


class TextValueBinder extends DefaultValueBinder
{
    public function bindValue(Cell $cell, $value)
    {
        // Force everything as text
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        return true;
    }
}

class StudentsImport extends TextValueBinder implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError,
    WithCustomValueBinder
{
    use SkipsFailures;

    // 🔢 Counters
    public $totalRows    = 0;
    public $insertedRows = 0;
    public $skippedRows  = 0;

    // Warnings
    public $duplicateContacts = [];

    /* ================= SKIP EMPTY ROWS ================= */
    public function isEmptyRow(array $row): bool
    {
        $filtered = collect($row)->filter(function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        return $filtered->isEmpty();
    }

    /* ================= VALIDATION RULES ================= */
    public function rules(): array
    {
        return [

            '*.student_name' => 'required|string',
            '*.f_name'       => 'required|string',

            '*.contact' => [
                'required',
                'regex:/^[0-9]+$/',
                'digits:10'
            ],

            // '*.email_id' => 'nullable|email',

            // Fees required
            // '*.session_id' => 'required|numeric',
            '*.total_fees' => 'required|numeric',
            '*.reg_fees'   => 'required|numeric',
            '*.paid_fees'  => 'nullable|numeric',
            // '*.duration'  => 'required|numeric',
            '*.college_name' => function ($attribute, $value, $fail) {
                if (!$value) {
                    $fail('College name is required.');
                    return;
                }

                if(count(explode(',', $value)) < 3){
                    $fail('Invalid college name format. Use: College Name, District, State.');
                }
            },

            // Session must exist
            // '*.session' => function ($attribute, $value, $fail) {
            //     if (!$value) {
            //         $fail('Session name is required.');
            //         return;
            //     }

            //     if (!StudentSession::where('session_name', trim($value))->exists()) {
            //         $fail("Enter correct session name. '{$value}' does not exist.");
            //     }
            // },

            '*.session_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (!StudentSession::where('id', $value)->exists()) {
                        $fail("Session ID {$value} does not exist.");
                    }
                },
            ],

 

            // '*.start_date' => function ($attribute, $value, $fail) {
            //     if (!empty($value) && !is_numeric($value)) {
            //         try {
            //             Carbon::createFromFormat('d/m/Y', $value);
            //         } catch (\Exception $e) {
            //             $fail('Date must be in DD/MM/YYYY format.');
            //         }
            //     }
            // },
            // '*.register_date' => function ($attribute, $value, $fail) {
            //     if (!empty($value) && !is_numeric($value)) {
            //         try {
            //             Carbon::createFromFormat('d/m/Y', $value);
            //         } catch (\Exception $e) {
            //             $fail('Date must be in DD/MM/YYYY format.');
            //         }
            //     }
            // },

            // '*.pending_fee_due_date' => function ($attribute, $value, $fail) {
            //     if (!empty($value) && !is_numeric($value)) {
            //         try {
            //             Carbon::createFromFormat('d/m/Y', $value);
            //         } catch (\Exception $e) {
            //             $fail('Date must be in DD/MM/YYYY format.');
            //         }
            //     }
            // },

//             '*.start_date' => function ($attribute, $value, $fail) {
//     if (!empty($value)) {
//         try {
//             Carbon::createFromFormat('d/m/Y', trim($value));
//         } catch (\Exception $e) {
//             $fail('Date must be in DD/MM/YYYY format.');
//         }
//     }
// },

// '*.register_date' => function ($attribute, $value, $fail) {
//     if (!empty($value)) {
//         try {
//             Carbon::createFromFormat('d/m/Y', trim($value));
//         } catch (\Exception $e) {
//             $fail('Date must be in DD/MM/YYYY format.');
//         }
//     }
// },

// '*.pending_fee_due_date' => function ($attribute, $value, $fail) {
//     if (!empty($value)) {
//         try {
//             Carbon::createFromFormat('d/m/Y', trim($value));
//         } catch (\Exception $e) {
//             $fail('Date must be in DD/MM/YYYY format.');
//         }
//     }
// },

    '*.start_date' => function ($attribute, $value, $fail) {
    if (empty($value)) return;

    // Excel numeric date allowed
    if (is_numeric($value)) return;

    try {
        Carbon::createFromFormat('d/m/Y', trim($value), null, true);
    } catch (\Exception $e) {
        $fail('Date must be in DD/MM/YYYY format.');
    }
},

'*.register_date' => function ($attribute, $value, $fail) {
    if (empty($value)) return;

    if (is_numeric($value)) return;

    try {
        Carbon::createFromFormat('d/m/Y', trim($value), null, true);
    } catch (\Exception $e) {
        $fail('Date must be in DD/MM/YYYY format.');
    }
},

'*.pending_fee_due_date' => function ($attribute, $value, $fail) {
    if (empty($value)) return;

    if (is_numeric($value)) return;

    try {
        Carbon::createFromFormat('d/m/Y', trim($value), null, true);
    } catch (\Exception $e) {
        $fail('Date must be in DD/MM/YYYY format.');
    }
},




 
        ];
    }

    /* ================= FRIENDLY MESSAGES ================= */
    public function customValidationMessages()
    {
        return [
            '*.student_name.required' => 'Student name is required.',
            '*.f_name.required'       => 'Father name is required.',
            '*.contact.required'      => 'Contact number is required.',
            '*.contact.regex'         => 'Contact number must contain only digits.',
            '*.total_fees.required'   => 'Total fees is required.',
            '*.reg_fees.required'     => 'Registration fees is required.',
        ];
    }

    /* ================= DATE CHECK ================= */

    private function parseDate($value): ?Carbon
{
    if ($value === null || trim((string)$value) === '') {
        return null;
    }

    // Excel serial number (46143 etc.)
    if (is_numeric($value)) {
        return Carbon::instance(
            ExcelDate::excelToDateTimeObject($value)
        );
    }

    $value = trim($value);

    // STRICT DD/MM/YYYY (no guessing)
    return Carbon::createFromFormat(
        'd/m/Y',
        $value,
        null,
        true
    );
}
 private function parseDaqwte($value): ?Carbon
{
    if ($value === null || trim($value) === '') {
        return null;
    }

    $value = trim((string) $value);

    // Reject Excel serial numbers (even as string)
    if (ctype_digit($value)) {
        throw new \Exception(
            'Invalid date. Use DD/MM/YYYY format (text).'
        );
    }

    try {
        return Carbon::createFromFormat('d/m/Y', $value);
    } catch (\Exception $e) {
        throw new \Exception(
            "Invalid date '{$value}'. Use DD/MM/YYYY."
        );
    }
}

    private function parse12Date($value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // ❌ Reject Excel numeric dates (safety)
        if (is_numeric($value)) {
            throw new \Exception(
                'Invalid date format. Please enter date as DD/MM/YYYY (Text), not Excel date.'
            );
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            throw new \Exception(
                "Invalid date '{$value}'. Use DD/MM/YYYY format."
            );
        }
    }

    private function parseDateq($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel date serial (PRIMARY case)
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            );
        }

        // Optional fallback (CSV / manual input)
        try {
            return Carbon::createFromFormat('d/m/Y', trim($value));
        } catch (\Exception $e) {
            return null;
        }
    }


    /* ================= MAIN INSERT ================= */
    public function model(array $row)
    {
        $this->totalRows++;

        // Skip empty row
        $nonEmpty = collect($row)->filter(fn($v) => !is_null($v) && trim($v) !== '');
        if ($nonEmpty->isEmpty()) {
            $this->skippedRows++;
            return null;
        }

        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

        /* -------- BLOCKED NUMBER -------- */
        if (!empty($row['contact']) && BlockedNumber::where('number', $row['contact'])->exists()) {
            $this->skippedRows++;
            $this->duplicateContacts[] = "Blocked contact skipped: {$row['contact']}";
            return null;
        }


        /* -------- SESSION -------- */
        // $sessionId = StudentSession::where('session_name', trim($row['session']))->value('id');

        $session = StudentSession::find($row['session_id']);
        $sessionId = $session->id;

        /* -------- DUPLICATE CONTACT -------- */
        // if (!empty($row['contact']) && Student::where('contact', $row['contact'])->where('session', $sessionId)->exists()) {
        //     $this->skippedRows++;
        //     $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";
        //     return null;
        // }

        if (
            !empty($row['contact']) &&
            Student::where('student_name', trim(strtolower($row['student_name'])))
                ->where('contact', $row['contact'])
                ->where('session', $sessionId)
                ->exists()
        ) {
            $this->skippedRows++;
            $this->duplicateContacts[] =
                "Duplicate skipped: {$row['student_name']} | {$row['contact']} | Session {$sessionId}";
            return null;
        }


        /* -------- COURSE -------- */
        // $courseName = (!empty($row['technology']) && $row['technology'] !== '-')
        //     ? $row['technology']
        //     : 'Not Selected';

        // $course = Course::firstOrCreate(['course_name' => $courseName]);

        $defaultCourse = Course::where('course_name', 'Not Selected')->first();

        $course = $defaultCourse;

        if (!empty($row['technology']) && $row['technology'] !== '-') {
            $found = Course::where('course_name', trim($row['technology']))->first();

            if ($found) {
                $course = $found;
            }
        }


        /* -------- COLLEGE -------- */
        $collegeId = null;
        if (!empty($row['college_name'])) {
            $college = app(CollegeResolver::class)->resolve($row['college_name']);
            $collegeId = $college->id;
        }

        
        /* -------- FEES -------- */
        $totalFees = (float) $row['total_fees'];
        $regFees   = (float) $row['reg_fees'];
        $paidFees  = is_numeric($row['paid_fees'] ?? null) ? (float)$row['paid_fees'] : 0;
        $pendingFees = max($totalFees - $regFees - $paidFees, 0);

        /* -------- START DATE (FIXED, NO TERNARY) -------- */
        $startDate = null;

        $startDate       = $this->parseDate($row['start_date'] ?? null);
        $registerDate    = $this->parseDate($row['register_date'] ?? null);
        $next_due_date   = $this->parseDate($row['pending_fee_due_date'] ?? null);
        // if (!empty($row['start_date'])) {

        //     if (is_numeric($row['start_date'])) {
        //         $startDate = Carbon::instance(
        //             ExcelDate::excelToDateTimeObject($row['start_date'])
        //         );
        //     } else {
        //         try {
        //             $startDate = Carbon::createFromFormat('d/m/Y', $row['start_date']);
        //         } catch (\Exception $e) {
        //             $startDate = Carbon::parse($row['start_date']);
        //         }
        //     }
        // }

        // $endDate = null;

        // if ($startDate && $session && $session->start_date && $session->end_date) {

        //     // Days student joined late
        //     $missedDays = max(
        //         $startDate->diffInDays($session->start_date, false),
        //         0
        //     );

        //     // Student end date = session end + missed days
        //     $endDate = $session->end_date->copy()->addDays($missedDays);

        //     // Skip Sunday
        //     if ($endDate->isSunday()) {
        //         $endDate->addDay();
        //     }
        // }
        $endDate = null;

        if ($startDate && $session && $session->start_date && $session->end_date) {

            // Student joined late by these many days
            $missedDays = max(
                $session->start_date->diffInDays($startDate, false),
                0
            );

            // Extend end date by missed days
            $endDate = $session->end_date->copy()->addDays($missedDays);

            // If end date falls on Sunday, move to Monday
            if ($endDate->isSunday()) {
                $endDate->addDay();
            }
        }



        /* -------- REGISTER DATE (FIXED, NO TERNARY) -------- */
        // $registerDate = null;
        // if (!empty($row['register_date'])) {

        //     if (is_numeric($row['register_date'])) {
        //         $registerDate = Carbon::instance(
        //             ExcelDate::excelToDateTimeObject($row['register_date'])
        //         );
        //     } else {
        //         try {
        //             $registerDate = Carbon::createFromFormat('d/m/Y', $row['register_date']);
        //         } catch (\Exception $e) {
        //             $registerDate = Carbon::parse($row['register_date']);
        //         }
        //     }
        // }

        $allowedDays = [179, 119, 59, 29, 41, 27, 20, 269,364];
        $durationDays = 179;
        if (!empty($row['duration']) && is_numeric($row['duration'])) {
            $duration = (int) $row['duration'];

            if (in_array($duration, $allowedDays, true)) {
                $durationDays = $duration;
            }
        }

        // $endDate = ($startDate && $durationDays)
        //     ? $startDate->copy()->addDays($durationDays)
        //     : null;

        // if ($endDate && $endDate->isSunday()) {
        //     $endDate->addDay();
        // }

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

        // New column added here

        // $next_due_date = null;
        // if (!empty($row['pending_fee_due_date'])) {

        //     if (is_numeric($row['pending_fee_due_date'])) {
        //         $next_due_date = Carbon::instance(
        //             ExcelDate::excelToDateTimeObject($row['pending_fee_due_date'])
        //         );
        //     } else {
        //         try {
        //             $next_due_date = Carbon::createFromFormat('d/m/Y', $row['pending_fee_due_date']);
        //         } catch (\Exception $e) {
        //             $next_due_date = Carbon::parse($row['pending_fee_due_date']);
        //         }
        //     }
        // }

        $allowedRef = array(
            '1' => 'website',
            '2' => 'college',
            '3' => 'social media',
            '4' => 'student reference',
            '5' => 'personal reference',
            '6' => 'Ads',
        );

        $reference = 2;

        if (!empty($row['reference'])) {
            $input = strtolower(trim($row['reference']));

            // normalize allowed values to lowercase
            $normalized = array_map('strtolower', $allowedRef);

            $key = array_search($input, $normalized, true);

            if ($key !== false) {
                $reference = $key; // matched key
            }
        }

        $part_time_offer = 0;

        if (!empty($row['reference'])) {
            $value = strtolower(trim($row['reference']));

            if (in_array($value, ['yes', 'y', '1', 'true'], true)) {
                $part_time_offer = 1;
            }
        }

        $part_time_offer = 0;

        if (!empty($row['part_time_job'])) {
            $value = strtolower(trim($row['part_time_job']));

            if (in_array($value, ['yes', 'y', '1', 'true'], true)) {
                $part_time_offer = 1;
            }
        }

        $placement_offer = 0;

        if (
            (!empty($row['placement_offer']) &&
                in_array(strtolower(trim($row['placement_offer'])), ['yes', 'y', '1', 'true'], true)
            )
            || $totalFees > 15000
        ) {
            $placement_offer = 1;
        }


        $pg_offer = 0;

        if (!empty($row['pg_offer'])) {
            $value = strtolower(trim($row['pg_offer']));

            if (in_array($value, ['yes', 'y', '1', 'true'], true)) {
                $pg_offer = 1;
            }
        }

        /* -------- SERIAL -------- */
        $lastSno = Student::orderBy('id', 'desc')->value('sno');
        $newSno  = is_numeric($lastSno) ? ((int)$lastSno + 1) : 1;

        $this->insertedRows++;

        return new Student([
            'student_name' => strtolower(trim($row['student_name'])),
            'f_name'       => strtolower(trim($row['f_name'])),
            'sno'          => $newSno,
            'email_id'     => $row['email_id'] ?? null,
            'contact'      => $row['contact'],
            'gender'         => $row['gender'] ?? 'Male',

            'college_name' => $collegeId,
            'session'      => $sessionId,
            'technology'   => $course->id,

            'total_fees'   => $totalFees,
            'reg_fees'     => $regFees,
            'paid_fees'    => $paidFees,
            'pending_fees' => $pendingFees,

            'status'         => $status,
            'duration'       => $durationDays,

            'join_date'    => $registerDate?->format('Y-m-d'),
            'start_date'   => $startDate?->format('Y-m-d'),
            'end_date'   => $endDate?->format('Y-m-d'),
            'next_due_date'   => $next_due_date?->format('Y-m-d'),
            'reference' => $reference,
            'part_time_offer' => $part_time_offer,
            'placement_offer' => $placement_offer,
            'pg_offer' => $pg_offer,
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
