<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanViewEnquiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $enquiry = $request->route('enquiry');

        if (!$enquiry) {
            return abort(404);
        }

        // Only assigned sales employee OR admin can view
        if (auth()->user()->role == 1) {
            return $next($request); // allow admin
        }

        if ($enquiry->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }

}
