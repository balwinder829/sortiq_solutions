<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class HybridAccessMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->view('errors.unauthorized', [], 403);
        }

        // ✅ Legacy roles: Admin, Trainer, Sales
        if (in_array($user->role, [1, 2, 3])) {
            return $next($request);
        }

        // 🔐 Manager role (4) → permission-based
        if ($user->role == 4) {
            $hasPermission = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', 4)
                ->where('permissions.name', $permission)
                ->exists();

            if ($hasPermission) {
                return $next($request);
            }
        }

        // ❌ Block everyone else
        return response()->view('errors.unauthorized', [], 403);
    }
}
