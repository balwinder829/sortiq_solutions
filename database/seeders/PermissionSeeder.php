<?php

namespace Database\Seeders;

// database/seeders/PermissionSeeder.php
use Illuminate\Database\Seeder; 
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // STUDENTS
            ['name' => 'students.view',   'label' => 'View Students'],
            ['name' => 'students.create', 'label' => 'Create Students'],
            ['name' => 'students.update', 'label' => 'Update Students'],
            ['name' => 'students.delete', 'label' => 'Delete Students'],

            // ENQUIRIES
            ['name' => 'enquiries.view',   'label' => 'View Enquiries'],
            ['name' => 'enquiries.assign','label' => 'Assign Enquiries'],
            ['name' => 'enquiries.convert','label' => 'Convert Enquiries'],

            // ANALYTICS
            ['name' => 'analytics.view', 'label' => 'View Analytics'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }
    }
}
