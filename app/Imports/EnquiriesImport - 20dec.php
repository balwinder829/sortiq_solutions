<?php

namespace App\Imports;

use App\Models\Enquiry;
use App\Models\College;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;

class EnquiriesImport implements ToCollection
{
    protected $creator;
    public $errors = []; // track row errors

    public function __construct($creator)
    {
        $this->creator = $creator;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // remove header

        foreach ($rows as $index => $row) {

            // Convert row to array for validation
            $data = [
                'name'     => $row[0] ?? null,
                'mobile'   => isset($row[1]) ? (string)$row[1] : null,
                'email'    => $row[2] ?? null,
                'college'  => trim($row[3] ?? ''),
                'study'    => $row[4] ?? null,
                'semester' => $row[5] ?? null,
            ];

            // Validate row
            $validator = Validator::make($data, [
                'name'   => 'required|string|max:255',
                'mobile' => 'nullable|string|max:20',
                'email'  => 'nullable|email',
            ]);

            // If invalid → record error
            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'error' => $validator->errors()->first()
                ];
                continue;
            }

            // Duplicate check (mobile or email)
            $exists = Enquiry::where('mobile', $data['mobile'])
                        ->orWhere('email', $data['email'])
                        ->first();

            if ($exists) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'error' => 'Duplicate entry (mobile/email already exists)'
                ];
                continue;
            }

            // College match or create
            $college = null;
            if ($data['college'] !== '') {
                $college = College::firstOrCreate(
                    ['college_name' => $data['college']],
                    ['college_name' => $data['college']]
                );
            }

            // Insert enquiry
            Enquiry::create([
                'name'        => $data['name'],
                'mobile'      => $data['mobile'],
                'email'       => $data['email'],
                'college'  => $college ? $college->id : null,
                'study'       => $data['study'],
                'semester'    => $data['semester'],
                'created_by'  => $this->creator,
                'source'      => 'excel',
            ]);
        }
    }
}
