<?php

// app/Http/Controllers/Admin/PageController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPageController extends Controller
{

    protected string $permissionPrefix = 'pages';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',
        'toggle'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
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
        $query = Page::query();

        // Ads Type filter
        if ($request->ads_type) {
            $query->where('ads_type', $request->ads_type);
        }

        // Status filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $pages = $query->latest('updated_at')->get();

        return view('pages.index', compact('pages'));
    }

    public function index27april()
    {
        $pages = Page::latest()->get();
        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        return view('pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:pages,slug',
            'ads_type' => 'required|in:internship,services,products,single product',
            'heading' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Page::create($request->only([
            'title',
            'slug',
            'content',
            'heading',
            'location',
            'meta_title',
            'meta_description',
            'ads_type',
            'meta_keywords',
            'banner_image',
            'featured_image',
            'is_active'
        ]));

        return redirect()->route('pages.index');
    }

    public function edit(Page $page)
    {
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'heading' => 'required|string|max:255',
            'ads_type' => 'required|in:internship,services,products,single product',
            'location' => 'required|string|max:255',
        ]);

        $page->update($request->only([
            'title',
            'slug',
            'content',
            'heading',
            'location',
            'meta_title',
            'meta_description',
            'ads_type',
            'meta_keywords',
            'banner_image',
            'featured_image',
            'is_active'
        ]));

        return redirect()->route('pages.index');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back();
    }

    public function toggle(Page $page)
    {
        $page->is_active = !$page->is_active;
        $page->save();

        return back();
    }
}
