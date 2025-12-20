<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryOtpController extends Controller
{
    public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    // CHECK IF EMAIL MATCHES THE LOGGED-IN USER
    if ($request->email !== auth()->user()->email) {
        return response()->json([
            'status' => 'error',
            'message' => 'This email does not belong to your account.'
        ], 422);
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    session(['enquiry_otp_code' => $otp]);

    // Send email
    Mail::raw("Your admin verification OTP is: $otp", function ($msg) use ($request) {
        $msg->to($request->email)->subject("Admin Access OTP");
    });

    return response()->json(['status' => 'sent']);
}



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        if ($request->otp == session('enquiry_otp_code')) {

            // Mark OTP as verified
            session(['enquiry_otp_verified' => true]);

            // Remove OTP code
            session()->forget('enquiry_otp_code');

            return response()->json([
                'status' => 'verified'
            ]);
        }

        return response()->json([
            'status' => 'invalid',
            'message' => 'OTP is incorrect'
        ], 422);
    }
}
