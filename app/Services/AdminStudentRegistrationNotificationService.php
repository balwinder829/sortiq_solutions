<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Notifications\StudentRegisteredSummaryNotification;
use Carbon\Carbon;

class AdminStudentRegistrationNotificationService
{
    public function sendDailySummary()
    {
        $sessionId = session('admin_session_id');

        if (!$sessionId) {
            return;
        }

        $count = Student::whereDate('created_at', Carbon::today())
            // ->where('session', $sessionId)
            ->count();

        if ($count == 0) {
            return;
        }

        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {

            $exists = $admin->notifications()
                ->where('data->template_key', 'student.registered.summary')
                ->where('data->session_id', $sessionId)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($exists) continue;

            $admin->notify(new StudentRegisteredSummaryNotification($count, $sessionId));
        }
    }
}
