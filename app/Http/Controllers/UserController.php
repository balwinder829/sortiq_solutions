<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Trainer;
use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Http\Middleware\LogSystemActivity;
use App\Http\DataTables\DataTablesServerSide;
use App\Rules\NotBlockedNumber;

class UserController extends Controller
{
    protected string $permissionPrefix = 'users';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',
        'restore'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
    // Blade: show all users
    // public function index()
    // {
    //     // $users = User::all();
    //     $users = User::whereIn('role', [1, 3, 4,5,8])
    //         ->withTrashed()
    //         ->with('roles')
    //         ->get();
    //     return view('users.index', compact('users'));
    // }

    public function index()
    {
        $users = User::withTrashed()
            ->role(['Admin','Sales','Manager','HR','Custom'])
            // ->with(['roles']) // ✅ important
            ->with(['roles','permissions']) // ✅ important
            ->get();

        return view('users.index', compact('users'));
    }


    public function data(Request $request)
    {
        // $query = User::whereIn('role', [1, 3, 4])
        //     ->withTrashed()
        //     ->with(['roles', 'legacyRole']);

        $query = User::withTrashed()
            ->role(['Admin','Sales','Manager','HR','Custom'])
            // ->with(['roles']) // ✅ important
            ->with(['roles','permissions']);

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id', 'name', 'username', 'role', 'status', 'created_at'],
            'searchable' => ['name', 'username', 'email'],
        ], function ($user, $index, $start) {
            // $roleName = $user->getRoleNames()->implode(', ') ?: ($user->legacyRole->name ?? '-');

            $roleHtml = '';

// ===== ROLE BADGES =====
foreach ($user->getRoleNames() as $role) {

    $roleColor = match($role) {
        'Admin'   => 'danger',
        'Manager' => 'primary',
        'HR'      => 'success',
        'Custom'  => 'warning',
        default   => 'secondary'
    };

    $roleHtml .= '<span class="badge bg-'.$roleColor.' me-1">'.$role.'</span>';
}


// ===== GROUP PERMISSIONS (NOT ADMIN) =====
if (!$user->hasRole('Admin')) {

    $groupedPermissions = collect($user->getPermissionNames())
        ->groupBy(function ($permission) {
            return explode('.', $permission)[0];
        });

    $roleHtml .= '<div class="mt-1">';

    if ($groupedPermissions->count()) {

        foreach ($groupedPermissions as $group => $permissions) {

            $popoverContent = $permissions
                ->map(fn($p) => '• '.str_replace($group.'.','',$p))
                ->implode('<br>');

            $roleHtml .= '
                <span class="badge bg-light text-dark border permission-group"
                    data-bs-toggle="popover"
                    data-bs-trigger="hover"
                    data-bs-placement="top"
                    data-bs-html="true"
                    data-bs-content="<div class=\'perm-popover\'>'.$popoverContent.'</div>">
                    '.ucfirst($group).' ('.$permissions->count().')
                </span>';
        }

    } else {
        $roleHtml .= '<span class="text-muted small">No Permissions</span>';
    }

    $roleHtml .= '</div>';
}
            $status = $user->trashed()
                ? '<span class="badge bg-danger">Deleted</span>'
                : ($user->status === 'inactive'
                    ? '<span class="badge bg-warning text-dark">Inactive</span>'
                    : '<span class="badge bg-success">Active</span>');
            $usernameCell = $user->trashed()
                ? '<span class="text-danger">' . e($user->username) . '</span>'
                : e($user->username);
            $actions = '';
            if (!$user->trashed()) {
                if (auth()->user()->hasRole('Admin') && $user->hasAnyRole(['Manager','HR','Custom'])) {
                    $actions .= '<a href="' . route('admin.manager.permissions.edit', ['user_id' => $user->id]) . '" class="btn btn-sm btn-outline-primary" title="Manage Permissions"><i class="fas fa-key"></i></a> ';
                }
                $actions .= '<a href="' . route('users.edit', $user) . '" class="btn btn-sm" title="Edit User"><i class="fa fa-edit"></i></a> ';
                $actions .= '<form action="' . route('users.destroy', $user) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Delete user?"><i class="fa fa-trash"></i></button></form>';
            } else {
                $actions .= '<form action="' . route('users.restore', $user->id) . '" method="POST" style="display:inline;">' . csrf_field() . '<button type="submit" class="btn btn-sm btn-success" title="Restore"><i class="fa fa-undo"></i></button></form>';
            }
            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                ucwords($user->name ?? ''),
                $usernameCell,
                $roleHtml,
                $status,
                $user->created_at->format('d M Y'),
                $actions,
            ];
        });
    }


    // Blade: show create form
    public function create()
    {
        // $roles = Role::whereIn('id', [2, 3, 4])->get();
        $roles = Role::whereIn('name', ['Hr', 'Manager','custom'])->orderby('name','desc')->get();
        return view('users.create', compact('roles'));
    }

    // Blade: store new user
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => [
                'required',
                'max:40',
                'unique:users,username',
                'regex:/^[A-Za-z0-9_]+$/'
            ],
            'password' => 'required|string|min:6',
            'role' => 'required',
            // 'phone'        => 'required|max:40|unique:users,phone',
            'phone' => ['required', 'digits:10', 'unique:users,phone', new NotBlockedNumber],
            // 'username'        => 'required|max:40|unique:users,username',
            'email'        => 'nullable|email|unique:users,email',
            // 'role' => 'required|string',
            'role'     => 'required|exists:roles,id',
        ],[
                'username.regex'    => 'Username can only contain letters, numbers, and underscores — no spaces allowed.',
        ]);

        // User::create($data);

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower(trim($data['username'])),
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'plain_pswd' => $request->password,
            'role' => $request->role,
            'status' => 'active',
        ]);

          /*
        |--------------------------------------------------------------------------
        | 🔑 SPATIE ROLE SYNC (ONLY FOR ADMIN & MANAGER)
        |--------------------------------------------------------------------------
        */
        // if (in_array($data['role'], [1, 4])) {

        //     // Find role name from roles table
        //     $roleName = SpatieRole::where('id', $data['role'])->value('name');

        //     if ($roleName) {
        //         $user->syncRoles([$roleName]); // replaces old spatie role
        //     }

        // } else {
        //     // Remove any spatie roles for fixed-role users
        //     $user->syncRoles([]);
        // }

        /* |--------------------------------------------------------------------------
        | 🔑 SPATIE ROLE ASSIGN
        |--------------------------------------------------------------------------
        */

        // Get role using ID from form (4,5,8 etc)
        $role = Role::find($data['role']);

        if ($role) {
            $user->syncRoles($role); // assign spatie role
        }

        return redirect()->route('users.index')->with('success', 'User created!');
    }

    // Blade: show edit form
    public function edit(User $user)
    {
        // $roles = Role::whereIn('id', [2, 3, 4])->get();
        // $roles = Role::whereNotIn('name', ['Admin', 'Trainer','HR','Employee'])->get();
        $roles = Role::whereIn('name', ['Hr', 'Manager','custom'])->orderby('name','desc')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    // Blade: update user


    public function update(Request $request, User $user)
    {
        $loggedInUser = auth()->user();

        // ✅ Validation Rules
        $rules = [
            'username' => [
                'required',
                'string',
                'max:40',
                'unique:users,username,' . $user->id, // ignore current user
                'regex:/^[A-Za-z0-9_]+$/', // no spaces allowed
            ],
            // 'phone' => 'required|max:40|unique:users,phone,' . $user->id,
            'phone' => ['required', 'digits:10', 'unique:users,phone,' . $user->id, new NotBlockedNumber],
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'status'   => 'required|in:active,inactive',
            'role'     => 'required|exists:roles,id',
        ];

        // ✅ Custom Messages
        $error_msg = [
            'username.regex' => 'Username can only contain letters, numbers, and underscores — no spaces allowed.',
        ];

        $data = $request->validate($rules, $error_msg);

        // ✅ Handle password
        if (!empty($data['password'])) {
            $data['plain_pswd'] = $data['password']; // optional column
            // $data['password'] = Hash::make($data['password']); // 🔥 hash password
        } else {
            unset($data['password']);
        }

        // ✅ Update User
        $prev_role = $user->role;
        $user->update($data);

        /*
        |--------------------------------------------------------------------------
        | 🔑 SPATIE ROLE SYNC
        |--------------------------------------------------------------------------
        */

        // $role = SpatieRole::find($data['role']);

        // if ($role) {
        //     $user->syncRoles($role);
        // }

        // dd($prev_role, $data['role']);
        // dd($user->role);
        if ($prev_role != $data['role']) {

            $role = SpatieRole::find($data['role']);

            if ($role) {
                $user->syncPermissions([]); // remove old permissions
                $user->syncRoles($role);    // assign new role
            }
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated Successfully!');
    }

    public function update13fev(Request $request, User $user)
    {
        $loggedInUser = auth()->user();

        $rules = [
            'username' => [
                'required',
                'max:40',
                'unique:users,username',
                'regex:/^[A-Za-z0-9_]+$/'. $user->id,
            ],
            'username' => 'required|string|unique:users,username,' . $user->id,
            'phone'        => 'required|max:40|unique:users,phone' . $user->id,
            'email'        => 'nullable|email|unique:users,email' . $user->id,
            'password' => 'nullable|string|min:6',
            'status'   => 'required|in:active,inactive',
            'role'   => 'required',
        ];

        // 🔒 Apply role validation ONLY if admin is NOT editing admin
        // if (!($loggedInUser->role == 1 && $user->role == 1)) {
        //     $rules['role'] = 'required';
        // }
        $error_msg = [
                'username.regex'    => 'Username can only contain letters, numbers, and underscores — no spaces allowed.',
        ];
        $data = $request->validate($rules, $error_msg);

        // 🔒 Do not change role when admin edits admin
        // if ($loggedInUser->role == 1 && $user->role == 1) {
        //     unset($data['role']);
        // }

        // ❌ Keep old password if empty
        if (empty($data['password'])) {
            unset($data['password']);
        }else{
            $data['plain_pswd'] = $request->password;
        }

        $user->update($data);

        /*
        |--------------------------------------------------------------------------
        | 🔑 SPATIE ROLE SYNC (ONLY FOR ADMIN & MANAGER)
        |--------------------------------------------------------------------------
        */
        if (in_array($data['role'], [1, 4])) {

            // Find role name from roles table
            $roleName = SpatieRole::where('id', $data['role'])->value('name');

            if ($roleName) {
                $user->syncRoles([$roleName]); // replaces old spatie role
            }

        } else {
            // Remove any spatie roles for fixed-role users
            $user->syncRoles([]);
        }

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
