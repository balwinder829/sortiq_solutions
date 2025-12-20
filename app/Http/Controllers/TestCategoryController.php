<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;
use Str;

class TestCategoryController extends Controller
{
    /* LIST ALL */
    public function index()
    {
        $categories = TestCategory::latest()->get();
        return view('test_categories.index', compact('categories'));
    }

    /* SHOW CREATE FORM */
    public function create()
    {
        return view('test_categories.create');
    }

    /* STORE NEW CATEGORY */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:test_categories,name'
        ]);

        TestCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('test-categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /* SHOW EDIT FORM */
    public function edit(TestCategory $test_category)
    {
        return view('test_categories.edit', compact('test_category'));
    }

    /* UPDATE CATEGORY */
    public function update(Request $request, TestCategory $test_category)
    {
        $request->validate([
            'name' => 'required|unique:test_categories,name,' . $test_category->id
        ]);

        $test_category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('test-categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    /* DELETE CATEGORY */
    public function destroy(TestCategory $test_category)
    {
        $test_category->delete();
        return redirect()->route('test-categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
