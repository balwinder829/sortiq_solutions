<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    public function index()
    {
        $references = Reference::all();
        return view('references.index', compact('references'));
    }

    public function create()
    {
        return view('references.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // 'employee_id' => 'required|unique:references'
        ]);

        Reference::create($request->all());

        return redirect()->route('references.index')->with('success', 'Reference created successfully.');
    }

    public function edit(Reference $reference)
    {
        return view('references.edit', compact('reference'));
    }

    public function update(Request $request, Reference $reference)
    {
        $request->validate([
            'name' => 'required',
            // 'employee_id' => 'required|unique:references,employee_id,' . $reference->id
        ]);

        $reference->update($request->all());

        return redirect()->route('references.index')->with('success', 'Reference updated successfully.');
    }

    public function destroy(Reference $reference)
    {
        $reference->delete();
        return redirect()->route('references.index')->with('success', 'Reference deleted successfully.');
    }
}
