<?php

namespace App\Http\Middleware;

use App\Models\SystemActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogSystemActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
        // WordPress-style: we do NOT log every page view.
        // Only login/logout and explicit actions (user created, etc.) are logged from controllers.
    }

    /**
     * @param  int|null  $trainerId  Set when logging trainer login (guard = trainer).
     */
    public static function log(Request $request, string $action = 'page_view', ?int $userId = null, ?string $guard = null, ?int $trainerId = null): void
    {
        try {
            $user = $request->user();
            if ($user && $userId === null && $trainerId === null) {
                if (auth()->guard('trainer')->check()) {
                    $trainerId = $user->getAuthIdentifier();
                    $guard = 'trainer';
                } else {
                    $userId = $user->getAuthIdentifier();
                    $guard = 'web';
                }
            }
            if ($guard === null) {
                $guard = $user ? 'web' : 'guest';
            }

            SystemActivityLog::create([
                'user_id'    => $userId,
                'trainer_id' => $trainerId,
                'guard'      => $guard,
                'action'     => $action,
                'url'        => $request->fullUrl(),
                'method'     => $request->method(),
                'ip_address' => self::getClientIp($request),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Get the real client IP (works behind proxy/load balancer).
     * When behind nginx/Apache/load balancer, the server only sees the proxy IP;
     * the real visitor IP is in X-Forwarded-For or X-Real-IP.
     */
    public static function getClientIp(Request $request): ?string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            // Can be "client, proxy1, proxy2" – first is the real client
            $ips = array_map('trim', explode(',', $forwarded));
            $clientIp = $ips[0];
            if (!empty($clientIp) && filter_var($clientIp, FILTER_VALIDATE_IP)) {
                return $clientIp;
            }
        }

        $realIp = $request->header('X-Real-IP');
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) {
            return $realIp;
        }

        return $request->ip();
    }
}
