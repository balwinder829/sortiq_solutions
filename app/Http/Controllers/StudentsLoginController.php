<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentsLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.students_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // include soft deleted
        $student = Student::withTrashed()
            ->where('sno', $request->username)
            ->first();
       
        // ❌ user not found
        if (!$student) {
            return back()->with('error', 'Invalid username or password');
        }

        // ❌ soft deleted
        if ($student->trashed()) {
            return back()->with('error', 'Your account has been deleted. Contact administration.');
        }

        // ❌ inactive status
        // if ($student->status === 'inactive') {
        //     return back()->with('error', 'Your account is inactive. Please contact administration.');
        // }
        // ❌ wrong password
        if (!Hash::check($request->password, $student->password)) {

            return back()->with('error', 'Invalid username or password');
        }

        // ✅ LOGIN EMPLOYEE
        Auth::guard('student')->login($student);
        // dd($student);
        // LogSystemActivity::log($request, 'login', null, 'trainer', $staff->id);
        return redirect()->route('students.dashboard');
        // $this->sendLoginNotification(
        //     $staff->name ?? $staff->username,
        //     LogSystemActivity::getClientIp($request),
        //     'trainer'
        // );
    }
     
 
}
