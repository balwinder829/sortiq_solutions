<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
