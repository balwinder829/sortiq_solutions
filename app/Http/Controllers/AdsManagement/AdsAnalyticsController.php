<?php

namespace App\Http\Controllers\AdsManagement;
use App\Http\Controllers\Controller;

use App\Models\Page;
use App\Models\InternshipRegistration;
use App\Models\ServicesRegistration;
use Illuminate\Support\Facades\DB;

class AdsAnalyticsController extends Controller
{
    public function __construct()
    {
        
        $this->middleware('permission:ads_analytics.view')->only(['index']);
        
    }
    public function index()
    {
        return view('pages.analytics', [

            // 📄 Pages
            'totalPages' => Page::count(),
            'internshipPages' => Page::where('slug', 'like', 'internship-%')->count(),
            'servicePages' => Page::where('slug', 'like', 'services-%')->count(),

            // 🎯 Leads
            'totalInternshipLeads' => InternshipRegistration::count(),
            'totalServiceLeads' => ServicesRegistration::count(),

            // 🏆 Top Pages
            'topInternshipPage' => InternshipRegistration::select('slug', DB::raw('count(*) as total'))
                ->groupBy('slug')->orderByDesc('total')->first(),

            'topServicePage' => ServicesRegistration::select('slug', DB::raw('count(*) as total'))
                ->groupBy('slug')->orderByDesc('total')->first(),

            // 📉 Least Page
            'leastPage' => InternshipRegistration::select('slug', DB::raw('count(*) as total'))
                ->groupBy('slug')->orderBy('total')->first(),

            // 🎓 Top College
            'topCollege' => InternshipRegistration::select('college', DB::raw('count(*) as total'))
                ->groupBy('college')->orderByDesc('total')->with('collegeData')->first(),

            // 📍 Top Location
            'topLocation' => ServicesRegistration::select('location', DB::raw('count(*) as total'))
                ->groupBy('location')->orderByDesc('total')->first(),

            // 💻 Top Course
            'topCourse' => InternshipRegistration::select('technology', DB::raw('count(*) as total'))
                ->groupBy('technology')->orderByDesc('total')->with('courseData')->first(),
        ]);
    }
}