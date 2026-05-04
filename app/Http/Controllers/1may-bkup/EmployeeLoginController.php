<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.employee_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        
        //clear old login user from any type
        Auth::guard('web')->logout();
        Auth::guard('trainer')->logout();
        Auth::guard('employee')->logout();
        Auth::guard('sales_staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // include soft deleted
        $employee = Employee::withTrashed()
            ->where('username', $request->username)
            ->first();

        // ❌ user not found
        if (!$employee) {
            return back()->with('error', 'Invalid username or password');
        }

        // ❌ soft deleted
        if ($employee->trashed()) {
            return back()->with('error', 'Your account has been deleted. Contact administration.');
        }

        // ❌ inactive status
        if ($employee->status === 'inactive') {
            return back()->with('error', 'Your account is inactive. Please contact administration.');
        }
        // ❌ wrong password
        if (!Hash::check($request->password, $employee->password)) {

            return back()->with('error', 'Invalid username or password');
        }


        // ✅ LOGIN EMPLOYEE
        Auth::guard('employee')->login($employee);

        // prevent session fixation
        $request->session()->regenerate();

        return redirect()->route('attendance.employee');
    }
    public function login2(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = Employee::withTrashed()
            ->where('username', $request->username)
            ->first();

        if ($user) {

            if ($user->trashed()) {
                return back()->with('error', 'Your account has been deleted. Contact administration.');
            }

            if ($user->status === 'inactive') {
                return back()->with('error', 'Your account is inactive. Please contact administration.');
            }

            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Invalid username or password');
            }

            Auth::guard('employee')->login($user);

             
            $request->session()->regenerate();

            
                return redirect()->route('attendance.employee');
            
        }
        // $credentials = $request->only('email','password');

        // if (Auth::guard('employee')->attempt($credentials)) {
        //     return redirect()->route('attendance.employee');
        // }

        // return back()->withErrors([
        //     'email' => 'Invalid credentials'
        // ]);
    }
 
}
