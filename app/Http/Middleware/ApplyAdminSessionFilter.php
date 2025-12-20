<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyAdminSessionFilter
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('admin_session_id')) {
            return redirect()->route('admin.login')->withErrors('Please select a session.');
        }

        return $next($request);
    }
}

