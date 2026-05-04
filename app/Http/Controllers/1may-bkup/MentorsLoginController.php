<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trainer;
use Illuminate\Support\Facades\Hash;

class MentorsLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.mentors_login');
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
        $staff = Trainer::withTrashed()
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
        Auth::guard('trainer')->login($staff);

        // LogSystemActivity::log($request, 'login', null, 'trainer', $staff->id);

        // $this->sendLoginNotification(
        //     $staff->name ?? $staff->username,
        //     LogSystemActivity::getClientIp($request),
        //     'trainer'
        // );
        $request->session()->regenerate();
        return redirect()->route('batches.mybatches');
    }
     
 
}
