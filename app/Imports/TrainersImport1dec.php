<?php

namespace App\Imports;

use App\Models\Trainer;
use App\Models\User;
use App\Models\StudentCourse;
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

    public function model(array $row)
    {
        // increment row counter (HeadingRow means first data row is row 2 typically)
        $this->rowNumber++;

        $name     = trim($row['trainer_name'] ?? '');
        $email    = trim($row['email'] ?? '');
        $phone    = trim($row['phone'] ?? '');
        $username = trim($row['username'] ?? '');

        // ------ validations ------
        if (empty($name)) {
            return $this->skip('Missing trainer name', $row);
        }

        if (!empty($email) && User::where('email', $email)->exists()) {
            return $this->skip('Duplicate email', $row);
        }

        if (!empty($phone) && User::where('phone', $phone)->exists()) {
            return $this->skip('Duplicate phone', $row);
        }

        if (!empty($username) && User::where('username', $username)->exists()) {
            return $this->skip('Duplicate username', $row);
        }

        // course lookup (case-insensitive)
        $courseId = StudentCourse::whereRaw("LOWER(course_name) = ?", [strtolower($row['technology'])])->value('id');
        if (!$courseId) {
            return $this->skip('Invalid technology', $row);
        }

        // username fallback
        // $username = !empty($email) ? strstr($email, '@', true) : $phone;

        // create user (your User model mutator will hash password)
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'username' => $username,
            'password' => 'trainer123', // mutator in User model will hash
            'role'     => 2,
            'status'   => 'active',
        ]);

        return new Trainer([
            'user_id'    => $user->id,
            'gender'     => strtolower($row['gender']),
            'technology' => $courseId,
        ]);
    }

    private function skip(string $reason, array $row)
    {
        $this->warnings[] = [
            'row'    => $this->rowNumber,
            'reason' => $reason,
            // store useful value so admin can identify the row (prefer email, phone, username)
            'value'  => $row['email'] ?? $row['phone'] ?? $row['username'] ?? '-',
        ];
        return null;
    }

    public function rules(): array
    {
        return [
            '*.trainer_name' => 'required|string',
            '*.gender'       => 'required|in:male,female',
            '*.phone'        => 'required|int',
            '*.email'        => 'nullable|email',
            '*.technology'   => 'required|string',
        ];
    }
}
