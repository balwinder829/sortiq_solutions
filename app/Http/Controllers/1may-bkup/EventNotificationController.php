<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventNotification;
use Carbon\Carbon;

class EventNotificationController extends Controller
{
    public function dismiss($key)
    {
        EventNotification::where('notification_key', $key)->update([
            'dismissed' => true
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function list()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        return view('events.notifications.notifications', [
            'todayEvents' => Event::whereDate('event_date', $today)->get(),
            'tomorrowEvents' => Event::whereDate('event_date', $tomorrow)->get(),
            'upcomingEvents' => Event::whereDate('event_date', '>', $tomorrow)
                                    ->orderBy('event_date')
                                    ->get(),
        ]);
    }


    public function list1()
    {
        $today = \Carbon\Carbon::today();
        $tomorrow = \Carbon\Carbon::tomorrow();

        return view('admin.events.notifications', [
            'todayEvents' => Event::whereDate('event_date', $today)->get(),
            'tomorrowEvents' => Event::whereDate('event_date', $tomorrow)->get(),
            'upcomingEvents' => Event::whereDate('event_date', '>', $tomorrow)->orderBy('event_date')->get(),
        ]);
    }

}
