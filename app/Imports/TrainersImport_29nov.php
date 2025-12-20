<?php

namespace App\Imports;

use App\Models\Trainer;
use App\Models\User;
use App\Models\StudentCourse; 
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;

class TrainersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    use Importable;

    public $warnings = [];

    public function model(array $row)
    {
        $name  = trim($row['trainer_name'] ?? '');
        $email = trim($row['email'] ?? '');
        $phone = trim($row['phone'] ?? '');
        $username = trim($row['username'] ?? '');

        // ---------- VALIDATIONS ----------
        if (empty($name)) {
            $this->warnings[] = "Missing trainer name — row skipped.";
            return null;
        }

        if (User::where('email', $email)->exists()) {
            $this->warnings[] = "Duplicate email skipped: {$email}";
            return null;
        }

        if (User::where('phone', $phone)->exists()) {
            $this->warnings[] = "Duplicate phone skipped: {$phone}";
            return null;
        }

        if (User::where('username', $username)->exists()) {
            $this->warnings[] = "Duplicate username skipped: {$username}";
            return null;
        }

        // ---------- COURSE LOOKUP ----------
        $courseId = StudentCourse::whereRaw("LOWER(course_name) = ?", strtolower($row['technology']))
                    ->value('id');

        if (!$courseId) {
            $this->warnings[] = "Invalid Technology: {$row['technology']}";
            return null;
        }

        // Generate username
        $username = !empty($row['email'])
            ? strstr($row['email'], '@', true)
            : $row['phone']; // fallback

        // Create User
        // $user = User::create([
        //     'name'     => $row['trainer_name'],
        //     'email'    => $row['email'],
        //     'phone'    => $row['phone'],
        //     'username' => $username,
        //     'password' => bcrypt('12345678'), // default password
        //     'role'     => 3,
        //     'status'   => 1,
        // ]);


        // ------------ CREATE USER ----------
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'username' => $username,
            // 'password' => Hash::make('trainer123'),  // default password
            'password' => 'trainer123', // de
            'role'     => 3, // trainer role
            'status'   => 'active',
        ]);

        // ------------ CREATE TRAINER ----------
        return new Trainer([
            'user_id'    => $user->id,
            'gender'     => strtolower($row['gender']),
            'technology' => $courseId,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.trainer_name' => 'required|string',
            '*.gender'       => 'required|in:male,female',
            '*.phone'        => 'required|string',
            '*.email'        => 'nullable|email',
            '*.technology'   => 'required|string',
        ];
    }
}
