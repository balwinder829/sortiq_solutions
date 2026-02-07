<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class ManagerPermissionController extends Controller
{
    // Show page
  public function index(Request $request)
{
    $managers = User::role('Manager')->get();
    // $permissions = Permission::all();

    $permissions = Permission::orderBy('name')->get()
    ->groupBy(function ($permission) {
        return explode('.', $permission->name)[0]; // students.view → students
    });
    

    $selectedUser = null;

    if ($request->filled('user_id')) {
        $user = User::find($request->user_id);

        // ✅ HARD SAFETY CHECK
        if ($user && $user->hasRole('Manager')) {
            $selectedUser = $user;
        }
    }

    return view(
        'roles.manager-permissions',
        compact('managers', 'permissions', 'selectedUser')
    );
}




    public function edit()
    {
        $managers = User::role('Manager')->get();   // Spatie helper
        $permissions = Permission::all();
        // dd($managers, $permissions);
        return view('roles.manager-permissions', compact('managers', 'permissions'));
    }

    // Save permissions
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
        ]);

        $user = User::findOrFail($request->user_id);

        // IMPORTANT: overwrite user-specific permissions
        $user->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Permissions updated successfully');
    }
}
