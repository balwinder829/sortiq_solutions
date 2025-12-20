<?php

namespace App\Imports;

use App\Models\Enquiry;
use App\Models\College;
use App\Models\BlockedNumber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;

class EnquiriesImport implements ToCollection
{
    protected $creator;
    public $errors = [];

    public function __construct($creator)
    {
        $this->creator = $creator;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // remove header row

        foreach ($rows as $index => $row) {

            $data = [
                'name'     => $row[0] ?? null,
                'mobile'   => isset($row[1]) ? trim((string)$row[1]) : null,
                'email'    => isset($row[2]) ? trim($row[2]) : null,
                'college'  => trim($row[3] ?? ''),
                'study'    => $row[4] ?? null,
                'semester' => $row[5] ?? null,
            ];

            /* ================= VALIDATION ================= */

            $validator = Validator::make($data, [
                'name'   => 'required|string|max:255',
                'mobile' => 'nullable|string|max:20',
                'email'  => 'nullable|email',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row'   => $index + 2,
                    'error' => $validator->errors()->first()
                ];
                continue;
            }

            /* ================= BLOCKED NUMBER CHECK ================= */
            if (!empty($data['mobile'])) {
                $isBlocked = BlockedNumber::where('number', $data['mobile'])->exists();

                if ($isBlocked) {
                    $this->errors[] = [
                        'row'   => $index + 2,
                        'error' => 'Blocked mobile number'
                    ];
                    continue;
                }
            }

            /* ================= ACTIVE DUPLICATE CHECK ================= */
            $exists = Enquiry::query()
                ->whereNull('deleted_at')
                ->where(function ($q) use ($data) {
                    if (!empty($data['mobile'])) {
                        $q->where('mobile', $data['mobile']);
                    }
                    if (!empty($data['email'])) {
                        $q->orWhere('email', $data['email']);
                    }
                })
                ->exists();

            if ($exists) {
                $this->errors[] = [
                    'row'   => $index + 2,
                    'error' => 'Duplicate entry (mobile/email already exists)'
                ];
                continue;
            }

            /* ================= COLLEGE LOOKUP ================= */
            $college = null;
            if ($data['college'] !== '') {
                $college = College::firstOrCreate(
                    ['college_name' => $data['college']]
                );
            }

            /* ================= CREATE ENQUIRY ================= */
            Enquiry::create([
                'name'       => $data['name'],
                'mobile'     => $data['mobile'],
                'email'      => $data['email'],
                'college'    => $college?->id,
                'study'      => $data['study'],
                'semester'   => $data['semester'],
                'created_by' => $this->creator,
                'source'     => 'excel',
            ]);
        }
    }
}
