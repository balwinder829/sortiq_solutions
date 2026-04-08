<?php

namespace App\Imports;

use App\Models\Trainer;
use App\Models\StudentCourse;
use App\Models\BlockedNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;

class TrainersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    use Importable;

    public $warnings = [];
    public $rowNumber = 1;

    /* ================= NORMALIZE ================= */
    public function prepareForValidation($data, $index)
    {
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/[\|\-_;\s,]+/', '', $data['phone']);
        }

        if (!empty($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        return $data;
    }

    /* ================= MAIN LOGIC ================= */
    public function model(array $row)
    {
        $this->rowNumber++;

        $name     = trim($row['trainer_name'] ?? '');
        $email    = trim($row['email'] ?? '');
        $phone    = trim($row['phone'] ?? '');
        $username = trim($row['username'] ?? '');
        $gender   = strtolower(trim($row['gender'] ?? ''));
        $tech     = strtolower(trim($row['technology'] ?? ''));

        /* -------- NAME FALLBACK -------- */
        if (empty($name)) {
            if (!empty($username)) {
                $name = $username;
            } else {
                return $this->skip('Missing trainer name and username', '-');
            }
        }

        /* -------- COURSE -------- */
        $courseId = StudentCourse::whereRaw("LOWER(course_name) = ?", [$tech])->value('id');
        if (!$courseId) {
            return $this->skip('Invalid technology', $tech);
        }

        /* -------- BLOCKED NUMBER (SKIP, NOT FAIL) -------- */
        if (!empty($phone) && BlockedNumber::where('number', $phone)->exists()) {
            return $this->skip('Blocked phone number', $phone);
        }

        /* -------- DUPLICATE CHECK -------- */
        if (!empty($email) && Trainer::where('email', $email)->exists()) {
            return $this->skip('Duplicate email', $email);
        }

        if (!empty($phone) && Trainer::where('phone', $phone)->exists()) {
            return $this->skip('Duplicate phone', $phone);
        }

        if (!empty($username) && Trainer::where('username', $username)->exists()) {
            return $this->skip('Duplicate username', $username);
        }

        /* -------- CREATE TRAINER -------- */
        return new Trainer([
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'username'     => $username,
            'password'     => 'trainer123',
            'trainer_pswd' => 'trainer123',
            'gender'       => $gender,
            'technology'   => $courseId,
            'status'       => 'active',
        ]);
    }

    /* ================= SKIP HANDLER ================= */
    private function skip(string $reason, $value = '-')
    {
        $this->warnings[] = [
            'row'    => $this->rowNumber,
            'reason' => $reason,
            'value'  => $value,
        ];

        return null;
    }

    /* ================= VALIDATION ================= */
    public function rules(): array
    {
        return [
            '*.trainer_name' => 'nullable|string',
            '*.gender'       => 'required|in:male,female',

            '*.phone' => [
                'required',
                'regex:/^\d{10}$/'
            ],

            '*.email' => 'nullable|email',

            '*.technology' => 'required|string',

            '*.username' => 'nullable|string',
        ];
    }
}