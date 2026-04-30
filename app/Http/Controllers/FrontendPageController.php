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
        
        $type = null;
        $type = $page->ads_type;

        $testimonials = collect(); 

        // ✅ View mapping based on ads_type
        if ($type === 'internship') {
            $view = 'ads_front_pages.internship-landing-page';

        } elseif ($type === 'services') {
            $view = 'ads_front_pages.ads-landing-page';

        } elseif ($type === 'products') {
            $view = 'ads_front_pages.product-landing-page'; // optional (change if needed)
            $type = 'services';

        } elseif ($type === 'single product') {
            // $view = 'product-single-landing-page';
            $singleProductViews = [
                'campusedgepro-demo'      => 'ads_front_pages.single_products.campusedgepro',
                'siterankify-demo'      => 'ads_front_pages.single_products.siterankify',
                'prop99X-demo'        => 'ads_front_pages.single_products.prop99X',
                'inventorymanagesuite-demo'     => 'ads_front_pages.single_products.inventorymanagesuite',
                'blogerzworld-demo'   => 'ads_front_pages.single_products.blogerzworld',
                'allmartX-demo'  => 'ads_front_pages.single_products.allmartX',
            ];

            $view = $singleProductViews[$slug] ?? 'product-single-landing-page';

            $type = 'services';

        }else {
            $view = 'custom_pages_default_show';
            $type = 'services';
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
