<?php

namespace App\Imports;

use App\Models\PartTimeJob;

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

class PartTimeJobImport implements
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
            '*.job_type' => 'nullable|string',
            '*.shift' => 'nullable|string',
            '*.salary_estimate' => 'nullable|numeric',
            // '*.contact_person'       => 'required|string',

            '*.contact' => [
                'required',
                // 'regex:/^\d{10}(,\d{10})*$/'
                'regex:/^\d{10,18}(,\d{10,18})*$/'
            ],

            '*.email' => [
                'nullable',
                'regex:/^[^,\s]+@[^,\s]+\.[^,\s]+(,[^,\s]+@[^,\s]+\.[^,\s]+)*$/'
            ],

            // Fees required
            '*.address' => 'nullable|string',
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
            '*.email.email'   => 'Add valid email address.',
            // '*.address.required'     => 'Address is required.',
            // '*.job_type.required'     => 'job type is required.',
            // '*.salary_estimate.required'     => 'salary estimate is required.',
            // '*.shift.required'     => 'shift is required.',
            '*.contact.regex' => 'Each contact number must be between 10 and 18 digits. Allowed separators: , - | _ space ;',

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
        // if (!empty($row['contact']) && PartTimeJob::where('mobile', $row['contact'])->exists()) {
        //     $this->skippedRows++;
        //     $this->duplicateContacts[] = "Duplicate contact skipped: {$row['contact']}";
        //     return null;
        // }
        
        // if (!empty($row['contact'])) {

        //     foreach (explode(',', $row['contact']) as $phone) {

        //         if (PartTimeJob::where('mobile', 'LIKE', "%{$phone}%")->exists()) {
        //             $this->skippedRows++;
        //             $this->duplicateContacts[] = "Duplicate contact skipped: {$phone}";
        //             return null;
        //         }
        //     }
        // }



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

        return new PartTimeJob([
            'name' => strtolower(trim($row['name'])),
            // 'contact_person'       => strtolower(trim($row['contact_person'])),
            'email'     => $row['email'],
            'mobile'      => $row['contact'],
            
            'address' => $row['address'],
            'location' => $row['address'],
            'salary_estimate' => $row['salary_estimate'],
            'job_type'      => $row['job_type']  ?? null,
            'shift'   => $row['shift'] ?? null,
            'status'   => 'active',
        ]);
    }

    public function onError(Throwable $e)
    {
        // Hide system errors from user
    }
}
