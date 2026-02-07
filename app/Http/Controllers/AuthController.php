<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentSession;
use App\Models\Trainer;
 

class AuthController extends Controller
{   
    public function showLogin()
    {
        // 🔹 If ADMIN / USER logged in
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->role == 1) {

                return redirect()->route('dashboard');
            } elseif ($user->role == 3) {
                return redirect()->route('sales.dashboard');
            } elseif ($user->role == 2) {
                return redirect()->route('batches.mybatches');
            } else {
                return redirect()->route('attendance.employee');
            }
        }

        // 🔹 If TRAINER logged in
        if (Auth::guard('trainer')->check()) {
            return redirect()->route('attendance.employee');
        }

        // 🔹 Show login page
        $sessions = StudentSession::orderBy('start_date', 'desc')->get();
        return view('auth.login', compact('sessions'));
    }

    public function showLogin27()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role == 1) {
                return redirect()->route('dashboard'); // admin
            }else if ($user->role == 3) {
                return redirect()->route('sales.dashboard');
            } elseif ($user->role == 2) {
                return redirect()->route('batches.mybatches');
            }else{
                return redirect()->route('attendance.employee');
            }

            
        }

        $sessions = StudentSession::orderBy('start_date', 'desc')->get();
        return view('auth.login', compact('sessions'));
    }

    public function showLoginold()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // $sessions = StudentSession::all();
        // $sessions = StudentSession::orderBy('session_year', 'desc')
        //     ->orderByRaw("FIELD(session_month, 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')")
        //     ->get();

         $sessions = StudentSession::orderBy('start_date', 'desc')->get();
        return view('auth.login', compact('sessions'));
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);

    //     $user = User::where('username', $request->username)->first();

    //     if ($user && Hash::check($request->password, $user->password)) {
    //         Auth::login($user);
    //         auth()->user()->update([
    //             'last_login' => now(),
    //         ]);
    //         $request->session()->regenerate();
    //         return redirect()->route('dashboard')->with('success', 'Login successful');
    //     }

    //     return back()->with('error', 'Invalid username or password');
    // }

   
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ TRY EMPLOYEE / ADMIN / SALES (USERS TABLE)
        |--------------------------------------------------------------------------
        */
        $user = User::withTrashed()
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

            // session validation for admin & role 4
            if (in_array($user->role, [1, 4])) {
                if (!$request->session_id || !StudentSession::where('id', $request->session_id)->exists()) {
                    return back()->with('error', 'Please choose a valid session');
                }
                session(['admin_session_id' => $request->session_id]);
            }

            Auth::guard('web')->login($user);

            // 🔥 SYNC OLD ROLE → SPATIE ROLE (SAFE)
            $this->syncSpatieRole($user);

            $user->update(['last_login' => now()]);
            $request->session()->regenerate();

            // redirects (UNCHANGED)
            if ($user->role == 3) {
                return redirect()->route('sales.dashboard');
            } elseif ($user->role == 2) {
                // return redirect()->route('batches.mybatches');
            } elseif (in_array($user->role, [1, 4])) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('attendance.employee');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ TRY TRAINER (TRAINERS TABLE)
        |--------------------------------------------------------------------------
        */
        $trainer = Trainer::where('username', $request->username)->first();
        // dd($trainer);
        if (!$trainer) {
            return back()->with('error', 'Invalid username or password');
        }

        if ($trainer->status === 'inactive') {
            return back()->with('error', 'Your trainer account is inactive. Contact administration.');
        }

        if (!Hash::check($request->password, $trainer->password)) {
            return back()->with('error', 'Invalid username or password');
        }
        // dd($trainer);
        // Trainer login
        Auth::guard('trainer')->login($trainer);
        $request->session()->regenerate();

        // Trainer redirect (attendance or trainer dashboard)
        return redirect()->route('batches.mybatches');
        // return redirect()->route('attendance.employee')
            // ->with('success', 'Login successful');
    }

    private function syncSpatieRole($user)
    {
        $map = [
            1 => 'Admin',
            2 => 'Trainer',
            3 => 'Sales',
            4 => 'Manager',
            5 => 'HR',
            6 => 'Employee',
        ];

        if (isset($map[$user->role])) {
            $roleName = $map[$user->role];

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }
    }

    public function login27jan(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            // 'session_id' => 'required|exists:student_sessions,id',
        ]);

        // $user = User::where('username', $request->username)->first();
        $user = User::withTrashed()->where('username', $request->username)->first();


        // If user not found → invalid
        if (!$user) {
            return back()->with('error', 'Invalid username or password');
        }

        if ($user->trashed()) {
            return back()->with('error', 'Your account has been deleted. Contact administration.');
        }


        // 🔥 BLOCK INACTIVE ACCOUNTS
        if ($user->status === 'inactive') {
            return back()->with('error', 'Your account is inactive. Please contact administration to activate your account.');
        }

        // If user exists but password is wrong
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid username or password');
        }

       // if ($user->role === 1) {
       //      $request->validate([
       //          'session_id' => 'required|exists:student_sessions,id',
       //      ], [
       //          'session_id.required' => 'Please choose a session',
       //          'session_id.exists' => 'The selected session is invalid',
       //      ]);
       //  }

        if ($user->role == 1 || $user->role == 4) {
            if (!$request->session_id || !StudentSession::where('id', $request->session_id)->exists()) {
                return back()->with('error', 'Please choose a valid session');
            }
        }


        session(['admin_session_id' => $request->session_id]);
        // If account is active and password is correct
        Auth::login($user);

        // Update login time
        $user->update([
            'last_login' => now(),
        ]);

        $request->session()->regenerate();

        if ($user->role == 3) {
            return redirect()->route('sales.dashboard')->with('success', 'Login successful');
        }else if($user->role == 2){
            return redirect()->route('batches.mybatches')->with('success', 'Login successful');
        }else if($user->role == 1){
            return redirect()->route('dashboard')->with('success', 'Login successful');
        }else if($user->role == 4){
            return redirect()->route('dashboard')->with('success', 'Login successful');
        }else{
            return redirect()->route('attendance.employee');
        }
        
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->password = Hash::make($request->password); // bcrypt
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account created and logged in');
    }

    public function logout(Request $request)
    {
        // clear OTP/session stuff (unchanged)
        $request->session()->forget([
            'enquiry_otp_verified',
            'enquiry_otp_code',
            'enquiry_otp_expires_at'
        ]);

        // logout both guards safely
        Auth::guard('web')->logout();
        Auth::guard('trainer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    public function logout27jan(Request $request)
    {   
         // Explicitly remove OTP-related session values
        $request->session()->forget([
            'enquiry_otp_verified',
            'enquiry_otp_code',
            'enquiry_otp_expires_at'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
