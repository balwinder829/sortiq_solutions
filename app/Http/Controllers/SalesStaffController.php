<?php

namespace App\Http\Controllers;

use App\Models\SalesStaff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\NotBlockedNumber;

class SalesStaffController extends Controller
{
    protected string $permissionPrefix = 'sales_staff';

    protected array $permissionMap = [
        'index'   => 'view',
        'create'  => 'create',
        'store'   => 'create',
        'edit'    => 'edit',
        'update'  => 'edit',
        'destroy' => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /**
     * List Sales Staff
     */
    public function index(Request $request)
    {
        $staff = SalesStaff::latest()->get();

        return view('sales_staff.index', compact('staff'));
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view('sales_staff.create');
    }

    /**
     * Store Sales Staff
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'gender'   => 'required|in:male,female',
            // 'phone'    => 'required|max:20|unique:sales_staff,phone',
            'phone' => [
                'required',
                'max:20',
                'unique:sales_staff,phone',
                new NotBlockedNumber,
            ],
            'username' => 'required|string|max:30|regex:/^[a-zA-Z0-9._-]+$/|unique:sales_staff,username',
            'password' => 'required|string|min:6',
            'email'    => 'nullable|email|unique:sales_staff,email',
            'status'   => 'required|in:active,inactive',
        ]);

        SalesStaff::create($validated + [
            'plain_pswd' => $validated['password']
        ]);

        // SalesStaff::create([
        //     'name'       => $validated['name'],
        //     'username'   => $validated['username'],
        //     'password'   => $validated['password'], // auto hashed by model
        //     'plain_pswd' => $validated['password'],
        //     'email'      => $validated['email'] ?? null,
        //     'phone'      => $validated['phone'],
        //     'gender'     => $validated['gender'],
        //     'status'     => $validated['status'],
        // ]);

        return redirect()->route('sales_staff.index')
            ->with('success', 'Sales staff added successfully!');
    }

    /**
     * Edit Form
     */
    public function edit(SalesStaff $sales_staff)
    {
        return view('sales_staff.edit', compact('sales_staff'));
    }

    /**
     * Update Sales Staff
     */

    public function update(Request $request, SalesStaff $sales_staff)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'username'  => 'required|max:20|unique:sales_staff,username,' . $sales_staff->id,
            // 'phone'  => 'required|max:20|unique:sales_staff,phone,' . $sales_staff->id,
            'phone' => [
                'required',
                'max:20',
                'unique:sales_staff,phone,' . $sales_staff->id,
                new NotBlockedNumber,
            ],
            'email'  => 'nullable|email|unique:sales_staff,email,' . $sales_staff->id,
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:6',
        ]);

        if ($request->filled('password')) {
            $validated['plain_pswd'] = $validated['password'] = $request->password;
        }
        // ✅ remove password if empty
        // if (empty($validated['password'])) {
        //     unset($validated['password']);
        // } else {
        //     $validated['plain_pswd'] = $validated['password'];
        // }

        $sales_staff->update($validated);

        return redirect()->route('sales_staff.index')
            ->with('success','Sales staff updated successfully!');
    }

    public function updateold(Request $request, SalesStaff $sales_staff)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'phone'  => [
                'required','max:20',
                Rule::unique('sales_staff','phone')->ignore($sales_staff->id)
            ],
            'email'  => [
                'nullable','email',
                Rule::unique('sales_staff','email')->ignore($sales_staff->id)
            ],
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name'   => $validated['name'],
            'gender' => $validated['gender'],
            'phone'  => $validated['phone'],
            'email'  => $validated['email'],
            'status' => $validated['status'],
        ];

        if ($request->filled('password')) {
            $data['password']   = $validated['password'];
            $data['plain_pswd'] = $validated['password'];
        }

        $sales_staff->update($data);

        return redirect()->route('sales_staff.index')
            ->with('success','Sales staff updated successfully!');
    }

    /**
     * Soft Delete
     */
    public function destroy(SalesStaff $sales_staff)
    {
        $sales_staff->update(['status' => 'inactive']);
        $sales_staff->delete();

        return redirect()->route('sales_staff.index')
            ->with('success','Sales staff deleted successfully!');
    }

    public function inactiveAll()
    {
        SalesStaff::where('status', 'active')->update([
            'status' => 'inactive'
        ]);

        return redirect()->route('sales_staff.index')
            ->with('success', 'All sales staff have been set to inactive.');
    }
}
