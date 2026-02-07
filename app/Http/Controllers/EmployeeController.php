<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use Carbon\Carbon;
// use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Role as SpatieRole;


class EmployeeController extends Controller
{   
    use PdfLayoutTrait;
    protected string $permissionPrefix = 'employees';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

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
     $request->merge([
        'emp_code' => strtoupper(trim($request->emp_code)),
        'username' => strtolower(trim($request->username)), // already good
    ]);

    $data = $request->validate([
        // 'emp_code'      => 'required|unique:employees',
        'emp_code' => [
            'required',
            'regex:/^SS-\d+$/',
            'unique:employees,emp_code',
        ],
        'emp_name'      => 'required|string|max:100',
        'position'      => 'required|string|max:100',
        'employment_type' => 'required',
        'joining_date'  => 'required|date',
        'role'          => 'required|exists:roles,id',

        'username'      => 'required|string|max:30|unique:users',
        'email'         => 'required|email|unique:users',
        'phone'         => 'required|unique:users',
        'password'      => 'required|min:6',

        'dob'           => 'nullable|date|before:today',
        'blood_group'   => 'nullable|string|max:5',
        'address'       => 'nullable|string|max:255',
        'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'alternative_phone'         => 'required',
        'probation_period' => 'required',
    ]);

    // 📸 Handle photo BEFORE transaction
    $photoName = null;
    if ($request->hasFile('photo')) {
        $dir = public_path('images/employee_images');

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $photoName = time() . '_' . $request->photo->getClientOriginalName();
        $request->photo->move($dir, $photoName);
    }

    DB::transaction(function () use ($data, $photoName) {

        $user = User::create([
            'name'     => $data['emp_name'],
            'username' => strtolower(trim($data['username'])),
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => trim($data['password']),
            'role'     => $data['role'],
            'status'   => 'active',
        ]);


        // ✅ SPATIE ROLE SYNC (ONLY ADMIN & MANAGER)
        if (in_array($data['role'], [1, 4])) {
            $roleName = SpatieRole::where('id', $data['role'])->value('name');

            if ($roleName) {
                $user->syncRoles([$roleName]); // replaces any old role
            }
        } else {
            // ensure no spatie roles for fixed roles
            $user->syncRoles([]);
        }

        Employee::create([
            'user_id'      => $user->id,
            'emp_code'     => $data['emp_code'],
            'emp_name'     => $data['emp_name'],
            'position'     => $data['position'],
            'employment_type'     => $data['employment_type'],
            'joining_date' => $data['joining_date'],
            'dob'          => $data['dob'] ?? null,
            'blood_group'  => $data['blood_group'] ?? null,
            'address'      => $data['address'] ?? null,
            'photo'        => $photoName, // ✅ only filename
            'alternative_phone'     => $data['alternative_phone'],
            'probation_period'     => $data['probation_period'],
            'emp_pswd'     => trim($data['password']),
        ]);
    });

