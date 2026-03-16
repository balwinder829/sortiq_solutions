<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $this->getClientIp($request);

        // Don't block localhost in local env (avoid locking yourself out)
        if (app()->environment('local') && in_array($ip, ['127.0.0.1', '::1'], true)) {
            return $next($request);
        }

        if (BlockedIp::where('ip_address', $ip)->exists()) {
            return response()->view('blocked-ip', [], 403);
        }

        return $next($request);
    }

    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            $ips = array_map('trim', explode(',', $forwarded));
            $clientIp = $ips[0] ?? '';
            if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP)) {
                return $clientIp;
            }
        }

        $realIp = $request->header('X-Real-IP');
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) {
            return $realIp;
        }

        return $request->ip() ?? '';
    }
}
