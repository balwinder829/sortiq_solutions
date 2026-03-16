<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\HelpdeskTechnology;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class HelpdeskTechnologyController extends Controller
{	
	public function __construct()
    {
        $this->middleware('permission:helpdesk_categories.view')->only('index');
        $this->middleware('permission:helpdesk_categories.create')->only(['create','store']);
        $this->middleware('permission:helpdesk_categories.edit')->only(['edit','update']);
        $this->middleware('permission:helpdesk_categories.delete')->only('destroy');
        $this->middleware('permission:helpdesk_categories.import')->only('import');
    }
    public function index()
    {
        $items = HelpdeskTechnology::latest()->get();
        return view('helpdesk.admin.technologies.index', compact('items'));
    }

    public function create()
    {
        return view('helpdesk.admin.technologies.create');
    }


public function store(Request $request)
{
    $slug = Str::slug($request->name,'_');

    $validator = Validator::make(
        [
            'name' => $request->name,
            'slug' => $slug,
        ],
        [
            'name' => 'required|string|max:255',
            'slug' => 'unique:helpdesk_technologies,slug',
        ],
        [
            'slug.unique' => 'This Category already exists.'
        ]
    );

    // 👇 move slug error to name field
    if ($validator->fails()) {
        $validator->errors()->add('name', $validator->errors()->first('slug'));
        return back()->withErrors($validator)->withInput();
    }

    HelpdeskTechnology::create([
        'name' => $request->name,
        'slug' => $slug
    ]);

    return redirect()->route('admin.helpdesk.categories.index')
        ->with('success','Category created successfully');
}

	public function store1(Request $request)
	{
	    $slug = Str::slug($request->name);

	    Validator::make(
	        [
	            'name' => $request->name,
	            'slug' => $slug
	        ],
	        [
	            'name' => 'required|string|max:255',
	            'slug' => 'required|unique:helpdesk_technologies,slug'
	        ]
	    )->validate();

	    HelpdeskTechnology::create([
	        'name' => $request->name,
	        'slug' => $slug
	    ]);

	    return redirect()->route('admin.helpdesk.categories.index')
	        ->with('success','Category created successfully');
	}



    public function show(HelpdeskTechnology $technology)
    {
        return view('helpdesk.admin.technologies.show', compact('technology'));
    }

    public function edit(HelpdeskTechnology $category)
    {

        return view('helpdesk.admin.technologies.edit', compact('category'));
    }
 public function update(Request $request, HelpdeskTechnology $category)
{
    $slug = Str::slug($request->name,'_');

    $validator = Validator::make(
        [
            'name' => $request->name,
            'slug' => $slug,
        ],
        [
            'name' => 'required|string|max:255',
            'slug' => 'unique:helpdesk_technologies,slug,' . $category->id,
        ],
        [
            'slug.unique' => 'This Category already exists.'
        ]
    );

    if ($validator->fails()) {
        $validator->errors()->add('name', $validator->errors()->first('slug'));
        return back()->withErrors($validator)->withInput();
    }

    $category->update([
        'name' => $request->name,
        'slug' => $slug
    ]);

    return redirect()->route('admin.helpdesk.categories.index')
        ->with('success','Category updated successfully');
}

	public function updat21e(Request $request, $id, HelpdeskTechnology $category)
	{
	    $technology = HelpdeskTechnology::findOrFail($id);

	    $slug = Str::slug($request->name,'_');

	    $validator = Validator::make(
	        [
	            'name' => $request->name,
	            'slug' => $slug,
	        ],
	        [
	            'name' => 'required|string|max:255',
	            // 👇 ignore current record id
	            'slug' => 'unique:helpdesk_technologies,slug,' . $technology->id,
	        ],
	        [
	            'slug.unique' => 'This Category already exists.'
	        ]
	    );

	    // 👇 move slug error under name input
	    if ($validator->fails()) {
	        $validator->errors()->add('name', $validator->errors()->first('slug'));
	        return back()->withErrors($validator)->withInput();
	    }

	    $technology->update([
	        'name' => $request->name,
	        'slug' => $slug
	    ]);

	    return redirect()->route('admin.helpdesk.categories.index')
	        ->with('success','Category updated successfully');
	}


    public function destroy(HelpdeskTechnology $category)
    {
    	// dd($category);
        $category->delete();

        return back()->with('success','Deleted successfully');
    }
}
