<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'AI-ML', 'BCA', 'CSE', 'MBA', 'ECE', 'MCA', 'ME', 'B.SC',
            'BBA', 'B.SC-IT', 'B.TECH', 'CIVIL', 'CSE-CEC', 'CSE-COF',
            'ECE-CEC', 'IT-CEC', 'ECE-EE', 'IT', 'M.SC'
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert(['name' => $dept]);
        }
    }
}
