<?php

namespace App\Imports;

use App\Models\Pg;

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

class PgImport implements
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

            '*.name' => 'required|string',
        
            '*.contact' => [
                'required',
                'regex:/^[0-9]+$/',
                'digits:10'
            ],

            '*.email' => 'required|email',

            // Fees required
            '*.address' => 'required|string',
            '*.pg_type' => function ($attribute, $value, $fail) {

                if (!$value) {
                    $fail('PG type is required.');
                    return;
                }

                // normalize value
                $pgType = strtolower(trim($value));

                // allowed values
                $allowed = ['boys', 'girls', 'both'];

                if (!in_array($pgType, $allowed, true)) {
                    $fail('Invalid PG type. Allowed values: boys, girls, both.');
                }
            },
            '*.food_type' => function ($attribute, $value, $fail) {

                if (!$value) {
                    $fail('Food type is required.');
                    return;
                }

                // normalize value
                $foodType = strtolower(trim($value));

                // allowed values
                $allowed = ['food', 'without_food'];

                if (!in_array($foodType, $allowed, true)) {
                    $fail('Invalid Food type. Allowed values: food, without_food.');
                }
            },

            // '*.food_type' => 'required|string',
            '*.rent_estimate' => 'required|numeric',
        ];
    }

    /* ================= FRIENDLY MESSAGES ================= */
    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'name is required.',
            '*.contact_person.required'       => 'Contact person is required.',
            '*.contact.required'      => 'Contact number is required.',
            '*.contact.regex'         => 'Contact number must contain only digits.',
            '*.email.required'   => 'Email is required.',
            '*.email.email'   => 'Add valid email address.',
            '*.address.required'     => 'Address is required.',
            '*.pg_type.required'     => 'pg type is required.',
            '*.food_type.required'     => 'food type is required.',
            '*.rent_estimate.required'     => 'rent estimate is required.',
        ];
    }

    /* ================= DATE CHECK ================= */

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
        

        /* -------- SESSION -------- */

        /* -------- DUPLICATE CONTACT -------- */
        if (!empty($row['contact']) && Pg::where('contact', $row['contact'])->exists()) {
            $this->skippedRows++;
            $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";
            return null;
        }
        
        $this->insertedRows++;

        return new Pg([
            'name' => strtolower(trim($row['name'])),
            'email'     => $row['email'],
            'contact'      => $row['contact'],
            
            'address' => $row['address'],
            'rent_estimate'      => $row['rent_estimate']  ?? null,
            'pg_type'      => $row['pg_type']  ?? null,
            'food_type'      => $row['food_type']  ?? null,
            'description'   => $row['description'] ?? null,
            'status'   => 'active',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
