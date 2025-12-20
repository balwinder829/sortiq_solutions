<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class NotificationComposer
{
    public function compose(View $view)
    {
        $groupedUnreadCounts = collect();
        $unreadCount = 0;
        $latestNotifications = collect();

        if (Auth::check()) {

            // 🔢 Grouped unread count by template_key
            $groupedUnreadCounts = Auth::user()
                ->notifications()
                ->whereNull('read_at')
                ->select(
                    DB::raw("data->>'$.template_key' as template_key"),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('template_key')
                ->get()
                ->pluck('total', 'template_key');

            // 🔔 Total unread count
            $unreadCount = $groupedUnreadCounts->sum();

            // 🕔 Latest 5 unread notifications
            $latestNotifications = Auth::user()
                ->unreadNotifications()
                ->take(5)
                ->get();
        }

        $view->with([
            'groupedUnreadCounts' => $groupedUnreadCounts,
            'unreadCount'         => $unreadCount,
            'headerNotifications' => $latestNotifications,
        ]);
    }
}
