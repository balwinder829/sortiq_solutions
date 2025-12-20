<?php

use App\Models\Permission;
use App\Models\RolePermission;

if (!function_exists('canAccess')) {
    function canAccess(string $permission): bool
    {
        $user = auth()->user();

        // Admin always allowed
        if ($user && $user->role == 1) {
            return true;
        }

        // Only manager is permission based
        if (!$user || $user->role != 4) {
            return false;
        }

        $permissionId = Permission::where('name', $permission)->value('id');

        if (!$permissionId) {
            return false;
        }

        return RolePermission::where('role', 4)
            ->where('permission_id', $permissionId)
            ->exists();
    }
}
