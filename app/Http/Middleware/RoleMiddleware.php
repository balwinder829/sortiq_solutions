<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, $roles)) {
            return response()->view('errors.unauthorized', [], 403);
        }

        // =============================
        // ADMIN FEES PENDING NOTIFICATION
        // =============================
        if ($user->role == 1) { // Only admin gets fee summary
            try {
                app(\App\Services\AdminFeeNotificationService::class)->sendDailyFeeSummary();
            } catch (\Exception $e) {
                \Log::error("Fee Notification Error: " . $e->getMessage());
            }

            try {
                app(\App\Services\AdminBinNotificationService::class)->sendDailySummary();
            } catch (\Exception $e) {
                \Log::error("BIN Summary Notification Error: " . $e->getMessage());
            }

            try {
                app(\App\Services\AdminStudentRegistrationNotificationService::class)->sendDailySummary();
            } catch (\Exception $e) {
                \Log::error("Student Registration Notification Error: " . $e->getMessage());
            }

            try {
                app(\App\Services\AdminUpcomingEventNotificationService::class)
                    ->sendDailySummary();
            } catch (\Exception $e) {
                \Log::error("Upcoming Event Notification Error: " . $e->getMessage());
            }

        }

        return $next($request);
    }

}
