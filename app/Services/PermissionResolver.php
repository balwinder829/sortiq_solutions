<?php

namespace App\Services;

class PermissionResolver
{
    public static function resolve(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        return match (true) {

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD & ANALYTICS
            |--------------------------------------------------------------------------
            */
            // $routeName === 'dashboard' =>
            //     'dashboard.view',

            str_starts_with($routeName, 'analytics.') =>
                'analytics.view',

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATIONS
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'notifications.') =>
                'notifications.view',

            /*
            |--------------------------------------------------------------------------
            | ENQUIRIES
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'enquiries.') =>
                'enquiries.view',

            str_contains($routeName, 'assign') =>
                'enquiries.assign',

            str_contains($routeName, 'followup') =>
                'enquiries.followup',

            str_contains($routeName, 'convert') =>
                'enquiries.convert',

            str_contains($routeName, 'import') =>
                'enquiries.import',

            str_contains($routeName, 'pipeline') =>
                'enquiries.pipeline',

            str_contains($routeName, 'dashboard') &&
            str_contains($routeName, 'enquiries') =>
                'enquiries.dashboard',

            /*
            |--------------------------------------------------------------------------
            | SALES / CALLS
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'calls') =>
                'calls.view',

            str_starts_with($routeName, 'salespersons.') =>
                'salespersons.view',

            str_contains($routeName, 'performance') =>
                'sales.performance',

            str_contains($routeName, 'assignments') =>
                'assignments.view',

            /*
            |--------------------------------------------------------------------------
            | STUDENTS
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'students.') =>
                'students.view',

            str_contains($routeName, 'confirm') =>
                'students.confirm',

            str_contains($routeName, 'download') =>
                'students.download',

            str_contains($routeName, 'certificate') =>
                'students.certificate',

            /*
            |--------------------------------------------------------------------------
            | SESSIONS / COURSES / COLLEGES
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'sessions.') =>
                'sessions.manage',

            str_starts_with($routeName, 'courses.') =>
                'courses.manage',

            str_starts_with($routeName, 'colleges.') =>
                'colleges.manage',

            /*
            |--------------------------------------------------------------------------
            | USERS & ROLES
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'users.') =>
                'users.view',

            str_contains($routeName, 'users.restore') =>
                'users.restore',

            str_contains($routeName, 'manager.permissions') =>
                'manager.permissions',

            /*
            |--------------------------------------------------------------------------
            | TRAINERS & BATCHES
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'trainers.') =>
                'trainers.view',

            str_contains($routeName, 'trainers.import') =>
                'trainers.import',

            str_starts_with($routeName, 'batches.') =>
                'batches.view',

            /*
            |--------------------------------------------------------------------------
            | TESTS & EXAMS
            |--------------------------------------------------------------------------
            */
            str_starts_with($routeName, 'admin.tests.') ||
            str_starts_with($routeName, 'admin.offline-tests.') =>
                'tests.view',

            str_contains($routeName, 'questions') =>
                'tests.questions',

            str_contains($routeName, 'results') =>
                'tests.results',

            str_contains($routeName, 'export') =>
                'tests.export',

            /*
            |--------------------------------------------------------------------------
            | EVENTS
            |--------------------------------------------------------------------------
            */

            str_starts_with($routeName, 'upcoming-events.') =>
    			'upcoming-events.view',
            str_contains($routeName, '.events.') =>
                'events.view',

            str_contains($routeName, 'event.notification') =>
                'events.notifications',

            // str_contains($routeName, 'upcoming-events.') =>
            //     'upcoming-events.view',

            /*
            |--------------------------------------------------------------------------
            | ATTENDANCE
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'attendance') =>
                'attendance.view',

            /*
            |--------------------------------------------------------------------------
            | FINANCE & EXPENSES
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'expenses') ||
            str_contains($routeName, 'office-assets') =>
                'expenses.view',

            /*
            |--------------------------------------------------------------------------
            | PLACEMENT / STUDENT SERVICES
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'placements') =>
                'placements.view',

            str_contains($routeName, 'pgs') ||
            str_contains($routeName, 'part-time-jobs') ||
            str_contains($routeName, 'placement-companies') =>
                'student_services.view',

            /*
            |--------------------------------------------------------------------------
            | BROCHURES & COMPANY PROFILE
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'brochures') =>
                'brochures.view',

            str_contains($routeName, 'company_profile') =>
                'company_profile.view',

            /*
            |--------------------------------------------------------------------------
            | ACTIVITY LOGS
            |--------------------------------------------------------------------------
            */
            str_contains($routeName, 'activity.') =>
                'activity.view',

            /*
            |--------------------------------------------------------------------------
            | DEFAULT (DENY)
            |--------------------------------------------------------------------------
            */
            default => null,
        };
    }
}
