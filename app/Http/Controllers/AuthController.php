<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentSession;

class AuthController extends Controller
{   
    public function showLogin()
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
