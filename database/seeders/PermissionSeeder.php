<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'trainer.view',
            'trainer.create',
            'trainer.edit',
            'trainer.delete',
            'entries.view',
            'entries.add',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::where('name', 'Admin')->first();

        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
