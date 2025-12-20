<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StudentSession;
use Illuminate\Support\Facades\View;

 
use Illuminate\Support\Facades\Blade;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
 

    // public function boot()
    // {
    //     View::composer('*', function ($view) {

    //         // get list of all sessions ordered by start_date
    //         $sessions = StudentSession::orderBy('start_date', 'desc')->get();

    //         // find active session (if selected)
    //         $currentSession = null;

    //         if (session()->has('admin_session_id')) {
    //             $currentSession = StudentSession::find(session('admin_session_id'));
    //         }

    //         // share with all views
    //         $view->with('sessions', $sessions)
    //              ->with('currentSession', $currentSession);
    //     });
    // }


        // public function boot()
        // {
        //     /*
        //     |--------------------------------------------------------------------------
        //     | GLOBAL VIEW COMPOSER (existing)
        //     |--------------------------------------------------------------------------
        //     */
        //     View::composer('*', function ($view) {

        //         // get list of all sessions ordered by start_date
        //         $sessions = StudentSession::orderBy('start_date', 'desc')->get();

        //         // find active session (if selected)
        //         $currentSession = null;

        //         if (session()->has('admin_session_id')) {
        //             $currentSession = StudentSession::find(session('admin_session_id'));
        //         }

        //         // share with all views
        //         $view->with('sessions', $sessions)
        //              ->with('currentSession', $currentSession);
        //     });

        //     /*
        //     |--------------------------------------------------------------------------
        //     | MANAGER PERMISSION BLADE DIRECTIVE (NEW)
        //     |--------------------------------------------------------------------------
        //     */
        //     Blade::if('canperm', function ($permission) {
        //         $user = auth()->user();

        //         // Admin: full access
        //         if ($user && $user->role == 1) {
        //             return true;
        //         }

        //         // Only manager is permission-based
        //         if (!$user || $user->role != 4) {
        //             return false;
        //         }

        //         $permissionId = Permission::where('name', $permission)->value('id');

        //         if (!$permissionId) {
        //             return false;
        //         }

        //         return RolePermission::where('role', 4)
        //             ->where('permission_id', $permissionId)
        //             ->exists();
        //     });


        //     Blade::if('cananyperm', function (...$permissions) {
        //         $user = auth()->user();

        //         if ($user && $user->role == 1) {
        //             return true;
        //         }

        //         if (!$user || $user->role != 4) {
        //             return false;
        //         }

        //         foreach ($permissions as $permission) {
        //             $permissionId = \App\Models\Permission::where('name', $permission)->value('id');
        //             if ($permissionId &&
        //                 \App\Models\RolePermission::where('role', 4)
        //                     ->where('permission_id', $permissionId)
        //                     ->exists()
        //             ) {
        //                 return true;
        //             }
        //         }

        //         return false;
        //     });

        // }

    public function boot()
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VIEW COMPOSER (EXISTING)
    |--------------------------------------------------------------------------
    */
    View::composer('*', function ($view) {

        // get list of all sessions ordered by start_date
        $sessions = \App\Models\StudentSession::orderBy('start_date', 'desc')->get();

        // find active session (if selected)
        $currentSession = null;

        if (session()->has('admin_session_id')) {
            $currentSession = \App\Models\StudentSession::find(session('admin_session_id'));
        }

        // share with all views
        $view->with('sessions', $sessions)
             ->with('currentSession', $currentSession);
    });

    /*
    |--------------------------------------------------------------------------
    | HEADER NOTIFICATION COMPOSER (NEW)
    |--------------------------------------------------------------------------
    */
    View::composer('layouts.header', function ($view) {

    $notifications = collect();
    $unreadCount = 0;

    if (\Illuminate\Support\Facades\Auth::check()) {

        $raw = \Illuminate\Support\Facades\Auth::user()
            ->unreadNotifications()
            ->get();

        // 🔥 GROUP BY template_key (CONFIRMED FROM LOG)
        $notifications = $raw
            ->groupBy(function ($n) {
                return $n->data['template_key'];
            })
            ->map(function ($group) {
                return [
                    'notification' => $group->first(), // representative
                    'count'        => $group->count(), // total per template
                ];
            })
            ->values(); // 🔥 IMPORTANT: reindex for Blade

        // total unread
        $unreadCount = $raw->count();
    }

    $view->with([
        'notifications' => $notifications,
        'unreadCount'   => $unreadCount,
    ]);
});


    /*
    |--------------------------------------------------------------------------
    | MANAGER PERMISSION BLADE DIRECTIVE
    |--------------------------------------------------------------------------
    */
    Blade::if('canperm', function ($permission) {
        $user = auth()->user();

        // Admin: full access
        if ($user && $user->role == 1) {
            return true;
        }

        // Only manager is permission-based
        if (!$user || $user->role != 4) {
            return false;
        }

        $permissionId = \App\Models\Permission::where('name', $permission)->value('id');

        if (!$permissionId) {
            return false;
        }

        return \App\Models\RolePermission::where('role', 4)
            ->where('permission_id', $permissionId)
            ->exists();
    });

    /*
    |--------------------------------------------------------------------------
    | MANAGER MULTI-PERMISSION BLADE DIRECTIVE
    |--------------------------------------------------------------------------
    */
    Blade::if('cananyperm', function (...$permissions) {
        $user = auth()->user();

        if ($user && $user->role == 1) {
            return true;
        }

        if (!$user || $user->role != 4) {
            return false;
        }

        foreach ($permissions as $permission) {
            $permissionId = \App\Models\Permission::where('name', $permission)->value('id');

            if ($permissionId &&
                \App\Models\RolePermission::where('role', 4)
                    ->where('permission_id', $permissionId)
                    ->exists()
            ) {
                return true;
            }
        }

        return false;
    });
}



}
