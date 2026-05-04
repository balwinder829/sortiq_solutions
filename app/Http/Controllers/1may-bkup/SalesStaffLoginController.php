<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesStaff;
use Illuminate\Support\Facades\Hash;

class SalesStaffLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.sale_staff_login');
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
        $staff = SalesStaff::withTrashed()
            ->where('username', $request->username)
            ->first();

        // ❌ user not found
        if (!$staff) {
            return back()->with('error', 'Invalid username or password');
        }

        // ❌ soft deleted
        if ($staff->trashed()) {
            return back()->with('error', 'Your account has been deleted. Contact administration.');
        }

        // ❌ inactive status
        if ($staff->status === 'inactive') {
            return back()->with('error', 'Your account is inactive. Please contact administration.');
        }
        // ❌ wrong password
        if (!Hash::check($request->password, $staff->password)) {

            return back()->with('error', 'Invalid username or password');
        }

        // ✅ LOGIN EMPLOYEE
        Auth::guard('sales_staff')->login($staff);

        // prevent session fixation
        $request->session()->regenerate();

        return redirect()->route('sales.dashboard');
    }
     
 
}
