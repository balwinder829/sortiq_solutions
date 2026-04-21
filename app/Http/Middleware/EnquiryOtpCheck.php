<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnquiryOtpCheck
{
    // public function handle(Request $request, Closure $next)
    // {
    //     // If OTP already verified → allow request
    //     if (session('enquiry_otp_verified')) {
    //         return $next($request);
    //     }

    //     // If OTP not sent yet → send OTP once
    //     // if (!session()->has('enquiry_otp_code')) {
    //     //     app(\App\Http\Controllers\EnquiryOtpController::class)
    //     //         ->sendOtp($request);
    //     // }

    //     // Show OTP popup and STOP further execution
    //     return response()->view('layouts.app', [
    //         'showOtpPopup' => true
    //     ]);
    // }

    public function handle(Request $request, Closure $next)
{
    $user = auth()->user();

    if ($user) {

        // ✅ Check admin role
        $isAdmin = $user->role == 1;

        // ✅ Allowed IPs
        $allowedIps = [
            '122.173.27.170',
            '127.0.0.1'
        ];

        $currentIp = $request->ip();

        $isAllowedIp = in_array($currentIp, $allowedIps);

        // ✅ FINAL CONDITION
        if ($isAdmin && $isAllowedIp) {
            return $next($request); // 🚀 SKIP OTP
        }
    }

    // ✅ Already verified OTP
    if (session('enquiry_otp_verified')) {
        return $next($request);
    }

    // ❌ Show OTP popup
    return response()->view('layouts.app', [
        'showOtpPopup' => true
    ]);
}
}
