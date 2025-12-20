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
        // Excel starts reading data at row 2 when using WithHeadingRow
        $this->rowNumber++;

        $name     = trim($row['trainer_name'] ?? '');
        $email    = trim($row['email'] ?? '');
        $phone    = trim($row['phone'] ?? '');
        $username = trim($row['username'] ?? '');
        $gender   = strtolower($row['gender'] ?? '');
        $tech     = strtolower($row['technology'] ?? '');

        // If name missing → fallback to username
        if (empty($name)) {
            if (!empty($username)) {
                $name = $username;
            } else {
                // username also missing -> skip
                return $this->skip('Missing trainer name and username', '-');
            }
        }

        // Course lookup
        $courseId = StudentCourse::whereRaw("LOWER(course_name) = ?", [$tech])->value('id');
        if (!$courseId) {
            return $this->skip('Invalid technology', $tech);
        }

        // Duplicate checks (manual)
        if (!empty($email) && User::where('email', $email)->exists()) {
            return $this->skip('Duplicate email', $email);
        }

        if (!empty($phone) && User::where('phone', $phone)->exists()) {
            return $this->skip('Duplicate phone', $phone);
        }

        if (!empty($username) && User::where('username', $username)->exists()) {
            return $this->skip('Duplicate username', $username);
        }

        // Create user safely (catch DB unique constraint errors)
        try {
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'username' => $username,
                'password' => 'trainer123', // hashed by mutator
                'role'     => 2,
                'status'   => 'active',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->skip('Duplicate user record (SQL constraint)', $email ?? $phone ?? $username ?? '-');
        }

        // Create trainer record
        return new Trainer([
            'user_id'    => $user->id,
            'gender'     => $gender,
            'technology' => $courseId,
        ]);
    }

    private function skip(string $reason, $value = '-')
    {
        $this->warnings[] = [
            'row'    => $this->rowNumber,
            'reason' => $reason,
            'value'  => $value,
        ];

        return null;
    }

    public function rules(): array
    {
        return [
            '*.trainer_name' => 'nullable|string',
            '*.gender'       => 'required|in:male,female',
            '*.phone'        => 'required',
            '*.email'        => 'nullable|email',
            '*.technology'   => 'required|string',
            '*.username'     => 'nullable|string',
        ];
    }
}
