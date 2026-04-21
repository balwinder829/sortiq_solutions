<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\StudentTest;
use Illuminate\Support\Facades\DB;

class TestAnalyticsController extends Controller
{
     public function __construct()
    {
        
        $this->middleware('permission:test_analytics.view')->only(['index']);
        
    }
    public function index()
    {
        // 📊 Basic Stats (SoftDeletes already handled by default)
        $totalTests = Test::count();
        $activeTests = Test::where('is_active', 1)->count();
        $inactiveTests = Test::where('is_active', 0)->count();

        $onlineTests = Test::where('test_mode', 'online')->count();
        $offlineTests = Test::where('test_mode', 'offline')->count();

        // ✅ FIX: exclude deleted tests
        $totalAttempts = StudentTest::whereHas('test')->count();

        // 🏆 Top Test (FIXED)
        $topTest = StudentTest::whereHas('test')
            ->select('test_id', DB::raw('count(*) as total'))
            ->groupBy('test_id')
            ->orderByDesc('total')
            ->first();

        if ($topTest) {
            $topTest->load('test');
        }

        // 📉 Least Test (FIXED)
        $leastTest = StudentTest::whereHas('test')
            ->select('test_id', DB::raw('count(*) as total'))
            ->groupBy('test_id')
            ->orderBy('total')
            ->first();

        if ($leastTest) {
            $leastTest->load('test');
        }

        // 🎓 Top College (FIXED)
        $topCollege = StudentTest::whereHas('test')
            ->whereHas('college') // extra safety
            ->select('college_id', DB::raw('count(*) as total'))
            ->groupBy('college_id')
            ->orderByDesc('total')
            ->first();

        if ($topCollege) {
            $topCollege->load('college');
        }

        // 💻 Top Course (only active tests already auto-filtered)
        $topCourse = Test::select('student_course_id', DB::raw('count(*) as total'))
            ->groupBy('student_course_id')
            ->orderByDesc('total')
            ->first();

        if ($topCourse) {
            $topCourse->load('course');
        }

        // 📂 Top Category
        $topCategory = Test::select('test_category_id', DB::raw('count(*) as total'))
            ->groupBy('test_category_id')
            ->orderByDesc('total')
            ->first();

        if ($topCategory) {
            $topCategory->load('category');
        }

        return view('admin.tests.analytics', compact(
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
            'topCategory'
        ));
    }
}