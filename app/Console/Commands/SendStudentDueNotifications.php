<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Mail\StudentDueSummaryMail;
use App\Mail\StudentDueReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendStudentDueNotifications extends Command
{
    protected $signature = 'notify:student-dues';
    protected $description = 'Send admin a list of students whose fees are due or overdue';

    public function handle()
    {
         $today = Carbon::today();

        // 🔍 Students whose due date is TODAY AND pending_fees > 0
        $dueToday = Student::whereDate('next_due_date', $today)
            ->where('pending_fees', '>', 0)
            ->get();

        // 🔍 Students whose due date has PASSED AND pending_fees > 0
        $overdue = Student::whereDate('next_due_date', '<', $today)
            ->where('pending_fees', '>', 0)
            ->get();

        if ($dueToday->isEmpty() && $overdue->isEmpty()) {
            $this->info('No pending dues found.');
            return 0;
        }


        // $adminEmail = config('app.admin_email', 'admin@example.com');
        // $adminEmail = "mehlakrish077@yopmail.com";
        $adminEmail = "mehlakrish07@gmail.com";

        Mail::to($adminEmail)->send(
            new StudentDueSummaryMail($dueToday, $overdue)
        );


        // ✅ Send student emails (Due Today)
        foreach ($dueToday as $student) {
            if ($student->email_id) {
                Mail::to($student->email_id)
                    ->send(new StudentDueReminderMail($student, 'due_today'));
            }

             $this->info("Student mail SENT");
        }

        // ✅ Send student emails (Overdue)
        foreach ($overdue as $student) {
            if ($student->email_id) {
                Mail::to($student->email_id)
                    ->send(new StudentDueReminderMail($student, 'overdue'));
            }

             $this->info("Student mail SENT" );
        }


        $this->info("Email sent to admin: $adminEmail");
        return 0;
    }
}
