<?php

// app/Http/Middleware/PermissionMiddleware.php
namespace App\Http\Middleware;

use Closure;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Services\PermissionResolver;

class PermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();


        // ❌ Block any role greater than 4
        // if ($user && $user->role > 4) {
        //     return response()->view('errors.unauthorized', [], 403);
        // }

        // ONLY Admin (1) and Manager (2)
        if (!in_array($user->role, [1, 4])) {

            return response()->view('errors.unauthorized', [], 403);
        }
        // dd($user->role);
        // Admin bypass
        if ($user->role == 1) {
            return $next($request);
        }

        // Trainer / Sales unaffected
        if ($user->role != 4) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $permissionName = PermissionResolver::resolve($routeName);

        \Log::info('ROLE CHECK', [
    'user_id' => $user->id ?? null,
    'role' => $user->role ?? null,
    'permission' => $permissionName
]);
        // No mapping = deny
        if (!$permissionName) {
            return response()->view('errors.unauthorized', [], 403);
        }

        $permissionId = Permission::where('name', $permissionName)->value('id');

        $allowed = RolePermission::where('role', 4)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$allowed) {
            return response()->view('errors.unauthorized', [], 403);
        }

        return $next($request);
    }
}
