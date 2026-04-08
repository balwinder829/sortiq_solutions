<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\AdminWorkshopReminderNotification;

class AdminWorkshopReminderService
{
    public function sendReminders()
    {
        $today = Carbon::today();

        // 7 days before
        $weekDate = $today->copy()->addDays(7);

        // 2 days before
        $twoDaysDate = $today->copy()->addDays(2);

        // Find workshops
         // 🔥 TEMP: Get today's workshops only
        // $weekWorkshops = Workshop::all();
        $weekWorkshops = Workshop::whereDate('date', $weekDate)->get();
        $twoDayWorkshops = Workshop::whereDate('date', $twoDaysDate)->get();

        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {

            foreach ($weekWorkshops as $workshop) {
                $admin->notify(
                    new AdminWorkshopReminderNotification(
                        $workshop,
                        'workshop.reminder.week'
                    )
                );
            }

            foreach ($twoDayWorkshops as $workshop) {
                $admin->notify(
                    new AdminWorkshopReminderNotification(
                        $workshop,
                        'workshop.reminder.two_days'
                    )
                );
            }
        }

        \Log::info('Workshop Reminder Service Running...');
    }
}