    return redirect()
        ->route('employees.index')
        ->with('success', 'Employee added successfully');
}

     
    public function edit(Employee $employee)
    {   
        $roles = Role::whereNotIn('name', ['Admin', 'Trainer'])->get();
        return view('employees.edit', compact('employee' ,'roles'));
    }

    public function update(Request $request, Employee $employee)
{

    $request->merge([
        'emp_code' => strtoupper(trim($request->emp_code)),
        'username' => strtolower(trim($request->username)), // already good
    ]);

    if ($request->filled('password')) {
    $request->merge([
        'password' => $request->password
    ]);
}

    $data = $request->validate([
        // Employee fields
        'emp_name'     => 'required|string|max:100',
        'position'     => 'required|string|max:100',
        'probation_period' => 'required',
        'employment_type' => 'required',
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
        'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'alternative_phone'         => 'required',
        'password' => 'nullable|min:6',
    ]);


    // 🔐 Prevent admin / trainer role assignment
    $role = \App\Models\Role::findOrFail($data['role']);
    if (in_array(strtolower($role->name), ['admin', 'trainer'])) {
        abort(403, 'This role cannot be assigned.');
    }

    // 🔄 Map employee status → user status
    $userStatus = $data['status'] === 'active' ? 'active' : 'inactive';

    // 📸 Handle photo BEFORE transaction
    $photoName = $employee->photo; // keep existing by default

    if ($request->hasFile('photo')) {

        $dir = public_path('images/employee_images');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // delete old photo if exists
        if ($employee->photo && file_exists($dir.'/'.$employee->photo)) {
            unlink($dir.'/'.$employee->photo);
        }

        $photoName = time() . '_' . $request->photo->getClientOriginalName();
        $request->photo->move($dir, $photoName);
    }

    DB::transaction(function () use ($data, $employee, $userStatus, $photoName,$request) {

         $empData = [
            'emp_name'     => $data['emp_name'],
            'position'     => $data['position'],
            'employment_type'     => $data['employment_type'],
            'alternative_phone'     => $data['alternative_phone'],
            'probation_period'     => $data['probation_period'],
            'joining_date' => $data['joining_date'],
            'status'       => $data['status'],
            'dob'          => $data['dob'],
            'blood_group'  => $data['blood_group'],
            'address'      => $data['address'],
            'photo'        => $photoName, // ✅ filename only
        ];

        if ($request->filled('password')) {
            $empData['emp_pswd'] = $request->password;
        }

        $employee->update($empData);

        // UPDATE User table
        $userData = [
            'username' => $data['username'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'role'     => $data['role'],
            'status'   => $userStatus,
        ];

        if ($request->filled('password')) {
            $userData['password'] = $request->password;
        }

        $employee->user->update($userData);

         // 🔁 SPATIE ROLE SYNC (ONLY ADMIN & MANAGER)
        if (in_array($data['role'], [1, 4])) {

            $roleName = SpatieRole::where('id', $data['role'])->value('name');

            if ($roleName) {
                $employee->user->syncRoles([$roleName]);
            }

        } else {
            // Remove spatie roles for fixed-role users
            $employee->user->syncRoles([]);
        }


    });

    return redirect()
        ->route('employees.index')
        ->with('success', 'Employee updated successfully');
}

    public function update29dec(Request $request, Employee $employee)
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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🔐 Prevent admin / trainer role assignment
        $role = \App\Models\Role::findOrFail($data['role']);
        if (in_array($role->name, ['admin', 'trainer'])) {
            abort(403, 'This role cannot be assigned.');
        }

        // 🔄 Map employee status → user status
        $userStatus = $data['status'] === 'active' ? 'active' : 'inactive';

        DB::transaction(function () use ($data, $employee, $userStatus) {

             if ($request->hasFile('photo')) {
                $dir = public_path('images/employee_images');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move($dir, $photoName);
                $employee->photo = 'images/employee_images/'.$photoName;
            }

            // ✅ Update employee table
            $employee->update([
                'emp_name'     => $data['emp_name'],
                'position'     => $data['position'],
                'joining_date' => $data['joining_date'],
                'status'       => $data['status'],
                'dob'          => $data['dob'],
                'blood_group'  => $data['blood_group'],
                'address'      => $data['address'],
                'photo'        => $employee->photo,
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

     /* ================= ID CARD ================= */

    private function generateIdCard(Employee $employee): string
    {
        $mpdf = new Mpdf([
            'format' => [54, 85.6],
            // 'format' => [85.6, 54],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_footer' => 0,
        ]);

        $bg = public_path('images/employee_id_card_images/id-card-bac-shape.png');

       
        $html = View::make('employees.id-card-pdf', compact('employee'))->render();
        
        $mpdf->WriteHTML($html);
         // ✅ Footer ONLY on last (odd) page
        $mpdf->SetHTMLFooter(
            '
            <div style="
                background:url(' . $bg . ') no-repeat center;
                background-size:cover;
                width:100%;
                height:100px;
            ">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="right" style="font-size:11px; padding-right:5px;">
                            Issuing Authority
                        </td>
                    </tr>
                </table>
            </div>
            ',
            'O'
        );

        // return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return $mpdf->Output('', 'S');
    }

    public function downloadIdCard(Employee $employee)
    {
        $pdf = $this->generateIdCard($employee);
        $name = strtoupper(str_replace(' ', '_', $employee->emp_name));

        // return response(
        //     $this->generateIdCard($employee),
        //     200,
        //     [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => 'inline; filename="id-card.pdf"',
        //     ]
        // );
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="ID_CARD_'.$name.'.pdf"');
    }

    public function emailIdCard(Employee $employee)
    {
        $pdf = $this->generateIdCard($employee);

        Mail::send([], [], function ($message) use ($employee, $pdf) {
            $message->to($employee->user->email)
                ->subject('Employee ID Card')
                ->attachData($pdf, 'ID_CARD_'.$employee->emp_code.'.pdf');
        });

        return back()->with('success', 'ID Card emailed successfully');
    }
}

