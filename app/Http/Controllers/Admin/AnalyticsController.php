<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\StudentTest;
use App\Models\Workshop;
use App\Models\Enquiry;
use App\Models\SalesStaff;
use App\Models\ManualData;
use App\Models\HardData;
use App\Models\Trainer;
use App\Models\StudentSession;
use App\Models\Student;
use App\Models\JoiningStudent;
use App\Models\StudentPendingRegistration;
use App\Models\Batch;
use App\Models\Event;
use App\Models\UpcomingEvent;
use Carbon\Carbon;
use DB;

class AnalyticsController extends Controller
{   
    protected string $permissionPrefix = 'all_analytics';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'export'         => 'view',
    ];

    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
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

        /*
        |--------------------------------------------------------------------------
        | SALES ANALYTICS (ENQUIRY BASED + SESSION)
        |--------------------------------------------------------------------------
        */

        $activeSessionNo = session('admin_session_id');

        $baseQuery = Enquiry::where('session_id', $activeSessionNo);

        /*
        |--------------------------------------------------------------------------
        | TOTAL SALES DATA
        |--------------------------------------------------------------------------
        */
        $totalLeads = (clone $baseQuery)->where('is_passout', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | PASSOUT DATA (USING SCOPE)
        |--------------------------------------------------------------------------
        */
        $passoutLeads = (clone $baseQuery)
            ->where('is_passout', 1)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ASSIGNED / UNASSIGNED
        |--------------------------------------------------------------------------
        */
        $assignedLeads = (clone $baseQuery)
            ->where('is_passout', 0)
            ->whereNotNull('assigned_to')
            ->count();

        $unassignedLeads = (clone $baseQuery)
            ->where('is_passout', 0)
            ->where(function($q){
                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to','');
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | REGISTRATIONS
        |--------------------------------------------------------------------------
        */
        $totalRegistrations = (clone $baseQuery)
            ->whereNotNull('registered_at')
            ->count();

        $pendingRegistrations = (clone $baseQuery)
            ->whereNull('registered_at')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | MANUAL / HARD DATA (SEPARATE TABLES)
        |--------------------------------------------------------------------------
        */
        $manualDataCount = ManualData::where('session_id', $activeSessionNo)->count();

        $hardDataCount = HardData::where('session_id', $activeSessionNo)->count();

        /*
        |--------------------------------------------------------------------------
        | HIDDEN DATA (ASSUMPTION: SOFT DELETE)
        |--------------------------------------------------------------------------
        */
        $hiddenDataCount = Enquiry::onlyTrashed()
            ->where('session_id', $activeSessionNo)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | SALES TEAM
        |--------------------------------------------------------------------------
        */
        $totalSalesStaff = SalesStaff::where('status', 'active')->count();

        /*
        |--------------------------------------------------------------------------
        | TOP / LOWEST PERFORMER (REGISTRATION)
        |--------------------------------------------------------------------------
        */

        $staffPerformance = Enquiry::where('session_id', $activeSessionNo)
            ->whereNotNull('registered_at')
            ->select('assigned_to', DB::raw('count(*) as total'))
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderByDesc('total')
            ->get();

        // TOP
        $topPerformer = $staffPerformance->first();

        // LOWEST
        $lowestPerformer = $staffPerformance->last();

        // LOAD RELATION (SAFE)
        if ($topPerformer && $topPerformer->assigned_to) {
            $topPerformer->load('salesStaff');
        }

        if ($lowestPerformer && $lowestPerformer->assigned_to) {
            $lowestPerformer->load('salesStaff');
        }

        // $activeSessionNo = session('admin_session_id');

        // $query = Enquiry::enquiries()
        //     ->where('session_id', $activeSessionNo);

        // // TOTAL
        // $totalLeads = (clone $query)->count();

        // // ASSIGNED
        // $assignedLeads = (clone $query)
        //     ->whereNotNull('assigned_to')
        //     ->count();

        // // UNASSIGNED
        // $unassignedLeads = (clone $query)
        //     ->where(function($q){
        //         $q->whereNull('assigned_to')
        //           ->orWhere('assigned_to','');
        //     })
        //     ->count();

        /*
        |--------------------------------------------------------------------------
        | PASSOUT LEADS ANALYTICS (SESSION BASED ✅)
        |--------------------------------------------------------------------------
        */

        // $activeSessionNo = session('admin_session_id');

        // $query = Enquiry::passouts()
        //     ->where('session_id', $activeSessionNo);

        // // TOTAL
        // $totalPassoutLeads = (clone $query)->count();

        // // ASSIGNED
        // $assignedPassoutLeads = (clone $query)
        //     ->whereNotNull('assigned_to')
        //     ->count();

        // // UNASSIGNED
        // $unassignedPassoutLeads = (clone $query)
        //     ->where(function($q){
        //         $q->whereNull('assigned_to')
        //           ->orWhere('assigned_to', '');
        //     })
        //     ->count();

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
        | BATCH / MENTOR ANALYTICS
        |--------------------------------------------------------------------------
        */

        $totalBatches = Batch::count();

        $onlineBatches = Batch::where('batch_mode', 'online')->count();

        $offlineBatches = Batch::where('batch_mode', 'offline')->count();

        /*
        |--------------------------------------------------------------------------
        | TOP / LOWEST MENTOR (BASED ON BATCH COUNT)
        |--------------------------------------------------------------------------
        */

       $mentorBatchCounts = Batch::with('mentor')
            ->select('batch_assign', DB::raw('count(*) as total'))
            ->whereNotNull('batch_assign')
            ->groupBy('batch_assign')
            ->orderByDesc('total')
            ->get();

        $topMentor = $mentorBatchCounts->first();
        $lowestMentor = $mentorBatchCounts->last();

        /*
        |--------------------------------------------------------------------------
        | LOAD TRAINER RELATION
        |--------------------------------------------------------------------------
        */

        if ($topMentor && $topMentor->batch_assign) {
            $topMentor->mentor = Trainer::find($topMentor->batch_assign);
        }

        if ($lowestMentor && $lowestMentor->batch_assign) {
            $lowestMentor->mentor = Trainer::find($lowestMentor->batch_assign);
        }

         /*
        |--------------------------------------------------------------------------
        | STUDENT DATA
        |--------------------------------------------------------------------------
        */
        $allstudents = Student::where('session', $activeSessionNo)
                ->selectRaw('
                    COUNT(*) as total_students,
                    SUM(CASE WHEN is_online = 1 THEN 1 ELSE 0 END) as online_students,
                    SUM(CASE WHEN is_online = 0 THEN 1 ELSE 0 END) as offline_students
                ')
                ->first();

         $totalConfirmed = Student::where('certificate_status', 0)->where('session', $activeSessionNo)->count();
        $totalCertificate = Student::where('certificate_status', 1)->where('send_to_close', 0)->where('session', $activeSessionNo)->count();
        $totalClosed = Student::where('send_to_close', 1)->where('session', $activeSessionNo)->count();
        $placedStudents = Student::where('is_placed', 1)->where('session', $activeSessionNo)->count();
        // Total Joined Students
        $totalJoinedStudents = \App\Models\JoiningStudent::count();

        // Pending Registrations
        $totalPendingRegistrations = \App\Models\StudentPendingRegistration::count();

        /*
        |--------------------------------------------------------------------------
        | EVENT ANALYTICS (TYPE WISE COUNT)
        |--------------------------------------------------------------------------
        */

        $eventCounts = Event::selectRaw('event_type, COUNT(*) as total')
            ->groupBy('event_type')
            ->pluck('total', 'event_type');

        // Safe values
        $employeeEvents = $eventCounts['employee'] ?? 0;
        $collegeEvents  = $eventCounts['college'] ?? 0;
        $studentEvents  = $eventCounts['student'] ?? 0;

        // Total Events (optional)
        $totalEvents = $eventCounts->sum();

        $today = Carbon::today();

        $upcomingEventsCount = UpcomingEvent::whereDate('event_date', '>=', $today)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | ADS MANAGEMENT ANALYTICS (CLEAN + SEPARATED)
        |--------------------------------------------------------------------------
        */

        // ===== PAGES (ONLY PAGES COUNT) =====
        $totalPages = \App\Models\Page::count();


        // ===== INDIVIDUAL ENTRIES =====

        // Services
        $serviceEntries = \App\Models\ServicesRegistration::count();

        // Internship
        $internshipEntries = \App\Models\InternshipRegistration::count();

        // Products
        $productEntries = \App\Models\ProductsRegistration::count();

        // Single Products
        $singleProductEntries = \App\Models\SingleProductRegistration::count();


        // ===== DATE BASED =====
        /*
        |--------------------------------------------------------------------------
        | INDIVIDUAL LATEST ENTRIES (TODAY / YESTERDAY)
        |--------------------------------------------------------------------------
        */

        // ===== SERVICES =====
        $todayService = \App\Models\ServicesRegistration::whereDate('created_at', today())->count();
        $yesterdayService = \App\Models\ServicesRegistration::whereDate('created_at', today()->subDay())->count();


        // ===== INTERNSHIP =====
        $todayInternship = \App\Models\InternshipRegistration::whereDate('created_at', today())->count();
        $yesterdayInternship = \App\Models\InternshipRegistration::whereDate('created_at', today()->subDay())->count();


        // ===== PRODUCT =====
        $todayProduct = \App\Models\ProductsRegistration::whereDate('created_at', today())->count();
        $yesterdayProduct = \App\Models\ProductsRegistration::whereDate('created_at', today()->subDay())->count();


        // ===== SINGLE PRODUCT =====
        $todaySingleProduct = \App\Models\SingleProductRegistration::whereDate('created_at', today())->count();
        $yesterdaySingleProduct = \App\Models\SingleProductRegistration::whereDate('created_at', today()->subDay())->count();

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE ANALYTICS (STATUS WISE)
        |--------------------------------------------------------------------------
        */

        $employeeCounts = \App\Models\Employee::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Individual counts (safe)
        $activeEmployees     = $employeeCounts['active'] ?? 0;
        $inactiveEmployees   = $employeeCounts['inactive'] ?? 0;
        $resignedEmployees   = $employeeCounts['resigned'] ?? 0;
        $terminatedEmployees = $employeeCounts['terminated'] ?? 0;

        // Total
        $totalEmployees = $employeeCounts->sum();

        /*
        |--------------------------------------------------------------------------
        | SECURITY ANALYTICS
        |--------------------------------------------------------------------------
        */

        // Blocked IPs
        $blockedIps = \App\Models\BlockedIp::count();

        // Allowed IPs
        $allowedIps = \App\Models\AllowedIp::count();

        // Blocked Numbers
        $blockedNumbers = \App\Models\BlockedNumber::count();

        // Total Users (excluding admin role = 1)
        $totalUsers = \App\Models\User::whereIn('role', [4, 5, 8])->count();
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
            // 'totalLeads',
            // 'assignedLeads',
            // 'unassignedLeads',
             // sales
            'totalLeads',
            'passoutLeads',
            'assignedLeads',
            'unassignedLeads',
            'manualDataCount',
            'hardDataCount',
            'hiddenDataCount',
            'totalRegistrations',
            'pendingRegistrations',
            'totalSalesStaff',
            'topPerformer',
            'lowestPerformer',
            'future',
             // passout
            // 'totalPassoutLeads',
            // 'assignedPassoutLeads',
            // 'unassignedPassoutLeads',
             // sale staff
            'totalStaff',
            'activeStaff',
            'inactiveStaff',
            // trainer
            'totalTrainers',
            'activeTrainers',
            'inactiveTrainers',
            'totalBatches',
            'onlineBatches',
            'offlineBatches',
            'topMentor',
            'lowestMentor',
            //students
            'allstudents',
            'totalConfirmed',
            'totalCertificate',
            'totalClosed',
            'placedStudents',
            'totalJoinedStudents',
            'totalPendingRegistrations',
            //events
            'employeeEvents',
            'collegeEvents',
            'studentEvents',
            'totalEvents',
            'upcomingEventsCount',
            //Ads
            'totalPages',
            'serviceEntries',
            'internshipEntries',
            'productEntries',
            'singleProductEntries',
            'todayService',
            'yesterdayService',
            'todayInternship',
            'yesterdayInternship',
            'todayProduct',
            'yesterdayProduct',
            'todaySingleProduct',
            'yesterdaySingleProduct',
            //HR MANAGEMENT
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'resignedEmployees',
            'terminatedEmployees',
            //SECURITY
            'blockedIps',
            'allowedIps',
            'blockedNumbers',
            'totalUsers',
        ));
    }
}