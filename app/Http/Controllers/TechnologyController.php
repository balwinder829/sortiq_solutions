<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technology;

class TechnologyController extends Controller
{
    public function index()
    {
        $technologies = Technology::orderBy('name')->get();
        return view('technologies.index', compact('technologies'));
    }

    public function create()
    {
        return view('technologies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:technologies,name',
            'category' => 'required'
        ]);

        Technology::create($request->all());

        return redirect()->route('technologies.index')
            ->with('success', 'Technology added');
    }

    public function edit(Technology $technology)
    {
        return view('technologies.edit', compact('technology'));
    }

    public function update(Request $request, Technology $technology)
    {
        $request->validate([
            'name' => 'required|unique:technologies,name,' . $technology->id,
            'category' => 'required'
        ]);

        $technology->update($request->all());

        return redirect()->route('technologies.index')
            ->with('success', 'Technology updated');
    }

    public function destroy(Technology $technology)
    {
        $technology->delete();
        return back()->with('success', 'Technology deleted');
    }
}

