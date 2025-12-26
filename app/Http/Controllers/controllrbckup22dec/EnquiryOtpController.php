<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryOtpController extends Controller
{
    /**
     * Send / Resend OTP
     */
    public function sendOtp(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $otp = rand(100000, 999999);

        session([
            'enquiry_otp_code'       => $otp,
            'enquiry_otp_expires_at' => now()->addMinutes(2)->timestamp,
        ]);

        Mail::raw("Your admin verification OTP is: $otp", function ($msg) use ($user) {
            $msg->to($user->email)
                ->subject('Admin Access OTP');
        });

        return response()->json([
            'status'     => 'sent',
            'expires_in' => 120
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $expiresAt = session('enquiry_otp_expires_at');

        if (!$expiresAt || now()->timestamp > $expiresAt) {
            return response()->json([
                'status'  => 'expired',
                'message' => 'OTP has expired'
            ], 422);
        }

        if ($request->otp == session('enquiry_otp_code')) {

            session([
                'enquiry_otp_verified' => true
            ]);

            session()->forget([
                'enquiry_otp_code',
                'enquiry_otp_expires_at'
            ]);

            return response()->json([
                'status' => 'verified'
            ]);
        }

        return response()->json([
            'status'  => 'invalid',
            'message' => 'OTP is incorrect'
        ], 422);
    }
}
