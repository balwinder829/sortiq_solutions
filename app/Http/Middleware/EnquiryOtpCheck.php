<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnquiryOtpCheck
{
    public function handle(Request $request, Closure $next)
    {
        // If OTP already verified → allow request
        if (session('enquiry_otp_verified')) {
            return $next($request);
        }

        // If OTP not sent yet → send OTP once
        if (!session()->has('enquiry_otp_code')) {
            app(\App\Http\Controllers\EnquiryOtpController::class)
                ->sendOtp($request);
        }

        // Show OTP popup and STOP further execution
        return response()->view('layouts.app', [
            'showOtpPopup' => true
        ]);
    }
}
