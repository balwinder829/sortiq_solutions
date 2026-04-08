<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // ✅ ADD THIS

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notify:student-dues')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground()
    // ->everyMinute(); // runs every minute (for testing)
    ->cron('0 9 * * *');   // ⬅ Runs every day at 9:00 AM

Schedule::command('events:send-reminders today')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground()
    // ->everyMinute(); // runs every minute (for testing)
    ->cron('0 9 * * *');   // ⬅ Runs every day at 9:00 AM

Schedule::command('events:send-reminders tomorrow')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground()
    // ->everyMinute(); // runs every minute (for testing)
    ->cron('0 9 * * *');   // ⬅ Runs every day at 9:00 AM

// Schedule::call(function () {
//     app(\App\Services\AdminWorkshopReminderService::class)
//         ->sendReminders();
// })
// ->name('workshop-reminder') 
// ->timezone('Asia/Kolkata')
// ->withoutOverlapping()
// ->runInBackground()
// ->everyMinute();


// Workshop Notifications
Schedule::call(function () {
    app(\App\Services\AdminWorkshopReminderService::class)
        ->sendReminders();
})
->name('workshop-reminder') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->everyMinute();
// ->cron('0 9 * * *');  

// Admin Fee Notification service
Schedule::call(function () {
   app(\App\Services\AdminFeeNotificationService::class)->sendDailyFeeSummary();
})
->name('admin-fee-reminder') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->cron('5 9 * * *'); 

// Admin Send to Bin Notification service
Schedule::call(function () {
    app(\App\Services\AdminBinNotificationService::class)->sendDailySummary();
})
->name('admin-send-to-bin-reminder') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->cron('10 9 * * *');  


// Admin Fee Notification service
Schedule::call(function () {
    app(\App\Services\AdminStudentRegistrationNotificationService::class)->sendDailySummary();
})
->name('admin-student-registration') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->cron('5 9 * * *'); 


// Admin Fee Notification service
Schedule::call(function () {
    app(\App\Services\AdminUpcomingEventNotificationService::class)->sendDailySummary();
})
->name('admin-upcoming-event') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->cron('15 9 * * *'); 


// Admin Fee Notification service
Schedule::call(function () {
    app(\App\Services\AdminTodayInterviewNotificationService::class)->sendTodaySummary();
})
->name('admin-today-interview') 
->timezone('Asia/Kolkata')
->withoutOverlapping()
->cron('55 8 * * *'); 

Schedule::command('queue:work --stop-when-empty')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground()
    ->cron('* * * * *'); // every minute