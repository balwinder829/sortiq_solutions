<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class ManagerPermissionController extends Controller
{
    // Allowed roles
    private array $allowedRoles = ['Manager','HR','Custom'];

    // =====================================================
    // SHOW PAGE
    // =====================================================

    public function index(Request $request)
    {
        // Load users having allowed roles
        $managers = User::role($this->allowedRoles)->get();

        // Load permissions ordered by tab + menu
        $permissions = Permission::orderBy('menu_group_order')
            ->orderBy('menu_item_order')
            ->orderBy('name')
            ->get()
            ->groupBy('menu_group');

        $selectedUser = null;

        if ($request->filled('user_id')) {

            $user = User::find($request->user_id);

            // HARD SECURITY CHECK
            if ($user && $user->hasAnyRole($this->allowedRoles)) {
                $selectedUser = $user;
            }
        }

        return view(
            'roles.manager-permissions',
            compact('managers','permissions','selectedUser')
        );
    }
    
    public function indexbkup5mrch(Request $request)
    {
        // Load users having allowed roles
        $managers = User::role($this->allowedRoles)->get();

        // Group permissions (students.view -> students)
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        $selectedUser = null;

        if ($request->filled('user_id')) {

            $user = User::find($request->user_id);

            // HARD SECURITY CHECK
            if ($user && $user->hasAnyRole($this->allowedRoles)) {
                $selectedUser = $user;
            }
        }

        return view(
            'roles.manager-permissions',
            compact('managers','permissions','selectedUser')
        );
    }

    // =====================================================
    // SAVE PERMISSIONS
    // =====================================================
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
        ]);

        $user = User::findOrFail($request->user_id);

        // SECURITY: only Manager/HR/Custom allowed
        if (!$user->hasAnyRole($this->allowedRoles)) {
            abort(403);
        }

        // overwrite user-specific permissions
        $user->syncPermissions($request->permissions ?? []);

        // return back()->with('success','Permissions updated successfully for'. $user->name);
        return redirect()
            ->route('users.index')
            ->with('success','Permissions updated successfully for '. $user->name);
    }
}
