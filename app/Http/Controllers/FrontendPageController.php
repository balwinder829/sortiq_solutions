<?php

// app/Http/Controllers/Frontend/PageController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\College;
use App\Models\Course;
use App\Models\Testimonial;

class FrontendPageController extends Controller
{
	public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Slug → Blade map
        |--------------------------------------------------------------------------
        */
        $colleges    = College::orderBy('college_display_name', 'asc')->get();
        $courses     = Course::orderBy('course_name', 'asc')->get();
        // $bladeMap = [
        //     'ads-landing-page'    => 'ads-landing-page',
        //     'internship'    => 'internship-landing-page',
        // ];

        // $view = $bladeMap[$slug] ?? 'custom_pages_default_show';
        $type = null;
        $testimonials = collect(); 
        if (str_contains($slug, 'internship')) {
            $view = 'internship-landing-page';
            $type = 'internship';
            
        } elseif (str_contains($slug, 'services')) {
            $view = 'ads-landing-page';
             $type = 'services';
            
        } else {
            $view = 'custom_pages_default_show';
        }

        if ($type) {
            $testimonials = Testimonial::where('status', 1)
                ->where('type', $type)
                ->latest()
                ->take(10)
                ->get();
        }

        return view($view, compact('page','colleges','courses','testimonials'));
    }
    public function showold($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail(); // 404 if inactive

        return view('frontend.pages.show', compact('page'));
    }

    public function show_ads()
    {
         return view('ads-landing-page');
    }
}
