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

    // If role = 1 send to their own email
    if ($user->role == 1) {
        $recipientEmail = $user->email;
    } 
    else {
        // Get responsible admin (username = admin AND role = 1)
        $admin = \App\Models\User::where('username', 'admin')
                    ->where('role', 1)
                    ->first();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Responsible admin not found'
            ]);
        }

        $recipientEmail = $admin->email;
    }

    // Generate OTP
    $otp = random_int(100000, 999999);

    // Expiry (2 minutes)
    $expiresAt = now()->addMinutes(2)->timestamp;

    // Store OTP in session
    session([
        'enquiry_otp_code'       => $otp,
        'enquiry_otp_expires_at' => $expiresAt,
    ]);

    // Send OTP
    Mail::raw("Your admin verification OTP is: $otp", function ($msg) use ($recipientEmail) {
        $msg->to($recipientEmail)
            ->subject('Admin Access OTP');
    });

    return response()->json([
        'status' => 'sent',
        'email'  => $recipientEmail,
        'expires_at' => $expiresAt,
        'expires_in' => 120
    ]);
}
    // public function sendOtp(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // Generate OTP
    //     $otp = rand(100000, 999999);

    //     // Expiry (2 minutes)
    //     $expiresAt = now()->addMinutes(2)->timestamp;

    //     // Get responsible admin (username = admin AND role = 1)
    //     $admin = \App\Models\User::where('username', 'admin')
    //                 ->where('role', 1)
    //                 ->first();

    //     if (!$admin) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Responsible admin not found'
    //         ]);
    //     }

    //     // Store in session
    //     session([
    //         'enquiry_otp_code'       => $otp,
    //         'enquiry_otp_expires_at' => $expiresAt,
    //     ]);

    //     // Send mail
    //     Mail::raw("Your admin verification OTP is: $otp", function ($msg) use ($user) {
    //         $msg->to($admin->email)
    //             ->subject('Admin Access OTP');
    //     });

    //     return response()->json([
    //         'status'      => 'sent',
    //         'expires_at'  => $expiresAt, // 🔥 IMPORTANT for timer
    //         'expires_in'  => 120
    //     ]);
    // }


    public function sendOtpold(Request $request)
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
