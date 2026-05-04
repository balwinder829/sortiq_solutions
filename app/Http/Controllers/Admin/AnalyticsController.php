<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\StudentTest;
use App\Models\Workshop;
use App\Models\Enquiry;
use App\Models\SalesStaff;
use App\Models\Trainer;
use Carbon\Carbon;
use DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | TEST ANALYTICS
        |--------------------------------------------------------------------------
        */

       // ================= TEST ANALYTICS =================

$totalTests = Test::count();
$activeTests = Test::where('is_active', 1)->count();
$inactiveTests = Test::where('is_active', 0)->count();

$onlineTests = Test::where('test_mode', 'online')->count();
$offlineTests = Test::where('test_mode', 'offline')->count();

$totalAttempts = StudentTest::whereHas('test')->count();

// 🏆 Top Test
$topTest = StudentTest::whereHas('test')
    ->select('test_id', DB::raw('count(*) as total'))
    ->groupBy('test_id')
    ->orderByDesc('total')
    ->first();

if ($topTest && $topTest->test_id) {
    $topTest->load('test');
}

// 📉 Least Test
$leastTest = StudentTest::whereHas('test')
    ->select('test_id', DB::raw('count(*) as total'))
    ->groupBy('test_id')
    ->orderBy('total')
    ->first();

if ($leastTest && $leastTest->test_id) {
    $leastTest->load('test');
}

// 🎓 Top College (IMPORTANT FIX)
$topCollege = StudentTest::whereHas('test')
    ->whereNotNull('college_id') // 🔥 FIX
    ->select('college_id', DB::raw('count(*) as total'))
    ->groupBy('college_id')
    ->orderByDesc('total')
    ->first();

if ($topCollege && $topCollege->college_id) {
    $topCollege->load('college');
}

// 💻 Top Course (IMPORTANT FIX)
$topCourse = Test::whereNotNull('student_course_id') // 🔥 FIX
    ->select('student_course_id', DB::raw('count(*) as total'))
    ->groupBy('student_course_id')
    ->orderByDesc('total')
    ->first();

if ($topCourse && $topCourse->student_course_id) {
    $topCourse->load('course');
}

// 📂 Top Category (IMPORTANT FIX)
$topCategory = Test::whereNotNull('test_category_id') // 🔥 FIX
    ->select('test_category_id', DB::raw('count(*) as total'))
    ->groupBy('test_category_id')
    ->orderByDesc('total')
    ->first();

if ($topCategory && $topCategory->test_category_id) {
    $topCategory->load('category');
}

        /*
        |--------------------------------------------------------------------------
        | WORKSHOP ANALYTICS
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | WORKSHOP ANALYTICS (SESSION BASED ONLY ✅)
        |--------------------------------------------------------------------------
        */

        $activeSessionNo = session('admin_session_id');

        $query = Workshop::where('session', $activeSessionNo);

        /*
        |--------------------------------------------------------------------------
        | STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $done = $statusCounts['done'] ?? 0;
        $meeting = $statusCounts['meeting'] ?? 0;
        $decided = $statusCounts['decided'] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | DATE COUNTS
        |--------------------------------------------------------------------------
        */

        $today = \Carbon\Carbon::today();

        $past = (clone $query)->whereDate('date', '<', $today)->count();
        $todayCount = (clone $query)->whereDate('date', $today)->count();
        $future = (clone $query)->whereDate('date', '>', $today)->count();

        /*
        |--------------------------------------------------------------------------
        | LEADS ANALYTICS (SESSION BASED ✅)
        |--------------------------------------------------------------------------
        */

        $activeSessionNo = session('admin_session_id');

        $query = Enquiry::enquiries()
            ->where('session_id', $activeSessionNo);

        // TOTAL
        $totalLeads = (clone $query)->count();

        // ASSIGNED
        $assignedLeads = (clone $query)
            ->whereNotNull('assigned_to')
            ->count();

        // UNASSIGNED
        $unassignedLeads = (clone $query)
            ->where(function($q){
                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to','');
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PASSOUT LEADS ANALYTICS (SESSION BASED ✅)
        |--------------------------------------------------------------------------
        */

        $activeSessionNo = session('admin_session_id');

        $query = Enquiry::passouts()
            ->where('session_id', $activeSessionNo);

        // TOTAL
        $totalPassoutLeads = (clone $query)->count();

        // ASSIGNED
        $assignedPassoutLeads = (clone $query)
            ->whereNotNull('assigned_to')
            ->count();

        // UNASSIGNED
        $unassignedPassoutLeads = (clone $query)
            ->where(function($q){
                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to', '');
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | SALES STAFF ANALYTICS
        |--------------------------------------------------------------------------
        */

        $totalStaff = SalesStaff::count();

        $activeStaff = SalesStaff::where('status', 'active')->count();

        $inactiveStaff = SalesStaff::where('status', 'inactive')->count();

        /*
        |--------------------------------------------------------------------------
        | TRAINER ANALYTICS
        |--------------------------------------------------------------------------
        */

        $totalTrainers = Trainer::count();

        $activeTrainers = Trainer::where('status', 'active')->count();

        $inactiveTrainers = Trainer::where('status', 'inactive')->count();
        /*
        |--------------------------------------------------------------------------
        | SEND DATA
        |--------------------------------------------------------------------------
        */

        return view('admin.analytics.index', compact(
            'totalTests',
            'activeTests',
            'inactiveTests',
            'onlineTests',
            'offlineTests',
            'totalAttempts',
            'topTest',
            'leastTest',
            'topCollege',
            'topCourse',
            'topCategory',
             // workshop (session-based)
            'done',
            'meeting',
            'decided',
            'past',
            'todayCount',
             // leads 🔥
            'totalLeads',
            'assignedLeads',
            'unassignedLeads',
            'future',
             // passout
            'totalPassoutLeads',
            'assignedPassoutLeads',
            'unassignedPassoutLeads',
             // sale staff
            'totalStaff',
            'activeStaff',
            'inactiveStaff',
            // trainer
            'totalTrainers',
            'activeTrainers',
            'inactiveTrainers',
        ));
    }
}