<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {   
        $roles = Role::whereNotIn('name', ['Admin', 'Trainer'])->get();
        return view('employees.create', compact('roles'));
        
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'emp_code'      => 'required|unique:employees',
            'emp_name'      => 'required|string|max:100',
            'position'      => 'required|string|max:100',
            'joining_date'  => 'required|date',
            'role' => 'required|exists:roles,id',

            'username'      => 'required|string|max:30|unique:users',
            'email'         => 'required|email|unique:users',
            'phone'         => 'required|unique:users',
            'password'      => 'required|min:6',
             'dob' => 'nullable|date|before:today',
            'blood_group'  => 'nullable|string|max:5',
            'address'      => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['emp_name'],
                'username' => strtolower(trim($data['username'])),
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $data['password'],
                'role'     => $data['role'],
                'status'   => 'active',
            ]);

            Employee::create([
                'user_id'      => $user->id,
                'emp_code'     => $data['emp_code'],
                'emp_name'     => $data['emp_name'],
                'position'     => $data['position'],
                'joining_date' => $data['joining_date'],
                'dob'          => $data['dob'] ?? null,
                'blood_group'  => $data['blood_group'] ?? null,
                'address'      => $data['address'] ?? null,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee added successfully');
    }

    public function edit(Employee $employee)
    {   
        $roles = Role::whereNotIn('name', ['Admin', 'Trainer'])->get();
        return view('employees.edit', compact('employee' ,'roles'));
    }

    public function update(Request $request, Employee $employee)
{
    $data = $request->validate([
        // Employee fields
        'emp_name'     => 'required|string|max:100',
        'position'     => 'required|string|max:100',
        'joining_date' => 'required|date',
        'status'       => 'required|in:active,inactive,terminated',

        // User fields
        'username' => 'required|string|max:20|regex:/^[a-zA-Z0-9._-]+$/|unique:users,username,' . $employee->user_id,
        'email'    => 'required|email|unique:users,email,' . $employee->user_id,
        'phone'    => 'required|digits:10|unique:users,phone,' . $employee->user_id,
        'role'     => 'required|exists:roles,id',
        'dob'          => 'nullable|date|before:today',
        'blood_group'  => 'nullable|string|max:5',
        'address'      => 'nullable|string|max:255',
    ]);

    // 🔐 Prevent admin / trainer role assignment
    $role = \App\Models\Role::findOrFail($data['role']);
    if (in_array($role->name, ['admin', 'trainer'])) {
        abort(403, 'This role cannot be assigned.');
    }

    // 🔄 Map employee status → user status
    $userStatus = $data['status'] === 'active' ? 'active' : 'inactive';

    DB::transaction(function () use ($data, $employee, $userStatus) {

        // ✅ Update employee table
        $employee->update([
            'emp_name'     => $data['emp_name'],
            'position'     => $data['position'],
            'joining_date' => $data['joining_date'],
            'status'       => $data['status'],
            'dob'          => $data['dob'],
            'blood_group'  => $data['blood_group'],
            'address'      => $data['address'],
        ]);

        // ✅ Update users table (INCLUDING STATUS)
        $employee->user->update([
            'username' => $data['username'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'role'     => $data['role'],
            'status'   => $userStatus,
        ]);
    });

    return redirect()
        ->route('employees.index')
        ->with('success', 'Employee updated successfully');
}


    public function destroy(Employee $employee)
    {
        $employee->update(['status' => 'terminated']);
        $employee->user->update(['status' => 'inactive']);

        $employee->delete(); // soft delete

        return redirect()->route('employees.index')->with('success', 'Employee deleted');
    }
}

