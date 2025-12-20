<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class AnalyticsController extends Controller
{
    public function index()
    {   

        $activeSessionId = session('admin_session_id');
        // 1. College-wise Student Count
        $collegeCounts = Student::with('collegeData')->select('college_name', DB::raw('COUNT(*) as total_students'))
            ->whereNotNull('college_name')
            ->where('session', $activeSessionId)
            ->groupBy('college_name')
            ->orderBy('total_students', 'DESC')
            ->get();

        // 2. College-wise Total Revenue
        $collegeRevenue = Student::with('collegeData')->select('college_name', DB::raw('SUM(total_fees) as total_revenue'))
            ->whereNotNull('college_name')
            ->where('session', $activeSessionId)
            ->groupBy('college_name')
            ->orderBy('total_revenue', 'DESC')
            ->get();

        // 3. Top College (Highest Students)
        $topCollege = $collegeCounts->first();

        // 4. Session-wise Student Count
        $sessionCounts = Student::with('sessionData')->select('session', DB::raw('COUNT(*) as total_students'))
            ->whereNotNull('session')
            ->groupBy('session')
            ->orderBy('session')
            ->get();

        // 5. Session-wise Revenue
        $sessionRevenue = Student::with('sessionData')->select('session', DB::raw('SUM(total_fees) as total_revenue'))
            ->whereNotNull('session')
            ->groupBy('session')
            ->orderBy('session')
            ->get();

        return view('analytics.index', compact(
            'collegeCounts',
            'collegeRevenue',
            'topCollege',
            'sessionCounts',
            'sessionRevenue'
        ));
    }
}
