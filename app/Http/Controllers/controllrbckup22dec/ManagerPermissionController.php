<?php

// app/Http/Controllers/Admin/ManagerPermissionController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\RolePermission;

class ManagerPermissionController extends Controller
{
    private int $managerRole = 4;

    /**
     * Show permission assignment page
     */
    public function edit()
    {
        // $permissions = Permission::orderBy('label')->get();

        // $assignedPermissions = RolePermission::where('role', $this->managerRole)
        //     ->pluck('permission_id')
        //     ->toArray();



        $permissions = Permission::orderBy('name')->get()
            ->groupBy(function ($perm) {
                return explode('.', $perm->name)[0]; // students, enquiries, analytics
            });

        $assignedPermissions = RolePermission::where('role', 4)
            ->pluck('permission_id')
            ->toArray();

        return view('roles.manager-permissions', compact(
            'permissions',
            'assignedPermissions'
        ));
    }

    /**
     * Save permissions for manager role
     */
    public function update(Request $request)
    {
        $permissionIds = $request->input('permissions', []);

        // Remove old permissions
        RolePermission::where('role', $this->managerRole)->delete();

        // Assign new permissions
        foreach ($permissionIds as $permissionId) {
            RolePermission::create([
                'role' => $this->managerRole,
                'permission_id' => $permissionId,
            ]);
        }

        return redirect()
            ->route('admin.manager.permissions.edit')
            ->with('success', 'Manager permissions updated successfully.');
    }
}
