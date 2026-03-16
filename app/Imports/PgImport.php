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

    public function prepareForValidation($data, $index)
    {
        // -------- CONTACT NORMALIZE --------
        if (!empty($data['contact'])) {

            $data['contact'] = preg_replace('/[\|\-_;\s]+/', ',', $data['contact']);
            $data['contact'] = preg_replace('/,+/', ',', $data['contact']);
            $data['contact'] = trim($data['contact'], ',');
        }

        // -------- EMAIL NORMALIZE --------
        if (!empty($data['email'])) {

            $data['email'] = preg_replace('/[\|\-_;\s]+/', ',', $data['email']);
            $data['email'] = preg_replace('/,+/', ',', $data['email']);
            $data['email'] = trim($data['email'], ',');
        }

        return $data;
    }

    /* ================= VALIDATION RULES ================= */
    public function rules(): array
    {
        return [

            '*.name' => 'required|string',
        
           '*.contact' => [
                'required',
                'regex:/^\d{10}(,\d{10})*$/'
            ],

            '*.email' => [
                'nullable',
                'regex:/^[^,\s]+@[^,\s]+\.[^,\s]+(,[^,\s]+@[^,\s]+\.[^,\s]+)*$/'
            ],


            // '*.email' => 'required|email',

            // Fees required
            '*.address' => 'nullable|string',
            // '*.pg_type' => function ($attribute, $value, $fail) {

            //     if (!$value) {
            //         $fail('PG type is required.');
            //         return;
            //     }

            //     // normalize value
            //     $pgType = strtolower(trim($value));

            //     // allowed values
            //     $allowed = ['boys', 'girls', 'both'];

            //     if (!in_array($pgType, $allowed, true)) {
            //         $fail('Invalid PG type. Allowed values: boys, girls, both.');
            //     }
            // },
            // '*.food_type' => function ($attribute, $value, $fail) {

            //     if (!$value) {
            //         $fail('Food type is required.');
            //         return;
            //     }

            //     // normalize value
            //     $foodType = strtolower(trim($value));

            //     // allowed values
            //     $allowed = ['food', 'without_food'];

            //     if (!in_array($foodType, $allowed, true)) {
            //         $fail('Invalid Food type. Allowed values: food, without_food.');
            //     }
            // },
            '*.pg_type' => 'required|string',
            '*.food_type' => 'required|string',

            // '*.food_type' => 'required|string',
            '*.rent_estimate' => 'nullable|numeric',
        ];
    }

    /* ================= FRIENDLY MESSAGES ================= */
    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'name is required.',
            // '*.contact_person.required'       => 'Contact person is required.',
            '*.contact.required'      => 'Contact number is required.',
            // '*.contact.regex'         => 'Contact number must contain only digits.',
            // '*.email.required'   => 'Email is required.',
            // '*.email.email'   => 'Add valid email address.',
            // '*.address.required'     => 'Address is required.',
            '*.pg_type.required'     => 'pg type is required.',
            '*.food_type.required'     => 'food type is required.',
            // '*.rent_estimate.required'     => 'rent estimate is required.',
            '*.contact.regex' => 'Each contact number must be exactly 10 digits. Allowed separators: , - | _ space ;',

            '*.email.regex' => 'Enter valid email addresses separated by , - | _ space ;',

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
        // if (!empty($row['contact']) && Pg::where('contact', 'LIKE', "%{$row['contact']}%")->exists()) {
        //     $this->skippedRows++;
        //     $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";
        //     return null;
        // }

        // if (!empty($row['contact'])) {

        //     foreach (explode(',', $row['contact']) as $phone) {

        //         if (Pg::where('contact', 'LIKE', "%{$phone}%")->exists()) {
        //             $this->skippedRows++;
        //             $this->duplicateContacts[] = "Duplicate contact skipped: {$phone}";
        //             return null;
        //         }
        //     }
        // }

         /* -------- NORMALIZE PG TYPE -------- */
        $pgType = strtolower($row['pg_type'] ?? '');

        $allowedPgTypes = ['boys', 'girls', 'both'];

        if (!in_array($pgType, $allowedPgTypes, true)) {
            $pgType = 'both'; // default value
        }

        /* -------- NORMALIZE FOOD TYPE -------- */
        $foodType = strtolower($row['food_type'] ?? '');

        $allowedFoodTypes = ['food', 'without_food'];

        if (!in_array($foodType, $allowedFoodTypes, true)) {
            $foodType = 'food'; // default value
        }

        $this->insertedRows++;
        
        return new Pg([
            'name' => strtolower(trim($row['name'])),
            'email'     => $row['email'] ?? null,
            'contact'      => $row['contact'],
            
            'address' => $row['address'],
            'rent_estimate'      => $row['rent_estimate']  ?? null,
            'pg_type' => $pgType,
            'food_type' => $foodType,
            'description'   => $row['description'] ?? null,
            'status'   => 'active',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
