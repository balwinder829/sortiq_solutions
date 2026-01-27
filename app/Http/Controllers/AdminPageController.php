<?php

// app/Http/Controllers/Admin/PageController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPageController extends Controller
{
    public function index()
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
