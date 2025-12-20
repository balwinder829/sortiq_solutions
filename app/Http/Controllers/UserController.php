<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Trainer;
use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    // Blade: show all users
    public function index()
    {
        // $users = User::all();
        $users = User::withTrashed()->with('roles')->get();
        return view('users.index', compact('users'));
    }

    // Blade: show create form
    public function create()
    {
        $roles = Role::whereIn('id', [2, 3, 4])->get();
        return view('users.create', compact('roles'));
    }

    // Blade: store new user
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:2,3,4',
            'phone'        => 'required|max:20|unique:users,phone',
            'username'        => 'required|max:20|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'role' => 'required|string',
        ]);

        // User::create($data);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => $request->role,
            'status' => 'active',
        ]);

        if ($data['role'] == 2) {
            Trainer::create([
                'user_id' => $user->id,
                'technology' => '',
            ]);
        }

        \Log::info('Create input password raw: ' . $request->password);
        \Log::info('Create bcrypt: ' . bcrypt($request->password));

        return redirect()->route('users.index')->with('success', 'User created!');
    }

    // Blade: show edit form
    public function edit(User $user)
    {
        $roles = Role::whereIn('id', [2, 3, 4])->get();
        return view('users.edit', compact('user', 'roles'));
    }

    // Blade: update user
    public function update(Request $request, User $user)
    {
        $loggedInUser = auth()->user();

        $rules = [
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'status'   => 'required|in:active,inactive',
        ];

        // 🔒 Apply role validation ONLY if admin is NOT editing admin
        if (!($loggedInUser->role == 1 && $user->role == 1)) {
            $rules['role'] = 'required|in:2,3,4';
        }

        $data = $request->validate($rules);

        // 🔒 Do not change role when admin edits admin
        if ($loggedInUser->role == 1 && $user->role == 1) {
            unset($data['role']);
        }

        // ❌ Keep old password if empty
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated!');
    }

    public function update15dec(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username,'.$user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:2,3,4',
            'status'   => 'required|in:active,inactive', // 🔥 Added
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User updated!');
    }

    // Blade: delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted!');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored successfully.');
    }


    // API: optional methods for JSON if you still want
    public function apiIndex() { return User::all(); }
    public function apiShow(User $user) { return $user; }
}
