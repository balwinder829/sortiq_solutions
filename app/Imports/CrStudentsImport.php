<?php

namespace App\Imports;

use App\Models\CrStudent;
use App\Models\Course;
use App\Models\StudentSession;
use App\Models\BlockedNumber;
use App\Services\CollegeResolver;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Throwable;

class CrTextValueBinder extends DefaultValueBinder
{
    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        return true;
    }
}

class CrStudentsImport extends CrTextValueBinder implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError,
    WithCustomValueBinder
{
    use SkipsFailures;

    public $totalRows = 0;
    public $insertedRows = 0;
    public $skippedRows = 0;

    public $duplicateContacts = [];

    /*
    |--------------------------------------------------------------------------
    | Skip completely empty rows
    |--------------------------------------------------------------------------
    */

    public function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(function ($value) {
            return !is_null($value) && trim((string) $value) !== '';
        })->isEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    | Required:
    | student_name, f_name, gender, college_name
    |
    | Session is automatically taken from the selected admin session.
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [
            '*.student_name' => 'required|string|max:255',
            '*.f_name'       => 'required|string|max:255',
            '*.gender'       => 'required|string|max:30',

            // '*.college_name' => [
            //     'required',
            //     'string',
            //     'max:512',
            //     function ($attribute, $value, $fail) {
            //         if (count(explode(',', (string) $value)) < 3) {
            //             $fail('College name format must be: College Name, District, State.');
            //         }
            //     },
            // ],

            // Optional fields
            '*.contact'      => 'nullable|regex:/^[0-9]+$/|digits:10',
            '*.email_id'     => 'nullable|email|max:255',
            '*.total_fees'   => 'nullable|numeric|min:0',
            '*.reg_fees'     => 'nullable|numeric|min:0',
            '*.paid_fees'    => 'nullable|numeric|min:0',
            // '*.duration'     => 'nullable|numeric',
            '*.technology'   => 'nullable|string',
            // '*.status'       => 'nullable|string',

            '*.start_date' => function ($attribute, $value, $fail) {
                $this->validateOptionalDate($value, $fail);
            },

            '*.register_date' => function ($attribute, $value, $fail) {
                $this->validateOptionalDate($value, $fail);
            },

            '*.pending_fee_due_date' => function ($attribute, $value, $fail) {
                $this->validateOptionalDate($value, $fail);
            },
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.student_name.required' => 'Student name is required.',
            '*.f_name.required'       => 'Father name is required.',
            '*.gender.required'       => 'Gender is required.',
            // '*.college_name.required' => 'College name is required.',
            '*.contact.digits'        => 'Contact number must contain exactly 10 digits.',
            '*.contact.regex'         => 'Contact number must contain only digits.',
        ];
    }

    private function validateOptionalDate($value, $fail): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        // Excel date serial values are allowed.
        if (is_numeric($value)) {
            return;
        }

        try {
            Carbon::createFromFormat('d/m/Y', trim((string) $value), null, true);
        } catch (\Exception $e) {
            $fail('Date must be in DD/MM/YYYY format.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Date parser
    |--------------------------------------------------------------------------
    */

    private function parseDate($value): ?Carbon
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            );
        }

        return Carbon::createFromFormat(
            'd/m/Y',
            trim((string) $value),
            null,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Main Import
    |--------------------------------------------------------------------------
    */

    public function model(array $row)
    {
        $this->totalRows++;

        $row = array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $row
        );

        // Skip empty rows.
        if ($this->isEmptyRow($row)) {
            $this->skippedRows++;
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENT ADMIN SESSION
        |--------------------------------------------------------------------------
        */

        $sessionId = session(
            'admin_header_session_id',
            session('admin_session_id')
        );

        if (!$sessionId) {
            $this->skippedRows++;
            return null;
        }

        $session = StudentSession::find($sessionId);

        if (!$session) {
            $this->skippedRows++;
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | BLOCKED CONTACT
        |--------------------------------------------------------------------------
        */

        $mobile = trim((string) ($row['contact'] ?? ''));

        if (
            $mobile !== '' &&
            BlockedNumber::where('number', $mobile)->exists()
        ) {
            $this->skippedRows++;

            $this->duplicateContacts[] =
                "Blocked contact skipped: {$mobile}";

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CONTACT
        |--------------------------------------------------------------------------
        */

        if (
            $mobile !== '' &&
            CrStudent::where(
                'student_name',
                strtolower(trim($row['student_name']))
            )
                ->where('contact', $mobile)
                ->where('session', $sessionId)
                ->exists()
        ) {
            $this->skippedRows++;

            $this->duplicateContacts[] =
                "Duplicate skipped: {$row['student_name']} | {$mobile} | Session {$sessionId}";

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | COLLEGE
        |--------------------------------------------------------------------------
        */

        // $college = app(CollegeResolver::class)->resolve(
        //     trim($row['college_name'])
        // );

        // if (!$college) {
        //     $this->skippedRows++;
        //     return null;
        // }

        // $collegeId = $college->id;

        /*
        |--------------------------------------------------------------------------
        | TECHNOLOGY
        |--------------------------------------------------------------------------
        */

        $course = Course::where('course_name', 'Not Selected')->first();

        if (
            !empty($row['technology']) &&
            trim($row['technology']) !== '-'
        ) {
            $foundCourse = Course::where(
                'course_name',
                trim($row['technology'])
            )->first();

            if ($foundCourse) {
                $course = $foundCourse;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FEES - OPTIONAL
        |--------------------------------------------------------------------------
        */

        $totalFees = is_numeric($row['total_fees'] ?? null)
            ? (float) $row['total_fees']
            : 0;

        $regFees = is_numeric($row['reg_fees'] ?? null)
            ? (float) $row['reg_fees']
            : 0;

        $paidFees = is_numeric($row['paid_fees'] ?? null)
            ? (float) $row['paid_fees']
            : 0;

        $pendingFees = max(
            $totalFees - $regFees - $paidFees,
            0
        );

        /*
        |--------------------------------------------------------------------------
        | DATES - OPTIONAL
        |--------------------------------------------------------------------------
        */

        $startDate = $this->parseDate($row['start_date'] ?? null);
        $registerDate = $this->parseDate($row['register_date'] ?? null);
        $nextDueDate = $this->parseDate(
            $row['pending_fee_due_date'] ?? null
        );

        $endDate = null;

        if (
            $startDate &&
            $session->start_date &&
            $session->end_date
        ) {
            $missedDays = max(
                Carbon::parse($session->start_date)
                    ->diffInDays($startDate, false),
                0
            );

            $endDate = Carbon::parse($session->end_date)
                ->addDays($missedDays);

            if ($endDate->isSunday()) {
                $endDate->addDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DURATION - OPTIONAL
        |--------------------------------------------------------------------------
        */

        // $allowedDays = [179, 119, 59, 29, 41, 27, 20, 269, 364];

        // $durationDays = 179;

        // if (
        //     !empty($row['duration']) &&
        //     is_numeric($row['duration'])
        // ) {
        //     $duration = (int) $row['duration'];

        //     if (in_array($duration, $allowedDays, true)) {
        //         $durationDays = $duration;
        //     }
        // }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        // $status = strtolower(trim((string) ($row['status'] ?? '')));

        // $allowedStatuses = [
        //     'joined',
        //     'dropout',
        //     'certificate_only',
        //     'shift_patiala',
        // ];

        // if (!in_array($status, $allowedStatuses, true)) {
        //     $status = 'joined';
        // }

        /*
        |--------------------------------------------------------------------------
        | REFERENCE
        |--------------------------------------------------------------------------
        */

        // $allowedRef = [
        //     '1' => 'website',
        //     '2' => 'college',
        //     '3' => 'social media',
        //     '4' => 'student reference',
        //     '5' => 'personal reference',
        //     '6' => 'Ads',
        // ];

        // $reference = 2;

        // if (!empty($row['reference'])) {
        //     $input = strtolower(trim($row['reference']));
        //     $normalized = array_map('strtolower', $allowedRef);

        //     $key = array_search($input, $normalized, true);

        //     if ($key !== false) {
        //         $reference = $key;
        //     }
        // }

        /*
        |--------------------------------------------------------------------------
        | OFFER FLAGS
        |--------------------------------------------------------------------------
        */

        $yesValues = ['yes', 'y', '1', 'true'];

        // $partTimeOffer = in_array(
        //     strtolower(trim((string) ($row['part_time_job'] ?? ''))),
        //     $yesValues,
        //     true
        // ) ? 1 : 0;

        // $placementOffer = (
        //     in_array(
        //         strtolower(trim((string) ($row['placement_offer'] ?? ''))),
        //         $yesValues,
        //         true
        //     ) || $totalFees > 15000
        // ) ? 1 : 0;

        // $pgOffer = in_array(
        //     strtolower(trim((string) ($row['pg_offer'] ?? ''))),
        //     $yesValues,
        //     true
        // ) ? 1 : 0;

        /*
        |--------------------------------------------------------------------------
        | SERIAL NUMBER
        |--------------------------------------------------------------------------
        */

        $lastSno = CrStudent::orderBy('id', 'desc')->value('sno');

        $newSno = is_numeric($lastSno)
            ? ((int) $lastSno + 1)
            : 1;

        /*
        |--------------------------------------------------------------------------
        | INSERT CR STUDENT
        |--------------------------------------------------------------------------
        */

        $this->insertedRows++;

        $crStudent = new CrStudent([
            'student_name' => strtolower(trim($row['student_name'])),
            'f_name'       => strtolower(trim($row['f_name'])),

            // 'sno'      => $newSno,
            'email_id' => !empty($row['email_id'])
                ? trim($row['email_id'])
                : null,

            'contact' => $mobile !== '' ? $mobile : null,

            'gender' => ucfirst(
                strtolower(trim($row['gender']))
            ),

            // 'college_name' => $collegeId,
            'session'      => $sessionId,
            'technology'   => $course?->id,

            'total_fees'   => $totalFees,
            'reg_fees'     => $regFees,
            'paid_fees'    => $paidFees,
            'pending_fees' => $pendingFees,

            // 'status'   => $status,
            // 'duration' => $durationDays,

            'join_date'     => $registerDate?->format('Y-m-d'),
            'start_date'    => $startDate?->format('Y-m-d'),
            'end_date'      => $endDate?->format('Y-m-d'),
            // 'next_due_date' => $nextDueDate?->format('Y-m-d'),

            // 'reference'      => $reference,
            // 'part_time_offer' => $partTimeOffer,
            // 'placement_offer' => $placementOffer,
            // 'pg_offer'        => $pgOffer,
        ]);

        // Imported CR entry.
        $crStudent->entry_type = 2;
        $crStudent->source_student_id = null;

        return $crStudent;
    }

    public function onError(Throwable $e)
    {
        $this->skippedRows++;
    }
}
