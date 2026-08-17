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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate; // add at top with other uses
use App\Models\Helpdesk\HelpdeskTechnology;
use App\Models\Notification;


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

    Gate::before(function ($user) {
        return $user->hasRole('Admin') ? true : null;
    });
    
    URL::forceScheme('https');
     if (app()->environment('production')) {
        // URL::forceScheme('https');
    }
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VIEW COMPOSER (EXISTING)
    |--------------------------------------------------------------------------
    */
    View::composer('*', function ($view) {

        // get list of all sessions ordered by start_date
        // $sessions = \App\Models\StudentSession::orderBy('start_date', 'desc')->get();
        // $sessions = \App\Models\StudentSession::withCount('students')->orderBy('start_date', 'desc')->get();

        $sessions = \App\Models\StudentSession::withoutGlobalScopes()
        ->withCount('students')
        ->whereIn('session_type', [0, 1])
        ->orderBy('start_date', 'desc')
        ->get();

        // find active session (if selected)
        $currentSession = null;

        if (session()->has('admin_session_id')) {
            $currentSession = \App\Models\StudentSession::find(session('admin_session_id'));
        }

        if (!$currentSession) {

            $currentSession = \App\Models\StudentSession::withoutGlobalScopes()
                ->where('session_type', 0)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->orderBy('start_date', 'desc')
                ->first();


            if ($currentSession) {

                session([
                    'admin_session_id' => $currentSession->id,
                ]);

            }

        }

        /*
|--------------------------------------------------------------------------
| SPECIAL HEADER SESSION SYSTEM
|--------------------------------------------------------------------------
*/

$isSpecialSessionRoute = request()->routeIs(
    'enquiries.*',
    'admin.manual_data.*',
    'admin.hard_data.*',
    'admin.enquiries.*',
);


/*
|--------------------------------------------------------------------------
| NORMAL HEADER SESSIONS
|--------------------------------------------------------------------------
| session_type = 0
*/

$headerNormalSessions = \App\Models\StudentSession::withoutGlobalScopes()
    ->withCount('students')
    ->where('session_type', 0)
    ->whereNull('deleted_at')
    ->orderBy('start_date', 'desc')
    ->get();


/*
|--------------------------------------------------------------------------
| SALE HEADER SESSIONS
|--------------------------------------------------------------------------
| session_type = 1
*/

$headerSaleSessions = \App\Models\StudentSession::withoutGlobalScopes()
    ->withCount('students')
    ->where('session_type', 1)
    ->whereNull('deleted_at')
    ->orderBy('start_date', 'desc')
    ->get();


/*
|--------------------------------------------------------------------------
| HEADER SESSION LIST
|--------------------------------------------------------------------------
*/

if ($isSpecialSessionRoute) {

    /*
    | Enquiry / Manual Data / Hard Data
    | Show NORMAL + SALE
    */

    $headerSessions = $headerNormalSessions
        ->concat($headerSaleSessions)
        ->sortByDesc('start_date')
        ->values();

} else {

    /*
    | Other modules
    | Show NORMAL only
    */

    $headerSessions = $headerNormalSessions;
}


/*
|--------------------------------------------------------------------------
| HEADER CURRENT SESSION
|--------------------------------------------------------------------------
*/

// $headerCurrentSession = $currentSession;


/*
|--------------------------------------------------------------------------
| LAST USED SALE SESSION
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| CURRENT HEADER SESSION
|--------------------------------------------------------------------------
*/

$headerCurrentSession = $currentSession;


if ($isSpecialSessionRoute) {

    /*
    | On Enquiry / Manual / Hard,
    | show exactly what the user selected.
    */

    if (session()->has('admin_header_session_id')) {

        $headerCurrentSession =
            \App\Models\StudentSession::withoutGlobalScopes()
                ->where('id', session('admin_header_session_id'))
                ->whereNull('deleted_at')
                ->first();

    }


    /*
    | If there is no special-module selection yet,
    | fall back to normal session.
    */

    if (!$headerCurrentSession) {

        $headerCurrentSession = $currentSession;

    }

}
// if (
//     $isSpecialSessionRoute &&
//     session()->has('admin_sale_session_id')
// ) {

//     $saleSession = \App\Models\StudentSession::withoutGlobalScopes()
//         ->where('id', session('admin_sale_session_id'))
//         ->where('session_type', 1)
//         ->first();

//     if ($saleSession) {
//         $headerCurrentSession = $saleSession;
//     }
// }

        // 🔥 HELP DESK CATEGORIES
        $helpdeskCategories = HelpdeskTechnology::all();

        // share with all views
        $view->with([
            'sessions' => $sessions,
            'currentSession' => $currentSession,
            'headerSessions' => $headerSessions,
            'headerCurrentSession' => $headerCurrentSession,
            'isSpecialSessionRoute' => $isSpecialSessionRoute,
            'helpdeskCategories' => $helpdeskCategories
        ]);
        // share with all views
        // $view->with('sessions', $sessions)
        //      ->with('currentSession', $currentSession);
    });

    /*
    |--------------------------------------------------------------------------
    | HEADER NOTIFICATION COMPOSER (NEW)
    |--------------------------------------------------------------------------
    */
    // View::composer('layouts.header', function ($view) {
   View::composer(['layouts.header','layouts.students.header'], function ($view) {

            $notifications = collect();
            $unreadCount = 0;

            if (Auth::check()) {

                $activeSessionNo = session('admin_session_id');

                $raw = Notification::where('notifiable_id', Auth::id())
                    ->where('notifiable_type', \App\Models\User::class)
                    ->whereNull('read_at')
                    ->when($activeSessionNo, function ($q) use ($activeSessionNo) {
                        $q->where(function ($query) use ($activeSessionNo) {
                            $query->where('session_id', $activeSessionNo)
                                  ->orWhereNull('session_id'); // global notifications
                        });
                    })
                    ->latest()
                    ->get();

                // Group by template_key
                $notifications = $raw
                    ->groupBy(function ($n) {
                        return $n->data['template_key'] ?? 'general';
                    })
                    ->map(function ($group) {
                        return [
                            'notification' => $group->first(),
                            'count'        => $group->count(),
                        ];
                    })
                    ->values();

                $unreadCount = $raw->count();
            }

            $view->with([
                'notifications' => $notifications,
                'unreadCount'   => $unreadCount,
            ]);
        });


   

}



}
