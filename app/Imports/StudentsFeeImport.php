<?php

namespace App\Imports;

use App\Models\Student;

use App\Models\StudentSession;
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


class StudentsFeeImport implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError
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

            '*.sno' => 'required|numeric',
            '*.student_name' => 'required|string',
            '*.f_name'       => 'required|string',

            '*.contact' => [
                'required',
                'regex:/^[0-9]+$/',
                'digits:10'
            ],

            '*.total_fees' => 'required|numeric',
            '*.reg_fees'   => 'required|numeric',
            '*.paid_fees'  => 'nullable|numeric',
            '*.fees_error' => function ($attr, $value, $fail) {
                $fail('Registration + Paid fees cannot exceed Total fees.');
            },
            // '*.paid_fees' => [
            //     'nullable',
            //     'numeric',
            //     function ($attribute, $value, $fail) {
            //         dd(request());
            //         $rowIndex = explode('.', $attribute)[0];
            //         $row = request()->input($rowIndex);

            //         $total = (int) ($row['total_fees'] ?? 0);
            //         $reg   = (int) ($row['reg_fees'] ?? 0);
            //         $paid  = (int) ($value ?? 0);

            //         if (($reg + $paid) > $total) {
            //             $fail('Registration + Paid fees cannot exceed Total fees.');
            //         }
            //     }
            // ],
            
            '*.session_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (!StudentSession::where('id', $value)->exists()) {
                        $fail("Session ID {$value} does not exist.");
                    }
                },
            ],

  
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $total = (int) ($data['total_fees'] ?? 0);
        $reg   = (int) ($data['reg_fees'] ?? 0);
        $paid  = (int) ($data['paid_fees'] ?? 0);

        if (($reg + $paid) > $total) {
            // Inject artificial field to trigger validation error
            $data['fees_error'] = 'invalid';
        }

        return $data;
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
            '*.paid_fees.required'    => 'Paid fees is required.',
        ];
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


        
        /* -------- FEES -------- */
        // $totalFees = (float) $row['total_fees'];
        // $regFees   = (float) $row['reg_fees'];
        // $paidFees  = is_numeric($row['paid_fees'] ?? null) ? (float)$row['paid_fees'] : 0;

        // $pendingFees = max($totalFees - $regFees - $paidFees, 0);

        /* ---------- FIND STUDENT BY SNO ---------- */
        $student = Student::where('sno', (int) $row['sno'])->first();

        if (!$student) {
            $this->skippedRows++;
            return null;
        }

        /* ---------- FEES (INT ONLY) ---------- */
        $totalFees = isset($row['total_fees']) && is_numeric($row['total_fees'])
            ? (int) $row['total_fees']
            : (int) $student->total_fees;

        $regFees = isset($row['reg_fees']) && is_numeric($row['reg_fees'])
            ? (int) $row['reg_fees']
            : (int) $student->reg_fees;

        $paidFees = isset($row['paid_fees']) && is_numeric($row['paid_fees'])
            ? (int) $row['paid_fees']
            : (int) $student->paid_fees;

        if($totalFees < ($regFees + $paidFees)){
            $this->skippedRows++;
            // $this->duplicateContacts[] = $row['sno']."- Registration + Paid fees cannot exceed Total fees.";
            return null;   
        }
         
        /* ---------- CALC PENDING ---------- */
        $pendingFees = $totalFees - $regFees - $paidFees;

        if ($pendingFees < 0) {
            $pendingFees = 0;
        }

        /* ---------- UPDATE ONLY FEES ---------- */
        $student->update([
            'total_fees'   => $totalFees,
            'reg_fees'     => $regFees,
            'paid_fees'    => $paidFees,
            'pending_fees' => $pendingFees,
        ]);

        $this->insertedRows++;

        return null; // Important: updating, not inserting
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
