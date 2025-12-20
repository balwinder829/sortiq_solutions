<?php

namespace App\Http\Middleware;

use Closure;

class EnquiryOtpCheck
{
    public function handle($request, Closure $next)
    {
        if (!session('enquiry_otp_verified')) {

            // Share variable with all views to trigger popup
            view()->share('showOtpPopup', true);
        }

        return $next($request);
    }
}
