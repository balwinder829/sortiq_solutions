<?php

namespace App\Imports;

use App\Models\PlacementCompany;

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

class PlacementCompanyImport implements
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

            '*.company_name' => 'required|string',
            '*.contact_person'       => 'required|string',

            '*.contact' => [
                'required',
                'regex:/^[0-9]+$/',
                'digits:10'
            ],

            '*.email' => 'required|email',

            // Fees required
            '*.address' => 'required|string',
        ];
    }

    /* ================= FRIENDLY MESSAGES ================= */
    public function customValidationMessages()
    {
        return [
            '*.company_name.required' => 'Company name is required.',
            '*.contact_person.required'       => 'Contact person is required.',
            '*.contact.required'      => 'Contact number is required.',
            '*.contact.regex'         => 'Contact number must contain only digits.',
            '*.email.required'   => 'Email is required.',
            '*.email.email'   => 'Add valid email address.',
            '*.address.required'     => 'Address is required.',
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
        if (!empty($row['contact']) && PlacementCompany::where('phone', $row['contact'])->exists()) {
            $this->skippedRows++;
            $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";
            return null;
        }


        // $allowedRef = array(
        //     '1' => 'website',
        //     '2' => 'college',
        //     '3' => 'social media',
        //     '4' => 'student reference',
        //     '5' => 'personal reference',
        //     '6' => 'Ads',
        // );

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

        
        $this->insertedRows++;

        return new PlacementCompany([
            'name' => strtolower(trim($row['company_name'])),
            'contact_person'       => strtolower(trim($row['contact_person'])),
            'email'     => $row['email'],
            'phone'      => $row['contact'],
            
            'address' => $row['address'],
            'website'      => $row['website']  ?? null,
            'remarks'   => $row['remarks'] ?? null,
            'status'   => 'active',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
